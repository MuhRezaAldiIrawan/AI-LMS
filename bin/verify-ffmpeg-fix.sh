#!/bin/bash
echo "========================================"
echo "  Quick Test: FFmpeg Fix Verification"
echo "========================================"
echo ""

cd /c/laragon/www/lms-bosowav2

echo "=== 1. Checking FFmpeg Installation ==="
FFMPEG_PATH="C:/laragon/www/lms-bosowav2/bin/ffmpeg/ffmpeg.exe"
if [ -f "$FFMPEG_PATH" ]; then
    echo "✓ FFmpeg found at: $FFMPEG_PATH"
    "$FFMPEG_PATH" -version | head -n 1
else
    echo "✗ FFmpeg NOT found at: $FFMPEG_PATH"
    echo "  Please run: bin/setup-ffmpeg.bat"
    exit 1
fi

echo ""
echo "=== 2. Checking .env Configuration ==="
if grep -q "FFMPEG_PATH=" .env; then
    echo "✓ FFMPEG_PATH configured:"
    grep "FFMPEG_PATH=" .env
else
    echo "✗ FFMPEG_PATH not found in .env"
    exit 1
fi

if grep -q "YOUTUBE_DL_PATH=" .env; then
    echo "✓ YOUTUBE_DL_PATH configured:"
    grep "YOUTUBE_DL_PATH=" .env
else
    echo "✗ YOUTUBE_DL_PATH not found in .env"
    exit 1
fi

echo ""
echo "=== 3. Clearing Config Cache ==="
php artisan config:clear
echo "✓ Config cache cleared"

echo ""
echo "=== 4. Checking Recent Failed Jobs ==="
php artisan tinker << 'TINKER_EOF'
$failedCount = DB::table('failed_jobs')->count();
echo "Failed jobs count: {$failedCount}\n";

if ($failedCount > 0) {
    echo "\nMost recent failed job:\n";
    $recent = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->first();
    if ($recent) {
        echo "- Failed at: {$recent->failed_at}\n";
        echo "- Exception (first 300 chars):\n";
        echo substr($recent->exception, 0, 300) . "...\n";
    }
}
TINKER_EOF

echo ""
echo "========================================"
echo "  Configuration Check Complete"
echo "========================================"
echo ""
echo "Next steps:"
echo "1. Start queue worker: php artisan queue:work"
echo "2. Create a test lesson with YouTube video"
echo "3. Monitor logs: tail -f storage/logs/laravel.log"
echo ""
