@echo off
setlocal
cd /d "%~dp0BACKEND"

where composer >nul 2>nul || (
    echo Composer tidak ditemukan. Install Composer terlebih dahulu.
    pause
    exit /b 1
)

composer install || goto :error
if not exist .env copy .env.example .env
if not exist database\database.sqlite type nul > database\database.sqlite
php artisan key:generate || goto :error
php artisan migrate:fresh --seed || goto :error
php artisan storage:link
php artisan optimize:clear || goto :error

echo.
echo Setup backend Contify selesai.
echo Jalankan run-project.bat untuk membuka backend dan frontend.
pause
exit /b 0

:error
echo.
echo Setup gagal. Baca pesan error di atas.
pause
exit /b 1
