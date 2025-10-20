@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">

    <style>
        .card-section {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            transition: 0.3s;
        }

        .card-section:hover {
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }
    </style>
@endsection

@section('content')
    @php
        // Hormati nilai $isOwner dari controller jika sudah dikirim.
        // Fallback: tentukan owner berdasarkan pemilik course.
        if (!isset($isOwner)) {
            $isOwner = auth()->check() && (auth()->id() === $course->user_id);
        }

        // Admin tidak mengelola kursus di UI ini -> tampilkan Overview saja
        if (function_exists('isAdmin') && isAdmin()) {
            $isOwner = false;
        }

        // Paksa mode pembelajar jika query ?mode=learn ada,
        // sehingga pemilik tidak melihat tampilan manage/editor.
        if (request()->query('mode') === 'learn') {
            $isOwner = false;
        }
    @endphp
    @if($isOwner)
        @include('pages.course._partials.wizard-header', [
            'title' => 'Create Course',
            'activeStep' => 'details',
            'course' => $course,
        ])
    @endif

    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <div class="setting-profile px-24">
                <!-- Tab bar disembunyikan untuk menghindari double header; navigasi pakai tombol Save & Continue -->
            </div>
        </div>
    </div>

    <div class="tab-content" id="pills-tabContent">
        @if($isOwner)
            <!-- Courses Tab start -->
            <div class="tab-pane fade show active" id="informasi-umum" role="tabpanel" aria-labelledby="informasi-umum-tab"
                tabindex="0">
                @include('pages.course._partials.course-detail', ['course' => $course])
            </div>
            <!-- Courses Tab End -->

            <!-- Kurikulum Tab Start -->
            <div class="tab-pane fade" id="kurikulum" role="tabpanel" aria-labelledby="kurikulum-tab" tabindex="0">
                @include('pages.course._partials.curriculum', ['course' => $course])
            </div>
            <!-- Kurikulum Tab End -->

            <!-- Users And Access Tab Start -->
            <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab" tabindex="0">
                @include('pages.course._partials.participants-access', ['course' => $course, 'users' => $users])
            </div>
            <!-- Users And Access Tab End -->
        @endif

        <!-- Overview Tab Start -->
        <div class="tab-pane fade {{ $isOwner ? '' : 'show active' }}" id="overview" role="tabpanel" aria-labelledby="overview-tab" tabindex="0">
            @include('pages.course._partials.overview', ['course' => $course])
        </div>
        <!-- Overview Tab End -->

    </div>
@endsection

