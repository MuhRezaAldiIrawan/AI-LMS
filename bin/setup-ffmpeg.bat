@echo off
echo ========================================
echo      FFmpeg Installation Check
echo ========================================
echo.

cd /d %~dp0

REM Check if FFmpeg already exists
if exist "ffmpeg\ffmpeg.exe" (
    echo FFmpeg is already installed!
    echo Location: %CD%\ffmpeg\ffmpeg.exe
    echo.

    echo Testing FFmpeg...
    ffmpeg\ffmpeg.exe -version
    echo.

    set /p REINSTALL="Do you want to reinstall FFmpeg? (Y/N): "
    if /i not "%REINSTALL%"=="Y" (
        echo.
        echo Installation skipped.
        pause
        exit /b 0
    )
)

echo.
echo ========================================
echo  FFmpeg Download Instructions
echo ========================================
echo.
echo FFmpeg cannot be auto-downloaded due to file size.
echo Please follow these steps:
echo.
echo 1. Visit: https://github.com/BtbN/FFmpeg-Builds/releases
echo 2. Download: ffmpeg-master-latest-win64-gpl.zip
echo 3. Extract the ZIP file
echo 4. Copy the contents of the 'bin' folder (ffmpeg.exe, ffprobe.exe, etc.)
echo 5. Paste into: %CD%\ffmpeg\
echo.
echo Or use this direct download link:
echo https://github.com/BtbN/FFmpeg-Builds/releases/download/latest/ffmpeg-master-latest-win64-gpl.zip
echo.

set /p AUTO="Do you want to open the download page in browser? (Y/N): "
if /i "%AUTO%"=="Y" (
    start https://github.com/BtbN/FFmpeg-Builds/releases
)

echo.
echo After downloading and extracting, your folder structure should be:
echo %CD%\ffmpeg\ffmpeg.exe
echo %CD%\ffmpeg\ffprobe.exe
echo %CD%\ffmpeg\ffplay.exe
echo.
echo ========================================
echo  Alternative: Quick Download via curl
echo ========================================
echo.
echo If you have curl and 7zip installed, we can try automatic download:
set /p AUTOCURL="Attempt automatic download? (Y/N): "

if /i "%AUTOCURL%"=="Y" (
    echo.
    echo Creating ffmpeg directory...
    if not exist "ffmpeg" mkdir ffmpeg

    echo Downloading FFmpeg (this may take a while)...
    curl -L "https://github.com/BtbN/FFmpeg-Builds/releases/download/latest/ffmpeg-master-latest-win64-gpl.zip" -o "ffmpeg-temp.zip"

    if %ERRORLEVEL% EQU 0 (
        echo Download complete!
        echo.
        echo Please extract ffmpeg-temp.zip manually and copy the files from
        echo the 'bin' folder inside the extracted folder to:
        echo %CD%\ffmpeg\
        echo.
        echo Then delete ffmpeg-temp.zip

        REM Try to extract if tar is available (Windows 10+)
        where tar >nul 2>&1
        if %ERRORLEVEL% EQU 0 (
            echo.
            echo Attempting to extract with tar...
            tar -xf ffmpeg-temp.zip
            echo.
            echo Looking for ffmpeg.exe...
            for /r %%i in (ffmpeg.exe) do (
                echo Found: %%i
                echo Copying to %CD%\ffmpeg\
                xcopy "%%~dpi*.*" "ffmpeg\" /Y /I
                goto :found
            )
            :found
            echo Cleaning up...
            del ffmpeg-temp.zip
            rmdir /s /q ffmpeg-master-latest-win64-gpl 2>nul
        )
    ) else (
        echo.
        echo Download failed! Please download manually from:
        echo https://github.com/BtbN/FFmpeg-Builds/releases
    )
)

echo.
echo ========================================
echo  Verification
echo ========================================
echo.

if exist "ffmpeg\ffmpeg.exe" (
    echo SUCCESS! FFmpeg is installed.
    echo.
    echo Testing FFmpeg...
    ffmpeg\ffmpeg.exe -version
    echo.
    echo FFmpeg is ready to use!
) else (
    echo FFmpeg not found. Please install manually.
    echo.
    echo Quick steps:
    echo 1. Download from: https://github.com/BtbN/FFmpeg-Builds/releases
    echo 2. Extract the ZIP
    echo 3. Copy bin\ffmpeg.exe, bin\ffprobe.exe to: %CD%\ffmpeg\
)

echo.
pause
