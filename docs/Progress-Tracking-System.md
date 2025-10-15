# Progress Tracking System - LMS Bosowa v2

## Overview
Sistem pelacakan progress pembelajaran yang mengukur penyelesaian kursus berdasarkan:
1. **Lesson Completion** - Pelajaran yang sudah diselesaikan
2. **Quiz Passing** - Quiz yang sudah lulus (passed = true)

## Database Structure

### Pivot Tables
1. **lesson_user** - Melacak lesson yang sudah diselesaikan user
   - `user_id`
   - `lesson_id`
   - `created_at`, `updated_at`

2. **course_user** - Melacak enrollment dan completion status
   - `user_id`
   - `course_id`
   - `enrolled_at` - Tanggal user menekan tombol "Enroll"
   - `completed_at` - Tanggal kursus diselesaikan
   - `created_at`, `updated_at`

3. **quiz_attempts** - Melacak percobaan quiz
   - `user_id`
   - `quiz_id`
   - `score`
   - `passed` (boolean) - Status kelulusan
   - Dan field lainnya

## How It Works

### 1. Lesson Completion
Ketika user menyelesaikan lesson:
```php
// Tandai lesson sebagai completed
$user->completedLessons()->attach($lessonId);

// Check if completed
$isCompleted = $user->hasCompletedLesson($lessonId);
```

### 2. Quiz Passing
Ketika user mengerjakan quiz:
```php
// Create quiz attempt dengan status passed
$attempt = QuizAttempt::create([
    'user_id' => $user->id,
    'quiz_id' => $quiz->id,
    'score' => $score,
    'passed' => $score >= $quiz->passing_score
]);
```

### 3. Progress Calculation
Method `Course::getCompletionPercentage(User $user)`:

```php
public function getCompletionPercentage(User $user): int
{
    // 1. Hitung total items (lessons + quizzes)
    $totalLessons = $this->modules->flatMap->lessons->count();
    $totalQuizzes = $this->modules->whereNotNull('quiz')->count();
    $totalItems = $totalLessons + $totalQuizzes;
    
    // 2. Hitung completed lessons
    $completedLessons = $user->completedLessons()
        ->whereIn('lesson_id', $this->modules->flatMap->lessons->pluck('id'))
        ->count();
    
    // 3. Hitung passed quizzes
    $passedQuizzes = 0;
    foreach ($this->modules->map->quiz->filter() as $quiz) {
        if ($user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists()) {
            $passedQuizzes++;
        }
    }
    
    // 4. Calculate percentage
    $completedItems = $completedLessons + $passedQuizzes;
    return round(($completedItems / $totalItems) * 100);
}
```

### 4. Course Completion Check
Method `Course::isCompletedByUser(User $user)`:

```php
public function isCompletedByUser(User $user): bool
{
    // Semua lessons harus diselesaikan
    $allLessonIds = $this->modules->flatMap->lessons->pluck('id');
    $completedLessonIds = $user->completedLessons->pluck('id');
    if ($allLessonIds->diff($completedLessonIds)->isNotEmpty()) {
        return false;
    }
    
    // Semua quizzes harus lulus
    foreach ($this->modules->map->quiz->filter() as $quiz) {
        if (!$user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists()) {
            return false;
        }
    }
    
    return true;
}
```

## User Enrollment States

### 1. No Access (Default)
- User tidak ada di `course_user` table
- Tidak bisa melihat konten
- **Action**: Hubungi admin untuk mendapatkan akses

### 2. Has Access but Not Enrolled
- User ada di `course_user` dengan `enrolled_at = NULL`
- Admin sudah assign user ke course
- Bisa melihat preview tapi belum mulai tracking
- **Action**: Klik tombol "Mulai Belajar" untuk enroll

### 3. Enrolled (Active Learning)
- User ada di `course_user` dengan `enrolled_at != NULL` dan `completed_at = NULL`
- Progress tracking aktif
- Bisa mengakses semua konten
- **Progress calculation aktif**

