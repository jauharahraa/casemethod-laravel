<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CaseStudy;
use App\Models\Classroom;
use App\Models\RubricVersion;
use App\Services\RubricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CaseController extends Controller
{
    private function mine(CaseStudy $k): CaseStudy
    {
        abort_unless($k->user_id === auth()->id(), 404);
        return $k;
    }

    private function classrooms()
    {
        return Classroom::where('user_id', auth()->id())->with('subject')->orderBy('name')->get();
    }

    public function index()
    {
        $cases = CaseStudy::where('user_id', auth()->id())->with('classroom.subject')->withCount('submissions')->latest()->get();
        return view('teacher.cases', compact('cases'));
    }

    public function create()
    {
        return view('teacher.case_form', [
            'case' => new CaseStudy(), 'classrooms' => $this->classrooms(),
            'rubric' => RubricService::default(), 'lockWeights' => false,
        ]);
    }

    private function rules(): array
    {
        return [
            'classroom_id' => ['required', Rule::exists('classrooms', 'id')->where('user_id', auth()->id())],
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:50000',
            'instructions' => 'required|string|max:10000',
            'objectives' => 'required|string|max:10000',
            'deadline' => 'nullable|date',
            'action' => 'required|in:draft,publish',
        ];
    }

    public function store(Request $r)
    {
        $d = $r->validate($this->rules());
        $rubric = RubricService::fromRequest($r);

        $case = DB::transaction(function () use ($d, $rubric) {
            $case = CaseStudy::create([
                'user_id' => auth()->id(),
                'status' => $d['action'] === 'publish' ? 'published' : 'draft',
            ] + collect($d)->except('action')->all());
            $case->rubricVersions()->create(['version' => 1, 'data' => $rubric]);
            return $case;
        });

        AuditLog::record('case.create', "Membuat kasus \"{$case->title}\" (".($case->isPublished() ? 'dipublikasikan' : 'draft').')');
        return redirect()->route('guru.cases')->with('ok', $case->isPublished() ? 'Kasus dipublikasikan.' : 'Kasus disimpan sebagai draft.');
    }

    public function edit(CaseStudy $kasus)
    {
        $this->mine($kasus)->load('currentRubric');
        $rubric = $kasus->currentRubric->data;
        $simple = RubricService::isSimplified($rubric);
        return view('teacher.case_form', [
            'case' => $kasus, 'classrooms' => $this->classrooms(),
            // Rubrik versi lama (banyak kriteria) ditampilkan dalam format baru; baru tersimpan sebagai versi baru saat guru menyimpan.
            'rubric' => $simple ? $rubric : RubricService::default(),
            'lockWeights' => $kasus->isPublished() && $simple,
        ]);
    }

    public function update(Request $r, CaseStudy $kasus)
    {
        $this->mine($kasus)->load('currentRubric');
        $d = $r->validate($this->rules());
        $current = $kasus->currentRubric;
        $lock = $kasus->isPublished() && RubricService::isSimplified($current->data);
        $rubric = RubricService::fromRequest($r, $lock ? RubricService::weights($current->data) : null);

        DB::transaction(function () use ($kasus, $d, $rubric, $current) {
            $kasus->update(collect($d)->except('action')->all() + ['status' => $d['action'] === 'publish' ? 'published' : $kasus->status]);
            if ($rubric != $current->data) {
                // Versi baru; pengumpulan lama tetap memakai versi rubrik yang dipin padanya.
                $kasus->rubricVersions()->create(['version' => $current->version + 1, 'data' => $rubric]);
            }
        });

        AuditLog::record('case.update', "Mengubah kasus \"{$kasus->title}\"");
        return redirect()->route('guru.cases')->with('ok', 'Kasus diperbarui.');
    }

    public function duplicate(CaseStudy $kasus)
    {
        $this->mine($kasus)->load('currentRubric');
        $copy = DB::transaction(function () use ($kasus) {
            $c = $kasus->replicate(['status']);
            $c->title = $kasus->title.' (Salinan)';
            $c->status = 'draft';
            $c->save();
            $c->rubricVersions()->create(['version' => 1, 'data' => $kasus->currentRubric->data]);
            return $c;
        });
        AuditLog::record('case.duplicate', "Menduplikasi kasus \"{$kasus->title}\"");
        return redirect()->route('guru.cases.edit', $copy)->with('ok', 'Kasus diduplikasi sebagai draft.');
    }

    public function toggle(CaseStudy $kasus)
    {
        $this->mine($kasus);
        $kasus->update(['status' => $kasus->isPublished() ? 'draft' : 'published']);
        AuditLog::record('case.status', ($kasus->isPublished() ? 'Memublikasikan' : 'Menarik ke draft')." kasus \"{$kasus->title}\"");
        return back()->with('ok', $kasus->isPublished() ? 'Kasus dipublikasikan ke siswa.' : 'Kasus ditarik menjadi draft.');
    }

    public function destroy(CaseStudy $kasus)
    {
        $this->mine($kasus)->delete();
        AuditLog::record('case.delete', "Menghapus kasus \"{$kasus->title}\"");
        return back()->with('ok', 'Kasus dihapus.');
    }
}