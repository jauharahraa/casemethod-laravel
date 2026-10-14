# Case Method AI (Laravel + MySQL + Gemini)

Aplikasi pembelajaran Case Method: guru membuat kasus & rubrik, siswa mengerjakan tiga bagian
(Identifikasi Masalah, Analisis Masalah, Solusi), mendapat feedback formatif dari Gemini, boleh merevisi
**satu kali**, lalu guru menilai dan memublikasikan nilai. Seluruh tampilan berbahasa Indonesia.

## Kebutuhan
- PHP 8.2+ (ekstensi: mbstring, xml, curl, pdo_mysql, openssl, ctype, json)
- Composer 2
- MySQL 8 / MariaDB 10.4+ (XAMPP/Laragon juga bisa)
- Koneksi internet saat dipakai (Tailwind, Alpine.js, dan ikon Lucide dimuat dari CDN)

## Instalasi (5 langkah)
1. Ekstrak ZIP, buka terminal di folder proyek.
2. Buat database: `mysql -u root -p < database/create_database.sql` (atau buat database `casemethod` lewat phpMyAdmin).
3. Salin `.env.example` menjadi `.env`, sesuaikan `DB_USERNAME` dan `DB_PASSWORD`.
4. Jalankan `./setup.sh` (Linux/macOS) atau `setup.bat` (Windows). Skrip ini menjalankan
   `composer install`, `php artisan key:generate`, dan `php artisan migrate --seed`.
5. `php artisan serve` lalu buka <http://localhost:8000>.

## Mengaktifkan Gemini
Isi `.env`:
```
GEMINI_API_KEY=isi_api_key_anda
GEMINI_MODEL=gemini-2.5-flash
```
Key dibuat di <https://aistudio.google.com/apikey>. Key hanya dibaca server (tidak pernah dikirim ke browser).
Tanpa key, aplikasi tetap jalan dalam **MODE SIMULASI**: feedback dibuat dengan aturan sederhana dan
diberi label "MODE SIMULASI" di layar siswa dan guru. Itu bukan AI sungguhan.
Setelah mengubah `.env`, jalankan `php artisan config:clear`.

## Data contoh (hapus sebelum dipakai sungguhan)
Seeder membuat data berlabel `[CONTOH]`:
- Guru: username `guru`, password `guru12345` (segera ganti/hapus)
- Kelas `XI IPS 1`, kode kelas `DEMO01`
- Siswa: `[CONTOH] Siswa Satu` / `DEMO-001`, `[CONTOH] Siswa Dua` / `DEMO-002`, `[CONTOH] Siswa Tiga` / `DEMO-003`
- Satu kasus terpublikasi dan satu draft
Untuk database kosong tanpa contoh: `php artisan migrate:fresh` (tanpa `--seed`).

## Cara pakai
**Guru:** Daftar → Mapel → Kelas (kode kelas dibuat otomatis) → tambah siswa (satu per satu atau impor
"Nama, Nomor") → Kasus baru (rubrik: 3 aspek × 1 kriteria, skala skor 0–4; bobot awal 25/35/40, total harus 100%)) → Publikasikan →
Penilaian (beri skor tiap kriteria, nilai akhir dihitung otomatis) → Simpan & publikasikan → Rekap/ekspor CSV.

**Siswa:** Masuk dengan nama lengkap + kelas + nomor peserta + kode kelas (harus sama dengan data yang
didaftarkan guru; huruf besar/kecil tidak berpengaruh) → buka kasus → isi 3 bagian (draft otomatis tersimpan) →
Kirim → baca feedback → Revisi (sekali) atau Selesai tanpa revisi → lihat nilai setelah dipublikasikan.

## Rubrik penilaian (disederhanakan)
Tiga aspek — Identifikasi Masalah (25%), Analisis Masalah (35%), Solusi (40%) — masing-masing satu kriteria
dengan deskripsi untuk skor 4, 3, 2, 1, 0. Nilai akhir = (skor identifikasi/4 × 25) + (skor analisis/4 × 35) + (skor solusi/4 × 40),
rentang 0–100, selalu dihitung oleh program (`RubricService::finalFromAspectScores`). AI hanya mengembalikan skor 0–4,
alasan, dan feedback per aspek; angka nilai dari AI diabaikan. Nilai resmi tetap dari guru.
Setelah memperbarui kode, jalankan `php artisan migrate` (migrasi non-destruktif: hanya menambah versi rubrik baru
untuk kasus lama; versi lama dan nilai yang sudah ada tidak diubah).

## Aturan penting yang diterapkan di backend
- Alur status: `draft → feedback → (revising) → locked`. Setiap transisi diperiksa di server dalam transaksi
  dengan `lockForUpdate`; versi jawaban punya `UNIQUE(submission_id, version)` sehingga revisi kedua mustahil
  walau request dikirim ganda atau tombol disembunyikan/dimanipulasi.
- Feedback AI gagal → tombol "Coba lagi" memperbarui baris feedback yang sama (tidak ada pengumpulan ganda,
  revisi tidak terpakai). Revisi baru bisa dimulai setelah feedback berhasil.
- Rubrik disimpan berversi; setiap pengumpulan "mengunci" versi rubrik yang berlaku saat siswa mulai.
  Bobot tidak dapat diubah setelah kasus dipublikasikan.
- AI hanya memberi feedback formatif (prompt melarang skor & jawaban lengkap). Nilai akhir hanya dari guru.
- Siswa hanya melihat kasus terpublikasi di kelasnya dan data miliknya; nilai tampil hanya setelah dipublikasikan.
  Guru hanya mengakses data miliknya (404 untuk data guru lain).
- Password guru di-hash (bcrypt), CSRF aktif, login dibatasi (throttle), ekspor CSV dilindungi dari CSV-injection.
- Tenggat bersifat penanda (peringatan "terlewat"), pengiriman tidak diblokir.

## Menjalankan tes otomatis
`php artisan test` (memakai SQLite in-memory, tidak menyentuh MySQL; butuh ekstensi `pdo_sqlite`).
Mencakup: registrasi/login guru, login siswa, hak akses, alur penuh + batas revisi satu kali, kegagalan AI + coba lagi,
perhitungan nilai, publikasi nilai, validasi bobot rubrik.

## Status pengujian (jujur)
Dibuat di lingkungan tanpa akses Packagist, sehingga `composer install` dan `php artisan test` **belum
dijalankan** oleh pembuat. Yang sudah diperiksa: sintaks seluruh file PHP (`php -l`, PHP 8.3) dan keseimbangan
direktif Blade. Jalankan `php artisan test` setelah instalasi untuk memverifikasi di mesin Anda dan laporkan error bila ada.
Panggilan ke Gemini belum diuji dengan key sungguhan.

## Struktur
`app/Http/Controllers` (Auth, Teacher/*, Student/*) · `app/Services` (RubricService, GeminiFeedbackService) ·
`app/Models` · `database/migrations` · `database/seeders` · `resources/views` · `routes/web.php` · `tests/Feature`
