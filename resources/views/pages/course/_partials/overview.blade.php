{{-- No top-level @php variables to avoid scope issues with Livewire/Blade caching --}}
<style>
/* Force a clearly disabled look for non-owner overview items */
.course-list .disabled-link { color: #9AA0A6 !important; cursor: not-allowed; text-decoration: none !important; }
.course-list .disabled-link span { color: inherit !important; }
.course-list .disabled-icon { color: #9AA0A6 !important; }
</style>
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
                    <img src="{{ $course->getThumbnailUrl() }}"
                         alt="{{ $course->title }}" class="w-100" style="height: 300px; object-fit: cover;">
                </div>

                <div class="mt-24">
                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                        <h5 class="mb-12 fw-bold">Ringkasan Kursus</h5>
                        <p class="text-gray-300 text-15">{{ $course->summary ?? '-' }}</p>
                    </div>
                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                        <h5 class="mb-12 fw-bold">Deskripsi Kursus</h5>
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
                    <div class="">
                        <h5 class="mb-12 fw-bold">Instruktur</h5>
                        <div class="flex-align gap-12">
                            @if($course->author?->profile_photo_url)
                                <img src="{{ $course->author->profile_photo_url }}" alt="{{ $course->author->name }}" class="w-48 h-48 rounded-circle object-fit-cover flex-shrink-0"/>
                            @else
                                <div class="w-48 h-48 rounded-circle bg-main-600 text-white d-flex align-items-center justify-content-center flex-shrink-0">
                                    {{ strtoupper(substr($course->author->name ?? 'Admin', 0, 2)) }}
                                </div>
                            @endif
                            <div class="d-flex flex-column">
                                <h6 class="text-15 fw-bold mb-1">{{ $course->author->name ?? 'Administrator' }}</h6>
                                <span class="text-13 text-gray-300">{{ $course->author->position ?? 'Instruktur' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Course Card End -->
    </div>

    <div class="col-md-4">
        @php($canManage = auth()->check() && (auth()->id() === $course->user_id || auth()->user()->hasRole('admin')))
        <div class="card">
            <div class="card-body p-0">
                @if($course->modules->count() > 0)
                    @foreach($course->modules as $index => $module)
                        <div class="course-item border-top border-gray-100 ">
                            <button type="button"
                                class="course-item__button flex-align gap-4 w-100 p-16">
                                <span class="d-block text-start">
                                    <span class="d-block h5 mb-0 text-line-1">{{ $module->title }}</span>
                                    <span class="d-block text-15 text-gray-300">0 / {{ $module->lessons->count() }} | {{ $module->lessons->count() * 15 }} min</span>
                                </span>
                                <span class="course-item__arrow ms-auto text-20 text-gray-500"><i
                                        class="ph ph-arrow-right"></i></span>
                            </button>
                            <div class="course-item-dropdown border-top border-gray-100">
                                <ul class="course-list p-16 pb-0">
                                    @foreach($module->lessons as $lessonIndex => $lesson)
                                        <li class="course-list__item flex-align gap-8 mb-16">
                                            <span class="circle flex-shrink-0 text-32 d-flex {{ $canManage ? 'text-gray-100' : 'disabled-icon' }}">
                                                <i class="ph {{ $canManage ? 'ph-circle' : 'ph-lock-simple' }}"></i>
                                            </span>
                                            <div class="w-100">
                                                @if($canManage)
                                                    <a href="{{ route('lesson.show', $lesson->id) }}"
                                                       class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                        {{ $lessonIndex + 1 }}. {{ $lesson->title }}
                                                        <span class="text-gray-300 fw-normal d-block">{{ $lesson->duration ?? '15 min' }}</span>
                                                    </a>
                                                @else
                                                    <div class="text-muted fw-medium d-block d-lg-block disabled-link" title="Konten terkunci">
                                                        {{ $lessonIndex + 1 }}. {{ $lesson->title }}
                                                        <span class="fw-normal d-block">{{ $lesson->duration ?? '15 min' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                    @if($module->quiz)
                                        <li class="course-list__item flex-align gap-8 mb-16">
                                            <span class="circle flex-shrink-0 text-32 d-flex {{ $canManage ? 'text-warning-600' : 'disabled-icon' }}">
                                                <i class="ph {{ $canManage ? 'ph-question' : 'ph-lock-simple' }}"></i>
                                            </span>
                                            <div class="w-100">
                                                @if($canManage)
                                                    <a href="{{ route('quiz.show', $module->quiz->id) }}"
                                                       class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                        Quiz: {{ $module->quiz->title }}
                                                        <span class="text-gray-300 fw-normal d-block">{{ $module->quiz->questions->count() }} pertanyaan • {{ $module->quiz->duration_in_minutes }} min</span>
                                                    </a>
                                                @else
                                                    <div class="text-muted fw-medium d-block d-lg-block disabled-link" title="Konten terkunci">
                                                        Quiz: {{ $module->quiz->title }}
                                                        <span class="fw-normal d-block">{{ $module->quiz->questions->count() }} pertanyaan • {{ $module->quiz->duration_in_minutes }} min</span>
                                                    </div>
                                                @endif
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
                            <h3 class="text-main-600 fw-bold mb-1">{{ $course->modules->filter(function($m){ return !empty($m->quiz); })->count() }}</h3>
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
                        <span class="badge {{ $course->status === 'published' ? 'bg-success' : 'bg-warning' }}">
                            {{ $course->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </div>

                    <!-- Action Buttons (owner/admin only) -->
                    @if($canManage)
                        <div class="mt-16 d-grid gap-2">
                            <button type="button" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-8 w-100" id="overviewSaveDraftBtn"
                                    data-course-id="{{ $course->id }}">
                                <i class="ph ph-file-arrow-down me-1"></i> Save as Draft
                            </button>
                            <button type="button" class="btn btn-main rounded-pill py-8 w-100" id="overviewPublishBtn"
                                    data-course-id="{{ $course->id }}" data-status="{{ $course->status }}">
                                <i class="ph ph-rocket-launch me-1"></i> {{ $course->status === 'published' ? 'Update Course' : 'Publish Course' }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buttons in Overview card
    const btnPublish = document.getElementById('overviewPublishBtn');
    const btnDraft = document.getElementById('overviewSaveDraftBtn');

    if (btnPublish) {
        btnPublish.addEventListener('click', function() {
            const courseId = this.dataset.courseId;
            const isPublished = (this.dataset.status === 'published');

            Swal.fire({
                title: isPublished ? 'Update Course?' : 'Publish Course?',
                text: isPublished ? 'Perbarui status dan informasi kursus?' : 'Apakah Anda yakin ingin mempublish kursus ini? Kursus akan tersedia untuk semua peserta.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: isPublished ? 'Ya, Update' : 'Ya, Publish',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitPublishToggle(courseId, true, btnPublish);
                }
            });
        });
    }

    if (btnDraft) {
        btnDraft.addEventListener('click', function() {
            const courseId = this.dataset.courseId;

            Swal.fire({
                title: 'Simpan sebagai Draft?',
                text: 'Kursus akan tidak tersedia untuk peserta baru sampai dipublish kembali.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan Draft',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitPublishToggle(courseId, false, btnDraft);
                }
            });
        });
    }

    function submitPublishToggle(courseId, publish, button){
        if(!button) return;
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> ' + (publish ? 'Publishing...' : 'Saving...');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('action', 'toggle_publish');
        formData.append('is_published', publish ? '1' : '0');

        fetch(`/course/publish/${courseId}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => { if(!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || (publish ? 'Kursus berhasil dipublish.' : 'Kursus kembali ke Draft.'),
                showConfirmButton: false,
                timer: 1500
            }).then(() => window.location.reload());
        })
        .catch(err => {
            console.error(err);
            Swal.fire({ icon:'error', title:'Terjadi Kesalahan', text:'Gagal mengubah status kursus. Silakan coba lagi.' });
        })
        .finally(() => { button.disabled = false; button.innerHTML = originalHtml; });
    }
});
</script>
