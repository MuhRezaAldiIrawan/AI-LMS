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
    <div class="card overflow-hidden">
        <div class="card-body p-0">

            <div class="setting-profile px-24">
                <div class="flex-between">
                    <div class="d-flex align-items-end flex-wrap mb-32 gap-24">
                    </div>
                </div>
                @php
                    $isOwner = auth()->check() && (auth()->id() === $course->user_id || auth()->user()->hasRole('admin'));
                @endphp
                <ul class="nav common-tab style-two nav-pills mb-0" id="pills-tab" role="tablist">
                    @if($isOwner)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="informasi-umum-tab" data-bs-toggle="pill"
                                data-bs-target="#informasi-umum" type="button" role="tab" aria-controls="informasi-umum"
                                aria-selected="true">Informasi Umum</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kurikulum-tab" data-bs-toggle="pill" data-bs-target="#kurikulum"
                                type="button" role="tab" aria-controls="kurikulum" aria-selected="false">Kurikulum</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="users-tab" data-bs-toggle="pill" data-bs-target="#users" type="button"
                                role="tab" aria-controls="users" aria-selected="false">Peserta & Akses</button>
                        </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $isOwner ? '' : 'active' }}" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview"
                            type="button" role="tab" aria-controls="overview" aria-selected="false">Overview</button>
                    </li>
                </ul>
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

            // Activate tab based on URL hash
            const hash = window.location.hash;
            if (hash === '#overview') {
                const tab = document.querySelector('#overview-tab');
                if (tab) new bootstrap.Tab(tab).show();
            } else if (hash === '#kurikulum') {
                const tab = document.querySelector('#kurikulum-tab');
                if (tab) new bootstrap.Tab(tab).show();
            } else if (hash === '#users') {
                const tab = document.querySelector('#users-tab');
                if (tab) new bootstrap.Tab(tab).show();
            }
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

        $('#createModuleForm').on('submit', function(e) {
            e.preventDefault(); // cegah reload halaman

            // Ambil data form
            let formData = new FormData(this);
            const courseid = {{ $course->id }};

            formData.append('course_id', courseid);

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
                        text: 'Data Modul berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = response.redirect_url;
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
@endsection
