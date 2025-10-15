<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property string $certificate_number
 * @property string|null $certificate_path
 * @property \Carbon\Carbon $issued_date
 * @property string|null $issued_by
 * @property string|null $verification_code
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read User $user
 * @property-read Course $course
 */
class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'certificate_path',
        'issued_date',
        'issued_by',
        'verification_code',
    ];

    protected $casts = [
        'issued_date' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        // Format: CERT-YYYYMM-XXXXX
        $lastCert = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastCert ? (intval(substr($lastCert->certificate_number, -5)) + 1) : 1;

        return sprintf('CERT-%s%s-%05d', $year, $month, $sequence);
    }

    /**
     * Generate verification code
     */
    public static function generateVerificationCode(): string
    {
        return strtoupper(bin2hex(random_bytes(8)));
    }

    /**
     * Get certificate download URL
     */
    public function getDownloadUrl(): string
    {
        return route('certificate.download', $this->id);
    }

    /**
     * Get certificate preview URL
     */
    public function getPreviewUrl(): string
    {
        return route('certificate.preview', $this->id);
    }

    /**
     * Check if certificate file exists
     */
    public function fileExists(): bool
    {
        return $this->certificate_path && Storage::disk('public')->exists($this->certificate_path);
    }

    /**
     * Get full path to certificate file
     */
    public function getFilePath(): ?string
    {
        if (!$this->certificate_path) {
            return null;
        }

        return Storage::disk('public')->path($this->certificate_path);
    }
}
