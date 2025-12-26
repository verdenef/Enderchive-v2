@echo off
echo Starting Laravel development server...
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 2 /nobreak >nul
echo Starting Vite dev server...
start "Vite Dev" cmd /k "npm run dev"
echo.
echo Laravel server: http://localhost:8000
echo Vite dev server is running
echo.
echo Press any key to stop servers...
pause >nul
taskkill /FI "WINDOWTITLE eq Laravel Server*" /T /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq Vite Dev*" /T /F >nul 2>&1
