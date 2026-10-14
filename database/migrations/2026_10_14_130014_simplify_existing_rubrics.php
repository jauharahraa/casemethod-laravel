<?php

use App\Services\RubricService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menyederhanakan rubrik kasus yang sudah ada (3 aspek x 1 kriteria, skala 0-4, bobot 25/35/40).
 *
 * NON-DESTRUKTIF: hanya MENAMBAH satu baris versi rubrik baru per kasus yang rubrik terbarunya masih format lama.
 * Versi lama, kasus, jawaban, feedback, dan nilai tidak diubah/dihapus. Pengumpulan yang sudah ada tetap
 * terikat ke versi rubrik lamanya (rubric_version_id), sehingga nilainya tidak berubah.
 * Aman dijalankan berulang kali (kasus yang sudah sederhana dilewati).
 */
return new class extends Migration {
    public function up(): void
    {
        foreach (DB::table('cases')->pluck('id') as $caseId) {
            $latest = DB::table('rubric_versions')->where('case_id', $caseId)->orderByDesc('version')->first();
            if (! $latest) {
                continue;
            }
            $data = json_decode($latest->data, true);
            if (is_array($data) && RubricService::isSimplified($data)) {
                continue;
            }
            DB::table('rubric_versions')->insert([
                'case_id' => $caseId,
                'version' => $latest->version + 1,
                'data' => json_encode(RubricService::default()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Sengaja kosong: tidak ada data yang dihapus. Versi rubrik baru dapat dibiarkan.
    }
};