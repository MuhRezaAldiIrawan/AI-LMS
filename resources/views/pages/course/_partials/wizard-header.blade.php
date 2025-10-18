@php
    // Props: $activeStep (details|module|quiz|participants|publish), $title (string), optional $course
    $title = $title ?? 'Create Course';
    $activeStep = $activeStep ?? 'details';

    $steps = [
        'details' => 'Course Details',
        'module' => 'Create Module & Quiz', // Curriculum step (module + quiz)
        'participants' => 'Assign Participants',
        'publish' => 'Publish Course',
    ];

    $order = array_keys($steps);
    $activeIndex = array_search($activeStep, $order, true);
@endphp

<div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
    <!-- Breadcrumb Start -->
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li>
                <a href="{{ url('/') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a>
            </li>
            <li>
                <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span>
            </li>
            <li><span class="text-main-600 fw-normal text-15">{{ $title }}</span></li>
        </ul>
    </div>
    <!-- Breadcrumb End -->

    <!-- Buttons Start -->
    <div class="flex-align justify-content-end gap-8">
        <button type="button" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9" id="btnSaveDraft" data-course-id="{{ $course->id ?? '' }}" data-status="{{ $course->status ?? 'draft' }}" {{ empty($course?->id) ? 'disabled' : '' }}>Save as Draft</button>
        <button type="button" class="btn btn-main rounded-pill py-9" id="btnPublishCourse" data-course-id="{{ $course->id ?? '' }}" data-status="{{ $course->status ?? 'draft' }}" {{ empty($course?->id) ? 'disabled' : '' }}>Publish Course</button>
    </div>
    <!-- Buttons End -->
</div>

<!-- Create Course Step List Start -->
<ul class="step-list mb-24" id="courseWizardSteps" data-active-step="{{ $activeStep }}">
    @foreach($steps as $key => $label)
        @php
            $index = array_search($key, $order, true);
            $classes = '';
            if ($index < $activeIndex) $classes = 'done';
            elseif ($index === $activeIndex) $classes = 'active';
        @endphp
        <li class="step-list__item py-15 px-24 text-15 text-heading fw-medium flex-center gap-6 {{ $classes }}">
            <span class="icon text-xl d-flex"><i class="ph ph-circle"></i></span>
            {{ $label }}
            <span class="line position-relative"></span>
        </li>
    @endforeach
    </ul>
<!-- Create Course Step List End -->

@push('js')
<script>
    // Optional client-side helpers for Draft/Publish actions (no-ops if routes not wired yet)
    (function(){
        const btnDraft = document.getElementById('btnSaveDraft');
        const btnPublish = document.getElementById('btnPublishCourse');
        const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        async function togglePublish(courseId, publish){
            if(!courseId) return;
            const btn = publish ? btnPublish : btnDraft;
            const original = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>' + (publish ? 'Publishing...' : 'Saving...');
            try{
                const fd = new FormData();
                fd.append('action', 'toggle_publish');
                fd.append('is_published', publish ? '1' : '0');
                const res = await fetch(`/course/${courseId}`, { method: 'POST', body: fd, headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': getCsrf() } });
                if(!res.ok){
                    const text = await res.text();
                    let errsHtml = text;
                    try { const j = JSON.parse(text); if(j && j.errors){ errsHtml = Object.values(j.errors).map(arr => '• ' + arr[0]).join('<br>'); } else if(j && j.message){ errsHtml = j.message; } } catch(e) {}
                    if(window.Swal){
                        Swal.fire({ icon:'error', title: publish ? 'Gagal Publish' : 'Gagal Simpan Draft', html: errsHtml });
                    } else { alert((publish ? 'Gagal publish: ' : 'Gagal simpan draft: ') + errsHtml); }
                    return;
                }
                const data = await res.json().catch(()=>({}));
                if(window.Swal){
                    Swal.fire({ icon:'success', title: data?.message || (publish ? 'Kursus berhasil dipublish.' : 'Kursus kembali ke Draft.'), timer: 1200, showConfirmButton:false });
                }
                // Optionally refresh status attribute
                btnDraft?.setAttribute('data-status', publish ? 'published' : 'draft');
                btnPublish?.setAttribute('data-status', publish ? 'published' : 'draft');
            } finally {
                btn.disabled = false; btn.innerHTML = original;
            }
        }

        if(btnDraft){
            btnDraft.addEventListener('click', function(){
                const id = this.getAttribute('data-course-id');
                if(!id) return;
                if(window.Swal){
                    Swal.fire({
                        title: 'Simpan sebagai Draft?',
                        text: 'Kursus akan tidak tersedia untuk peserta baru sampai dipublish kembali.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f39c12',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan Draft',
                        cancelButtonText: 'Batal'
                    }).then((res)=>{ if(res.isConfirmed) togglePublish(id, false); });
                } else if(confirm('Simpan sebagai draft?')) {
                    togglePublish(id, false);
                }
            });
        }
        if(btnPublish){
            btnPublish.addEventListener('click', function(){
                const id = this.getAttribute('data-course-id');
                if(!id) return;
                const status = this.getAttribute('data-status') || 'draft';
                const isPublished = status === 'published';
                const title = isPublished ? 'Update Course?' : 'Publish Course?';
                const text = isPublished ? 'Perbarui status dan informasi kursus?' : 'Apakah Anda yakin ingin mempublish kursus ini? Kursus akan tersedia untuk semua peserta.';
                const confirmText = isPublished ? 'Ya, Update' : 'Ya, Publish';

                if(window.Swal){
                    Swal.fire({
                        title, text, icon:'question',
                        showCancelButton:true,
                        confirmButtonColor:'#3085d6', cancelButtonColor:'#d33',
                        confirmButtonText: confirmText, cancelButtonText:'Batal'
                    }).then((res)=>{ if(res.isConfirmed) togglePublish(id, true); });
                } else if(confirm((isPublished? 'Update':'Publish') + ' course?')) {
                    togglePublish(id, true);
                }
            });
        }
    })();

    // Helper to set active step dynamically (e.g., when switching tabs)
    window.setCourseWizardStep = function(stepKey){
        const el = document.getElementById('courseWizardSteps');
        if(!el) return;
        const order = ['details','module','participants','publish'];
        const activeIdx = order.indexOf(stepKey);
        if(activeIdx === -1) return;
        // update classes
        [...el.children].forEach((li, idx) => {
            li.classList.remove('active','done');
            if(idx < activeIdx) li.classList.add('done');
            else if(idx === activeIdx) li.classList.add('active');
        });
        el.dataset.activeStep = stepKey;
    }
</script>
@endpush
