@echo off
title Enderchive ITE18 - Dev Environment

REM Change directory to this script's location (project root)
cd /d "%~dp0"

echo =============================================
echo   Starting Enderchive ITE18 development servers...
echo   Backend: Laravel (php artisan serve)
echo   Frontend: Vite (npm run dev) - Inertia.js SPA
echo =============================================
echo.

REM Check if node_modules exists in Laravel project, if not, install dependencies
if not exist "enderchive-laravel\node_modules" (
    echo Installing Laravel/Vue dependencies...
    cd enderchive-laravel
    call npm install
    cd ..
    echo.
)

REM Start Laravel server in a new window
start "Enderchive (Laravel)" cmd /k "cd /d %~dp0enderchive-laravel && php artisan serve"

REM Wait a moment for Laravel to start
timeout /t 2 /nobreak >nul

REM Start Vite dev server in a new window (for hot module replacement)
start "Enderchive (Vite)" cmd /k "cd /d %~dp0enderchive-laravel && npm run dev"

echo.
echo Servers are starting in separate windows:
echo   - Laravel + Inertia.js: http://localhost:8000
echo   - Vite HMR: Running on port 5173 (or configured port)
echo.
echo The application is now fully migrated to Laravel with Inertia.js!
echo All pages are served from Laravel - no separate frontend server needed.
echo.
echo Close this window if you don't need it.
echo.
pause

