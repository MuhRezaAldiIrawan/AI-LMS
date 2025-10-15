<?php

namespace App\Models;

use App\Models\User;
use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $summary
 * @property string|null $thumbnail
 * @property string $status
 * @property int $total_duration
 * @property int $user_id
 * @property int $category_id
 * @property int $course_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $author
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\CourseType $courseType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $enrolledUsers
 * @property-read int|null $enrolled_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Module> $modules
 * @property-read int|null $modules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCourseTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereTotalDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUserId($value)
 * @mixin \Eloquent
 */
class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title', 'slug', 'description', 'summary', 'thumbnail', 'status',
        'total_duration', 'user_id', 'category_id', 'course_type_id', 'points_awarded',
    ];

    /**
     * Mendapatkan data user (author) yang memiliki course ini.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendapatkan data kategori yang dimiliki course ini.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Mendapatkan data tipe kursus yang dimiliki course ini.
     */
    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }
     public function enrolledUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user')
                    ->withPivot('enrolled_at', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * Relasi ke Certificates
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }



    public function getCompletionPercentage(User $user): int
    {
        // 1. Ambil semua item kurikulum dengan efisien
        // Eager load relasi yang dibutuhkan agar tidak terjadi N+1 query
        $this->loadMissing('modules.lessons', 'modules.quiz');

        // 2. Hitung total item yang harus diselesaikan
        $totalLessons = $this->modules->flatMap->lessons->count();
        $totalQuizzes = $this->modules->whereNotNull('quiz')->count();
        $totalItems = $totalLessons + $totalQuizzes;

        // Jika kursus tidak memiliki item sama sekali (pelajaran/kuis), progresnya 0%
        if ($totalItems === 0) {
            return 0;
        }

        // 3. Hitung item yang SUDAH diselesaikan oleh user
        //    a. Hitung pelajaran yang telah diselesaikan
        $completedLessonsCount = $user->completedLessons()
            ->whereIn('lesson_id', $this->modules->flatMap->lessons->pluck('id'))
            ->count();

        //    b. Hitung kuis yang telah LULUS
        $passedQuizzesCount = 0;
        $allQuizzes = $this->modules->map->quiz->filter(); // Ambil semua kuis yang ada

        foreach ($allQuizzes as $quiz) {
            // Cek apakah ada riwayat pengerjaan kuis ini yang statusnya LULUS
            $hasPassed = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->where('passed', true)
                ->exists();

            if ($hasPassed) {
                $passedQuizzesCount++;
            }
        }

        $completedItems = $completedLessonsCount + $passedQuizzesCount;

        // 4. Hitung dan kembalikan persentase akhirnya
        return round(($completedItems / $totalItems) * 100);
    }

    public function isCompletedByUser(User $user): bool
    {
        // 1. Dapatkan semua ID pelajaran dalam kursus ini
        $allLessonIds = $this->modules->flatMap(function ($module) {
            return $module->lessons->pluck('id');
        });

        // 2. Dapatkan semua pelajaran yang telah diselesaikan user
        $completedLessonIds = $user->completedLessons->pluck('id');

        // Jika jumlah pelajaran yang diselesaikan tidak sama dengan total pelajaran, maka belum selesai.
        if ($allLessonIds->diff($completedLessonIds)->isNotEmpty()) {
            return false;
        }

        // 3. Dapatkan semua kuis dalam kursus ini
        $allQuizzes = $this->modules->map(function ($module) {
            return $module->quiz;
        })->filter(); // filter() untuk menghapus modul yang tidak punya kuis

        // 4. Periksa apakah semua kuis telah lulus
        foreach ($allQuizzes as $quiz) {
            $hasPassed = $user->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->where('passed', true)
                ->exists();

            // Jika ada satu saja kuis yang belum lulus, maka belum selesai.
            if (!$hasPassed) {
                return false;
            }
        }

        // Jika semua pelajaran selesai dan semua kuis lulus, maka kursus dianggap selesai.
        return true;
    }

    public function getPublishValidationErrors(): array
    {
        $errors = [];

        // Aturan 1: Deskripsi harus terisi
        if (empty($this->description)) {
            $errors[] = 'Deskripsi kursus tidak boleh kosong.';
        }

        // Aturan 2: Harus ada minimal satu modul
        if ($this->modules->isEmpty()) {
            $errors[] = 'Kursus harus memiliki minimal satu modul.';
        } else {
            // Aturan 2.1: Setiap modul harus punya minimal satu pelajaran
            foreach ($this->modules as $module) {
                if ($module->lessons->isEmpty()) {
                    $errors[] = "Modul '{$module->title}' harus memiliki minimal satu pelajaran.";
                }
            }
        }

        // Aturan 3: Harus ada minimal satu peserta
        if ($this->enrolledUsers->isEmpty()) {
            $errors[] = 'Harus ada minimal satu peserta yang terdaftar di kursus ini.';
        }

        return $errors;
    }
    public function getTotalDurationInHours(): string
    {
        $totalMinutes = $this->modules->flatMap->lessons->sum('duration_in_minutes');

        if ($totalMinutes === 0) {
            return '0 Jam';
        }

        $hours = floor($totalMinutes / 60);
        // $minutes = ($totalMinutes % 60); // Bisa ditambahkan jika ingin format Jam & Menit

        return $hours . ' Jam';
    }

    public function getThumbnailUrl(): string
    {
        // Cek apakah properti 'thumbnail' memiliki nilai (tidak null atau kosong)
        if ($this->thumbnail) {
            // Jika ada, kembalikan URL yang valid dari storage
            return Storage::url($this->thumbnail);
        }

        // Jika tidak ada, kembalikan URL placeholder default
        return 'https://via.placeholder.com/640x480.png/1F2937/FFFFFF?text=LMS+Bosowa';
    }

    public function markAsCompletedFor(User $user)
    {
        $enrollment = $user->enrolledCourses()->where('course_id', $this->id)->first();

        // Hanya jalankan jika kursus diikuti dan BELUM PERNAH ditandai selesai
        if ($enrollment && !$enrollment->pivot->completed_at) {
            // 1. Catat tanggal selesai di pivot table
            $user->enrolledCourses()->updateExistingPivot($this->id, [
                'completed_at' => now(),
            ]);

            // 2. Award 20 points for completing course and getting certificate
            $user->addPoints(20, "Menyelesaikan kursus dan mendapat sertifikat: {$this->title}", $this);

            // 3. Generate certificate otomatis
            try {
                $certificateService = app(\App\Services\CertificateService::class);
                $certificate = $certificateService->generateCertificate($user, $this);

                if ($certificate) {
                    \Log::info("Certificate auto-generated for user {$user->id} completing course {$this->id}");
                } else {
                    \Log::warning("Failed to auto-generate certificate for user {$user->id} completing course {$this->id}");
                }
            } catch (\Exception $e) {
                \Log::error("Error auto-generating certificate: " . $e->getMessage());
            }
        }
    }

    public function getPointsEarnedByUser(User $user)
    {
        $this->loadMissing('modules.lessons', 'modules.quiz');

        // 1. Poin dari menyelesaikan kursus itu sendiri
        $courseCompletionLog = $user->pointLogs()
            ->where('related_type', self::class)
            ->where('related_id', $this->id)
            ->first();
        $totalPoints = $courseCompletionLog ? $courseCompletionLog->points_earned : 0;

        // 2. Poin dari semua pelajaran di kursus ini
        $lessonIds = $this->modules->flatMap->lessons->pluck('id');
        if ($lessonIds->isNotEmpty()) {
            $lessonPoints = $user->pointLogs()
                ->where('related_type', \App\Models\Lesson::class)
                ->whereIn('related_id', $lessonIds)
                ->sum('points_earned');
            $totalPoints += $lessonPoints;
        }

        // 3. Poin dari semua kuis yang lulus di kursus ini
        $quizIds = $this->modules->map->quiz->filter()->pluck('id');
        if ($quizIds->isNotEmpty()) {
            $quizAttemptIds = $user->quizAttempts()->whereIn('quiz_id', $quizIds)->where('passed', true)->pluck('id');
            if($quizAttemptIds->isNotEmpty()){
                $quizPoints = $user->pointLogs()
                    ->where('related_type', \App\Models\QuizAttempt::class)
                    ->whereIn('related_id', $quizAttemptIds)
                    ->sum('points_earned');
                $totalPoints += $quizPoints;
            }
        }

        return $totalPoints > 0 ? $totalPoints : 'N/A';
    }
}
