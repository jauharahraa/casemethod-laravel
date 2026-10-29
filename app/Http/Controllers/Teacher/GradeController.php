<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CaseStudy;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Submission;
use App\Services\RubricService;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    private function mine(Submission $s): Submission
    {
        $s->loadMissing('case');
        abort_unless($s->case->user_id === auth()->id(), 404);
        return $s;
    }

    public function dashboard()
    {
        $uid = auth()->id();
        $own = fn ($q) => $q->where('user_id', $uid);
        return view('teacher.dashboard', [
            'stats' => [
                'subjects' => \App\Models\Subject::where('user_id', $uid)->count(),
                'classrooms' => Classroom::where('user_id', $uid)->count(),
                'students' => \App\Models\Student::where('user_id', $uid)->count(),
                'cases' => CaseStudy::where('user_id', $uid)->count(),
            ],
            'ungraded' => Submission::whereHas('case', $own)->where('status', Submission::LOCKED)->whereDoesntHave('grade')->count(),
            'cases' => CaseStudy::where('user_id', $uid)->with('classroom')->withCount('submissions')->latest()->limit(6)->get(),
            'recent' => AuditLog::where('owner_id', $uid)->latest('id')->limit(10)->get(),
        ]);
    }

    public function index(Request $r)
    {
        $uid = auth()->id();
        $subs = Submission::whereHas('case', fn ($q) => $q->where('user_id', $uid))
            ->where('status', '!=', Submission::DRAFT)
            ->when($r->filled('case_id'), fn ($q) => $q->where('case_id', $r->case_id))
            ->when($r->input('filter') === 'ungraded', fn ($q) => $q->where('status', Submission::LOCKED)->whereDoesntHave('grade'))
            ->with(['case', 'student', 'grade'])->latest('updated_at')->paginate(20)->withQueryString();
        $cases = CaseStudy::where('user_id', $uid)->orderBy('title')->get();
        return view('teacher.grading', compact('subs', 'cases'));
    }

    public function show(Submission $submission)
    {
        $this->mine($submission)->load(['student', 'rubricVersion', 'versions.feedback', 'grade']);
        return view('teacher.grade_show', ['s' => $submission, 'rubric' => $submission->rubricVersion->data]);
    }

    public function save(Request $r, Submission $submission)
    {
        $this->mine($submission)->load('rubricVersion');
        abort_unless($submission->status === Submission::LOCKED, 422, 'Siswa belum menyelesaikan jawaban.');
        $rubric = $submission->rubricVersion->data;

        $rules = ['comment' => 'nullable|string|max:5000', 'publish' => 'nullable|boolean'];
        foreach ($rubric['aspects'] as $k => $a) {
            foreach ($a['criteria'] as $i => $c) {
                $rules["scores.$k.$i"] = 'required|numeric|min:0|max:'.$c['max'];
            }
        }
        $d = $r->validate($rules);
        $calc = RubricService::calculate($rubric, $d['scores']);
        $publish = (bool) ($d['publish'] ?? false);

        $grade = Grade::firstOrNew(['submission_id' => $submission->id]);
        $grade->fill([
            'criterion_scores' => $d['scores'],
            'aspect_scores' => $calc['aspect_scores'],
            'final_score' => $calc['final'],
            'comment' => $d['comment'] ?? null,
        ]);
        if ($publish && ! $grade->published) {
            $grade->published = true;
            $grade->published_at = now();
        }
        $grade->save();

        AuditLog::record($publish ? 'grade.publish' : 'grade.save', ($publish ? 'Memublikasikan' : 'Menyimpan').' nilai '.$submission->student->full_name.' ('.$submission->case->title.') = '.$calc['final']);
        return redirect()->route('guru.grading.show', $submission)->with('ok', $publish ? 'Nilai disimpan dan dipublikasikan.' : 'Nilai disimpan (belum dipublikasikan).');
    }

    public function togglePublish(Submission $submission)
    {
        $this->mine($submission)->load('grade', 'student');
        $g = $submission->grade;
        abort_unless($g, 422, 'Nilai belum dibuat.');
        $g->update(['published' => ! $g->published, 'published_at' => $g->published ? null : now()]);
        AuditLog::record('grade.toggle', ($g->published ? 'Memublikasikan' : 'Menarik publikasi').' nilai '.$submission->student->full_name);
        return back()->with('ok', $g->published ? 'Nilai dipublikasikan.' : 'Publikasi nilai ditarik.');
    }

    // ---- Rekap
    private function recapRows(Request $r): array
    {
        $uid = auth()->id();
        $cases = CaseStudy::where('user_id', $uid)->where('status', 'published')
            ->when($r->filled('classroom_id'), fn ($q) => $q->where('classroom_id', $r->classroom_id))
            ->with(['classroom.subject', 'classroom.students'])->get();
        $subs = Submission::whereIn('case_id', $cases->pluck('id'))->with('grade')->get()->keyBy(fn ($s) => $s->case_id.'-'.$s->student_id);
        $q = mb_strtolower(trim((string) $r->q));

        $rows = [];
        foreach ($cases as $case) {
            foreach ($case->classroom->students as $st) {
                if ($q !== '' && ! str_contains(mb_strtolower($st->full_name.' '.$st->participant_number), $q)) continue;
                $s = $subs[$case->id.'-'.$st->id] ?? null;
                $rows[] = [
                    'sub_id' => $s?->id, 'status' => $s?->status, 'graded' => (bool) $s?->grade, 'published' => (bool) $s?->grade?->published,
                    'class' => $case->classroom->name, 'subject' => $case->classroom->subject->name, 'case' => $case->title,
                    'student' => $st->full_name, 'student_id' => $st->id, 'number' => $st->participant_number,
                    'score' => $s?->grade?->final_score,
                ];
            }
        }
        return $rows;
    }

    public function recap(Request $r)
    {
        $rows = $this->recapRows($r);
        $progress = [];
        foreach ($rows as $row) {
            $p = &$progress[$row['student_id']];
            $p['done'] = ($p['done'] ?? 0) + ($row['status'] === Submission::LOCKED ? 1 : 0);
            $p['total'] = ($p['total'] ?? 0) + 1;
            unset($p);
        }
        $classrooms = Classroom::where('user_id', auth()->id())->orderBy('name')->get();
        return view('teacher.recap', compact('rows', 'progress', 'classrooms'));
    }

    public function export(Request $r)
    {
        $rows = $this->recapRows($r);
        $safe = fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'".$v : $v; // cegah CSV injection
        $labels = ['draft' => 'Draft', 'feedback' => 'Feedback AI', 'revising' => 'Revisi', 'locked' => 'Selesai'];

        return response()->streamDownload(function () use ($rows, $safe, $labels) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
            
            // Menggunakan pemisah titik koma (;) agar otomatis rapi di Excel Indonesia
            fputcsv($out, ['Kelas', 'Materi', 'Kasus', 'Nama Siswa', 'Nomor Peserta', 'Status', 'Nilai Akhir', 'Nilai Dipublikasikan'], ';');
            foreach ($rows as $x) {
                fputcsv($out, array_map($safe, [
                    $x['class'], $x['subject'], $x['case'], $x['student'], $x['number'],
                    $labels[$x['status']] ?? 'Belum mulai', $x['score'] ?? '', $x['published'] ? 'Ya' : 'Tidak',
                ]), ';');
            }
            fclose($out);
        }, 'rekap-nilai-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
