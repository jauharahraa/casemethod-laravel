<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CaseStudy;
use App\Models\Submission;
use App\Models\SubmissionVersion;
use App\Services\GeminiFeedbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private function student(Request $r) { return $r->attributes->get('student'); }

    /** Siswa hanya boleh membuka kasus terpublikasi milik kelas yang ia masuki. */
    private function caseFor(Request $r, CaseStudy $kasus): CaseStudy
    {
        $c = $r->attributes->get('classroom');
        abort_unless($kasus->classroom_id === $c->id && $kasus->status === 'published', 404);
        return $kasus;
    }

    private function submissionFor(Request $r, CaseStudy $kasus): Submission
    {
        $kasus->loadMissing('currentRubric');
        $sub = Submission::firstOrCreate(
            ['case_id' => $kasus->id, 'student_id' => $this->student($r)->id],
            ['rubric_version_id' => $kasus->currentRubric->id, 'status' => Submission::DRAFT]
        );
        $sub->versions()->firstOrCreate(['version' => 1], ['status' => 'draft']);
        return $sub;
    }

    private function answerRules(bool $strict): array
    {
        $base = $strict ? 'required|string|min:20|max:20000' : 'nullable|string|max:20000';
        return ['identification' => $base, 'analysis' => $base, 'solution' => $base];
    }

    private function lock(int $id): Submission
    {
        return Submission::whereKey($id)->lockForUpdate()->firstOrFail();
    }

    public function dashboard(Request $r)
    {
        $student = $this->student($r);
        $classroom = $r->attributes->get('classroom');
        $cases = CaseStudy::where('classroom_id', $classroom->id)->where('status', 'published')->orderByRaw('deadline is null')->orderBy('deadline')->get();
        $subs = Submission::where('student_id', $student->id)->whereIn('case_id', $cases->pluck('id'))->with('grade')->get()->keyBy('case_id');
        return view('student.dashboard', compact('student', 'classroom', 'cases', 'subs'));
    }

    public function show(Request $r, CaseStudy $kasus)
    {
        $kasus = $this->caseFor($r, $kasus);
        $sub = $this->submissionFor($r, $kasus)->load(['versions.feedback', 'grade', 'rubricVersion']);
        return view('student.case', ['case' => $kasus, 'sub' => $sub, 'rubric' => $sub->rubricVersion->data]);
    }

    public function saveDraft(Request $r, CaseStudy $kasus)
    {
        $sub = $this->submissionFor($r, $this->caseFor($r, $kasus));
        $v = $sub->versions()->get()->last();
        $editable = in_array($sub->status, [Submission::DRAFT, Submission::REVISING]) && $v->status === 'draft';
        if (! $editable) {
            return $r->expectsJson() ? response()->json(['message' => 'Jawaban sudah dikunci.'], 409) : back()->withErrors(['answer' => 'Jawaban sudah dikunci.']);
        }
        $v->update($r->validate($this->answerRules(false)));
        return $r->expectsJson() ? response()->json(['saved_at' => now()->format('H:i:s')]) : back()->with('ok', 'Draft tersimpan.');
    }

    public function submit(Request $r, CaseStudy $kasus, GeminiFeedbackService $ai)
    {
        $sub = $this->submissionFor($r, $this->caseFor($r, $kasus));
        $d = $r->validate($this->answerRules(true));

        $version = DB::transaction(function () use ($sub, $d) {
            $locked = $this->lock($sub->id);
            abort_unless($locked->status === Submission::DRAFT, 409, 'Jawaban pertama sudah dikirim.');
            $v = $locked->versions()->where('version', 1)->firstOrFail();
            $v->update($d + ['status' => 'submitted', 'submitted_at' => now()]);
            $locked->update(['status' => Submission::FEEDBACK, 'submitted_at' => now()]);
            return $v;
        });

        AuditLog::record('submission.submit', "{$this->student($r)->full_name} mengirim jawaban \"{$kasus->title}\"", $kasus->user_id, $this->student($r)->id);
        $fb = $ai->generate($version);
        return redirect()->route('siswa.case', $kasus)->with($fb->status === 'ok' ? 'ok' : 'warn',
            $fb->status === 'ok' ? 'Jawaban terkirim. Baca feedback di bawah.' : 'Jawaban terkirim, tetapi feedback AI gagal. Tekan "Coba lagi".');
    }

    public function retryFeedback(Request $r, CaseStudy $kasus, GeminiFeedbackService $ai)
    {
        $sub = $this->submissionFor($r, $this->caseFor($r, $kasus));
        abort_unless($sub->status === Submission::FEEDBACK, 409, 'Feedback hanya dapat dibuat ulang sebelum revisi dimulai.');
        $v = $sub->versions()->where('version', 1)->with('feedback')->firstOrFail();
        if ($v->feedback?->status !== 'ok') {
            $fb = $ai->generate($v); // updateOrCreate: tidak ada pengumpulan ganda, revisi tidak terpakai
            return back()->with($fb->status === 'ok' ? 'ok' : 'warn', $fb->status === 'ok' ? 'Feedback berhasil dibuat.' : 'Feedback AI masih gagal. Coba lagi nanti.');
        }
        return back();
    }

    public function startRevision(Request $r, CaseStudy $kasus)
    {
        $sub = $this->submissionFor($r, $this->caseFor($r, $kasus));

        DB::transaction(function () use ($sub) {
            $locked = $this->lock($sub->id);
            abort_unless($locked->status === Submission::FEEDBACK, 409, 'Revisi tidak tersedia.');
            $v1 = $locked->versions()->where('version', 1)->with('feedback')->firstOrFail();
            abort_unless($v1->feedback?->status === 'ok', 409, 'Revisi hanya dapat dimulai setelah feedback AI tersedia.');
            abort_if($locked->versions()->where('version', 2)->exists(), 409, 'Kesempatan revisi sudah terpakai.');
            $locked->versions()->create([
                'version' => 2, 'status' => 'draft',
                'identification' => $v1->identification, 'analysis' => $v1->analysis, 'solution' => $v1->solution,
            ]); // unique(submission_id, version) menjamin maksimal satu revisi
            $locked->update(['status' => Submission::REVISING]);
        });

        return redirect()->route('siswa.case', $kasus)->with('ok', 'Mode revisi dimulai. Ini kesempatan revisi satu-satunya.');
    }

    public function submitRevision(Request $r, CaseStudy $kasus)
    {
        $sub = $this->submissionFor($r, $this->caseFor($r, $kasus));
        $d = $r->validate($this->answerRules(true));

        DB::transaction(function () use ($sub, $d) {
            $locked = $this->lock($sub->id);
            abort_unless($locked->status === Submission::REVISING, 409, 'Tidak ada revisi yang sedang dikerjakan.');
            $locked->versions()->where('version', 2)->firstOrFail()->update($d + ['status' => 'submitted', 'submitted_at' => now()]);
            $locked->update(['status' => Submission::LOCKED, 'locked_at' => now()]);
        });

        AuditLog::record('submission.revise', "{$this->student($r)->full_name} mengirim revisi \"{$kasus->title}\"", $kasus->user_id, $this->student($r)->id);
        return redirect()->route('siswa.case', $kasus)->with('ok', 'Revisi terkirim. Jawaban dikunci.');
    }

    public function finish(Request $r, CaseStudy $kasus)
    {
        $sub = $this->submissionFor($r, $this->caseFor($r, $kasus));

        DB::transaction(function () use ($sub) {
            $locked = $this->lock($sub->id);
            abort_unless($locked->status === Submission::FEEDBACK, 409, 'Tidak dapat diselesaikan pada tahap ini.');
            $locked->update(['status' => Submission::LOCKED, 'locked_at' => now()]);
        });

        AuditLog::record('submission.finish', "{$this->student($r)->full_name} menyelesaikan \"{$kasus->title}\" tanpa revisi", $kasus->user_id, $this->student($r)->id);
        return redirect()->route('siswa.case', $kasus)->with('ok', 'Selesai. Jawaban dikunci dan menunggu penilaian guru.');
    }
}
