@echo off
chcp 65001 >nul
title DRXStore - Setup Portable PHP for Windows

echo ================================================================
echo    DRXStore — Downloading Portable PHP 8.2 for Windows
echo ================================================================
echo.

set APP_DIR=%~dp0
set PHP_DIR=%APP_DIR%php
set ZIP_FILE=%APP_DIR%php_win.zip
set PHP_URL=https://downloads.php.net/~windows/releases/php-8.2.35-nts-Win32-vs16-x64.zip

if exist "%PHP_DIR%\php.exe" (
    echo [i] Portable PHP is already installed in: %PHP_DIR%
    echo If you want to reinstall, delete the 'php' folder and run this again.
    pause
    exit /b 0
)

echo [*] Downloading portable PHP from official php.net mirrors...
powershell -NoProfile -ExecutionPolicy Bypass -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri '%PHP_URL%' -OutFile '%ZIP_FILE%'"

if not exist "%ZIP_FILE%" (
    echo [ERROR] Download failed. Please check your internet connection.
    pause
    exit /b 1
)

echo [*] Extracting PHP to %PHP_DIR% ...
powershell -NoProfile -ExecutionPolicy Bypass -Command "Expand-Archive -LiteralPath '%ZIP_FILE%' -DestinationPath '%PHP_DIR%' -Force"
del /f /q "%ZIP_FILE%" >nul 2>&1

echo [*] Configuring php.ini ...
if exist "%PHP_DIR%\php.ini-development" (
    copy /y "%PHP_DIR%\php.ini-development" "%PHP_DIR%\php.ini" >nul
)

echo [OK] Portable PHP setup complete!
echo You can now run DRXStore.exe or start.bat with one click.
echo.
pause
