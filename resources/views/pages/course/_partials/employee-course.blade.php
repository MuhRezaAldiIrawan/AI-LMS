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
        }

        .course-item-dropdown.active {
            max-height: 1000px;
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

        .btn-main:hover:not(:disabled), .btn-success:hover:not(:disabled), .btn-warning:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        /* Pulsing effect untuk button utama */
        #enrollBtn {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            }
            50% {
                box-shadow: 0 8px 25px rgba(40, 167, 69, 0.6);
            }
            100% {
                box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            }
        }

        /* Gradient untuk alert */
        .alert-warning {
            border: 2px solid #f0ad4e !important;
        }

        .alert-success {
            border: 2px solid #28a745 !important;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb Start -->
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><a href="{{ route('course') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Courses</a></li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><span class="text-main-600 fw-normal text-15">{{ $course->title }}</span></li>
        </ul>
    </div>
    <!-- Breadcrumb End -->

    <div class="row gy-4">
        <div class="col-md-8">
            <!-- Course Card Start -->
            <div class="card">
                <div class="card-body p-lg-20 p-sm-3">
                    <div class="flex-between flex-wrap gap-12 mb-20">
                        <div>
                            <h3 class="mb-4">{{ $course->title }}</h3>
                            <p class="text-gray-600 text-15">{{ $course->author->name ?? 'Tidak ada pengajar' }}</p>
                            <p class="text-gray-600 text-15 mb-2"><strong>Role Anda:</strong> Karyawan</p>
                            @if($isEnrolled)
                                @php
                                    $enrollment = Auth::user()->enrolledCourses()->where('course_id', $course->id)->first();
                                    $enrolledDate = $enrollment ? $enrollment->pivot->enrolled_at : null;
                                @endphp
                                @if($enrolledDate)
                                    <p class="text-success-600 text-13">
                                        <i class="ph ph-calendar me-1"></i>Terdaftar sejak: {{ \Carbon\Carbon::parse($enrolledDate)->format('d M Y') }}
                                    </p>
                                @endif
                            @endif
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

                    <!-- Enrollment Section -->
                    @if(!$hasAccess)
                        <!-- No Access - Contact Admin -->
                        <div class="alert alert-danger border-danger rounded-16 p-24 mb-20 text-center" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: 2px solid #dc3545;">
                            <div class="mb-20">
                                <i class="ph ph-lock text-danger" style="font-size: 64px;"></i>
                            </div>
                            <h4 class="text-dark mb-16 fw-bold">🚫 Akses Terbatas</h4>
                            <p class="text-dark mb-20 fs-16">
                                Anda belum memiliki akses ke kursus ini. Hubungi administrator untuk mendapatkan akses.
                            </p>
                            <div class="d-grid gap-2 d-md-block">
                                <button type="button" class="btn btn-outline-danger btn-lg rounded-pill py-12 px-24" disabled>
                                    <i class="ph ph-prohibition me-2"></i>
                                    Tidak Dapat Mengakses
                                </button>
                            </div>
                        </div>
                    @elseif(!$isEnrolled)
                        <!-- Has Access but Not Enrolled - Show Enroll Button -->
                        <div class="alert alert-warning border-warning rounded-16 p-24 mb-20 text-center" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 2px solid #f0ad4e;">
                            <div class="mb-20">
                                <i class="ph ph-lock-simple-open text-warning" style="font-size: 64px; color: #f39c12!important;"></i>
                            </div>
                            <h4 class="text-dark mb-16 fw-bold">🎓 Siap untuk Memulai?</h4>
                            <p class="text-dark mb-8 fs-16">
                                Anda memiliki akses ke kursus ini! Klik tombol di bawah untuk memulai pembelajaran.
                            </p>
                            <p class="text-muted mb-20 fs-14">
                                <strong>{{ $course->modules->count() }} modul</strong> •
                                <strong>{{ $course->modules->sum(fn($module) => $module->lessons->count()) }} pelajaran</strong> •
                                <strong>{{ $course->modules->whereNotNull('quiz')->count() }} quiz</strong>
                            </p>

                            <!-- Button yang sangat menonjol -->
                            <div class="d-grid gap-2 d-md-block">
                                <button type="button" class="btn btn-success btn-lg rounded-pill py-16 px-32 fw-bold"
                                        id="enrollBtn" onclick="confirmEnrollment()" data-action="enroll"
                                        style="font-size: 18px; box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4); text-transform: uppercase; letter-spacing: 1px;">
                                    <i class="ph ph-graduation-cap me-3" style="font-size: 24px;"></i>
                                    MULAI BELAJAR
                                    <i class="ph ph-arrow-right ms-3" style="font-size: 20px;"></i>
                                </button>
                            </div>

                            <p class="text-muted mt-16 mb-0 fs-14">
                                <i class="ph ph-shield-check me-1"></i>
                                Gratis • Akses seumur hidup • Sertifikat tersedia
                            </p>

                            <!-- Hidden form -->
                            <form action="{{ route('course.enroll', $course->id) }}" method="POST" class="d-none" id="enrollForm">
                                @csrf
                            </form>
                        </div>
                    @else
                        <!-- Actually Enrolled - Compact Success Indicator with Progress -->
                        @php
                            $enrolledProgressPercentage = $course->getCompletionPercentage(Auth::user());
                        @endphp

                        <div class="alert alert-success border-success rounded-12 p-16 mb-16" style="background: rgba(212, 237, 218, 0.7); border: 1px solid #28a745;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="ph ph-check-circle text-success me-2" style="font-size: 24px;"></i>
                                    <div>
                                        <span class="text-success fw-bold text-15 d-block">✅ Terdaftar - Siap belajar!</span>
                                        @if($enrolledProgressPercentage > 0)
                                            <span class="text-success text-13">Progress: {{ $enrolledProgressPercentage }}% selesai</span>
                                        @endif
                                    </div>
                                </div>
                                @if($enrolledProgressPercentage === 100)
                                    <span class="badge bg-warning text-dark py-6 px-12">
                                        <i class="ph ph-trophy me-1"></i>Selesai!
                                    </span>
                                @endif
                            </div>
                            @if($enrolledProgressPercentage > 0 && $enrolledProgressPercentage < 100)
                                <div class="progress mt-12" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $enrolledProgressPercentage }}%"></div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Certificate Section - Tampil jika sudah 100% -->
                    @if($isEnrolled && $enrolledProgressPercentage === 100)
                        @php
                            $certificate = Auth::user()->getCertificateForCourse($course->id);
                        @endphp

                        @if($certificate)
                            <div class="alert alert-info border-primary rounded-16 p-20 mb-20" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border: 2px solid #0ea5e9;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="ph ph-certificate text-primary" style="font-size: 48px;"></i>
                                        <div class="ms-16">
                                            <h5 class="text-dark mb-4 fw-bold">🎉 Sertifikat Tersedia!</h5>
                                            <p class="text-dark mb-8 text-14">
                                                Selamat! Anda telah menyelesaikan kursus ini dengan baik.
                                            </p>
                                            <p class="text-muted mb-0 text-13">
                                                <strong>No. Sertifikat:</strong> {{ $certificate->certificate_number }}<br>
                                                <strong>Tanggal Terbit:</strong> {{ $certificate->issued_date->format('d F Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-12 mt-16">
                                    <a href="{{ route('certificate.download', $certificate->id) }}"
                                       class="btn btn-primary btn-sm rounded-pill py-8 px-20"
                                       style="box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);">
                                        <i class="ph ph-download me-2"></i>Download Sertifikat
                                    </a>
                                    <a href="{{ route('certificate.preview', $certificate->id) }}"
                                       class="btn btn-outline-primary btn-sm rounded-pill py-8 px-20"
                                       target="_blank">
                                        <i class="ph ph-eye me-2"></i>Preview
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning border-warning rounded-12 p-16 mb-16">
                                <div class="d-flex align-items-center">
                                    <i class="ph ph-hourglass text-warning me-2" style="font-size: 24px;"></i>
                                    <span class="text-dark text-14">
                                        Sertifikat sedang diproses. Refresh halaman dalam beberapa saat.
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Course Thumbnail -->
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

                    <div class="mt-24">
                        <div class="mb-24 pb-24 border-bottom border-gray-100">
                            <h5 class="mb-12 fw-bold">Tentang Kursus Ini</h5>
                            <p class="text-gray-300 text-15">{{ $course->description ?? 'Deskripsi kursus belum tersedia.' }}</p>
                        </div>

                        @if($isEnrolled)
                        <div class="mb-24 pb-24 border-bottom border-gray-100">
                            <h5 class="mb-12 fw-bold">Progress Pembelajaran</h5>
                            @php
                                $user = Auth::user();

                                // Hitung total lessons dan quizzes
                                $totalLessons = $course->modules->sum(fn($module) => $module->lessons->count());
                                $totalQuizzes = $course->modules->whereNotNull('quiz')->count();
                                $totalItems = $totalLessons + $totalQuizzes;

                                // Hitung lessons yang sudah diselesaikan
                                $completedLessons = $user->completedLessons()
                                    ->whereIn('lesson_id', $course->modules->flatMap->lessons->pluck('id'))
                                    ->count();

                                // Hitung quiz yang sudah lulus
                                $passedQuizzes = 0;
                                $allQuizzes = $course->modules->map->quiz->filter();
                                foreach ($allQuizzes as $quiz) {
                                    if ($user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists()) {
                                        $passedQuizzes++;
                                    }
                                }

                                $completedItems = $completedLessons + $passedQuizzes;

                                // Gunakan method dari Course model untuk konsistensi
                                $progressPercentage = $course->getCompletionPercentage($user);
                            @endphp
                            <div class="mb-12">
                                <div class="d-flex justify-content-between mb-8">
                                    <span class="text-gray-600 text-15">
                                        {{ $completedItems }} dari {{ $totalItems }} materi selesai
                                        @if($completedItems > 0)
                                            <span class="text-success-600">({{ $completedLessons }} lesson, {{ $passedQuizzes }} quiz)</span>
                                        @endif
                                    </span>
                                    <span class="text-main-600 fw-medium">{{ $progressPercentage }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-main-600" role="progressbar" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>

                            @if($progressPercentage === 100)
                                <div class="alert alert-success p-12 rounded-8 mt-12">
                                    <i class="ph ph-check-circle me-2"></i>
                                    <span class="fw-medium">🎉 Selamat! Anda telah menyelesaikan kursus ini!</span>
                                </div>
                            @endif
                        </div>
                        @endif
                        <div class="mb-24 pb-24 border-bottom border-gray-100">
                            <h5 class="mb-16 fw-bold">Yang Termasuk dalam Kursus</h5>
                            <div class="row g-20">
                                <div class="col-md-6 col-sm-6">
                                    <ul>
                                        <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                            <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                            {{ $course->modules->count() }} Module Pembelajaran
                                        </li>
                                        <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                            <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                            {{ $course->modules->sum(fn($module) => $module->lessons->count()) }} Pelajaran
                                        </li>
                                        <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                            <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                            {{ $course->modules->whereNotNull('quiz')->count() }} Quiz
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <ul>
                                        <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                            <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                            Akses Seumur Hidup
                                        </li>
                                        <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                            <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                            Sertifikat Penyelesaian
                                        </li>
                                        <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                            <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                            Akses Mobile & Desktop
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <h5 class="mb-12 fw-bold">Pengajar</h5>
                            <div class="flex-align gap-8">
                                @if($course->author && $course->author->avatar)
                                    <img src="{{ Storage::url($course->author->avatar) }}" alt="{{ $course->author->name }}"
                                        class="w-44 h-44 rounded-circle object-fit-cover flex-shrink-0">
                                @else
                                    <div class="w-44 h-44 rounded-circle bg-main-50 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="ph ph-user text-main-600" style="font-size: 20px;"></i>
                                    </div>
                                @endif
                                <div class="d-flex flex-column">
                                    <h6 class="text-15 fw-bold mb-0">{{ $course->author->name ?? 'Tidak ada pengajar' }}</h6>
                                    <span class="text-13 text-gray-300">{{ $course->author->position ?? 'Instruktur' }}</span>
                                    @if($course->author)
                                        <div class="flex-align gap-4 mt-4">
                                            <span class="text-15 fw-bold text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                            <span class="text-13 fw-bold text-gray-600">5.0</span>
                                            <span class="text-13 fw-bold text-gray-300">({{ $course->enrolledUsers->count() }} siswa)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Course Card End -->
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-0">
                    <!-- Course Curriculum Header -->
                    <div class="p-16 bg-main-50">
                        <h5 class="mb-0 fw-bold text-main-600">
                            <i class="ph ph-list-bullets me-2"></i>Kurikulum Kursus
                        </h5>
                    </div>

                    @if(!$hasAccess)
                        <!-- No Access State -->
                        <div class="p-20 text-center" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border-radius: 12px;">
                            <i class="ph ph-lock text-danger" style="font-size: 56px;"></i>
                            <h6 class="text-dark mt-12 mb-8 fw-bold">🚫 Tidak Ada Akses</h6>
                            <p class="text-muted text-14 mb-16">Hubungi admin untuk mendapatkan akses ke kursus ini</p>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill py-8 px-16 text-13" disabled>
                                <i class="ph ph-prohibition me-1"></i>Akses Ditolak
                            </button>
                        </div>
                    @elseif(!$isEnrolled)
                        <!-- Has Access but Not Enrolled State -->
                        <div class="p-20 text-center" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-radius: 12px;">
                            <i class="ph ph-lock-simple-open text-warning" style="font-size: 56px;"></i>
                            <h6 class="text-dark mt-12 mb-8 fw-bold">� Siap Memulai?</h6>
                            <p class="text-muted text-14 mb-16">Klik tombol untuk mengakses <strong>{{ $course->modules->count() }} modul</strong> pembelajaran</p>

                            <button type="button" class="btn btn-success btn-sm rounded-pill py-8 px-16 text-13 fw-bold"
                                    onclick="confirmEnrollment()" data-action="enroll"
                                    style="box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);">
                                <i class="ph ph-graduation-cap me-1"></i>MULAI BELAJAR
                            </button>

                            <p class="text-muted mt-8 mb-0" style="font-size: 11px;">
                                <i class="ph ph-check me-1"></i>Akses Sudah Diberikan
                            </p>
                        </div>
                    @else
                        <!-- Enrolled State - Show Full Curriculum -->
                        @if($course->modules && $course->modules->count() > 0)
                            @foreach($course->modules as $index => $module)
                                <div class="course-item">
                                    <button type="button"
                                        class="course-item__button {{ $index === 0 ? 'active' : '' }} flex-align gap-4 w-100 p-16">
                                        <span class="d-block text-start">
                                            <span class="d-block h5 mb-0 text-line-1">{{ $module->title }}</span>
                                            <span class="d-block text-15 text-gray-300">
                                                {{ $module->lessons->count() }} pelajaran{{ $module->quiz ? ' + 1 quiz' : '' }}
                                            </span>
                                        </span>
                                        <span class="course-item__arrow ms-auto text-20 text-gray-500">
                                            <i class="ph ph-arrow-right"></i>
                                        </span>
                                    </button>
                                    <div class="course-item-dropdown {{ $index === 0 ? 'active' : '' }} ">
                                        <ul class="course-list p-16 pb-0 border-top border-gray-100">
                                            @foreach($module->lessons as $lessonIndex => $lesson)
                                                <li class="course-list__item flex-align gap-8 mb-16">
                                                    <span class="circle flex-shrink-0 text-32 d-flex text-success-600">
                                                        <i class="ph ph-play-circle"></i>
                                                    </span>
                                                    <div class="w-100">
                                                        <a href="{{ route('lesson.show', $lesson->id) }}" class="text-gray-700 fw-medium d-block hover-text-main-600 lesson-link">
                                                            {{ $lessonIndex + 1 }}. {{ $lesson->title }}
                                                            <span class="text-gray-500 fw-normal d-block">
                                                                <i class="ph ph-video me-1"></i>Pelajaran • {{ $lesson->duration_in_minutes ?? 5 }} menit
                                                            </span>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach

                                            @if($module->quiz)
                                                <li class="course-list__item flex-align gap-8 mb-16">
                                                    <span class="circle flex-shrink-0 text-32 d-flex text-warning-600">
                                                        <i class="ph ph-exam"></i>
                                                    </span>
                                                    <div class="w-100">
                                                        <a href="{{ route('quiz.show', $module->quiz->id) }}" class="text-gray-700 fw-medium d-block hover-text-warning-600 quiz-link">
                                                            Quiz: {{ $module->quiz->title }}
                                                            <span class="text-warning-600 fw-normal d-block">
                                                                <i class="ph ph-question me-1"></i>{{ $module->quiz->questions->count() }} Pertanyaan
                                                            </span>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- No Content Available -->
                            <div class="p-20 text-center">
                                <i class="ph ph-book-open text-gray-400" style="font-size: 48px;"></i>
                                <h6 class="text-gray-600 mt-12 mb-8">Belum Ada Konten</h6>
                                <p class="text-gray-500 text-14">Materi pembelajaran sedang dalam tahap persiapan</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card mt-24">
                <div class="card-body">
                    <h4 class="mb-20">Enrolled Courses</h4>
                    <button>Enroll this course</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Define enrollment functions globally
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

    // Fungsi untuk proses enrollment
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
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
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

    // Course Item Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const courseButtons = document.querySelectorAll('.course-item__button');

        courseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const dropdown = this.nextElementSibling;
                const arrow = this.querySelector('.course-item__arrow i');

                // Toggle active class
                this.classList.toggle('active');
                dropdown.classList.toggle('active');

                // Rotate arrow
                if (this.classList.contains('active')) {
                    arrow.style.transform = 'rotate(90deg)';
                } else {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        });

        // Handle enrollment form submission (legacy support)
        const enrollForms = document.querySelectorAll('form[action*="enroll"]');
        enrollForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const button = this.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;

                // Show confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi Pendaftaran',
                    text: 'Apakah Anda yakin ingin mendaftar di kursus ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Daftar!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        button.disabled = true;
                        button.innerHTML = '<i class="ph ph-spinner me-2"></i>Mendaftar...';

                        // Submit form
                        fetch(this.action, {
                            method: 'POST',
                            body: new FormData(this),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        }).then(response => {
                            if (response.ok) {
                                // Show success message and reload
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Anda berhasil mendaftar di kursus ini!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error('Network response was not ok');
                            }
                        }).catch(error => {
                            // Show error message and restore button
                            button.disabled = false;
                            button.innerHTML = originalText;

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        });
                    }
                });
            });
        });

        // Show flash messages from server
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





