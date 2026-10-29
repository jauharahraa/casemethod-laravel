<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Classroom;
use App\Services\GradeExportService;
use App\Services\XlsxWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Ekspor nilai akhir GURU per kelas ke Excel (.xlsx). Tidak memuat skor/feedback AI. */
class GradeExportController extends Controller
{
    public function __invoke(Request $r, GradeExportService $service)
    {
        $uid = auth()->id();
        $d = $r->validate([
            'subject_id' => ['required', Rule::exists('subjects', 'id')->where('user_id', $uid)],
            'classroom_id' => ['required', Rule::exists('classrooms', 'id')->where('user_id', $uid)],
        ], [], ['subject_id' => 'materi', 'classroom_id' => 'kelas']);

        $classroom = Classroom::with('subject')->where('user_id', $uid)->findOrFail($d['classroom_id']);

        if ((int) $classroom->subject_id !== (int) $d['subject_id']) {
            return back()->withErrors(['classroom_id' => 'Kelas yang dipilih bukan bagian dari materi tersebut.']);
        }
        if (! XlsxWriter::available()) {
            return back()->withErrors(['export' => 'Ekstensi PHP "zip" belum aktif. Aktifkan "extension=zip" di php.ini (XAMPP: Config > PHP (php.ini)), restart Apache, lalu coba lagi.']);
        }
        if (! $classroom->students()->exists()) {
            return back()->withErrors(['export' => 'Kelas ini belum memiliki siswa, jadi belum ada yang bisa diekspor.']);
        }

        $onlyPublished = $r->boolean('only_published');
        $result = $service->forClassroom($classroom, $onlyPublished);

        $tmp = tempnam(sys_get_temp_dir(), 'nilai_');
        try {
            $service->buildWorkbook($result, [
                'subject' => $classroom->subject->name,
                'classroom' => $classroom->name,
                'teacher' => auth()->user()->name,
                'date' => now()->translatedFormat('d F Y H:i'),
                'only_published' => $onlyPublished,
            ], $tmp);
        } catch (\Throwable $e) {
            @unlink($tmp);
            report($e);
            return back()->withErrors(['export' => 'Gagal membuat berkas Excel: '.$e->getMessage()]);
        }

        AuditLog::record('export.grades', "Mengekspor nilai {$classroom->name} ({$classroom->subject->name}) ke Excel");

        $filename = 'nilai-case-method-'.Str::slug($classroom->subject->name.' '.$classroom->name).'-'.now()->format('Ymd-His').'.xlsx';

        return response()->download($tmp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}