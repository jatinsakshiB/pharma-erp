@echo off
chcp 65001 >nul
title DRXStore - Pharmacy Management System

echo ================================================================
echo    DRXStore — Portable Pharmacy Management System
echo ================================================================
echo.

set PORT=5051
set URL=http://127.0.0.1:%PORT%
set APP_DIR=%~dp0
set DATA_DIR=%APP_DIR%data
set LOG_FILE=%DATA_DIR%\server.log

if not exist "%DATA_DIR%" mkdir "%DATA_DIR%"

:: 1. Check and terminate any process listening on port 5051
echo [*] Checking if port %PORT% is in use...
set FOUND_PID=
for /f "tokens=5" %%a in ('netstat -aon ^| findstr /r /c:":%PORT% .*LISTENING"') do (
    set FOUND_PID=%%a
    echo [!] Port %PORT% is occupied by process PID %%a. Terminating...
    taskkill /F /PID %%a >nul 2>&1
)

if defined FOUND_PID (
    echo [OK] Port %PORT% successfully freed.
    timeout /t 1 /nobreak >nul
) else (
    echo [OK] Port %PORT% is free.
)

:: 2. Detect PHP runtime (Bundled portable PHP first, then system PHP)
set PHP_BIN=
set PHP_INI=

if exist "%APP_DIR%php\php.exe" (
    set "PHP_BIN=%APP_DIR%php\php.exe"
    if exist "%APP_DIR%php\php.ini" set "PHP_INI=%APP_DIR%php\php.ini"
    echo [OK] Using bundled portable PHP runtime.
) else (
    where php >nul 2>&1
    if not errorlevel 1 (
        for /f "delims=" %%i in ('where php') do set "PHP_BIN=%%i"
        echo [OK] Using system PHP: %PHP_BIN%
    )
)

if not defined PHP_BIN (
    echo.
    echo [ERROR] PHP runtime not found!
    echo Please make sure the 'php' folder exists in the project directory,
    echo or install PHP on your system.
    echo.
    pause
    exit /b 1
)

:: 3. Start PHP built-in web server in background
echo [*] Starting DRXStore server on %URL% ...

if defined PHP_INI (
    start "DRXStore Server" /b "%PHP_BIN%" -c "%PHP_INI%" -S 127.0.0.1:%PORT% -t "%APP_DIR%." > "%LOG_FILE%" 2>&1
) else (
    start "DRXStore Server" /b "%PHP_BIN%" -S 127.0.0.1:%PORT% -t "%APP_DIR%." > "%LOG_FILE%" 2>&1
)

:: 4. Wait for server startup
echo [*] Waiting for server to initialize...
ping 127.0.0.1 -n 3 >nul

:: 5. Open Default Browser
echo [*] Opening browser to %URL% ...
start %URL%

echo.
echo ================================================================
echo  DRXStore is now RUNNING!
echo  URL: %URL%
echo  To STOP the server, double click: stop.bat
echo ================================================================
echo.
timeout /t 5 >nul
exit /b 0
