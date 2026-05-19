@echo off
REM Server Manager - Development Startup Script for Windows

echo Launching Server Manager...
echo.

REM Check if Docker is available
where docker >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo Docker found - Starting with Docker Compose...
    echo.
    
    docker-compose up -d
    
    echo Waiting for services to be ready...
    timeout /t 5 /nobreak
    
    echo Running migrations...
    docker-compose exec app php artisan migrate:fresh --seed
    
    echo.
    echo Backend running at http://localhost:8000
    echo Database configured and seeded
    echo.
    echo Starting frontend development server...
    
    cd frontend
    call npm install
    start cmd /k npm run dev
    cd ..
    
    echo Frontend running at http://localhost:5173
    echo.
    echo Default Login Credentials:
    echo   Email: admin@servermanager.local
    echo   Password: admin123
    echo.
    echo Press Ctrl+C to stop
    
) else (
    echo Docker not found. Using manual setup...
    echo.
    
    cd backend
    
    where php >nul 2>nul
    if %ERRORLEVEL% NEQ 0 (
        echo PHP not found. Please install PHP 8.2+
        pause
        exit /b 1
    )
    
    echo Setting up backend...
    call composer install
    copy .env.example .env
    call php artisan key:generate
    
    echo.
    echo Configure your database in .env, then run:
    echo   php artisan migrate:fresh --seed
    echo   php artisan serve
    echo.
    
    cd ..\frontend
    
    where node >nul 2>nul
    if %ERRORLEVEL% NEQ 0 (
        echo Node.js not found. Please install Node.js 18+
        pause
        exit /b 1
    )
    
    call npm install
    call npm run dev
)

pause
