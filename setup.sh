#!/usr/bin/env bash
# Instalasi otomatis (Linux/macOS/Git Bash). Pastikan database "casemethod" sudah dibuat di MySQL.
set -e
[ -f .env ] || cp .env.example .env
composer install --no-interaction
grep -q '^APP_KEY=base64' .env || php artisan key:generate --force
php artisan migrate --seed --force
echo ""
echo "Selesai. Jalankan:  php artisan serve   lalu buka http://localhost:8000"
echo "Akun guru contoh: guru / guru12345   |   Kode kelas contoh: DEMO01"
