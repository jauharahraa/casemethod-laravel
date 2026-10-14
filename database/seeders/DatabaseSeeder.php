<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\RubricService;
use Illuminate\Database\Seeder;

/**
 * DATA CONTOH untuk uji coba. Semua berlabel "[CONTOH]". Hapus sebelum dipakai sungguhan.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $guru = User::firstOrCreate(['username' => 'guru'], ['name' => '[CONTOH] Guru Demo', 'password' => 'guru12345']);

        $subject = Subject::firstOrCreate(['user_id' => $guru->id, 'name' => '[CONTOH] Ekonomi']);
        $class = Classroom::firstOrCreate(['code' => 'DEMO01'], ['user_id' => $guru->id, 'subject_id' => $subject->id, 'name' => 'XI IPS 1']);

        foreach ([['[CONTOH] Siswa Satu', 'DEMO-001'], ['[CONTOH] Siswa Dua', 'DEMO-002'], ['[CONTOH] Siswa Tiga', 'DEMO-003']] as [$n, $no]) {
            $s = Student::firstOrCreate(['participant_number' => $no], ['user_id' => $guru->id, 'full_name' => $n]);
            $class->students()->syncWithoutDetaching([$s->id]);
        }

        $make = function (string $title, string $status) use ($guru, $class) {
            $case = CaseStudy::firstOrCreate(['user_id' => $guru->id, 'title' => $title], [
                'classroom_id' => $class->id,
                'status' => $status,
                'description' => "Sebuah warung kopi lokal mengalami penurunan pengunjung selama tiga bulan terakhir sejak dua kedai kopi waralaba dibuka di dekatnya. Pemilik belum pernah mencatat keuangan secara rapi, harga jual tidak berubah selama dua tahun, dan karyawan mengeluhkan jam kerja yang panjang.\n\n(Teks kasus contoh untuk uji coba aplikasi.)",
                'instructions' => 'Baca kasus dengan saksama, lalu isi tiga bagian: identifikasi masalah, analisis masalah, dan solusi. Gunakan fakta dari kasus.',
                'objectives' => 'Siswa mampu mengidentifikasi masalah bisnis, menganalisis penyebabnya, dan merumuskan solusi yang layak.',
                'deadline' => now()->addDays(14),
            ]);
            if (! $case->rubricVersions()->exists()) {
                $case->rubricVersions()->create(['version' => 1, 'data' => RubricService::default()]);
            }
        };
        $make('[CONTOH] Warung Kopi Kehilangan Pelanggan', 'published');
        $make('[CONTOH] Kasus Draft (tidak terlihat siswa)', 'draft');
    }
}
