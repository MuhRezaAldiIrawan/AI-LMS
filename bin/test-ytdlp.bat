@echo off
echo ========================================
echo      yt-dlp Test Script
echo ========================================
echo.

cd /d %~dp0

if not exist yt-dlp.exe (
    echo ERROR: yt-dlp.exe not found in current directory!
    echo Please run update-ytdlp.bat first.
    pause
    exit /b 1
)

echo Current directory: %CD%
echo.

echo Checking yt-dlp version...
yt-dlp.exe --version
echo.

echo ========================================
echo Testing with sample YouTube video
echo ========================================
set /p VIDEO_URL="Enter YouTube URL to test (or press Enter for default): "

if "%VIDEO_URL%"=="" (
    set VIDEO_URL=https://www.youtube.com/watch?v=jNQXAC9IVRw
    echo Using default URL: %VIDEO_URL%
)
echo.

echo Listing available formats...
echo.
yt-dlp.exe --list-formats %VIDEO_URL%
echo.

echo ========================================
echo Attempting to download audio...
echo ========================================
echo.

set OUTPUT_FILE=test_audio_%RANDOM%

REM Check if FFmpeg exists
if exist "ffmpeg\ffmpeg.exe" (
    echo FFmpeg found at: %CD%\ffmpeg
    set FFMPEG_PATH=%CD%\ffmpeg
) else (
    echo WARNING: FFmpeg not found in %CD%\ffmpeg
    echo Please download FFmpeg and extract to bin\ffmpeg\
    pause
    exit /b 1
)

yt-dlp.exe --extract-audio --audio-format mp3 --audio-quality 0 --no-playlist --ffmpeg-location "%FFMPEG_PATH%" -o "%OUTPUT_FILE%.%%(ext)s" %VIDEO_URL%

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo  SUCCESS! Audio downloaded.
    echo ========================================
    echo.

    dir %OUTPUT_FILE%.*
    echo.

    set /p DELETE="Delete test file? (Y/N): "
    if /i "%DELETE%"=="Y" (
        del %OUTPUT_FILE%.*
        echo Test file deleted.
    )
) else (
    echo.
    echo ========================================
    echo  ERROR: Download failed!
    echo ========================================
    echo.
    echo Possible reasons:
    echo - Video is private or age-restricted
    echo - YouTube changed their API
    echo - Network connection issue
    echo - yt-dlp needs to be updated
    echo.
    echo Try running: update-ytdlp.bat
)

echo.
pause
