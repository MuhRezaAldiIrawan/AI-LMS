<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserPoint;
use App\Models\PointLog;
use App\Models\RewardRedemption;
use App\Models\AiChatHistory;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    /**
     * Mendapatkan total poin milik user.
     * Relasi one-to-one dengan model UserPoint.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    protected $appends = ['profile_photo_url'];
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'nik',
        'join_date',
        'position',
        'division',
        'location_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
     public function enrolledCourses()
    {
        // [PENTING] Pastikan completed_at ada di withPivot
        return $this->belongsToMany(Course::class, 'course_user')
                    ->withPivot('enrolled_at', 'completed_at')
                    ->withTimestamps();
    }

    public function completedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->withTimestamps();
    }
    public function quizAttempts(): HasMany
    {

        return $this->hasMany(QuizAttempt::class);
    }

    public function isEnrolledIn(Course $course): bool
    {
        // enrolledCourses() adalah relasi yang sudah kita buat sebelumnya
        return $this->enrolledCourses()->where('course_id', $course->id)->exists();
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        // URL default jika tidak ada foto, menggunakan ui-avatars.com
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }

    public function getOnProgressCourses()
    {
        // Ambil semua kursus, lalu filter yang belum selesai
        return $this->enrolledCourses()->get()->filter(function ($course) {
            return !$course->isCompletedByUser($this);
        });
    }

    /**
     * Mengambil koleksi kursus yang sudah selesai untuk user ini.
     */
    public function getCompletedCourses()
    {
        // Ambil semua kursus, lalu filter yang sudah selesai
        return $this->enrolledCourses()->get()->filter(function ($course) {
            return $course->isCompletedByUser($this);
        });
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Mendapatkan total poin milik user.
     * Relasi one-to-one dengan model UserPoint.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function points()
    {
        // Ganti 'App\Models\UserPoint' jika nama model Anda berbeda
        return $this->hasOne(UserPoint::class);
    }

    /**
     * Mendapatkan semua riwayat perolehan poin user.
     * Relasi one-to-many dengan model PointLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pointLogs()
    {
        // Ganti 'App\Models\PointLog' jika nama model Anda berbeda
        return $this->hasMany(PointLog::class);
    }

    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Menghitung total durasi dari semua pelajaran yang telah diselesaikan oleh user.
     * @return int Total durasi dalam menit.
     */
    public function getTotalStudyTimeInMinutes(): int
    {
        // 'completedLessons' adalah relasi yang sudah kita buat sebelumnya
        return $this->completedLessons()->sum('duration_in_minutes');
    }

    /**
     * Memformat total menit menjadi format Jam:Menit:Detik (HH:MM:SS).
     * @return string
     */
     public function getFormattedStudyTime(): string
    {
        $totalMinutes = $this->getTotalStudyTimeInMinutes();
        if ($totalMinutes <= 0) return '00:00:00';
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d:00', $hours, $minutes);
    }

    /**
     * [FUNGSI BARU] Fungsi terpusat untuk menambahkan poin dan mencatat log.
     */
    public function addPoints(int $points, string $reason, Model $relatedModel)
    {
        if ($points <= 0) {
            return;
        }

        // Tambah total poin pengguna. Jika belum ada, buat dulu.
        $this->points()->firstOrCreate([])->increment('total_points', $points);

        // Buat log poin baru
        $this->pointLogs()->create([
            'points_earned' => $points,
            'reason' => $reason,
            'related_type' => get_class($relatedModel),
            'related_id' => $relatedModel->id,
        ]);
    }

    public function deductPoints(int $points, string $reason, Model $relatedModel)
    {
        if ($points <= 0) return;
        $this->points()->firstOrCreate([])->decrement('total_points', $points);
        // Poin yang dicatat adalah negatif untuk menandakan pengurangan
        $this->pointLogs()->create([
            'points_earned' => -$points, 'reason' => $reason,
            'related_type' => get_class($relatedModel), 'related_id' => $relatedModel->id,
        ]);
    }

    /**
     * [FUNGSI BARU] Mengembalikan poin dan mencatat log.
     */
    public function refundPoints(int $points, string $reason, Model $relatedModel)
    {
        if ($points <= 0) return;
        $this->points()->firstOrCreate([])->increment('total_points', $points);
        // Poin yang dicatat adalah positif untuk menandakan pengembalian
        $this->pointLogs()->create([
            'points_earned' => $points, 'reason' => $reason,
            'related_type' => get_class($relatedModel), 'related_id' => $relatedModel->id,
        ]);
    }

    /**
     * Relasi untuk AI chat history
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aiChatHistories(): HasMany
    {
        return $this->hasMany(AiChatHistory::class);
    }

    /**
     * Get current chat session or create new one
     * @param string|null $sessionId
     * @return string
     */
    public function getCurrentChatSession(?string $sessionId = null): string
    {
        // Jika session_id diberikan dan valid, gunakan itu
        if ($sessionId && $this->aiChatHistories()->forSession($sessionId)->exists()) {
            return $sessionId;
        }

        // Cari sesi terakhir (dalam 24 jam terakhir)
        $lastSession = $this->aiChatHistories()
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastSession ? $lastSession->session_id : AiChatHistory::generateSessionId();
    }

    /**
     * Cleanup old chat histories for this user
     */
    public function cleanupOldChats($keepDays = 30, $maxMessages = 500)
    {
        AiChatHistory::cleanupOldChats($this->id, $keepDays, $maxMessages);
    }
}
