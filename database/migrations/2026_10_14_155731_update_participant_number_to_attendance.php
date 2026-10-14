<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Hapus indeks unique pada participant_number agar nomor yang sama bisa dipakai
            try {
                $table->dropUnique(['participant_number']);
            } catch (\Exception $e) {
                // Abaikan jika sudah tidak ada
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unique('participant_number');
        });
    }
};