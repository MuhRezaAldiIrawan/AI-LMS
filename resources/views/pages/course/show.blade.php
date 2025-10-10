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
                <ul class="nav common-tab style-two nav-pills mb-0" id="pills-tab" role="tablist">
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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview"
                            type="button" role="tab" aria-controls="overview" aria-selected="false">Overview</button>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="tab-content" id="pills-tabContent">
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

        <!-- Overview Tab Start -->
        <div class="tab-pane fade" id="overview" role="tabpanel" aria-labelledby="overview-tab" tabindex="0">
            <div class="card mt-24">
                <div class="card-header border-bottom">
                    <h4 class="mb-4">Pricing Breakdown</h4>
                    <p class="text-gray-600 text-15">Creating a detailed pricing plan for your course requries considering
                        various factors.</p>
                </div>
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="plan-item rounded-16 border border-gray-100 transition-2 position-relative">
                                <span class="text-2xl d-flex mb-16 text-main-600"><i class="ph ph-package"></i></span>
                                <h3 class="mb-4">Basic Plan</h3>
                                <span class="text-gray-600">Perfect plan for students</span>
                                <h2
                                    class="h1 fw-medium text-main mb-32 mt-16 pb-32 border-bottom border-gray-100 d-flex gap-4">
                                    $50 <span class="text-md text-gray-600">/year</span>
                                </h2>
                                <ul>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Intro video the course
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Interactive quizes
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Course curriculum
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Community supports
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Certificate of completion
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Sample lesson showcasing
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Access to course community
                                    </li>
                                </ul>
                                <a href="#"
                                    class="btn btn-outline-main w-100 rounded-pill py-16 border-main-300 text-17 fw-medium mt-32">Get
                                    Started</a>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="plan-item rounded-16 border border-gray-100 transition-2 position-relative active">
                                <span
                                    class="plan-badge py-4 px-16 bg-main-600 text-white position-absolute inset-inline-end-0 inset-block-start-0 mt-8 text-15">Recommended</span>
                                <span class="text-2xl d-flex mb-16 text-main-600"><i class="ph ph-planet"></i></span>
                                <h3 class="mb-4">Standard Plan</h3>
                                <span class="text-gray-600">For users who want to do more</span>
                                <h2
                                    class="h1 fw-medium text-main mb-32 mt-16 pb-32 border-bottom border-gray-100 d-flex gap-4">
                                    $129 <span class="text-md text-gray-600">/year</span>
                                </h2>

                                <ul>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Intro video the course
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Interactive quizes
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Course curriculum
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Community supports
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Certificate of completion
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Sample lesson showcasing
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Access to course community
                                    </li>
                                </ul>
                                <a href="#"
                                    class="btn btn-main w-100 rounded-pill py-16 border-main-600 text-17 fw-medium mt-32">Get
                                    Started</a>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="plan-item rounded-16 border border-gray-100 transition-2 position-relative">
                                <span class="text-2xl d-flex mb-16 text-main-600"><i class="ph ph-trophy"></i></span>
                                <h3 class="mb-4">Premium Plan</h3>
                                <span class="text-gray-600">Your entire friends in one place</span>
                                <h2
                                    class="h1 fw-medium text-main mb-32 mt-16 pb-32 border-bottom border-gray-100 d-flex gap-4">
                                    $280 <span class="text-md text-gray-600">/year</span>
                                </h2>

                                <ul>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Intro video the course
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Interactive quizes
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Course curriculum
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Community supports
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Certificate of completion
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4 mb-20">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Sample lesson showcasing
                                    </li>
                                    <li class="flex-align gap-8 text-gray-600 mb-lg-4">
                                        <span class="text-24 d-flex text-main-600"><i
                                                class="ph ph-check-circle"></i></span>
                                        Access to course community
                                    </li>
                                </ul>
                                <a href="#"
                                    class="btn btn-outline-main w-100 rounded-pill py-16 border-main-300 text-17 fw-medium mt-32">Get
                                    Started</a>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-8 h6 mt-32">Terms & Policy</label>
                            <ul class="list-inside">
                                <li class="text-gray-600 mb-4">1. Set up multiple pricing levels with different features
                                    and functionalities to maximize revenue</li>
                                <li class="text-gray-600 mb-4">2. Continuously test different price points and discounts to
                                    find the sweet spot that resonates with your target audience</li>
                                <li class="text-gray-600 mb-4">3. Price your course based on the perceived value it
                                    provides to students, considering factors</li>
                            </ul>
                            <button type="button"
                                class="btn btn-main text-sm btn-sm px-24 rounded-pill py-12 d-flex align-items-center gap-2 mt-24"
                                data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="ph ph-plus me-4"></i>
                                Add New Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
        });

        $('#editCourseForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let id = $('#courseid').val();

            $.ajax({
                url: '/course/' + id,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data Kursus berhasil diupdate.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/course';
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });

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
