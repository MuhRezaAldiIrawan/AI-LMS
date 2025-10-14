@extends('layouts.main')

@section('css')
<style>
    .lesson-video-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        background: #000;
        border-radius: 12px;
        overflow: hidden;
    }

    .lesson-video-container iframe,
    .lesson-video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .lesson-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .lesson-navigation {
        position: sticky;
        top: 20px;
    }

    .lesson-progress {
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
    }

    .lesson-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    .lesson-sidebar {
        max-height: calc(100vh - 140px);
        overflow-y: auto;
    }

    .lesson-item {
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-bottom: 8px;
    }

    .lesson-item:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }

    .lesson-item.active {
        background: #dbeafe;
        border-left: 4px solid #3b82f6;
    }

    .completion-button {
        transition: all 0.3s ease;
    }

    .completion-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
</style>
@endSection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Main Content Area -->
        <div class="col-lg-9 col-md-8">
            <div class="lesson-content h-100">
                <!-- Lesson Header -->
                <div class="p-24 border-bottom border-gray-100">
                    <div class="d-flex align-items-center justify-content-between mb-16">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('course.show', $lesson->module->course->id) }}" class="text-decoration-none">
                                        <i class="ph ph-arrow-left me-1"></i> {{ $lesson->module->course->title }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item text-muted">{{ $lesson->module->title }}</li>
                                <li class="breadcrumb-item active">{{ $lesson->title }}</li>
                            </ol>
                        </nav>

                        <div class="d-flex align-items-center gap-12">
                            <span class="badge bg-success-50 text-success-600 py-6 px-12 rounded-pill">
                                <i class="ph ph-video me-1"></i>Pelajaran
                            </span>
                            @if($lesson->duration_in_minutes)
                                <span class="text-gray-500 text-14">
                                    <i class="ph ph-clock me-1"></i>{{ $lesson->duration_in_minutes }} menit
                                </span>
                            @endif
                        </div>
                    </div>

                    <h1 class="h3 fw-bold text-gray-900 mb-8">{{ $lesson->title }}</h1>
                    @if($lesson->description)
                        <p class="text-gray-600 mb-0">{{ $lesson->description }}</p>
                    @endif
                </div>

                <!-- Video/Content Area -->
                <div class="p-24">
                    @if($lesson->video_url)
                        <div class="lesson-video-container mb-24">
                            @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                                @php
                                    $video_id = '';
                                    if(str_contains($lesson->video_url, 'youtu.be')) {
                                        $video_id = substr(parse_url($lesson->video_url, PHP_URL_PATH), 1);
                                    } elseif(str_contains($lesson->video_url, 'youtube.com')) {
                                        parse_str(parse_url($lesson->video_url, PHP_URL_QUERY), $query);
                                        $video_id = $query['v'] ?? '';
                                    }
                                @endphp
                                @if($video_id)
                                    <iframe src="https://www.youtube.com/embed/{{ $video_id }}?rel=0&modestbranding=1"
                                            frameborder="0" allowfullscreen></iframe>
                                @endif
                            @else
                                <video controls class="w-100">
                                    <source src="{{ $lesson->video_url }}" type="video/mp4">
                                    Browser Anda tidak mendukung video HTML5.
                                </video>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-64">
                            <i class="ph ph-video-camera text-gray-300" style="font-size: 64px;"></i>
                            <h5 class="text-gray-500 mt-16 mb-8">Video Belum Tersedia</h5>
                            <p class="text-gray-400">Video pembelajaran akan segera ditambahkan</p>
                        </div>
                    @endif

                    <!-- Lesson Content -->
                    @if($lesson->content)
                        <div class="lesson-text-content">
                            <h4 class="fw-bold mb-16 text-gray-900">Materi Pembelajaran</h4>
                            <div class="text-gray-700 line-height-lg">
                                {!! nl2br(e($lesson->content)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Completion Section -->
                    <div class="mt-40 pt-24 border-top border-gray-100">
                        <div class="text-center">
                            @if($isCompleted)
                                <div class="d-inline-flex align-items-center bg-success-50 text-success-600 py-12 px-24 rounded-pill mb-16">
                                    <i class="ph ph-check-circle me-2" style="font-size: 20px;"></i>
                                    <span class="fw-medium">Pelajaran Selesai</span>
                                </div>
                                <p class="text-gray-600 mb-24">Selamat! Anda telah menyelesaikan pelajaran ini.</p>
                            @else
                                <h5 class="fw-bold text-gray-900 mb-16">Selesaikan Pelajaran</h5>
                                <p class="text-gray-600 mb-24">Tandai sebagai selesai untuk melanjutkan ke materi berikutnya</p>
                                <button type="button" id="completeBtn" class="btn btn-success btn-lg rounded-pill py-12 px-32 completion-button">
                                    <i class="ph ph-check-circle me-2"></i>
                                    Tandai Selesai
                                </button>
                            @endif

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-32">
                                @if($previousLesson)
                                    <a href="{{ route('lesson.show', $previousLesson->id) }}" class="btn btn-outline-primary rounded-pill py-10 px-20">
                                        <i class="ph ph-arrow-left me-1"></i> Sebelumnya
                                    </a>
                                @else
                                    <div></div>
                                @endif

                                @if($nextLesson)
                                    <a href="{{ route('lesson.show', $nextLesson->id) }}" class="btn btn-primary rounded-pill py-10 px-20" id="nextLessonBtn">
                                        Selanjutnya <i class="ph ph-arrow-right ms-1"></i>
                                    </a>
                                @elseif($moduleQuiz)
                                    <a href="{{ route('quiz.show', $moduleQuiz->id) }}" class="btn btn-warning rounded-pill py-10 px-20">
                                        <i class="ph ph-exam me-1"></i> Kerjakan Quiz
                                    </a>
                                @else
                                    <div class="text-muted small">Tidak ada materi selanjutnya</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 bg-gray-50 border-start lesson-sidebar">
            <div class="p-24">
                <div class="lesson-navigation">
                    <h6 class="fw-bold text-gray-900 mb-16">
                        <i class="ph ph-list-bullets me-2"></i>Daftar Materi
                    </h6>

                    <!-- Progress Bar -->
                    <div class="mb-20">
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-14 text-gray-600">Progress</span>
                            <span class="text-14 fw-medium text-success-600">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="lesson-progress">
                            <div class="lesson-progress-bar" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                    </div>

                    <!-- Lessons List -->
                    @foreach($courseModules as $module)
                        <div class="mb-20">
                            <h6 class="fw-medium text-gray-800 mb-12 text-14">{{ $module->title }}</h6>

                            @foreach($module->lessons as $moduleLesson)
                                <div class="lesson-item {{ $moduleLesson->id == $lesson->id ? 'active' : '' }} p-12">
                                    <a href="{{ route('lesson.show', $moduleLesson->id) }}" class="text-decoration-none d-flex align-items-start">
                                        <div class="me-8 mt-2">
                                            @if($moduleLesson->isCompletedByUser(Auth::user()))
                                                <i class="ph ph-check-circle text-success-600" style="font-size: 16px;"></i>
                                            @else
                                                <i class="ph ph-circle text-gray-400" style="font-size: 16px;"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-14 fw-medium text-gray-800 mb-2">{{ $moduleLesson->title }}</div>
                                            <div class="text-12 text-gray-500">
                                                <i class="ph ph-video me-1"></i>
                                                {{ $moduleLesson->duration_in_minutes ?? 5 }} menit
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                            @if($module->quiz)
                                <div class="lesson-item p-12">
                                    <a href="{{ route('quiz.show', $module->quiz->id) }}" class="text-decoration-none d-flex align-items-start">
                                        <div class="me-8 mt-2">
                                            <i class="ph ph-exam text-warning-600" style="font-size: 16px;"></i>
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

<!-- Hidden form for completion -->
<form id="completeForm" action="{{ route('lesson.complete', $lesson->id) }}" method="POST" class="d-none">
    @csrf
</form>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const completeBtn = document.getElementById('completeBtn');
    const completeForm = document.getElementById('completeForm');

    if (completeBtn && completeForm) {
        completeBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Selesaikan Pelajaran?',
                text: 'Tandai pelajaran ini sebagai selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    completeBtn.disabled = true;
                    completeBtn.innerHTML = '<i class="ph ph-spinner me-2" style="animation: spin 1s linear infinite;"></i>Memproses...';

                    // Submit form
                    fetch(completeForm.action, {
                        method: 'POST',
                        body: new FormData(completeForm),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            @if($nextLesson)
                                // Redirect to next lesson
                                Swal.fire({
                                    title: '✅ Pelajaran Selesai!',
                                    text: 'Lanjut ke materi berikutnya...',
                                    icon: 'success',
                                    timer: 1000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route("lesson.show", $nextLesson->id) }}';
                                });
                            @elseif($moduleQuiz)
                                // Redirect to quiz
                                Swal.fire({
                                    title: '✅ Pelajaran Selesai!',
                                    text: 'Saatnya mengerjakan quiz...',
                                    icon: 'success',
                                    timer: 1000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route("quiz.show", $moduleQuiz->id) }}';
                                });
                            @else
                                // No next lesson or quiz, just reload
                                Swal.fire({
                                    title: '✅ Pelajaran Selesai!',
                                    text: 'Selamat! Anda telah menyelesaikan materi ini.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            @endif
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        completeBtn.disabled = false;
                        completeBtn.innerHTML = '<i class="ph ph-check-circle me-2"></i>Tandai Selesai';

                        Swal.fire({
                            title: '❌ Gagal!',
                            text: 'Terjadi kesalahan. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'Oke'
                        });
                    });
                }
            });
        });
    }
});
</script>
@endsection
