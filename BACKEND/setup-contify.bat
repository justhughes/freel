@echo off
setlocal

if not exist .env (
    copy .env.example .env >nul
)

if not exist database\database.sqlite (
    type nul > database\database.sqlite
)

call composer install
if errorlevel 1 goto :error

call php artisan key:generate
if errorlevel 1 goto :error

call php artisan migrate:fresh --seed
if errorlevel 1 goto :error

call php artisan storage:link
call php artisan optimize:clear

if exist package.json (
    call npm install
    if errorlevel 1 goto :error
    call npm run build
    if errorlevel 1 goto :error
)

echo.
echo Setup Contify selesai.
echo Jalankan run-contify.bat untuk membuka server.
exit /b 0

:error
echo.
echo Setup gagal. Periksa pesan error di atas.
exit /b 1
