@echo off
cd /d "%~dp0BACKEND"
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
