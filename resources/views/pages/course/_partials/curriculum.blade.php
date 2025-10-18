@push('css')
    <style>
        .module-edit-mode .form-control {
            font-size: 1.25rem;
            font-weight: bold;
            border: 2px solid #3b82f6;
        }

        .module-edit-buttons .btn {
            height: 38px;
            min-width: 38px;
        }

        .module-edit-btn:hover,
        .module-delete-btn:hover {
            text-decoration: underline;
        }

        .module-display-mode,
        .module-edit-mode {
            transition: all 0.3s ease;
        }
    </style>
@endpush

<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card mt-24">
            <div class="card-body">
                <h4 class="mb-12">Susun Kurikulum</h4>
                {{-- add Modules --}}
                <div class="card-section mt-24 p-3">
                    <div class="card-body">
                        <form id="createModuleForm">
                            @csrf
                            <small> Judul Modul Baru</small>
                            <div class="input-group mt-2">
                                <input type="text" name="title" class="form-control" placeholder="Judul Modul Baru">
                                <button class="btn btn-primary" type="submit">Tambah Modul</button>
                            </div>
                        </form>
                    </div>
                </div>


                {{-- Modules List --}}
                <div class=" mt-24 p-3">
                    <div class="">
                        <h5 class="mb-20">Modul List</h5>
                        <div class="row g-3" id="moduleContainer">
                            @forelse($course->modules as $index => $module)
                                <!-- Module Card {{ $index + 1 }} -->
                                <div class="col-12">
                                    <div class="card card-section mt-24">
                                        <div class="card-body">
                                            <div class="pb-24 flex-between gap-4 flex-wrap">
                                                <!-- Module Title Display/Edit -->
                                                <div class="flex-grow-1">
                                                    <div class="module-display-mode"
                                                        data-module-id="{{ $module->id }}">
                                                        <h5 class="mb-12 fw-bold module-title-display"
                                                            data-module-id="{{ $module->id }}">{{ $module->title }}
                                                        </h5>
                                                    </div>
                                                    <div class="module-edit-mode" data-module-id="{{ $module->id }}"
                                                        style="display: none;">
                                                        <form id="module-edit-form-{{ $module->id }}"
                                                            class="module-edit-form d-flex align-items-center gap-2"
                                                            data-module-id="{{ $module->id }}">
                                                            @csrf
                                                            <input type="text"
                                                                class="form-control module-title-input"
                                                                value="{{ $module->title }}" required>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- Display Mode Buttons -->
                                                <div class="module-display-buttons flex-align gap-8"
                                                    data-module-id="{{ $module->id }}">
                                                    <a href="#" class="module-edit-btn"
                                                        data-module-id="{{ $module->id }}"
                                                        style="color: #3b82f6">Edit</a>
                                                    <a href="#" class="module-delete-btn"
                                                        data-module-id="{{ $module->id }}"
                                                        style="color: #ef4444">Hapus</a>
                                                </div>
                                                <!-- Edit Mode Buttons -->
                                                <div class="module-edit-buttons flex-align gap-8"
                                                    data-module-id="{{ $module->id }}" style="display: none;">
                                                    <button type="submit" form="module-edit-form-{{ $module->id }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ph ph-check"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger module-edit-cancel"
                                                        data-module-id="{{ $module->id }}">
                                                        <i class="ph ph-x"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <ul class="comment-list">
                                                <li>
                                                    <div class="d-flex align-items-start gap-8 flex-xs-row flex-column">
                                                        <div class="w-100">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <h6 class="text-15 fw-bold mb-0">Pelajaran</h6>
                                                                <button
                                                                    class="text-15 fw-bold p-0 mb-0 border-0 bg-transparent"
                                                                    style="color: #3b82f6; text-decoration: none;"
                                                                    data-bs-toggle="modal" data-bs-target="#lessonModal"
                                                                    data-module-id="{{ $module->id }}">Tambah
                                                                    Pelajaran</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <ul class="mt-10">
                                                        @forelse($module->lessons as $lesson)
                                                            <li>
                                                                <div
                                                                    class="d-flex align-items-start gap-8 flex-xs-row flex-column">
                                                                    <div
                                                                        class="w-100 bg-white rounded shadow-sm border py-5 px-5 mb-4">
                                                                        <div class="d-flex justify-content-between align-items-center"
                                                                            style="padding: 10px">
                                                                            <div class="flex-align flex-wrap gap-8">
                                                                                <h6 class="text-15 fw-bold mb-0">
                                                                                    {{ $lesson->title }}</h6>
                                                                                <span class="py-0 px-8 bg-main-50 text-main-600 rounded-4 text-15 fw-medium h5 mb-0 fw-bold">{{ $lesson->duration_in_minutes }}
                                                                                    menit</span>
                                                                            </div>
                                                                            <div class="flex-align gap-8">
                                                                                <a href="#"
                                                                                    class="edit-lesson-btn"
                                                                                    style="color: #3b82f6"
                                                                                    data-lesson-id="{{ $lesson->id }}"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#lessonModal">Edit</a>
                                                                                <a href="#"
                                                                                    class="delete-lesson-btn"
                                                                                    style="color: #ef4444"
                                                                                    data-lesson-id="{{ $lesson->id }}">Hapus</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @empty
                                                            <li>
                                                                <div
                                                                    class="d-flex align-items-start gap-8 flex-xs-row flex-column">
                                                                    <div
                                                                        class="w-100 bg-light rounded border py-3 px-4 mb-3">
                                                                        <p class="text-center text-muted mb-0">Belum ada
                                                                            pelajaran dalam modul ini</p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforelse
                                                    </ul>
                                                </li>
                                            </ul>

                                            @if ($module->quiz)
                                                <!-- Quiz Section -->
                                                <div class="mt-24 pt-24 border-top">
                                                    <div class="d-flex justify-content-between align-items-center mb-3" style="padding: 10px">
                                                        <h6 class="text-15 fw-bold mb-0">Kuis</h6>
                                                        <button class="text-15 fw-bold p-0 mb-0 border-0 bg-transparent"
                                                            style="color: #3b82f6; text-decoration: none;"
                                                            data-bs-toggle="modal" data-bs-target="#quizModal"
                                                            data-quiz-id="{{ $module->quiz->id }}">Edit</button>
                                                    </div>
                                                    <div class="bg-white rounded shadow-sm border py-4 px-4">
                                                        <div class="d-flex justify-content-between align-items-center" style="padding: 10px">
                                                            <div class="flex-align flex-wrap gap-8">
                                                                <h6 class="text-15 fw-bold mb-0">
                                                                    {{ $module->quiz->title }}</h6>
                                                                <span class="py-0 px-8 bg-main-50 text-main-600 rounded-4 text-15 fw-medium h5 mb-0 fw-bold">{{ $module->quiz->duration_in_minutes }}
                                                                    menit</span>
                                                                <span class="py-0 px-8 bg-success text-white rounded-4 text-15 fw-medium h5 mb-0 fw-bold">Passing:
                                                                    {{ $module->quiz->passing_score }}%</span>
                                                            </div>
                                                            <div class="flex-align gap-8">
                                                                <a href="{{ route('quiz.manage', $module->quiz->id) }}"
                                                                    class="manage-quiz-btn" style="color: #28a745">Kelola</a>
                                                                <a href="#" class="edit-quiz-btn" style="color: #3b82f6"
                                                                    data-quiz-id="{{ $module->quiz->id }}"
                                                                    data-bs-toggle="modal" data-bs-target="#quizModal">Edit</a>
                                                                <a href="#" class="delete-quiz-btn" style="color: #ef4444"
                                                                    data-quiz-id="{{ $module->quiz->id }}">Hapus</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Add Quiz Button -->
                                                <div class="mt-24 pt-24 border-top">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="text-15 fw-bold mb-0">Kuis</h6>
                                                        <button
                                                            class="text-15 fw-bold p-0 mb-0 border-0 bg-transparent"
                                                            style="color: #3b82f6; text-decoration: none;"
                                                            data-bs-toggle="modal" data-bs-target="#quizModal"
                                                            data-module-id="{{ $module->id }}">Tambah Kuis</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="card card-section mt-24">
                                        <div class="card-body text-center py-5">
                                            <p class="text-muted mb-0">Belum ada modul dalam kursus ini. Silakan tambah
                                                modul baru.</p>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex-align justify-content-end gap-8 mt-16">
                    <button type="button" class="btn btn-outline-main rounded-pill py-9" id="btnBackToDetails">Kembali</button>
                    <button type="button" class="btn btn-main rounded-pill py-9" id="btnContinueParticipants">Simpan & Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lesson (Tambah/Edit) -->
