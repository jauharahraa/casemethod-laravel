<?php

return [
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'numeric' => ':attribute harus berupa angka.',
    'array' => ':attribute tidak valid.',
    'boolean' => ':attribute tidak valid.',
    'date' => ':attribute bukan tanggal yang valid.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'unique' => ':attribute sudah dipakai.',
    'exists' => ':attribute tidak valid.',
    'in' => ':attribute tidak valid.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan underscore.',
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute minimal :min item.',
    ],
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
        'array' => ':attribute maksimal :max item.',
    ],
    'attributes' => [
        'name' => 'Nama', 'username' => 'Username', 'password' => 'Password',
        'full_name' => 'Nama lengkap', 'class_name' => 'Kelas', 'participant_number' => 'Nomor peserta',
        'class_code' => 'Kode kelas', 'title' => 'Judul', 'description' => 'Deskripsi',
        'instructions' => 'Instruksi', 'objectives' => 'Tujuan pembelajaran', 'deadline' => 'Tenggat',
        'classroom_id' => 'Kelas', 'subject_id' => 'Materi', 'comment' => 'Komentar',
        'identification' => 'Identifikasi Masalah', 'analysis' => 'Analisis Masalah', 'solution' => 'Solusi',
        'bulk' => 'Data siswa',
    ],
];
