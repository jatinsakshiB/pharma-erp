@echo off
chcp 65001 >nul
title Stop DRXStore

echo ================================================================
echo    Stopping DRXStore Server (Port 5051)...
echo ================================================================

set STOPPED=0
for /f "tokens=5" %%a in ('netstat -aon ^| findstr /r /c:":5051 .*LISTENING"') do (
    echo [*] Terminating process PID %%a listening on port 5051...
    taskkill /F /PID %%a >nul 2>&1
    set STOPPED=1
)

if "%STOPPED%"=="1" (
    echo [OK] DRXStore server has been stopped.
) else (
    echo [i] No active DRXStore server was found running on port 5051.
)

echo.
timeout /t 3 >nul
exit /b 0
