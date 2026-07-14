@echo off
cd /d "%~dp0"
start "Contify Backend" cmd /k "call run-backend.bat"
start "Contify Frontend" cmd /k "call run-frontend.bat"
timeout /t 3 /nobreak >nul
start "" "http://127.0.0.1:5500/contify-v3.html"
