<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;

class EnsureStudent
{
    public function handle(Request $request, Closure $next)
    {
        $student = Student::find($request->session()->get('student_id'));
        $classroom = $student?->classrooms()->with('subject')->find($request->session()->get('classroom_id'));

        if (! $student || ! $classroom) {
            $request->session()->forget(['student_id', 'classroom_id']);
            return redirect()->route('siswa.login')->withErrors(['login' => 'Silakan masuk terlebih dahulu.']);
        }

        $request->attributes->set('student', $student);
        $request->attributes->set('classroom', $classroom);
        return $next($request);
    }
}
