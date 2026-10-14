@echo off
REM Instalasi otomatis (Windows/XAMPP). Pastikan database "casemethod" sudah dibuat di MySQL.
if not exist .env copy .env.example .env
call composer install --no-interaction
findstr /b "APP_KEY=base64" .env >nul || php artisan key:generate --force
php artisan migrate --seed --force
echo.
echo Selesai. Jalankan:  php artisan serve   lalu buka http://localhost:8000
echo Akun guru contoh: guru / guru12345   ^|   Kode kelas contoh: DEMO01
