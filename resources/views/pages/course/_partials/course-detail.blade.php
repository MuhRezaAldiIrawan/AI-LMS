<div class="card mt-24">
<div class="card-header border-bottom">
    <h4 class="mb-4">Detail Kursus</h4>
</div>
<div class="card-body">
    <form id="editCourseForm" action="{{ route('course.update', $course->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-20">
            <input type="hidden" name="courseid" id="courseid" value="{{ $course->id ?? '' }}">

            <div class="col-sm-12">
                <label for="title" class="h7 mb-8 fw-semibold font-heading">Judul Kursus</label>
                <div class="position-relative">
                    <input type="text" name="title" id="title" class="form-control py-9"
                        value="{{ old('title', $course->title ?? '') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <label for="category_id" class="h7 mb-8 fw-semibold font-heading">Kategori</label>
                <div class="position-relative">
                    <select name="category_id" id="category_id" class="form-select py-9">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $category->id == $course->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-12">
                <label for="course_type_id" class="h7 mb-8 fw-semibold font-heading">Tipe Kursus</label>
                <div class="position-relative">
                    <select name="course_type_id" id="course_type_id" class="form-select py-9">
                        <option value="">Pilih Tipe Kursus</option>
                        @foreach ($courseType as $type)
                            <option value="{{ $type->id }}"
                                {{ $type->id == $course->course_type_id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-12">
                <label for="summary" class="h7 mb-8 fw-semibold font-heading">About Course (Ringkasan)</label>
                <div class="position-relative">
                    <textarea name="summary" id="summary" cols="30" rows="4" class="form-control py-9" placeholder="Tuliskan ringkasan singkat tentang kursus ini...">{{ old('summary', $course->summary ?? '') }}</textarea>

                </div>
            </div>

            <div class="col-sm-12">
                <label for="description" class="h7 mb-8 fw-semibold font-heading">Deskripsi</label>
                <div class="position-relative">
                    <textarea name="description" id="description" cols="30" rows="4" class="form-control py-9" placeholder="Tuliskan Deskripsi tentang kursus ini...">{{ old('description', $course->description ?? '') }}</textarea>
                </div>
            </div>

            <div class="col-sm-12">
                <label for="thumbnail" class="h7 mb-8 fw-semibold font-heading">Thumbnail</label>
                <div id="thumbnail_fallback" class="mt-8">
                    <input type="file" name="thumbnail" accept="image/*" class="form-control" />
                    @if(!empty($course->thumbnail))
                        <img src="{{ $course->getThumbnailUrl() }}" class="mt-12 rounded-8 border" style="max-width: 100%; height:auto;" />
                    @endif
                </div>
                <!-- Fallback input if JS is disabled -->
                <noscript>
                    <input type="file" name="thumbnail" accept="image/*" class="form-control mt-8" />
                    @if(!empty($course->thumbnail))
                        <img src="{{ $course->getThumbnailUrl() }}" class="mt-12" style="max-width: 100%; height:auto;" />
                    @endif
                </noscript>
            </div>

        </div>

        <div class="flex-align justify-content-end gap-8 mt-16">
            <a href="{{ route('course') }}" class="btn btn-outline-main rounded-pill py-9">Batal</a>
            <button class="btn btn-main rounded-pill py-9" type="button" id="btnSaveAndContinueDetails">
                Simpan & Lanjutkan ke Kurikulum
            </button>
        </div>
    </form>
</div>
</div>

<script>
    (function(){
        const form = document.getElementById('editCourseForm');
        const btnNext = document.getElementById('btnSaveAndContinueDetails');

        function ajaxSubmit(onSuccess){
            const fd = new FormData(form);
            const id = document.getElementById('courseid').value;
            const submitter = btnNext;
            const original = submitter.innerHTML;
            submitter.disabled = true; submitter.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
            fetch('/course/' + id, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(async (r) => {
                    if(!r.ok){
                        const txt = await r.text().catch(()=> '');
                        throw new Error(txt || 'Request failed');
                    }
                    // Best-effort JSON parse (optional), ignore errors
                    try { await r.clone().json(); } catch(e) {}
                    return;
                })
                .then(() => {
                    submitter.disabled = false; submitter.innerHTML = original;
                    // Dispatch event and also directly activate pane as a fallback
                    window.dispatchEvent(new CustomEvent('course:navigate', { detail: { to: 'module' } }));
                    if (window.activateCoursePane) {
                        window.activateCoursePane('kurikulum');
                    } else {
                        // Hard fallback: directly switch tab-pane classes and update wizard+hash
                        try {
                            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show','active'));
                            const targetPane = document.getElementById('kurikulum');
                            if (targetPane) {
                                targetPane.classList.add('show','active');
                                if (window.setCourseWizardStep) window.setCourseWizardStep('module');
                                location.hash = '#kurikulum';
                                document.querySelector('.tab-content')?.scrollIntoView({ behavior:'smooth', block:'start' });
                            }
                        } catch(e) { console.warn('Pane switch fallback failed', e); }
                    }
                })
                .catch(err => {
                    submitter.disabled=false; submitter.innerHTML = original;
                    console.error('Save failed:', err);
                    if(window.Swal){
                        Swal.fire({ icon:'error', title:'Gagal menyimpan', text:'Silakan coba lagi.' });
                    }
                });
        }

        if(btnNext) btnNext.addEventListener('click', ()=>ajaxSubmit('next'));
    })();
</script>
