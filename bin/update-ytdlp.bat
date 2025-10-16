@echo off
echo ========================================
echo      yt-dlp Update Script
echo ========================================
echo.

cd /d %~dp0

echo Current directory: %CD%
echo.

echo Backing up current yt-dlp.exe...
if exist yt-dlp.exe (
    copy yt-dlp.exe yt-dlp.exe.backup
    echo Backup created: yt-dlp.exe.backup
) else (
    echo No existing yt-dlp.exe found.
)
echo.

echo Downloading latest yt-dlp from GitHub...
curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp.exe -o yt-dlp.exe

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo  yt-dlp updated successfully!
    echo ========================================
    echo.

    echo Checking version...
    yt-dlp.exe --version
    echo.
) else (
    echo.
    echo ========================================
    echo  ERROR: Failed to download yt-dlp
    echo ========================================
    echo.

    if exist yt-dlp.exe.backup (
        echo Restoring backup...
        copy /Y yt-dlp.exe.backup yt-dlp.exe
        echo Backup restored.
    )
)

echo.
pause
