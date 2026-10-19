<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|alpha_dash|min:4|max:30|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::create($data);
        Auth::login($user);
        $r->session()->regenerate();
        return redirect()->route('guru.dashboard')->with('ok', 'Akun guru berhasil dibuat. Mulailah dengan menambah mata pelajaran.');
    }

    public function login(Request $r)
    {
        $cred = $r->validate(['username' => 'required|string', 'password' => 'required|string']);
        if (! Auth::attempt($cred, $r->boolean('remember'))) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }
        $r->session()->regenerate();
        return redirect()->intended(route('guru.dashboard'));
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function studentLogin(Request $r)
    {
        $d = $r->validate([
            'full_name' => 'required|string|max:150',
            'class_name' => 'required|string|max:100',
            'participant_number' => 'required|string|max:50',
            'class_code' => 'required|string|max:20',
        ]);
        $norm = fn ($s) => mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));

        $classroom = Classroom::where('code', strtoupper(trim($d['class_code'])))->first();
        $student = $classroom?->students()
            ->where('participant_number', trim($d['participant_number']))
            ->get()
            ->first(fn ($s) => $norm($s->full_name) === $norm($d['full_name']));
        $ok = $classroom && $student
            && $norm($student->full_name) === $norm($d['full_name'])
            && $norm($classroom->name) === $norm($d['class_name']);

        if (! $ok) {
            return back()->withErrors(['login' => 'Data tidak cocok. Periksa nama lengkap, kelas, nomor peserta, dan kode kelas dari gurumu.'])
                ->onlyInput('full_name', 'class_name', 'participant_number', 'class_code');
        }

        $r->session()->regenerate();
        $r->session()->put(['student_id' => $student->id, 'classroom_id' => $classroom->id]);
        AuditLog::create(['student_id' => $student->id, 'owner_id' => $classroom->user_id, 'action' => 'student.login', 'description' => "{$student->full_name} masuk ({$classroom->name})"]);
        return redirect()->route('siswa.dashboard');
    }

    public function studentLogout(Request $r)
    {
        $r->session()->forget(['student_id', 'classroom_id']);
        $r->session()->regenerate();
        return redirect()->route('home');
    }
}
