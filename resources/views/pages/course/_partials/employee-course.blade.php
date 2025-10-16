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
        <!-- Left: Main Content -->
        <div class="col-lg-8">
            <!-- Course Card Start -->
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

                <!-- Enrollment Section: tampilkan hanya saat sudah enrolled (banner hijau). 
                    Untuk no-access & belum-enroll dipindahkan ke sidebar kanan agar tidak duplikat. -->
                    @if($isEnrolled)
                        <!-- Actually Enrolled - Compact Success Indicator with Progress -->
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

                    <!-- Certificate Section dipindahkan ke sidebar (kanan) -->

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
                        {{-- Ringkasan Kursus (teks singkat) --}}
                        <div class="mb-24 pb-24 border-bottom border-gray-100">
                            <h5 class="mb-12 fw-bold">Ringkasan Kursus</h5>
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

                        

                        {{-- Progress Pembelajaran --}}
                        @if($isEnrolled)
                        <div class="mt-24 mb-0 pb-0">
                            <h5 class="mb-12 fw-bold">Progress Pembelajaran</h5>
                            @php
                                $user = Auth::user();
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
                        </div>
                        @endif
                    </div>
                </div> <!-- /card-body -->
            </div> <!-- /card -->
        </div> <!-- /col-lg-8 -->

        <!-- Right: Sidebar -->
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

            {{-- Certificate card (only when 100% complete) --}}
            @if($isEnrolled && $rightProgress === 100)
                @php
                    $certificate = $user->getCertificateForCourse($course->id);
                @endphp
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
        </div> <!-- /col-lg-4 -->
    </div> <!-- /row -->
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





