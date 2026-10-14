<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\CaseController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Teacher\MasterController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/guru/daftar', 'auth.register')->name('guru.register');
    Route::post('/guru/daftar', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::view('/guru/masuk', 'auth.login')->name('guru.login');
    Route::post('/guru/masuk', [AuthController::class, 'login'])->middleware('throttle:10,1');
});
Route::post('/guru/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('guru.logout');

Route::view('/siswa/masuk', 'auth.student_login')->name('siswa.login');
Route::post('/siswa/masuk', [AuthController::class, 'studentLogin'])->middleware('throttle:10,1');
Route::post('/siswa/keluar', [AuthController::class, 'studentLogout'])->name('siswa.logout');

Route::middleware('auth')->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [GradeController::class, 'dashboard'])->name('dashboard');

    Route::get('mapel', [MasterController::class, 'subjects'])->name('subjects');
    Route::post('mapel', [MasterController::class, 'subjectStore'])->name('subjects.store');
    Route::put('mapel/{subject}', [MasterController::class, 'subjectUpdate'])->name('subjects.update');
    Route::delete('mapel/{subject}', [MasterController::class, 'subjectDestroy'])->name('subjects.destroy');

    Route::get('kelas', [MasterController::class, 'classrooms'])->name('classrooms');
    Route::post('kelas', [MasterController::class, 'classroomStore'])->name('classrooms.store');
    Route::get('kelas/{classroom}', [MasterController::class, 'classroomShow'])->name('classrooms.show');
    Route::delete('kelas/{classroom}', [MasterController::class, 'classroomDestroy'])->name('classrooms.destroy');
    Route::post('kelas/{classroom}/siswa', [MasterController::class, 'studentStore'])->name('students.store');
    Route::post('kelas/{classroom}/siswa-massal', [MasterController::class, 'studentBulk'])->name('students.bulk');
    Route::delete('kelas/{classroom}/siswa/{student}', [MasterController::class, 'studentRemove'])->name('students.remove');

    Route::get('kasus', [CaseController::class, 'index'])->name('cases');
    Route::get('kasus/buat', [CaseController::class, 'create'])->name('cases.create');
    Route::post('kasus', [CaseController::class, 'store'])->name('cases.store');
    Route::get('kasus/{kasus}/ubah', [CaseController::class, 'edit'])->name('cases.edit');
    Route::put('kasus/{kasus}', [CaseController::class, 'update'])->name('cases.update');
    Route::post('kasus/{kasus}/duplikat', [CaseController::class, 'duplicate'])->name('cases.duplicate');
    Route::post('kasus/{kasus}/publikasi', [CaseController::class, 'toggle'])->name('cases.toggle');
    Route::delete('kasus/{kasus}', [CaseController::class, 'destroy'])->name('cases.destroy');

    Route::get('penilaian', [GradeController::class, 'index'])->name('grading');
    Route::get('penilaian/{submission}', [GradeController::class, 'show'])->name('grading.show');
    Route::post('penilaian/{submission}', [GradeController::class, 'save'])->name('grading.save');
    Route::post('penilaian/{submission}/publikasi', [GradeController::class, 'togglePublish'])->name('grading.toggle');

    Route::get('rekap', [GradeController::class, 'recap'])->name('recap');
    Route::get('rekap/ekspor', [GradeController::class, 'export'])->name('recap.export');
});

Route::middleware('student')->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('kasus/{kasus}', [StudentController::class, 'show'])->name('case');
    Route::post('kasus/{kasus}/draft', [StudentController::class, 'saveDraft'])->name('case.draft');
    Route::post('kasus/{kasus}/kirim', [StudentController::class, 'submit'])->name('case.submit');
    Route::post('kasus/{kasus}/coba-lagi', [StudentController::class, 'retryFeedback'])->name('case.retry');
    Route::post('kasus/{kasus}/revisi', [StudentController::class, 'startRevision'])->name('case.revise');
    Route::post('kasus/{kasus}/kirim-revisi', [StudentController::class, 'submitRevision'])->name('case.revise.submit');
    Route::post('kasus/{kasus}/selesai', [StudentController::class, 'finish'])->name('case.finish');
});