<div class="modal fade" id="lessonModal" tabindex="-1" aria-labelledby="lessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lessonModalLabel">Tambah Pelajaran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="lessonForm">
                    @csrf
                    <input type="hidden" id="lessonMethod" name="_method" value="POST">
                    <input type="hidden" id="lessonId" name="lesson_id" value="">

                    <div class="mb-3">
                        <label for="lessonTitle" class="form-label">Judul Pelajaran</label>
                        <input type="text" class="form-control" id="lessonTitle" name="title"
                            placeholder="Masukkan judul pelajaran" required>
                    </div>
                    <div class="mb-3">
                        <label for="lessonSummary" class="form-label">Ringkasan</label>
                        <textarea class="form-control" id="lessonSummary" name="summary" rows="3"
                            placeholder="Ringkasan pelajaran (opsional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="lessonDuration" class="form-label">Durasi (menit)</label>
                        <input type="number" class="form-control" id="lessonDuration" name="duration_in_minutes"
                            placeholder="Durasi dalam menit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="lessonContent" class="form-label">Tipe Konten</label>
                        <select class="form-select" id="lessonContent" name="content_type" required>
                            <option value="">Pilih Tipe Konten</option>
                            <option value="video">Video</option>
                            <option value="file">File Lampiran</option>
                            <option value="text">Teks/Artikel</option>
                        </select>
                    </div>

                    <!-- Dynamic Content Input -->
                    <div class="mb-3" id="dynamicContentInput" style="display: none;">
                        <label id="dynamicLabel" class="form-label">Konten</label>
                        <div id="contentInputContainer">
                            <!-- Dynamic input will be inserted here -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="lessonForm" class="btn btn-primary" id="lessonSubmitBtn">Tambah
                    Pelajaran</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quiz (Tambah/Edit) -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quizModalLabel">Tambah Kuis Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quizForm">
                    @csrf
                    <input type="hidden" id="quizMethod" name="_method" value="POST">
                    <input type="hidden" id="quizId" name="quiz_id" value="">
                    <input type="hidden" id="quizModuleId" name="module_id" value="">

                    <div class="mb-3">
                        <label for="quizTitle" class="form-label">Judul Kuis</label>
                        <input type="text" class="form-control" id="quizTitle" name="title"
                            placeholder="Masukkan judul kuis" required>
                    </div>
                    <div class="mb-3" style="margin-top: 10px">
                        <label for="quizDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="quizDescription" name="description" rows="3"
                            placeholder="Deskripsi kuis (opsional)"></textarea>
                    </div>
                    <div class="row" style="margin-top: 10px">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quizDuration" class="form-label">Durasi Pengerjaan (menit)</label>
                                <input type="number" class="form-control" id="quizDuration" name="duration_in_minutes"
                                    placeholder="Durasi dalam menit" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quizPassingScore" class="form-label">Nilai Minimum Lulus (Contoh 80)</label>
                                <input type="number" class="form-control" id="quizPassingScore" name="passing_score"
                                    placeholder="Contoh: 75" min="1" max="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="quizMaxAttempts" class="form-label">Maksimal Percobaan (isi 0 jika tidak terbatas)</label>
                                <input type="number" class="form-control" id="quizMaxAttempts" name="max_attempts"
                                    placeholder="Contoh: 3" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="quizForm" class="btn btn-primary" id="quizSubmitBtn">Tambah Kuis</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Only run "activate kurikulum" logic when the tab is explicitly requested
        (function ready(fn){
            if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', fn); }
            else { fn(); }
        })(function(){
            const requestedByHash = (window.location.hash === '#kurikulum');
            // Only respect session flag when user is reloading the same course page (post-save)
            const samePageReferrer = document.referrer && new URL(document.referrer, window.location.origin).pathname === window.location.pathname;
            const requestedBySession = samePageReferrer && (sessionStorage.getItem('activeTab') === 'kurikulum');
            const pane = document.getElementById('kurikulum');
            const isActive = pane && pane.classList.contains('show') && pane.classList.contains('active');

            // If user arrived with #kurikulum or the pane is already active, sync the wizard + hash
            if (requestedByHash || requestedBySession || isActive) {
                window.setCourseWizardStep && window.setCourseWizardStep('module');
                try { history.replaceState(null, '', '#kurikulum'); } catch(e) {}
            }

            const btnNext = document.getElementById('btnContinueParticipants');
            const btnBack = document.getElementById('btnBackToDetails');
            if(btnNext){
                btnNext.addEventListener('click', function(){
                    console.debug('[wizard] next -> participants');
                    // 1) Broadcast event for host page
                    window.dispatchEvent(new CustomEvent('course:navigate', { detail: { to: 'participants' } }));
                    // 2) Direct helper (accepts step key or pane id)
                    if (window.activateCoursePane) {
                        window.activateCoursePane('participants');
                    } else {
                        // 3) Hard fallback: switch classes manually
                        try {
                            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show','active'));
                            const targetPane = document.getElementById('users');
                            if (targetPane) {
                                targetPane.classList.add('show','active');
                                window.setCourseWizardStep && window.setCourseWizardStep('participants');
                                history.replaceState(null, '', '#users');
                                document.querySelector('.tab-content')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        } catch(e) { console.warn('Pane switch fallback (next) failed', e); }
                    }
                });
            }
            if(btnBack){
                btnBack.addEventListener('click', function(){
                    console.debug('[wizard] back -> details');
                    if (window.setCourseWizardStep) { window.setCourseWizardStep('details'); }
                    window.dispatchEvent(new CustomEvent('course:navigate', { detail: { to: 'details' } }));
                    if(window.activateCoursePane) {
                        window.activateCoursePane('details');
                    } else {
                        // Hard fallback: switch to informasi-umum
                        try {
                            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show','active'));
                            const targetPane = document.getElementById('informasi-umum');
                            if (targetPane) {
                                targetPane.classList.add('show','active');
                                window.setCourseWizardStep && window.setCourseWizardStep('details');
                                history.replaceState(null, '', '#informasi-umum');
                                document.querySelector('.tab-content')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        } catch(e) { console.warn('Pane switch fallback (back) failed', e); }
                    }
                });
            }
        });

        $('#createModuleForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append('course_id', '{{ $course->id }}');

            $.ajax({
                url: "{{ route('module.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data Module berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        sessionStorage.setItem('activeTab', 'kurikulum');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.log(xhr)
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';

                        $.each(errors, function(key, value) {
                            errorMessages += `• ${value[0]}<br>`;
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: errorMessages,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Silakan coba lagi nanti.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    }

                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentTypeSelect = document.getElementById('lessonContent');
            const dynamicContentInput = document.getElementById('dynamicContentInput');
            const dynamicLabel = document.getElementById('dynamicLabel');
            const contentInputContainer = document.getElementById('contentInputContainer');

            contentTypeSelect.addEventListener('change', function() {
                const selectedType = this.value;
                // Use helper function - will clear data since no data passed and isEditMode handles it
                generateDynamicContent(selectedType);
            });



            document.getElementById('lessonModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('lessonForm').reset();
                dynamicContentInput.style.display = 'none';
                contentInputContainer.innerHTML = '';
                selectedModuleId = null;
                selectedLessonId = null;
                isEditMode = false;
            });
        });

        let selectedModuleId = null;
        let selectedLessonId = null;

        let isEditMode = false;

        // Helper function to generate dynamic content for lesson form
        function generateDynamicContent(contentType, data = {}) {
            const dynamicContentInput = document.getElementById('dynamicContentInput');
            const dynamicLabel = document.getElementById('dynamicLabel');
            const contentInputContainer = document.getElementById('contentInputContainer');

            if (!contentType) {
                dynamicContentInput.style.display = 'none';
                return;
            }

            dynamicContentInput.style.display = 'block';

            switch (contentType) {
                case 'video':
                    dynamicLabel.textContent = 'URL Video';
                    const videoValue = isEditMode && data.video_url ? data.video_url : '';
                    contentInputContainer.innerHTML = `
                        <input type="url" class="form-control" name="video_url" placeholder="Masukkan URL video (YouTube, Vimeo, dll)" value="${videoValue}" required>
                        <small class="form-text text-muted">Contoh: https://www.youtube.com/watch?v=xxxxx</small>
                    `;
                    break;

                case 'file':
                    dynamicLabel.textContent = 'Upload Dokumen';
                    let currentFileInfo = isEditMode && data.attachment_path ?
                        `<small class="form-text text-info d-block mt-2">File saat ini: ${data.attachment_path.split('/').pop()}</small>` :
                        '';
                    let fileRequired = isEditMode ? '' : 'required';
                    let fileHelpText = isEditMode ?
                        'Format yang didukung: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX (Max: 10MB). Kosongkan jika tidak ingin mengubah file.' :
                        'Format yang didukung: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX (Max: 10MB)';

                    contentInputContainer.innerHTML = `
                        <input type="file" class="form-control" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" ${fileRequired}>
                        <small class="form-text text-muted">${fileHelpText}</small>
                        ${currentFileInfo}
                    `;
                    break;

                case 'text':
                    dynamicLabel.textContent = 'Konten Artikel/Teks';
                    const textValue = isEditMode && data.content_text ? data.content_text : '';
                    contentInputContainer.innerHTML = `
                        <textarea class="form-control" name="content_text" rows="8" placeholder="Tulis konten artikel atau teks pelajaran di sini..." required>${textValue}</textarea>
                    `;
                    break;

                default:
                    dynamicContentInput.style.display = 'none';
                    break;
            }
        }

        // Function to setup modal for add mode
        function setupAddLessonModal(moduleId) {
            isEditMode = false;
            selectedModuleId = moduleId;
            selectedLessonId = null;

            // Reset form
            document.getElementById('lessonForm').reset();
            document.getElementById('lessonMethod').value = 'POST';
            document.getElementById('lessonId').value = '';

            // Update modal title and button
            document.getElementById('lessonModalLabel').textContent = 'Tambah Pelajaran Baru';
            document.getElementById('lessonSubmitBtn').textContent = 'Tambah Pelajaran';
            document.getElementById('lessonSubmitBtn').className = 'btn btn-primary';

            // Hide dynamic content
            document.getElementById('dynamicContentInput').style.display = 'none';
            document.getElementById('contentInputContainer').innerHTML = '';
        }

        // Function to setup modal for edit mode
        function setupEditLessonModal(lessonId) {
            isEditMode = true;
            selectedLessonId = lessonId;
            selectedModuleId = null;

            // Update modal title and button
            document.getElementById('lessonModalLabel').textContent = 'Edit Pelajaran';
            document.getElementById('lessonSubmitBtn').textContent = 'Update Pelajaran';
            document.getElementById('lessonSubmitBtn').className = 'btn btn-warning';

            // Set form method and lesson ID
            document.getElementById('lessonMethod').value = 'PUT';
            document.getElementById('lessonId').value = lessonId;

            // Load lesson data
            loadLessonData(lessonId);
        }

        document.addEventListener('click', function(e) {
            if (e.target.matches('[data-bs-target="#lessonModal"]')) {
                if (e.target.hasAttribute('data-module-id')) {
                    // Add lesson mode
                    const moduleId = e.target.getAttribute('data-module-id');
                    console.log('Add lesson for Module ID:', moduleId);
                    setupAddLessonModal(moduleId);
                } else if (e.target.matches('.edit-lesson-btn')) {
                    // Edit lesson mode
                    const lessonId = e.target.getAttribute('data-lesson-id');
                    console.log('Edit Lesson ID:', lessonId);
                    setupEditLessonModal(lessonId);
                }
            }

            // Handle delete module
            if (e.target.matches('.module-delete-btn')) {
                e.preventDefault();
                const moduleId = e.target.getAttribute('data-module-id');
                deleteModule(moduleId);
            }

            // Handle delete lesson
            if (e.target.matches('.delete-lesson-btn')) {
                e.preventDefault();
                const lessonId = e.target.getAttribute('data-lesson-id');
                deleteLesson(lessonId);
            }
        });

        // Function to load lesson data for editing
        function loadLessonData(lessonId) {
            $.ajax({
                url: `/lesson/${lessonId}/edit`,
                method: 'GET',
                success: function(response) {
                    console.log('Loaded lesson data:', response); // Debug log

                    // Fill form with lesson data
                    $('#lessonTitle').val(response.title);
                    $('#lessonSummary').val(response.summary);
                    $('#lessonDuration').val(response.duration_in_minutes);
                    $('#lessonContent').val(response.content_type);

                    // Generate dynamic content with data
                    generateDynamicContent(response.content_type, response);
                },
                error: function(xhr) {
                    console.error('Error loading lesson data:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memuat data pelajaran.',
                    });
                }
            });
        }

        // Function to delete module
        function deleteModule(moduleId) {
            Swal.fire({
                title: 'Hapus Modul?',
                text: 'Apakah Anda yakin ingin menghapus modul ini? Semua pelajaran dalam modul ini juga akan terhapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/module/${moduleId}`,
                        method: 'DELETE',
                        data: {
                            _token: $('input[name="_token"]').first().val()
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Modul berhasil dihapus.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                sessionStorage.setItem('activeTab', 'kurikulum');
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Gagal menghapus modul.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }

        // Function to delete lesson
        function deleteLesson(lessonId) {
            Swal.fire({
                title: 'Hapus Pelajaran?',
                text: 'Apakah Anda yakin ingin menghapus pelajaran ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/lesson/${lessonId}`,
                        method: 'DELETE',
                        data: {
                            _token: $('input[name="_token"]').first().val()
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pelajaran berhasil dihapus.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                sessionStorage.setItem('activeTab', 'kurikulum');
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Gagal menghapus pelajaran.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }

        $('#lessonForm').on('submit', function(e) {
            e.preventDefault();

            // Check if it's add mode and module ID is required
            if (!isEditMode && !selectedModuleId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Module ID tidak ditemukan.',
                });
                return;
            }

            // Check if it's edit mode and lesson ID is required
            if (isEditMode && !selectedLessonId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Lesson ID tidak ditemukan.',
                });
                return;
            }

            const submitButton = $('#lessonSubmitBtn');
            const originalText = submitButton.text();
            const loadingText = isEditMode ? 'Mengupdate...' : 'Menyimpan...';
            submitButton.prop('disabled', true).text(loadingText);

            let formData = new FormData(this);

            // Prepare request based on mode
            let ajaxConfig = {
                processData: false,
                contentType: false,
                data: formData
            };

            if (isEditMode) {
                // Edit mode
                ajaxConfig.url = `/lesson/${selectedLessonId}`;
                ajaxConfig.method = "POST"; // Laravel uses POST with _method=PUT for file uploads
            } else {
                // Add mode
                const courseId = {{ $course->id }};
                formData.append('course_id', courseId);
                formData.append('module_id', selectedModuleId);
                ajaxConfig.url = "{{ route('lesson.store') }}";
                ajaxConfig.method = "POST";
            }

            $.ajax(ajaxConfig).done(function(response) {
                submitButton.prop('disabled', false).text(originalText);

                $('#lessonModal').modal('hide');

                const successText = isEditMode ? 'Data Pelajaran berhasil diperbarui.' :
                    'Data Pelajaran berhasil disimpan.';

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: successText,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    sessionStorage.setItem('activeTab', 'kurikulum');
                    location.reload();
                });
            }).fail(function(xhr) {
                submitButton.prop('disabled', false).text(originalText);

                console.error('Error:', xhr);

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '';

                    $.each(errors, function(key, value) {
                        errorMessages += `• ${value[0]}<br>`;
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errorMessages,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    $('#lessonModal').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Silakan coba lagi nanti.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });

        $(document).ready(function() {
            const activeTab = sessionStorage.getItem('activeTab');
            const urlHash = window.location.hash;
            const samePageReferrer = document.referrer && new URL(document.referrer, window.location.origin).pathname === window.location.pathname;

            if (urlHash === '#kurikulum' || (samePageReferrer && activeTab === 'kurikulum')) {
                sessionStorage.removeItem('activeTab');
                // Prefer the host page helper if available; otherwise, update hash
                if (window.activateCoursePane) {
                    window.activateCoursePane('kurikulum');
                } else {
                    try { history.replaceState(null, '', '#kurikulum'); } catch(e) {}
                }
            }
        });

        // Inline Edit Module Title
        $(document).on('click', '.module-edit-btn', function(e) {
            e.preventDefault();
            const moduleId = $(this).data('module-id');

            // Hide display mode and show edit mode
            $(`.module-display-mode[data-module-id="${moduleId}"]`).hide();
            $(`.module-display-buttons[data-module-id="${moduleId}"]`).hide();
            $(`.module-edit-mode[data-module-id="${moduleId}"]`).show();
            $(`.module-edit-buttons[data-module-id="${moduleId}"]`).show();

            // Focus on input
            $(`.module-title-input[data-module-id="${moduleId}"]`).focus().select();
        });

        // Cancel edit
        $(document).on('click', '.module-edit-cancel', function(e) {
            e.preventDefault();
            const moduleId = $(this).data('module-id');

            // Show display mode and hide edit mode
            $(`.module-display-mode[data-module-id="${moduleId}"]`).show();
            $(`.module-display-buttons[data-module-id="${moduleId}"]`).show();
            $(`.module-edit-mode[data-module-id="${moduleId}"]`).hide();
            $(`.module-edit-buttons[data-module-id="${moduleId}"]`).hide();

            // Reset input value
            const originalTitle = $(`.module-title-display[data-module-id="${moduleId}"]`).text();
            $(`.module-title-input[data-module-id="${moduleId}"]`).val(originalTitle);
        });

        // Submit edit form
        $(document).on('submit', '.module-edit-form', function(e) {
            e.preventDefault();

            const moduleId = $(this).data('module-id');
            const newTitle = $(this).find('.module-title-input').val().trim();
            const submitBtn = $(`.module-edit-buttons[data-module-id="${moduleId}"] button[type="submit"]`);
            const cancelBtn = $(`.module-edit-buttons[data-module-id="${moduleId}"] .module-edit-cancel`);

            if (!newTitle) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Judul module tidak boleh kosong.',
                });
                return;
            }

            // Disable buttons
            submitBtn.prop('disabled', true);
            cancelBtn.prop('disabled', true);

            $.ajax({
                url: `/module/${moduleId}`,
                method: 'PUT',
                data: {
                    _token: $('input[name="_token"]').first().val(),
                    title: newTitle
                },
                success: function(response) {
                    // Update display title
                    $(`.module-title-display[data-module-id="${moduleId}"]`).text(newTitle);

                    // Show display mode and hide edit mode
                    $(`.module-display-mode[data-module-id="${moduleId}"]`).show();
                    $(`.module-display-buttons[data-module-id="${moduleId}"]`).show();
                    $(`.module-edit-mode[data-module-id="${moduleId}"]`).hide();
                    $(`.module-edit-buttons[data-module-id="${moduleId}"]`).hide();

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Judul module berhasil diperbarui.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr);

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';

                        $.each(errors, function(key, value) {
                            errorMessages += `• ${value[0]}<br>`;
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: errorMessages,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal memperbarui judul module.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                complete: function() {
                    // Re-enable buttons
                    submitBtn.prop('disabled', false);
                    cancelBtn.prop('disabled', false);
                }
            });
        });

        // Handle escape key to cancel edit
        $(document).on('keydown', '.module-title-input', function(e) {
            if (e.key === 'Escape') {
                const moduleId = $(this).closest('.module-edit-form').data('module-id');
                $(`.module-edit-cancel[data-module-id="${moduleId}"]`).click();
            }
        });

        // Quiz functionality
        let selectedQuizModuleId = null;
        let selectedQuizId = null;
        let isQuizEditMode = false;

        // Function to setup modal for add quiz mode
        function setupAddQuizModal(moduleId) {
            console.log('setupAddQuizModal called with moduleId:', moduleId);
            isQuizEditMode = false;
            selectedQuizModuleId = moduleId;
            selectedQuizId = null;

            // Reset form
            document.getElementById('quizForm').reset();
            document.getElementById('quizMethod').value = 'POST';
            document.getElementById('quizId').value = '';
            document.getElementById('quizModuleId').value = moduleId;

            console.log('Hidden input quizModuleId set to:', document.getElementById('quizModuleId').value);

            // Update modal title and button
            document.getElementById('quizModalLabel').textContent = 'Tambah Kuis Baru';
            document.getElementById('quizSubmitBtn').textContent = 'Tambah Kuis';
            document.getElementById('quizSubmitBtn').className = 'btn btn-primary';
        }

        // Function to setup modal for edit quiz mode
        function setupEditQuizModal(quizId) {
            isQuizEditMode = true;
            selectedQuizId = quizId;
            selectedQuizModuleId = null;

            // Update modal title and button
            document.getElementById('quizModalLabel').textContent = 'Edit Kuis';
            document.getElementById('quizSubmitBtn').textContent = 'Update Kuis';
            document.getElementById('quizSubmitBtn').className = 'btn btn-warning';

            // Set form method and quiz ID
            document.getElementById('quizMethod').value = 'PUT';
            document.getElementById('quizId').value = quizId;
            document.getElementById('quizModuleId').value = ''; // Clear module_id untuk edit mode

            // Load quiz data
            loadQuizData(quizId);
        }

        // Handle quiz modal triggers
        $(document).on('click', '[data-bs-target="#quizModal"]', function(e) {
            if ($(this).attr('data-module-id')) {
                // Add quiz mode
                const moduleId = $(this).attr('data-module-id');
                console.log('Add quiz for Module ID:', moduleId);
                setupAddQuizModal(moduleId);
            } else if ($(this).hasClass('edit-quiz-btn') || $(this).attr('data-quiz-id')) {
                // Edit quiz mode
                const quizId = $(this).attr('data-quiz-id');
                console.log('Edit Quiz ID:', quizId);
                setupEditQuizModal(quizId);
            }
        });

        // Handle delete quiz
        $(document).on('click', '.delete-quiz-btn', function(e) {
            e.preventDefault();
            const quizId = $(this).attr('data-quiz-id');
            deleteQuiz(quizId);
        });

        // Function to load quiz data for editing
        function loadQuizData(quizId) {
            $.ajax({
                url: `/quiz/${quizId}/edit`,
                method: 'GET',
                success: function(response) {
                    console.log('Loaded quiz data:', response);

                    // Fill form with quiz data
                    $('#quizTitle').val(response.title);
                    $('#quizDescription').val(response.description);
                    $('#quizDuration').val(response.duration_in_minutes);
                    $('#quizPassingScore').val(response.passing_score);
                    $('#quizMaxAttempts').val(response.max_attempts);
                    $('#quizInstructions').val(response.instructions);
                },
                error: function(xhr) {
                    console.error('Error loading quiz data:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal memuat data kuis.',
                    });
                }
            });
        }

        // Function to delete quiz
        function deleteQuiz(quizId) {
            Swal.fire({
                title: 'Hapus Kuis?',
                text: 'Apakah Anda yakin ingin menghapus kuis ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/quiz/${quizId}`,
                        method: 'DELETE',
                        data: {
                            _token: $('input[name="_token"]').first().val()
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Kuis berhasil dihapus.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                sessionStorage.setItem('activeTab', 'kurikulum');
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Gagal menghapus kuis.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }

        // Quiz form submit
        $('#quizForm').on('submit', function(e) {
            e.preventDefault();

            console.log('Quiz form submit - isQuizEditMode:', isQuizEditMode);
            console.log('Quiz form submit - selectedQuizModuleId:', selectedQuizModuleId);

            // Check if it's add mode and module ID is required
            const hiddenModuleId = document.getElementById('quizModuleId').value;
            console.log('Quiz form submit - hiddenModuleId:', hiddenModuleId);

            if (!isQuizEditMode && !selectedQuizModuleId && !hiddenModuleId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Module ID tidak ditemukan.',
                });
                return;
            }

            // Check if it's edit mode and quiz ID is required
            if (isQuizEditMode && !selectedQuizId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Quiz ID tidak ditemukan.',
                });
                return;
            }

            const submitButton = $('#quizSubmitBtn');
            const originalText = submitButton.text();
            const loadingText = isQuizEditMode ? 'Mengupdate...' : 'Menyimpan...';
            submitButton.prop('disabled', true).text(loadingText);

            let formData = new FormData(this);

            // Prepare request based on mode
            let ajaxConfig = {
                processData: false,
                contentType: false,
                data: formData
            };

            if (isQuizEditMode) {
                // Edit mode
                ajaxConfig.url = `/quiz/${selectedQuizId}`;
                ajaxConfig.method = "POST"; // Laravel uses POST with _method=PUT
            } else {
                // Add mode
                const courseId = {{ $course->id }};
                formData.append('course_id', courseId);
                // module_id sudah ada dalam hidden input form
                ajaxConfig.url = "{{ route('quiz.store') }}";
                ajaxConfig.method = "POST";
            }

            $.ajax(ajaxConfig).done(function(response) {
                submitButton.prop('disabled', false).text(originalText);

                $('#quizModal').modal('hide');

                const successText = isQuizEditMode ? 'Data Kuis berhasil diperbarui.' : 'Data Kuis berhasil disimpan.';
                // Jika mode tambah, arahkan langsung ke halaman kelola soal kuis
                if (!isQuizEditMode && response && response.redirect_url) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Kuis dibuat. Mengarahkan ke halaman kelola soal...',
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        window.location.href = response.redirect_url;
                    });
                    return;
                }

                // Default behavior (edit mode atau jika tidak ada redirect_url)
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: successText,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    sessionStorage.setItem('activeTab', 'kurikulum');
                    location.reload();
                });
            }).fail(function(xhr) {
                submitButton.prop('disabled', false).text(originalText);

                console.error('Error:', xhr);

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '';

                    $.each(errors, function(key, value) {
                        errorMessages += `• ${value[0]}<br>`;
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errorMessages,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    $('#quizModal').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Silakan coba lagi nanti.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });

        // Quiz modal reset
        $('#quizModal').on('hidden.bs.modal', function() {
            document.getElementById('quizForm').reset();
            selectedQuizModuleId = null;
            selectedQuizId = null;
            isQuizEditMode = false;
        });
    </script>
@endpush