@section('js')
    @stack('js')
    <script src="{{ asset('assets/js/file-upload.js') }}"></script>
    <script src="{{ asset('assets/js/plyr.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            if ($.fn.fileUpload) {
                $('.fileUpload').fileUpload();
                console.log("✅ File upload initialized successfully.");
            } else {
                console.error("❌ fileUpload plugin not found. Check file-upload.js loading order.");
            }

            // Ensure CSRF header is sent for all AJAX
            const csrf = $('meta[name="csrf-token"]').attr('content');
            if (csrf) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': csrf }
                });
            }

            // Helper: activate pane by step key or pane id
            window.activateCoursePane = function(target){
                const mapStepToPane = { details: 'informasi-umum', module: 'kurikulum', participants: 'users', publish: 'overview' };
                const paneId = mapStepToPane[target] || target; // allow passing pane id directly
                const targetPane = document.getElementById(paneId);
                if(!targetPane) return;
                console.debug('[wizard] Activating pane:', paneId);
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show','active'));
                targetPane.classList.add('show','active');
                // set wizard step if step key provided
                const mapPaneToStep = { 'informasi-umum':'details', 'kurikulum':'module', 'users':'participants', 'overview':'publish' };
                const stepKey = mapPaneToStep[paneId] || (target in mapStepToPane ? target : 'details');
                window.setCourseWizardStep && window.setCourseWizardStep(stepKey);
                const hashMap = { 'informasi-umum':'#informasi-umum', 'kurikulum':'#kurikulum', 'users':'#users', 'overview':'#overview' };
                history.replaceState(null, '', hashMap[paneId] || '#');
                // Smooth scroll to top of tab content
                try { document.querySelector('.tab-content')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch(e) {}
            }

            // Activate tab based on URL hash (works even without nav bar)
            const hash = window.location.hash;
            if (hash === '#overview') {
                window.activateCoursePane('overview');
            } else if (hash === '#kurikulum') {
                window.activateCoursePane('kurikulum');
            } else if (hash === '#users') {
                window.activateCoursePane('users');
            } else if (hash === '#informasi-umum') {
                window.activateCoursePane('informasi-umum');
            } else {
                // If no hash, use current active pane to set the step header correctly
                const activePane = document.querySelector('.tab-pane.show.active');
                if (activePane) window.activateCoursePane(activePane.id);
            }

            // React to future hash changes (defensive)
            window.addEventListener('hashchange', function(){
                const h = window.location.hash;
                if (h === '#overview') window.activateCoursePane('overview');
                else if (h === '#kurikulum') window.activateCoursePane('kurikulum');
                else if (h === '#users') window.activateCoursePane('users');
                else if (h === '#informasi-umum') window.activateCoursePane('informasi-umum');
            });

            // Sync wizard header with active tab (owner view only)
            const syncStep = () => {
                // Determine active pane by visible tab-pane show/active
                const pane = document.querySelector('.tab-pane.show.active');
                if(!pane) return;
                const paneId = pane.getAttribute('id');
                if(paneId === 'informasi-umum') window.setCourseWizardStep && window.setCourseWizardStep('details');
                else if(paneId === 'kurikulum') window.setCourseWizardStep && window.setCourseWizardStep('module');
                else if(paneId === 'users') window.setCourseWizardStep && window.setCourseWizardStep('participants');
                else if(paneId === 'overview') window.setCourseWizardStep && window.setCourseWizardStep('publish');
            };
            syncStep();

            // Listen to custom events from Save & Continue buttons to switch panes and steps
            window.addEventListener('course:navigate', function(e){
                const to = e.detail && e.detail.to;
                if(!to) return;
                console.debug('[wizard] course:navigate to:', to);
                window.activateCoursePane(to);
            });
        });

        // Use delegated binding to avoid timing issues
        $(document).on('submit', '#editCourseForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const btn = $form.find('button[type="submit"]');
            const original = btn.html();

            const formData = new FormData(this);
            const id = $('#courseid').val();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

            $.ajax({
                url: '/course/' + id,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response) {
                    const message = (response && response.message) ? response.message : 'Data Kursus berhasil diupdate.';
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // If server provides redirect, use it; else go to course list
                        if (response && response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            window.location.href = '/course';
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Update error:', xhr);
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors || {};
                        let html = '';
                        Object.keys(errors).forEach(k => { html += `• ${errors[k][0]}<br>`; });
                        Swal.fire({ icon: 'error', title: 'Validasi Gagal', html, confirmButtonColor: '#d33' });
                    } else if (xhr.status === 401) {
                        Swal.fire({ icon: 'error', title: 'Unauthorized', text: 'Silakan login kembali.' });
                    } else if (xhr.status === 403) {
                        Swal.fire({ icon: 'error', title: 'Tidak diizinkan', text: 'Anda tidak memiliki akses untuk mengupdate kursus ini.' });
                    } else if (xhr.status === 419) {
                        Swal.fire({ icon: 'error', title: 'Session Habis', text: 'Silakan muat ulang halaman dan coba lagi.' });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Gagal menyimpan perubahan. Coba lagi.' });
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html(original);
                }
            });
        });

        // Reveal fallback input if the upload widget failed to render
        setTimeout(function() {
            const hasWidget = document.querySelector('.fileUpload label.file-upload');
            const fallback = document.getElementById('thumbnail_fallback');
            if (hasWidget && fallback) {
                // Widget rendered fine -> hide fallback
                fallback.style.display = 'none';
            }
        }, 0);

        // Note: createModuleForm submission is handled inside the curriculum partial to avoid double binding
    </script>
@endsection