### 4. Completed
- User ada di `course_user` dengan `completed_at != NULL`
- Semua lessons selesai dan semua quizzes lulus
- **Points awarded**

## Implementation in Views

### Employee Course View (`employee-course.blade.php`)

```php
@php
    $user = Auth::user();
    
    // Calculate totals
    $totalLessons = $course->modules->sum(fn($module) => $module->lessons->count());
    $totalQuizzes = $course->modules->whereNotNull('quiz')->count();
    $totalItems = $totalLessons + $totalQuizzes;
    
    // Calculate completed
    $completedLessons = $user->completedLessons()
        ->whereIn('lesson_id', $course->modules->flatMap->lessons->pluck('id'))
        ->count();
    
    // Calculate passed quizzes
    $passedQuizzes = 0;
    $allQuizzes = $course->modules->map->quiz->filter();
    foreach ($allQuizzes as $quiz) {
        if ($user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists()) {
            $passedQuizzes++;
        }
    }
    
    $completedItems = $completedLessons + $passedQuizzes;
    
    // Use Course method for percentage
    $progressPercentage = $course->getCompletionPercentage($user);
@endphp
```

## Controller Optimization

### Eager Loading for Performance
```php
$course = Course::with([
    'author', 
    'category', 
    'courseType', 
    'enrolledUsers', 
    'modules.lessons', 
    'modules.quiz.questions'
])->findOrFail($id);

// Load user's progress data
$user->load(['completedLessons', 'quizAttempts' => function($query) use ($course) {
    $quizIds = $course->modules->map->quiz->filter()->pluck('id');
    if ($quizIds->isNotEmpty()) {
        $query->whereIn('quiz_id', $quizIds)->where('passed', true);
    }
}]);
```

## Key Points

1. **Progress = Completed Lessons + Passed Quizzes**
2. **Always use `passed = true` for quiz completion**
3. **Eager loading prevents N+1 queries**
4. **Use Course model methods for consistency**
5. **`enrolled_at` menandakan mulai tracking, bukan assignment**
6. **`completed_at` di-set otomatis saat 100% selesai**

## Testing Progress Calculation

### Manual Test Steps
1. Login sebagai karyawan
2. Buka course dengan lessons dan quizzes
3. Selesaikan beberapa lessons (tandai di `lesson_user`)
4. Kerjakan dan lulus beberapa quizzes
5. Refresh halaman course
6. Progress harus tampil dengan benar: X dari Y materi (Z%)

### Expected Behavior
- Progress bar berubah sesuai penyelesaian
- Detail menampilkan: "X dari Y materi selesai (A lesson, B quiz)"
- Persentase dihitung: `(completedLessons + passedQuizzes) / (totalLessons + totalQuizzes) * 100`
- Alert "Selamat!" muncul ketika 100%

## Common Issues & Solutions

### Issue: Progress selalu 0%
**Solution**: 
- Pastikan user sudah enrolled (`enrolled_at != NULL`)
- Cek `lesson_user` table untuk completed lessons
- Cek `quiz_attempts` table dengan `passed = true`

### Issue: N+1 Query Problem
**Solution**: 
- Gunakan eager loading di controller
- Load `completedLessons` dan `quizAttempts` pada user

### Issue: Progress tidak update setelah menyelesaikan lesson/quiz
**Solution**:
- Pastikan data sudah tersimpan di database
- Refresh cache jika ada
- Cek apakah `lesson_id` atau `quiz_id` sesuai

## Related Files
- `app/Models/Course.php` - Methods: `getCompletionPercentage()`, `isCompletedByUser()`
- `app/Models/User.php` - Relationships: `completedLessons()`, `quizAttempts()`
- `app/Http/Controllers/Course/CourseController.php` - Method: `show()`
- `resources/views/pages/course/_partials/employee-course.blade.php` - Progress display
- `database/migrations/*_create_lesson_user_table.php` - Pivot table
- `database/migrations/*_create_quiz_attempts_table.php` - Quiz tracking
