

<div class="row gy-4" style="margin-top: 10px">
    <div class="col-md-8">
        <!-- Course Card Start -->
        <div class="card">
            <div class="card-body p-lg-20 p-sm-3">
                <div class="flex-between flex-wrap gap-12 mb-20">
                    <div>
                        <h3 class="mb-4">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-15">{{ $course->author->name ?? 'Administrator' }}</p>
                    </div>

                    <div class="flex-align flex-wrap gap-24">
                        <span class="py-6 px-16 bg-main-50 text-main-600 rounded-pill text-15">{{ $course->category->name ?? 'Umum' }}</span>
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
                        <button type="button"
                            class="bookmark-icon text-gray-200 text-26 d-flex hover-text-main-600">
                            <i class="ph ph-bookmarks"></i>
                        </button>
                    </div>
                </div>

                <div class="rounded-16 overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}"
                             alt="{{ $course->title }}" class="w-100" style="height: 300px; object-fit: cover;">
                    @else
                        <div class="w-100 bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <i class="ph ph-image text-muted" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                </div>

                <div class="mt-24">
                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                        <h5 class="mb-12 fw-bold">Tentang Kursus Ini</h5>
                        <p class="text-gray-300 text-15">{{ $course->description }}</p>
                    </div>
                    @if($course->objectives)
                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                        <h5 class="mb-12 fw-bold">Tujuan Pembelajaran</h5>
                        @foreach(explode("\n", $course->objectives) as $objective)
                            @if(trim($objective))
                                <p class="text-gray-300 text-15 mb-8">{{ trim($objective) }}</p>
                            @endif
                        @endforeach
                    </div>
                    @endif
                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                        <h5 class="mb-16 fw-bold">Kursus Ini Mencakup</h5>
                        <div class="row g-20">
                            <div class="col-md-6 col-sm-6">
                                <ul>
                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i
                                                class="ph ph-checks"></i> </span>
                                        {{ $course->modules->count() }} Modul Pembelajaran
                                    </li>
                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i
                                                class="ph ph-checks"></i> </span>
                                        {{ $course->modules->sum(function($module) { return $module->lessons->count(); }) }} Pelajaran
                                    </li>
                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i
                                                class="ph ph-checks"></i> </span>
                                        {{ $course->modules->where('quiz')->count() }} Quiz Interaktif
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <ul>
                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i
                                                class="ph ph-checks"></i> </span>
                                        {{ $course->modules->sum(function($module) { return $module->quiz ? $module->quiz->questions->count() : 0; }) }} Pertanyaan Quiz
                                    </li>
                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i
                                                class="ph ph-checks"></i> </span>
                                        {{ $course->enrolledUsers->count() }} Peserta Aktif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <h5 class="mb-12 fw-bold">Instruktur</h5>
                        <div class="flex-align gap-8">
                            <div class="w-44 h-44 rounded-circle bg-main-600 text-white d-flex align-items-center justify-content-center flex-shrink-0">
                                {{ strtoupper(substr($course->author->name ?? 'Admin', 0, 2)) }}
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="text-15 fw-bold mb-0">{{ $course->author->name ?? 'Administrator' }}</h6>
                                <span class="text-13 text-gray-300">{{ $course->author->bio ?? 'Instruktur Kursus' }}</span>
                                <div class="flex-align gap-4 mt-4">
                                    <span class="text-15 fw-bold text-warning-600 d-flex"><i
                                            class="ph-fill ph-star"></i></span>
                                    <span class="text-13 fw-bold text-gray-600">4.8</span>
                                    <span class="text-13 fw-bold text-gray-300">({{ $course->enrolledUsers->count() }})</span>
                                </div>
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
                @if($course->modules->count() > 0)
                    @foreach($course->modules as $index => $module)
                        <div class="course-item">
                            <button type="button"
                                class="course-item__button {{ $index === 0 ? 'active' : '' }} flex-align gap-4 w-100 p-16 border-bottom border-gray-100">
                                <span class="d-block text-start">
                                    <span class="d-block h5 mb-0 text-line-1">{{ $module->title }}</span>
                                    <span class="d-block text-15 text-gray-300">0 / {{ $module->lessons->count() }} | {{ $module->lessons->count() * 15 }} min</span>
                                </span>
                                <span class="course-item__arrow ms-auto text-20 text-gray-500"><i
                                        class="ph ph-arrow-right"></i></span>
                            </button>
                            <div class="course-item-dropdown {{ $index === 0 ? 'active' : '' }} border-bottom border-gray-100">
                                <ul class="course-list p-16 pb-0">
                                    @foreach($module->lessons as $lessonIndex => $lesson)
                                        <li class="course-list__item flex-align gap-8 mb-16 {{ $index === 0 && $lessonIndex < 2 ? 'active' : '' }}">
                                            <span class="circle flex-shrink-0 text-32 d-flex text-gray-100"><i
                                                    class="ph ph-circle"></i></span>
                                            <div class="w-100">
                                                <a href="{{ route('lesson.show', $lesson->id) }}"
                                                    class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                    {{ $lessonIndex + 1 }}. {{ $lesson->title }}
                                                    <span class="text-gray-300 fw-normal d-block">{{ $lesson->duration ?? '15 min' }}</span>
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                    @if($module->quiz)
                                        <li class="course-list__item flex-align gap-8 mb-16">
                                            <span class="circle flex-shrink-0 text-32 d-flex text-warning-600"><i
                                                    class="ph ph-question"></i></span>
                                            <div class="w-100">
                                                <a href="#"
                                                    class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                    Quiz: {{ $module->quiz->title }}
                                                    <span class="text-gray-300 fw-normal d-block">{{ $module->quiz->questions->count() }} pertanyaan • {{ $module->quiz->duration_in_minutes }} min</span>
                                                </a>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-20 text-center">
                        <i class="ph ph-book text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">Belum ada modul</h5>
                        <p class="text-muted">Modul akan ditampilkan di sini setelah dibuat.</p>
                    </div>
                @endif


            </div>
        </div>

        <div class="card mt-24">
            <div class="card-body">
                <h4 class="mb-20">Statistik Kursus</h4>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-main-50 p-12 rounded-8 text-center">
                            <h3 class="text-main-600 fw-bold mb-1">{{ $course->enrolledUsers->count() }}</h3>
                            <p class="text-13 text-gray-300 mb-0">Peserta</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-success-50 p-12 rounded-8 text-center">
                            <h3 class="text-success-600 fw-bold mb-1">{{ $course->modules->count() }}</h3>
                            <p class="text-13 text-gray-300 mb-0">Modul</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-warning-50 p-12 rounded-8 text-center">
                            <h3 class="text-warning-600 fw-bold mb-1">{{ $course->modules->sum(function($module) { return $module->lessons->count(); }) }}</h3>
                            <p class="text-13 text-gray-300 mb-0">Pelajaran</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-info-50 p-12 rounded-8 text-center">
                            <h3 class="text-info-600 fw-bold mb-1">{{ $course->modules->where('quiz')->count() }}</h3>
                            <p class="text-13 text-gray-300 mb-0">Quiz</p>
                        </div>
                    </div>
                </div>
                <div class="mt-20">
                    <h6 class="mb-12">Informasi Kursus</h6>
                    <div class="d-flex justify-content-between mb-8">
                        <span class="text-13 text-gray-300">Dibuat:</span>
                        <span class="text-13 fw-medium">{{ $course->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-8">
                        <span class="text-13 text-gray-300">Kategori:</span>
                        <span class="text-13 fw-medium">{{ $course->category->name ?? 'Umum' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-16">
                        <span class="text-13 text-gray-300">Status:</span>
                        @php
                            $isPublished = $course->status === 'published';
                        @endphp
                        <span class="badge {{ $isPublished ? 'bg-success' : 'bg-warning' }}">
                            {{ $isPublished ? 'Published' : 'Draft' }}
                        </span>
                    </div>

                    <!-- Publish Course Button -->
                    <div class="mt-16">
                        @if(!$isPublished)
                            <button type="button" class="btn btn-main rounded-pill py-8 w-100" id="publishCourseBtn"
                                    data-course-id="{{ $course->id }}">
                                <i class="ph ph-rocket-launch me-1"></i> Publish Course
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-secondary rounded-pill py-8 w-100" id="unpublishCourseBtn"
                                    data-course-id="{{ $course->id }}">
                                <i class="ph ph-archive me-1"></i> Unpublish Course
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Publish Course Button
    const publishBtn = document.getElementById('publishCourseBtn');
    const unpublishBtn = document.getElementById('unpublishCourseBtn');

    if (publishBtn) {
        publishBtn.addEventListener('click', function() {
            const courseId = this.dataset.courseId;

            Swal.fire({
                title: 'Publish Course?',
                text: 'Apakah Anda yakin ingin mempublish kursus ini? Kursus akan tersedia untuk semua peserta.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Publish!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    publishCourse(courseId, true);
                }
            });
        });
    }

    if (unpublishBtn) {
        unpublishBtn.addEventListener('click', function() {
            const courseId = this.dataset.courseId;

            Swal.fire({
                title: 'Unpublish Course?',
                text: 'Apakah Anda yakin ingin meng-unpublish kursus ini? Kursus akan tidak tersedia untuk peserta baru.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Unpublish!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    publishCourse(courseId, false);
                }
            });
        });
    }

    function publishCourse(courseId, publish) {
        const button = publish ? publishBtn : unpublishBtn;
        const originalHtml = button.innerHTML;

        // Show loading state
        button.disabled = true;
        button.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> ' +
                          (publish ? 'Publishing...' : 'Unpublishing...');

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('action', 'toggle_publish');
        formData.append('is_published', publish ? '1' : '0');

        fetch(`/course/publish/${courseId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || (publish ? 'Kursus berhasil dipublish.' : 'Kursus berhasil di-unpublish.'),
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Reload page to update UI
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal mengubah status kursus. Silakan coba lagi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
        })
        .finally(() => {
            // Restore button state
            button.disabled = false;
            button.innerHTML = originalHtml;
        });
    }
});
</script>
