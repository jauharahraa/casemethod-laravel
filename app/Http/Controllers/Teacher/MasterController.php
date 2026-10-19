<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    private function mine($model)
    {
        abort_unless($model->user_id === auth()->id(), 404);
        return $model;
    }

    /**
     * Menghapus satu kelas beserta isinya dengan urutan yang aman terhadap foreign key:
     * pengumpulan (submissions) dulu, baru kasus (yang ikut menghapus versi rubrik), lalu kelas.
     * Siswa tidak dihapus, hanya dilepas dari kelas.
     */
    private function purgeClassroom(Classroom $classroom): void
    {
        $caseIds = $classroom->cases()->pluck('id');
        Submission::whereIn('case_id', $caseIds)->delete();
        $classroom->students()->detach();
        $classroom->cases()->delete();
        $classroom->delete();
    }

    // ---- Mata pelajaran
    public function subjects()
    {
        $subjects = Subject::where('user_id', auth()->id())->withCount('classrooms')->orderBy('name')->get();
        return view('teacher.subjects', compact('subjects'));
    }

    public function subjectStore(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string|max:500']);
        Subject::create($d + ['user_id' => auth()->id()]);
        return back()->with('ok', 'Mata pelajaran ditambahkan.');
    }

    public function subjectUpdate(Request $r, Subject $subject)
    {
        $this->mine($subject)->update($r->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string|max:500']));
        return back()->with('ok', 'Mata pelajaran diperbarui.');
    }

    public function subjectDestroy(Subject $subject)
    {
        $this->mine($subject);
        $name = $subject->name;

        DB::transaction(function () use ($subject) {
            foreach ($subject->classrooms()->get() as $classroom) {
                $this->purgeClassroom($classroom);
            }
            $subject->delete();
        });

        AuditLog::record('subject.delete', "Menghapus mata pelajaran {$name}");
        return back()->with('ok', 'Mata pelajaran beserta kelas dan kasusnya dihapus.');
    }

    // ---- Kelas
    public function classrooms()
    {
        $classrooms = Classroom::where('user_id', auth()->id())->with('subject')->withCount('students')->orderBy('name')->get();
        $subjects = Subject::where('user_id', auth()->id())->orderBy('name')->get();
        return view('teacher.classrooms', compact('classrooms', 'subjects'));
    }

    public function classroomStore(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:100',
            'subject_id' => ['required', Rule::exists('subjects', 'id')->where('user_id', auth()->id())],
        ]);
        $c = Classroom::create($d + ['user_id' => auth()->id(), 'code' => Classroom::newCode()]);
        return redirect()->route('guru.classrooms.show', $c)->with('ok', "Kelas dibuat. Kode kelas: {$c->code}");
    }

    public function classroomShow(Classroom $classroom)
    {
        $this->mine($classroom)->load('subject', 'students');
        return view('teacher.classroom_show', compact('classroom'));
    }

    public function classroomDestroy(Classroom $classroom)
    {
        $classroom = $this->mine($classroom);
        $name = $classroom->name;

        DB::transaction(fn () => $this->purgeClassroom($classroom));

        AuditLog::record('classroom.delete', "Menghapus kelas {$name}");
        return redirect()->route('guru.classrooms')->with('ok', 'Kelas dihapus.');
    }

    // ---- Siswa
    private function addStudent(Classroom $classroom, string $name, string $number): ?string
    {
        // Rapikan spasi agar cocok dengan pencocokan nama saat siswa login
        $name = trim(preg_replace('/\s+/u', ' ', $name));
        $number = trim($number);
        if ($name === '' || $number === '') return 'Nama dan nomor absen wajib diisi.';
        if (mb_strlen($name) > 150 || mb_strlen($number) > 50) return "Data terlalu panjang: $name";

        // Di dalam SATU kelas, satu nomor hanya boleh dipakai satu nama.
        // (Nomor yang sama boleh dipakai di kelas atau guru lain.)
        $clash = $classroom->students()
            ->where('participant_number', $number)
            ->where('full_name', '!=', $name)
            ->first();
        if ($clash) {
            return "Nomor $number sudah dipakai {$clash->full_name} di kelas ini.";
        }

        // Pakai data siswa milik guru ini bila nama dan nomornya persis sama, jika belum ada buat baru.
        $student = Student::where('user_id', auth()->id())
            ->where('participant_number', $number)
            ->where('full_name', $name)
            ->first()
            ?? Student::create([
                'user_id' => auth()->id(),
                'full_name' => $name,
                'participant_number' => $number,
            ]);

        $classroom->students()->syncWithoutDetaching([$student->id]);
        return null;
    }

    public function studentStore(Request $r, Classroom $classroom)
    {
        $this->mine($classroom);
        $d = $r->validate(['full_name' => 'required|string|max:150', 'participant_number' => 'required|string|max:50']);
        $err = $this->addStudent($classroom, $d['full_name'], $d['participant_number']);
        return $err ? back()->withErrors(['participant_number' => $err])->withInput() : back()->with('ok', 'Siswa ditambahkan.');
    }

    public function studentBulk(Request $r, Classroom $classroom)
    {
        $this->mine($classroom);
        $r->validate(['bulk' => 'required|string|max:20000']);
        $ok = 0;
        $errors = [];
        foreach (preg_split('/\R/', $r->bulk) as $line) {
            if (trim($line) === '') continue;
            $parts = preg_split('/[,;\t]/', $line);
            $err = count($parts) < 2 ? "Format salah: $line" : $this->addStudent($classroom, $parts[0], $parts[1]);
            $err ? $errors[] = $err : $ok++;
        }
        $back = back()->with('ok', "$ok siswa berhasil ditambahkan.");
        return $errors ? $back->withErrors(['bulk' => implode(' | ', array_slice($errors, 0, 8))]) : $back;
    }

    public function studentRemove(Classroom $classroom, Student $student)
    {
        $this->mine($classroom);
        $this->mine($student);
        $classroom->students()->detach($student->id);
        return back()->with('ok', 'Siswa dikeluarkan dari kelas.');
    }
}