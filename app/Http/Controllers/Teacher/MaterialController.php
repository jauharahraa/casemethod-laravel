<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    private function mine(Material $m): Material
    {
        abort_unless($m->user_id === auth()->id(), 404);
        return $m;
    }

    private function classrooms()
    {
        return Classroom::where('user_id', auth()->id())->with('subject')->orderBy('name')->get();
    }

    private function rules(): array
    {
        return [
            'classroom_id' => ['required', Rule::exists('classrooms', 'id')->where('user_id', auth()->id())],
            'title' => 'required|string|max:200',
            'summary' => 'nullable|string|max:300',
            'content' => 'required|string|max:100000',
            'theme' => ['required', Rule::in(array_keys(Material::THEMES))],
            'icon' => ['required', Rule::in(Material::ICONS)],
            'link_url' => 'nullable|url:http,https|max:500',
            'link_label' => 'nullable|string|max:60',
            'action' => 'required|in:draft,publish',
        ];
    }

    public function index()
    {
        $materials = Material::where('user_id', auth()->id())->with('classroom')->withCount('reads')->latest()->get();
        return view('teacher.materials', compact('materials'));
    }

    public function create()
    {
        return view('teacher.material_form', [
            'material' => new Material(['theme' => 'blue', 'icon' => 'book-open']),
            'classrooms' => $this->classrooms(),
        ]);
    }

    public function store(Request $r)
    {
        $d = $r->validate($this->rules());
        $m = Material::create(collect($d)->except('action')->all() + [
            'user_id' => auth()->id(),
            'status' => $d['action'] === 'publish' ? 'published' : 'draft',
        ]);
        AuditLog::record('material.create', "Membuat materi \"{$m->title}\" (".($m->isPublished() ? 'dipublikasikan' : 'draft').')');
        return redirect()->route('guru.materials')->with('ok', $m->isPublished() ? 'Materi dipublikasikan ke siswa.' : 'Materi disimpan sebagai draft.');
    }

    public function show(Material $materi)
    {
        $this->mine($materi)->load('classroom.subject');
        return view('teacher.material_show', ['material' => $materi]);
    }

    public function edit(Material $materi)
    {
        return view('teacher.material_form', ['material' => $this->mine($materi), 'classrooms' => $this->classrooms()]);
    }

    public function update(Request $r, Material $materi)
    {
        $d = $r->validate($this->rules());
        $this->mine($materi)->update(collect($d)->except('action')->all()
            + ['status' => $d['action'] === 'publish' ? 'published' : $materi->status]);
        AuditLog::record('material.update', "Mengubah materi \"{$materi->title}\"");
        return redirect()->route('guru.materials')->with('ok', 'Materi diperbarui.');
    }

    public function toggle(Material $materi)
    {
        $this->mine($materi)->update(['status' => $materi->isPublished() ? 'draft' : 'published']);
        AuditLog::record('material.status', ($materi->isPublished() ? 'Memublikasikan' : 'Menarik ke draft')." materi \"{$materi->title}\"");
        return back()->with('ok', $materi->isPublished() ? 'Materi dipublikasikan ke siswa.' : 'Materi ditarik menjadi draft.');
    }

    public function destroy(Material $materi)
    {
        $this->mine($materi)->delete();
        AuditLog::record('material.delete', "Menghapus materi \"{$materi->title}\"");
        return back()->with('ok', 'Materi dihapus.');
    }
}