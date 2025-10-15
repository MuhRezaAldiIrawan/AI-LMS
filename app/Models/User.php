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
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Certificate;
use Illuminate\Database\Eloquent\Model;

/**
 * User model with role-based permissions and course enrollment functionality
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $avatar
 * @property string|null $phone_number
 * @property int|null $location_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method bool isEnrolledIn(\App\Models\Course $course) Check if user is enrolled in a specific course
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany enrolledCourses() Get courses the user is enrolled in
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany completedLessons() Get lessons completed by user
 * @method \Illuminate\Database\Eloquent\Relations\HasMany quizAttempts() Get quiz attempts by user
 * @method \Illuminate\Database\Eloquent\Relations\HasOne userPoint() Get user points
 * @method \Illuminate\Database\Eloquent\Relations\HasMany pointLogs() Get point transaction logs
 * @method void addPoints(int $points, \Illuminate\Database\Eloquent\Model $source, string $description) Add points to user
 * @method void deductPoints(int $points, \Illuminate\Database\Eloquent\Model $source, string $description) Deduct points from user
 * @property string|null $nik
 * @property string|null $join_date
 * @property string|null $position
 * @property string|null $division
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $profile_photo_path
 * @property string|null $remember_token
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AiChatHistory> $aiChatHistories
 * @property-read int|null $ai_chat_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Lesson> $completedLessons
 * @property-read int|null $completed_lessons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Course> $enrolledCourses
 * @property-read int|null $enrolled_courses_count
 * @property-read mixed $profile_photo_url
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PointLog> $pointLogs
 * @property-read int|null $point_logs_count
 * @property-read UserPoint|null $points
 * @property-read \Illuminate\Database\Eloquent\Collection<int, QuizAttempt> $quizAttempts
 * @property-read int|null $quiz_attempts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RewardRedemption> $rewardRedemptions
 * @property-read int|null $reward_redemptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJoinDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
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
    public function enrolledCourses(): BelongsToMany
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

    public function isEnrolledIn($course): bool
    {
        // Jika parameter adalah Course object, ambil ID-nya
        $courseId = $course instanceof Course ? $course->id : $course;

        // User dianggap enrolled HANYA jika enrolled_at tidak NULL
        // Ini berarti user sudah aktif menekan tombol "Enroll"
        return $this->enrolledCourses()
                    ->where('course_id', $courseId)
                    ->whereNotNull('enrolled_at')
                    ->exists();
    }

    /**
     * Check if user has access to course (assigned by admin but may not be enrolled yet)
     */
    public function hasAccessToCourse($course): bool
    {
        $courseId = $course instanceof Course ? $course->id : $course;

        // User memiliki akses jika ada record di course_user (assigned by admin)
        return $this->enrolledCourses()->where('course_id', $courseId)->exists();
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

    /**
     * Check if user has completed a specific lesson
     */
    public function hasCompletedLesson($lessonId): bool
    {
        return $this->completedLessons()->where('lesson_id', $lessonId)->exists();
    }

    /**
     * Relasi ke Certificates
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get certificate for a specific course
     */
    public function getCertificateForCourse($courseId)
    {
        return $this->certificates()->where('course_id', $courseId)->first();
    }
}
