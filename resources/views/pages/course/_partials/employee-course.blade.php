@extends('layouts.main')

@section('css')
    <style>
        .course-item__arrow i {
            transition: transform 0.3s ease;
        }

        .course-item-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            will-change: max-height;
        }

        /* BARU: Atur rotasi panah berdasarkan class .active pada parent */
        .course-item.active .course-item__arrow i {
            transform: rotate(90deg);
        }

        .course-list__item:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Loading spinner animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .ph-spinner {
            animation: spin 1s linear infinite;
        }

        /* Enrollment button states */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-main:hover:not(:disabled), .btn-primary:hover:not(:disabled), .btn-success:hover:not(:disabled), .btn-warning:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        /* Pulsing effect untuk button utama */
        #enrollBtn {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 2px 10px rgba(14, 165, 233, 0.25); }
            50% { box-shadow: 0 3px 12px rgba(14, 165, 233, 0.35); }
            100% { box-shadow: 0 2px 10px rgba(14, 165, 233, 0.25); }
        }

        /* Gradient untuk alert */
        .alert-warning {
            border: 2px solid #f0ad4e !important;
        }

        .alert-success {
            border: 2px solid #28a745 !important;
        }

        /* Make Preview button visible even without hover */
        .btn-preview {
            background-color: #ffffff !important;
            color: #0ea5e9 !important; /* primary tone used in certificate panel */
            border: 1px solid #0ea5e9 !important;
        }
        .btn-preview:hover {
            background-color: #0ea5e9 !important;
            color: #ffffff !important;
            border-color: #0ea5e9 !important;
        }

        /* Sidebar enroll card polish */
        .side-enroll__header {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            border: 1px solid #9bd5fb;
            border-radius: 12px;
        }
        .side-enroll__benefits li i { color: #28a745; }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            border: 1px solid var(--chip-border, #e5e7eb);
            background: var(--chip-bg, #fff);
            color: var(--chip-color, #6b7280);
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><a href="{{ route('course') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Courses</a></li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><span class="text-main-600 fw-normal text-15">{{ $course->title }}</span></li>
        </ul>
    </div>
    <div class="row gy-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-lg-20 p-sm-3">
                    <div class="flex-between flex-wrap gap-12 mb-20">
                        <div>
                            <h3 class="mb-8">{{ $course->title }}</h3>
                            @php
                                $modulesCount = $course->modules->count();
                                $lessonsCount = $course->modules->sum(fn($module) => $module->lessons->count());
                                $quizzesCount = $course->modules->whereNotNull('quiz')->count();
                                $totalDuration = $course->getTotalDurationInHours();
                            @endphp
                            <ul class="d-flex flex-wrap gap-16 p-0 m-0 list-unstyled text-gray-600 text-13">
                                <li class="d-flex align-items-center gap-6"><i class="ph ph-books text-main-600"></i> {{ $modulesCount }} Modul</li>
                                <li class="d-flex align-items-center gap-6"><i class="ph ph-play-circle text-main-600"></i> {{ $lessonsCount }} Pelajaran</li>
                                <li class="d-flex align-items-center gap-6"><i class="ph ph-exam text-main-600"></i> {{ $quizzesCount }} Quiz</li>
                                <li class="d-flex align-items-center gap-6"><i class="ph ph-timer text-main-600"></i> {{ $totalDuration }} Durasi Total</li>
                            </ul>
                        </div>

                        <div class="flex-align flex-wrap gap-24">
                            <span class="py-6 px-16 bg-main-50 text-main-600 rounded-pill text-15">{{ $course->category->name ?? 'Umum' }}</span>
                            @if($isEnrolled)
                                <span class="py-6 px-16 bg-success-50 text-success-600 rounded-pill text-15">
                                    <i class="ph ph-check-circle me-1"></i>Terdaftar
                                </span>
                            @else
                                <span class="py-6 px-16 bg-warning-50 text-warning-600 rounded-pill text-15">
                                    <i class="ph ph-clock me-1"></i>Belum Terdaftar
                                </span>
                            @endif
                            <div class=" share-social position-relative">
                                <button type="button"
                                    class="share-social__button text-gray-200 text-26 d-flex hover-text-main-600"><i
                                        class="ph ph-share-network"></i></button>
                                <div
                                    class="share-social__icons bg-white box-shadow-2xl p-16 border border-gray-100 rounded-8 position-absolute inset-block-start-100 inset-inline-end-0">
                                    <ul class="flex-align gap-8">
                                        <li>
                                            <a href="https://www.facebook.com"
                                                class="flex-center w-36 h-36 border border-main-600 text-white rounded-circle text-xl bg-main-600 hover-bg-main-800 hover-border-main-800"><i
                                                    class="ph ph-facebook-logo"></i></a>
                                        </li>
                                        <li>
                                            <a href="https://www.google.com"
                                                class="flex-center w-36 h-36 border border-main-600 text-white rounded-circle text-xl bg-main-600 hover-bg-main-800 hover-border-main-800">
                                                <i class="ph ph-twitter-logo"></i></a>
                                        </li>
                                        <li>
                                            <a href="https://www.twitter.com"
                                                class="flex-center w-36 h-36 border border-main-600 text-white rounded-circle text-xl bg-main-600 hover-bg-main-800 hover-border-main-800"><i
                                                    class="ph ph-linkedin-logo"></i></a>
                                        </li>
                                        <li>
                                            <a href="https://www.instagram.com"
                                                class="flex-center w-36 h-36 border border-main-600 text-white rounded-circle text-xl bg-main-600 hover-bg-main-800 hover-border-main-800"><i
                                                    class="ph ph-instagram-logo"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="bookmark-icon text-gray-200 text-26 d-flex hover-text-main-600">
                                <i class="ph ph-bookmarks"></i>
                            </button>
                        </div>
                    </div>

                @if($isEnrolled)
                        @php
                            $enrolledProgressPercentage = $course->getCompletionPercentage(Auth::user());
                        @endphp

                        <div class="alert alert-success border-success rounded-12 p-16 mb-16" style="background: rgba(212, 237, 218, 0.7); border: 1px solid #28a745;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="ph ph-check-circle text-success me-2" style="font-size: 24px;"></i>
                                    <div>
                                        @if($enrolledProgressPercentage === 100)
                                            <span class="text-success fw-bold text-15 d-block">🎉 Selesai - Anda telah menyelesaikan kursus ini!</span>
                                        @elseif($enrolledProgressPercentage > 0)
                                            <span class="text-success fw-bold text-15 d-block">📚 Sedang belajar - Lanjutkan pembelajaran</span>
                                            <span class="text-success text-13">Progress: {{ $enrolledProgressPercentage }}% selesai</span>
                                        @else
                                            <span class="text-success fw-bold text-15 d-block">✅ Terdaftar - Siap belajar!</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    @if($enrolledProgressPercentage === 100)
                                        <span class="badge bg-warning text-dark py-6 px-12">
                                            <i class="ph ph-trophy me-1"></i>Selesai!
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($enrolledProgressPercentage > 0 && $enrolledProgressPercentage < 100)
                                <div class="progress mt-12" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $enrolledProgressPercentage }}%"></div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @php
                        // Data untuk tombol "Selanjutnya" (di bawah thumbnail)
                        $firstLessonCandidate = $course->modules->sortBy('order')->flatMap->lessons->sortBy('order')->first();
                        $firstLessonUrl = $firstLessonCandidate ? route('lesson.show', $firstLessonCandidate->id) : null;
                        $progressForCta = $isEnrolled ? $course->getCompletionPercentage(Auth::user()) : null;
                    @endphp

                    <div class="rounded-16 overflow-hidden">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage' . '/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                 class="w-100" style="height: 300px; object-fit: cover;">
                        @else
                            <div class="bg-gray-100 d-flex align-items-center justify-content-center" style="height: 300px;">
                                <i class="ph ph-image text-gray-400" style="font-size: 48px;"></i>
                            </div>
                        @endif
                    </div>

                    {{-- CTA: Selanjutnya (di bawah thumbnail, rata kanan, biru) --}}
                    @if($isEnrolled && $firstLessonUrl && ($progressForCta === 0 || $progressForCta < 100))
                        <div class="mt-12 d-flex justify-content-end">
                            <a href="{{ $firstLessonUrl }}" class="btn btn-primary rounded-pill py-8 px-16">
                                Selanjutnya <i class="ph ph-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif

                    <div class="mt-24">
                        {{-- About Course (Ringkasan) --}}
                        <div class="mb-24 pb-24 border-bottom border-gray-100">
                            <h5 class="mb-12 fw-bold">About Course</h5>
                            <p class="text-gray-300 text-15">{{ $course->summary ?? 'Ringkasan kursus belum tersedia.' }}</p>
                        </div>

                        {{-- Deskripsi Kursus --}}
                        <div class="mb-24 pb-24 border-bottom border-gray-100">
                            <h5 class="mb-12 fw-bold">Deskripsi Kursus</h5>
                            <p class="text-gray-300 text-15">{{ $course->description ?? 'Deskripsi kursus belum tersedia.' }}</p>
                        </div>

                        {{-- Pengajar --}}
                        <div class="mb-24">
                            <h5 class="mb-12 fw-bold">Pengajar</h5>
                            <div class="flex-align gap-8">
                                @if($course->author && $course->author->avatar)
                                    <img src="{{ Storage::url($course->author->avatar) }}" alt="{{ $course->author->name }}" class="w-44 h-44 rounded-circle object-fit-cover flex-shrink-0">
                                @else
                                    <div class="w-44 h-44 rounded-circle bg-main-50 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="ph ph-user text-main-600" style="font-size: 20px;"></i>
                                    </div>
                                @endif
                                <div class="d-flex flex-column">
                                    <h6 class="text-15 fw-bold mb-0">{{ $course->author->name ?? 'Tidak ada pengajar' }}</h6>
                                    <span class="text-13 text-gray-300">{{ $course->author->position ?? 'Instruktur' }}</span>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div> 
            </div> 
        </div> 
        <div class="col-lg-4">
            @php
                $user = Auth::user();
                $rightProgress = $course->getCompletionPercentage($user);
            @endphp

            {{-- Enroll CTA / Access State --}}
            @if(!$isEnrolled)
                <div class="card side-enroll">
                    <div class="card-body">
                        @if(isset($hasAccess) && $hasAccess)
                            <div class="side-enroll__header p-12 d-flex align-items-center gap-10 mb-14">
                                <div class="w-36 h-36 rounded-circle bg-white d-flex align-items-center justify-content-center">
                                    <i class="ph ph-graduation-cap text-main-600" style="font-size:18px"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Mulai Belajar</div>
                                    <div class="text-12 text-gray-500">Buka semua materi, video & kuis</div>
                                </div>
                            </div>
                            <form id="enrollForm" action="{{ route('course.enroll', $course->id) }}" method="POST">
                                @csrf
                                <button type="button" id="enrollBtn" class="btn btn-primary w-100 rounded-pill py-10" onclick="window.confirmEnrollment()">
                                    <i class="ph ph-check me-2"></i> Daftar Kursus Ini
                                </button>
                            </form>
                        @else
                            <div class="side-enroll__header p-12 d-flex align-items-center gap-10 mb-14" style="background:linear-gradient(135deg,#fef3c7 0%, #fde68a 100%); border-color:#f7d177;">
                                <div class="w-36 h-36 rounded-circle bg-white d-flex align-items-center justify-content-center">
                                    <i class="ph ph-lock text-warning" style="font-size:18px"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Akses Diperlukan</div>
                                    <div class="text-12 text-gray-500">Hubungi admin untuk mendapatkan akses</div>
                                </div>
                            </div>
                            <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                <i class="ph ph-lock me-2"></i> Belum Memiliki Akses
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Locked Modules list in sidebar --}}
                <div class="card mt-24">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-10">
                            <h6 class="mb-0">Daftar Modul</h6>
                            <span class="badge bg-main-50 text-main-600">Terkunci</span>
                        </div>
                        @forelse($course->modules as $module)
                            <div class="border rounded-12 p-12 mb-10 course-list__item">
                                <button type="button" class="w-100 d-flex justify-content-between align-items-center course-item__button bg-transparent border-0">
                                    <div class="d-flex align-items-center gap-10">
                                        <i class="ph ph-lock text-warning"></i>
                                        <span class="fw-medium">{{ $module->title }}</span>
                                    </div>
                                    <span class="course-item__arrow"><i class="ph ph-caret-right"></i></span>
                                </button>
                                <div class="course-item-dropdown">
                                    <div class="text-gray-400 text-13 mt-8">Pelajaran di modul ini akan terlihat setelah Anda mendaftar.</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-400">Belum ada modul.</div>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- Sidebar: Daftar Modul untuk user yang sudah terdaftar --}}
            @if($isEnrolled)
                {{-- Progress Pembelajaran (Sidebar - posisi pertama) --}}
                <div class="card mt-0">
                    <div class="card-body">
                        <h6 class="mb-12">Progress Pembelajaran</h6>
                        @php
                            $totalLessons = $course->modules->sum(fn($module) => $module->lessons->count());
                            $totalQuizzes = $course->modules->whereNotNull('quiz')->count();
                            $totalItems = $totalLessons + $totalQuizzes;
                            $completedLessons = $user->completedLessons()->whereIn('lesson_id', $course->modules->flatMap->lessons->pluck('id'))->count();
                            $passedQuizzes = 0;
                            $allQuizzes = $course->modules->map->quiz->filter();
                            foreach ($allQuizzes as $quiz) {
                                if ($user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists()) {
                                    $passedQuizzes++;
                                }
                            }
                            $completedItems = $completedLessons + $passedQuizzes;
                        @endphp
                        <div class="d-flex justify-content-between mb-8 text-14">
                            <span class="text-gray-600">{{ $completedItems }} dari {{ $totalItems }} materi selesai</span>
                            <span class="text-main-600 fw-medium">{{ $rightProgress }}%</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-main-600" role="progressbar" style="width: {{ $rightProgress }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Intro + Daftar Modul (digabung dalam satu card seperti overview) --}}
                <div class="card mt-24">
                    <div class="card-body p-0">
                        @php
                            $firstLessonCandidate = $course->modules->sortBy('order')->flatMap->lessons->sortBy('order')->first();
                            $firstLessonUrl = $firstLessonCandidate ? route('lesson.show', $firstLessonCandidate->id) : null;
                            // Prefetch progress datasets to avoid N+1 queries inside the loop
                            $allLessonIds = $course->modules->flatMap->lessons->pluck('id');
                            $completedLessonIds = $user
                                ? $user->completedLessons()->whereIn('lesson_id', $allLessonIds)->pluck('lesson_id')->toArray()
                                : [];
                            $quizIds = $course->modules->map->quiz->filter()->pluck('id');
                            $passedQuizIds = $user && $quizIds->isNotEmpty()
                                ? $user->quizAttempts()->whereIn('quiz_id', $quizIds)->where('passed', true)->pluck('quiz_id')->toArray()
                                : [];
                        @endphp

                        {{-- Item: Intro Kursus (posisi teratas) --}}
                        <div class="course-item">
                            <button type="button"
                                class="course-item__button flex-align gap-4 w-100 p-16 border-bottom border-gray-100">
                                <span class="d-block text-start">
                                    <span class="d-block h5 mb-0 text-line-1">Intro Kursus</span>
                                    <span class="d-block text-15 text-gray-300">Ringkasan & mulai</span>
                                </span>
                                <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-caret-down"></i></span>
                            </button>
                            <div class="course-item-dropdown border-bottom border-gray-100 active">
                                <ul class="course-list p-16 pb-0">
                                    <li class="course-list__item flex-align gap-8 mb-16 active">
                                        <span class="circle flex-shrink-0 text-32 d-flex text-main-600"><i class="ph-fill ph-check-circle"></i></span>
                                        <div class="w-100">
                                            @if($firstLessonUrl)
                                                <a href="{{ $firstLessonUrl }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                    Mulai Pelajaran Pertama
                                                    <span class="text-gray-300 fw-normal d-block">{{ $firstLessonCandidate->title ?? 'Pelajaran 1' }} • {{ $firstLessonCandidate->duration ?? '15 min' }}</span>
                                                </a>
                                            @else
                                                <span class="text-gray-300 fw-medium d-block">Belum ada pelajaran pertama</span>
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Items: Modul --}}
                        @forelse($course->modules as $index => $module)
                            @php
                                // Hitung progres per modul untuk user saat ini (tanpa query tambahan)
                                $moduleLessonIds = $module->lessons->pluck('id');
                                $moduleCompletedLessons = collect($completedLessonIds)->intersect($moduleLessonIds)->count();
                                $moduleQuiz = $module->quiz;
                                $quizPassed = 0;
                                if ($moduleQuiz) {
                                    $quizPassed = in_array($moduleQuiz->id, $passedQuizIds) ? 1 : 0;
                                }
                                $moduleTotalItems = $module->lessons->count() + ($moduleQuiz ? 1 : 0);
                                $moduleCompletedItems = $moduleCompletedLessons + $quizPassed;
                                $moduleDurationMinutes = $module->lessons->sum('duration_in_minutes');
                            @endphp
                            <div class="course-item">
                                <button type="button"
                                    class="course-item__button flex-align gap-4 w-100 p-16 border-bottom border-gray-100">
                                    <span class="d-block text-start">
                                        <span class="d-block h5 mb-0 text-line-1">{{ $module->title }}</span>
                                        <span class="d-block text-15 text-gray-300">{{ $moduleCompletedItems }} / {{ $moduleTotalItems }} | {{ $moduleDurationMinutes }} min</span>
                                    </span>
                                    <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-arrow-right"></i></span>
                                </button>
                                <div class="course-item-dropdown border-bottom border-gray-100">
                                    <ul class="course-list p-16 pb-0">
                                        @foreach($module->lessons as $lessonIndex => $lesson)
                                            @php $isDone = in_array($lesson->id, $completedLessonIds); @endphp
                                            <li class="course-list__item flex-align gap-8 mb-16">
                                                <span class="circle flex-shrink-0 text-32 d-flex {{ $isDone ? 'text-main-600' : 'text-gray-100' }}">
                                                    <i class="{{ $isDone ? 'ph-fill ph-check-circle' : 'ph ph-circle' }}"></i>
                                                </span>
                                                <div class="w-100">
                                                    <a href="{{ route('lesson.show', $lesson->id) }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                        {{ $lessonIndex + 1 }}. {{ $lesson->title }}
                                                        <span class="text-gray-300 fw-normal d-block">{{ $lesson->duration_in_minutes ?? 0 }} menit</span>
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                        @if($module->quiz)
                                            @php
                                                $passed = in_array($module->quiz->id, $passedQuizIds);
                                            @endphp
                                            <li class="course-list__item flex-align gap-8 mb-16">
                                                <span class="circle flex-shrink-0 text-32 d-flex {{ $passed ? 'text-success-600' : 'text-warning-600' }}">
                                                    <i class="{{ $passed ? 'ph-fill ph-check-circle' : 'ph ph-question' }}"></i>
                                                </span>
                                                <div class="w-100">
                                                    <a href="{{ route('quiz.show', $module->quiz->id) }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                        Quiz: {{ $module->quiz->title }}
                                                        <span class="text-gray-300 fw-normal d-block">{{ $module->quiz->questions->count() }} pertanyaan • {{ $module->quiz->duration_in_minutes }} min</span>
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

                {{-- Certificate card (only when 100% complete) - ditempatkan setelah card modul --}}
                @if($rightProgress === 100)
                    @php $certificate = $user->getCertificateForCourse($course->id); @endphp
                    <div class="card mt-24">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <h5 class="mb-0">Sertifikat</h5>
                                <span class="badge bg-success text-dark py-6 px-12"><i class="ph ph-trophy me-1"></i> Tersedia</span>
                            </div>
                            @if($certificate)
                                <div class="p-12 rounded-12 mb-16" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border: 1px solid #0ea5e9;">
                                    <div class="d-flex align-items-start gap-12">
                                        <i class="ph ph-certificate text-primary" style="font-size: 32px;"></i>
                                        <div>
                                            <div class="fw-bold text-dark mb-4">Selamat! Sertifikat Anda siap.</div>
                                            <div class="text-13 text-dark">
                                                <div><strong>No.:</strong> {{ $certificate->certificate_number }}</div>
                                                <div><strong>Terbit:</strong> {{ $certificate->issued_date->format('d F Y') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-10">
                                    <a href="{{ route('certificate.download', $certificate->id) }}" class="btn btn-primary btn-sm rounded-pill py-8 px-16">
                                        <i class="ph ph-download me-2"></i>Download
                                    </a>
                                    <a href="{{ route('certificate.preview', $certificate->id) }}" target="_blank" rel="noopener noreferrer" class="btn btn-preview btn-sm rounded-pill py-8 px-16">
                                        <i class="ph ph-eye me-2"></i>Preview
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning border-warning rounded-12 p-12 mb-0">
                                    <i class="ph ph-hourglass text-warning me-2"></i>
                                    Sertifikat sedang diproses, silakan refresh beberapa saat lagi.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div> 
    </div> 
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // FUNGSI UNTUK ENROLLMENT (TIDAK BERUBAH)
    // ==========================================
    window.confirmEnrollment = function() {
        if (typeof Swal === 'undefined') {
            alert('SweetAlert tidak tersedia. Silakan refresh halaman.');
            return;
        }

        Swal.fire({
            title: '🎓 Mulai Pembelajaran?',
            html: `
                <div class="text-start">
                    <h5 class="mb-3">{{ $course->title }}</h5>
                    <div class="d-flex align-items-center mb-2">
                        <i class="ph ph-user-circle me-2 text-primary"></i>
                        <span>Pengajar: {{ $course->author->name ?? 'Tidak ada pengajar' }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="ph ph-books me-2 text-success"></i>
                        <span>{{ $course->modules->count() }} Modul & {{ $course->modules->sum(fn($module) => $module->lessons->count()) }} Pelajaran</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="ph ph-certificate me-2 text-warning"></i>
                        <span>Sertifikat digital tersedia</span>
                    </div>
                    <hr>
                    <p class="text-muted mb-0">Dengan memulai pembelajaran, progress Anda akan mulai terlacak dan Anda dapat mengerjakan semua quiz.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ph ph-check me-2"></i>Ya, Mulai Sekarang!',
            cancelButtonText: '<i class="ph ph-x me-2"></i>Batal',
            width: 500,
            padding: '2rem',
            backdrop: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-success btn-lg me-2',
                cancelButton: 'btn btn-secondary btn-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.enrollUser();
            }
        });
    };

    window.enrollUser = function() {
        const button = document.getElementById('enrollBtn');
        const form = document.getElementById('enrollForm');

        if (!button || !form) {
            alert('Terjadi kesalahan: Element tidak ditemukan. Silakan refresh halaman.');
            return;
        }

        const originalText = button.innerHTML;

        // Show loading state
        button.disabled = true;
        button.innerHTML = '<i class="ph ph-spinner me-2" style="animation: spin 1s linear infinite;"></i>Sedang Mendaftar...';

        // Submit form dengan fetch
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '✅ Berhasil Terdaftar!',
                    text: 'Selamat belajar!',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Oke',
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(() => {
            button.disabled = false;
            button.innerHTML = originalText;
            Swal.fire({
                title: '❌ Gagal!',
                text: 'Maaf, terjadi kesalahan saat mendaftar. Silakan coba lagi.',
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Coba Lagi'
            });
        });
    };


    // LOGIKA DROPDOWN YANG DIPERBAIKI
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Dapatkan semua elemen yang relevan
        const courseItems = document.querySelectorAll('.course-item');

        // 2. Tambahkan event listener untuk setiap item
        courseItems.forEach(item => {
            const button = item.querySelector('.course-item__button');
            const dropdown = item.querySelector('.course-item-dropdown');
            const arrowIcon = item.querySelector('.course-item__arrow i');

            if (button && dropdown) {
                button.addEventListener('click', () => {
                    // Cek apakah item yang diklik saat ini sudah aktif
                    const isActive = item.classList.contains('active');

                    // 3. Tutup semua item lain (fungsi accordion)
                    courseItems.forEach(otherItem => {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.course-item-dropdown').style.maxHeight = null;
                        const otherArrow = otherItem.querySelector('.course-item__arrow i');
                        if(otherArrow) otherArrow.style.transform = 'rotate(0deg)';
                    });

                    // 4. Buka item yang diklik (jika sebelumnya tertutup)
                    if (!isActive) {
                        item.classList.add('active');
                        // Set maxHeight sesuai dengan tinggi konten di dalamnya agar animasi mulus
                        dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                        if(arrowIcon) arrowIcon.style.transform = 'rotate(90deg)';
                    }
                    // Jika item yang diklik sudah aktif, langkah no 3 sudah menutupnya.
                });
            }
        });

        // Inisialisasi: Buka dropdown yang sudah memiliki class 'active' saat halaman dimuat
        const initiallyActiveItem = document.querySelector('.course-item .course-item-dropdown.active');
        if (initiallyActiveItem) {
            const parentItem = initiallyActiveItem.closest('.course-item');
            const arrowIcon = parentItem.querySelector('.course-item__arrow i');

            parentItem.classList.add('active');
            initiallyActiveItem.style.maxHeight = initiallyActiveItem.scrollHeight + 'px';
            if(arrowIcon) arrowIcon.style.transform = 'rotate(90deg)';
        }


        // Handle flash messages dari server (TIDAK BERUBAH)
        // ==========================================
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: '{{ session('info') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endsection