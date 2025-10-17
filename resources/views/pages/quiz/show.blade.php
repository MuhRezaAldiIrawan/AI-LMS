@extends('layouts.main')

@section('css')
<style>
    .quiz-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .quiz-header {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .quiz-progress {
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .quiz-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .quiz-sidebar {
        max-height: calc(100vh - 140px);
        overflow-y: auto;
    }

    .quiz-navigation {
        position: sticky;
        top: 20px;
    }

    .attempt-card {
        transition: all 0.2s ease;
    }

    .attempt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }

    .quiz-stats {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 8px;
    }

    /* Sidebar/course item styles to match course/lesson */
    .course-item__arrow i { transition: transform 0.3s ease; }
    .course-item-dropdown { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; will-change: max-height; }
    .course-item.active .course-item__arrow i { transform: rotate(90deg); }
    .course-list__item:hover { background-color: #f8f9fa; border-radius: 8px; transition: background-color 0.2s ease; }
    .progress { border-radius: 10px; background-color: #e9ecef; }
    .progress-bar { border-radius: 10px; transition: width 0.6s ease; }
</style>
@endsection

@section('content')
@php
    $course = $quiz->module->course;
    $user = Auth::user();
    $rightProgress = method_exists($course, 'getCompletionPercentage') && $user ? $course->getCompletionPercentage($user) : ($completionPercentage ?? 0);
    $courseModules = isset($courseModules) ? $courseModules : $course->modules;
@endphp
<div class="row gy-4">
    <!-- Main Content -->
    <div class="col-lg-8 col-md-8">
        <div class="card">
            <div class="card-body p-lg-20 p-sm-3">
                <!-- Quiz Header -->
                <div class="quiz-header p-24 rounded-12">
                    <nav aria-label="breadcrumb" class="mb-16">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('course.show', $course->id) }}" class="text-white text-decoration-none opacity-75">
                                    <i class="ph ph-arrow-left me-1"></i> {{ $course->title }}
                                </a>
                            </li>
                            <li class="breadcrumb-item text-white opacity-75">{{ $quiz->module->title }}</li>
                            <li class="breadcrumb-item text-white fw-medium">{{ $quiz->title }}</li>
                        </ol>
                    </nav>

                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h1 class="h3 fw-bold mb-8">{{ $quiz->title }}</h1>
                            @if($quiz->description)
                                <p class="mb-0 opacity-90">{{ $quiz->description }}</p>
                            @endif
                        </div>

                        <div class="text-end">
                            <div class="badge bg-white bg-opacity-25 text-white py-8 px-16 rounded-pill mb-8">
                                <i class="ph ph-exam me-1"></i>Quiz
                            </div>
                            <div class="text-white opacity-90 text-14">
                                <i class="ph ph-question me-1"></i>{{ $quiz->questions->count() }} Pertanyaan
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quiz Content -->
                <div class="p-24">
                    <!-- Quiz Statistics -->
                    <div class="quiz-stats p-20 mb-24">
                        <div class="row g-16">
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-24 text-warning-600">{{ $quiz->questions->count() }}</div>
                                    <div class="text-14 text-gray-600">Total Soal</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-24 text-main-600">{{ $quiz->time_limit ?? 60 }}</div>
                                    <div class="text-14 text-gray-600">Menit</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-24 text-success-600">{{ $quiz->passing_score ?? 70 }}%</div>
                                    <div class="text-14 text-gray-600">Nilai Lulus</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-24 text-primary-600">{{ $quiz->max_attempts ?? 3 }}</div>
                                    <div class="text-14 text-gray-600">Max Percobaan</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Instructions -->
                    <div class="mb-24">
                        <h5 class="fw-bold text-gray-900 mb-16">Petunjuk Pengerjaan</h5>
                        <div class="bg-info-50 border border-info-200 rounded-12 p-20">
                            <ul class="mb-0 text-gray-700">
                                <li class="mb-8">Pastikan koneksi internet Anda stabil</li>
                                <li class="mb-8">Baca setiap pertanyaan dengan teliti</li>
                                <li class="mb-8">Waktu pengerjaan: <strong>{{ $quiz->time_limit ?? 60 }} menit</strong></li>
                                <li class="mb-8">Nilai minimum untuk lulus: <strong>{{ $quiz->passing_score ?? 70 }}%</strong></li>
                                <li class="mb-0">Anda memiliki <strong>{{ $quiz->max_attempts ?? 3 }} kali kesempatan</strong> untuk mengerjakan</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Attempt History -->
                    @if($attempts->count() > 0)
                        <div class="mb-24">
                            <h5 class="fw-bold text-gray-900 mb-16">Riwayat Percobaan</h5>
                            <div class="row g-16">
                                @foreach($attempts as $attempt)
                                    <div class="col-md-6">
                                        <div class="attempt-card border border-gray-200 rounded-12 p-16">
                                            <div class="d-flex justify-content-between align-items-start mb-12">
                                                <div>
                                                    <div class="fw-medium text-gray-900">Percobaan #{{ $loop->iteration }}</div>
                                                    <div class="text-14 text-gray-600">{{ $attempt->created_at->format('d M Y, H:i') }}</div>
                                                </div>
                                                <div class="text-end">
                                                    @if($attempt->passed)
                                                        <span class="badge bg-success-50 text-success-600 py-4 px-12 rounded-pill">
                                                            <i class="ph ph-check me-1"></i>Lulus
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-50 text-danger-600 py-4 px-12 rounded-pill">
                                                            <i class="ph ph-x me-1"></i>Tidak Lulus
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-14 text-gray-600">Skor:</span>
                                                <span class="fw-medium {{ $attempt->passed ? 'text-success-600' : 'text-danger-600' }}">{{ $attempt->score }}%</span>
                                            </div>
                                            @if($attempt->finished_at)
                                                <div class="mt-12 pt-12 border-top">
                                                    <button onclick="showAttemptReview({{ $attempt->id }})" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                                        <i class="ph ph-eye me-1"></i>Lihat Review
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Start Quiz Section -->
                    <div class="text-center">
                        @if($canAttempt)
                            @if($hasPassedQuiz)
                                <div class="mb-24">
                                    <div class="d-inline-flex align-items-center bg-success-50 text-success-600 py-12 px-24 rounded-pill mb-16">
                                        <i class="ph ph-check-circle me-2" style="font-size: 20px;"></i>
                                        <span class="fw-medium">Quiz Sudah Lulus</span>
                                    </div>
                                    <p class="text-gray-600">Selamat! Anda telah lulus quiz ini dengan skor {{ $bestScore }}%</p>
                                </div>
                            @endif

                            <button type="button" id="startQuizBtn" class="btn btn-warning btn-lg rounded-pill py-16 px-32 fw-bold">
                                <i class="ph ph-play-circle me-2" style="font-size: 20px;"></i>
                                {{ $attempts->count() > 0 ? 'Coba Lagi' : 'Mulai Quiz' }}
                            </button>

                            @if($remainingAttempts > 0)
                                <p class="text-gray-600 mt-12 mb-0">
                                    Sisa percobaan: <strong>{{ $remainingAttempts }}</strong>
                                </p>
                            @endif
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-12 p-32">
                                <i class="ph ph-lock text-gray-400" style="font-size: 48px;"></i>
                                <h5 class="text-gray-600 mt-16 mb-8">Percobaan Habis</h5>
                                <p class="text-gray-500 mb-0">Anda telah menggunakan semua kesempatan untuk quiz ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4 col-md-4">
        @php
            $totalLessons = $course->modules->sum(fn($m) => $m->lessons->count());
            $totalQuizzes = $course->modules->whereNotNull('quiz')->count();
            $allQuizzes = $course->modules->map->quiz->filter();
            $passedQuizzes = 0;
            if($user){
                foreach ($allQuizzes as $q) {
                    if ($user->quizAttempts()->where('quiz_id', $q->id)->where('passed', true)->exists()) {
                        $passedQuizzes++;
                    }
                }
                $completedLessons = $user->completedLessons()->whereIn('lesson_id', $course->modules->flatMap->lessons->pluck('id'))->count();
            } else {
                $completedLessons = 0;
            }
            $totalItems = $totalLessons + $totalQuizzes;
            $completedItems = $completedLessons + $passedQuizzes;
        @endphp

        <!-- Progress card -->
        <div class="card mt-0">
            <div class="card-body">
                <h6 class="mb-12">Progress Pembelajaran</h6>
                <div class="d-flex justify-content-between mb-8 text-14">
                    <span class="text-gray-600">{{ $completedItems }} dari {{ $totalItems }} materi selesai</span>
                    <span class="text-main-600 fw-medium">{{ $rightProgress }}%</span>
                </div>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar bg-main-600" role="progressbar" style="width: {{ $rightProgress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Daftar Materi card with accordion -->
        <div class="card mt-24">
            <div class="card-body p-0">
                @php
                    $firstLessonCandidate = $course->modules->sortBy('order')->flatMap->lessons->sortBy('order')->first();
                    $firstLessonUrl = $firstLessonCandidate ? route('lesson.show', $firstLessonCandidate->id) : null;
                @endphp

                <div class="course-item">
                    <button type="button" class="course-item__button flex-align gap-4 w-100 p-16 border-bottom border-gray-100">
                        <span class="d-block text-start">
                            <span class="d-block h5 mb-0 text-line-1">Intro Kursus</span>
                            <span class="d-block text-15 text-gray-300">Ringkasan & mulai</span>
                        </span>
                        <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-caret-down"></i></span>
                    </button>
                    <div class="course-item-dropdown border-bottom border-gray-100">
                        <ul class="course-list p-16 pb-0">
                            <li class="course-list__item flex-align gap-8 mb-16 active">
                                <span class="circle flex-shrink-0 text-32 d-flex text-main-600"><i class="ph-fill ph-check-circle"></i></span>
                                <div class="w-100">
                                    <a href="{{ route('course.show', $course->id) }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                        Buka Ringkasan Kursus
                                        <span class="text-gray-300 fw-normal d-block">Lihat deskripsi, pengajar, dan daftar modul</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                @forelse($courseModules as $index => $module)
                    @php $moduleContainsQuiz = $module->quiz && $module->quiz->id == $quiz->id; @endphp
                    <div class="course-item">
                        <button type="button" class="course-item__button flex-align gap-4 w-100 p-16 border-bottom border-gray-100">
                            <span class="d-block text-start">
                                <span class="d-block h5 mb-0 text-line-1">{{ $module->title }}</span>
                                <span class="d-block text-15 text-gray-300">{{ $module->lessons->count() }} pelajaran</span>
                            </span>
                            <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-arrow-right"></i></span>
                        </button>
                        <div class="course-item-dropdown border-bottom border-gray-100 {{ $moduleContainsQuiz ? 'active' : '' }}">
                            <ul class="course-list p-16 pb-0">
                                @foreach($module->lessons as $lessonIndex => $moduleLesson)
                                    <li class="course-list__item flex-align gap-8 mb-16">
                                        <span class="circle flex-shrink-0 text-32 d-flex {{ $moduleLesson->isCompletedByUser($user) ? 'text-main-600' : 'text-gray-100' }}">
                                            <i class="{{ $moduleLesson->isCompletedByUser($user) ? 'ph-fill ph-check-circle' : 'ph ph-circle' }}"></i>
                                        </span>
                                        <div class="w-100">
                                            <a href="{{ route('lesson.show', $moduleLesson->id) }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                {{ $lessonIndex + 1 }}. {{ $moduleLesson->title }}
                                                <span class="text-gray-300 fw-normal d-block">{{ $moduleLesson->duration_in_minutes ?? 5 }} menit</span>
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                                @if($module->quiz)
                                    <li class="course-list__item flex-align gap-8 mb-16 {{ $module->quiz->id == $quiz->id ? 'active ' : '' }}">
                                        <span class="circle flex-shrink-0 text-32 d-flex text-warning-600"><i class="ph ph-question"></i></span>
                                        <div class="w-100">
                                            <a href="{{ route('quiz.show', $module->quiz->id) }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                Quiz: {{ $module->quiz->title }}
                                                <span class="text-gray-300 fw-normal d-block">{{ $module->quiz->questions->count() }} pertanyaan • {{ $module->quiz->duration_in_minutes ?? ($module->quiz->time_limit ?? 60) }} min</span>
                                            </a>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="p-20 text-center">
                        <i class="ph ph-book text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">Belum ada modul</h5>
                        <p class="text-muted">Modul akan ditampilkan di sini setelah dibuat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startQuizBtn = document.getElementById('startQuizBtn');

    if (startQuizBtn) {
        startQuizBtn.addEventListener('click', function() {
            Swal.fire({
                title: '🚀 Mulai Quiz?',
                html: `
                    <div class="text-start">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ph ph-clock me-2 text-info"></i>
                            <span>Waktu: {{ $quiz->time_limit ?? 60 }} menit</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="ph ph-target me-2 text-success"></i>
                            <span>Nilai lulus: {{ $quiz->passing_score ?? 70 }}%</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="ph ph-repeat me-2 text-warning"></i>
                            <span>Sisa percobaan: {{ $remainingAttempts }}</span>
                        </div>
                        <hr>
                        <p class="text-muted mb-0 text-14">Pastikan Anda siap sebelum memulai. Timer akan dimulai setelah Anda mengklik "Mulai".</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="ph ph-play me-1"></i>Mulai Sekarang',
                cancelButtonText: 'Batal',
                width: 500,
                backdrop: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('quiz.attempt', $quiz->id) }}";
                }
            });
        });
    }
});

// Sidebar accordion logic to mirror course/lesson behavior
document.addEventListener('DOMContentLoaded', function () {
    const courseItems = document.querySelectorAll('.course-item');
    courseItems.forEach(item => {
        const button = item.querySelector('.course-item__button');
        const dropdown = item.querySelector('.course-item-dropdown');
        const arrowIcon = item.querySelector('.course-item__arrow i');
        if (button && dropdown) {
            button.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                courseItems.forEach(other => {
                    other.classList.remove('active');
                    const dd = other.querySelector('.course-item-dropdown');
                    if (dd) dd.style.maxHeight = null;
                    const arr = other.querySelector('.course-item__arrow i');
                    if (arr) arr.style.transform = 'rotate(0deg)';
                });
                if (!isActive) {
                    item.classList.add('active');
                    dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                    if (arrowIcon) arrowIcon.style.transform = 'rotate(90deg)';
                }
            });
        }
    });

    const activeDropdown = document.querySelector('.course-item-dropdown.active');
    if (activeDropdown) {
        const parent = activeDropdown.closest('.course-item');
        if (parent) {
            parent.classList.add('active');
            activeDropdown.style.maxHeight = activeDropdown.scrollHeight + 'px';
            const arr = parent.querySelector('.course-item__arrow i');
            if (arr) arr.style.transform = 'rotate(90deg)';
        }
    }
});

function showAttemptReview(attemptId) {
    window.location.href = `/quiz/attempt/${attemptId}/review`;
}

// Show review after quiz submission
@if(session('show_review'))
    Swal.fire({
        title: '{{ session("success") ? "🎉 Selamat!" : "💪 Tetap Semangat!" }}',
        text: '{{ session("success") ?? session("warning") }}',
        icon: '{{ session("success") ? "success" : "info" }}',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="ph ph-eye me-1"></i>Lihat Review',
        cancelButtonText: 'Tutup'
    }).then((result) => {
        if (result.isConfirmed) {
            showAttemptReview({{ session('show_review') }});
        }
    });
@endif
</script>
@endsection
