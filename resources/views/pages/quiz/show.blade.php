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
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="quiz-container h-100">
                <!-- Quiz Header -->
                <div class="quiz-header p-24">
                    <nav aria-label="breadcrumb" class="mb-16">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('course.show', $quiz->module->course->id) }}" class="text-white text-decoration-none opacity-75">
                                    <i class="ph ph-arrow-left me-1"></i> {{ $quiz->module->course->title }}
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
                                    <div class="fw-bold text-24 text-info-600">{{ $quiz->time_limit ?? 60 }}</div>
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
                                                <span class="fw-medium {{ $attempt->passed ? 'text-success-600' : 'text-danger-600' }}">
                                                    {{ $attempt->score }}%
                                                </span>
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

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 bg-gray-50 border-start quiz-sidebar">
            <div class="p-24">
                <div class="quiz-navigation">
                    <h6 class="fw-bold text-gray-900 mb-16">
                        <i class="ph ph-list-bullets me-2"></i>Daftar Materi
                    </h6>

                    <!-- Progress Bar -->
                    <div class="mb-20">
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-14 text-gray-600">Progress</span>
                            <span class="text-14 fw-medium text-success-600">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="quiz-progress">
                            <div class="quiz-progress-bar" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                    </div>

                    <!-- Course Content -->
                    @foreach($courseModules as $module)
                        <div class="mb-20">
                            <h6 class="fw-medium text-gray-800 mb-12 text-14">{{ $module->title }}</h6>

                            @foreach($module->lessons as $lesson)
                                <div class="lesson-item p-12 rounded-8 mb-8">
                                    <a href="{{ route('lesson.show', $lesson->id) }}" class="text-decoration-none d-flex align-items-start">
                                        <div class="me-8 mt-2">
                                            @if($lesson->isCompletedByUser(Auth::user()))
                                                <i class="ph ph-check-circle text-success-600" style="font-size: 16px;"></i>
                                            @else
                                                <i class="ph ph-circle text-gray-400" style="font-size: 16px;"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-14 fw-medium text-gray-800 mb-2">{{ $lesson->title }}</div>
                                            <div class="text-12 text-gray-500">
                                                <i class="ph ph-video me-1"></i>
                                                {{ $lesson->duration_in_minutes ?? 5 }} menit
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                            @if($module->quiz)
                                <div class="lesson-item {{ $module->quiz->id == $quiz->id ? 'active bg-warning-50 border-warning-200' : '' }} p-12 rounded-8 border">
                                    <a href="{{ route('quiz.show', $module->quiz->id) }}" class="text-decoration-none d-flex align-items-start">
                                        <div class="me-8 mt-2">
                                            @if($module->quiz->isPassedByUser(Auth::user()))
                                                <i class="ph ph-check-circle text-success-600" style="font-size: 16px;"></i>
                                            @else
                                                <i class="ph ph-exam text-warning-600" style="font-size: 16px;"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-14 fw-medium text-gray-800 mb-2">Quiz: {{ $module->quiz->title }}</div>
                                            <div class="text-12 text-warning-600">
                                                <i class="ph ph-question me-1"></i>
                                                {{ $module->quiz->questions->count() }} pertanyaan
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
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
