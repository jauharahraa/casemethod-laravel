<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialRead;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /** Siswa hanya boleh membuka materi terpublikasi milik kelas yang ia masuki. */
    private function allowed(Request $r, Material $m): Material
    {
        abort_unless($m->classroom_id === $r->attributes->get('classroom')->id && $m->status === 'published', 404);
        return $m;
    }

    public function index(Request $r)
    {
        $student = $r->attributes->get('student');
        $classroom = $r->attributes->get('classroom');
        $materials = Material::where('classroom_id', $classroom->id)->where('status', 'published')->latest()->get();
        $readIds = MaterialRead::where('student_id', $student->id)->whereIn('material_id', $materials->pluck('id'))->pluck('material_id')->all();
        return view('student.materials', compact('materials', 'readIds', 'classroom'));
    }

    public function show(Request $r, Material $materi)
    {
        $this->allowed($r, $materi)->load('classroom.subject');
        $isRead = MaterialRead::where('material_id', $materi->id)->where('student_id', $r->attributes->get('student')->id)->exists();
        return view('student.material', ['material' => $materi, 'isRead' => $isRead]);
    }

    public function markRead(Request $r, Material $materi)
    {
        $this->allowed($r, $materi);
        MaterialRead::firstOrCreate(['material_id' => $materi->id, 'student_id' => $r->attributes->get('student')->id]);
        return back()->with('ok', 'Materi ditandai sudah dibaca. Kerja bagus!');
    }
}