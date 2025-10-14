@extends('layouts.main')

@section('css')
    <style>
        /* --- Tabs Styling --- */
        .nav-pills .nav-link {
            color: #555;
            background: transparent;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            background-color: #4f46e5;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
        }

        .nav-pills .nav-link:hover {
            background-color: #e0e7ff;
            color: #4f46e5;
        }

        /* --- Search Input --- */
        #searchInput {
            border-radius: 30px;
            padding-left: 36px;
            transition: 0.3s;
        }

        #searchInput:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);
            border-color: #4f46e5;
        }

        /* --- Button Tambah --- */
        #addDataBtn {
            border-radius: 30px;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
            transition: 0.3s;
        }

        #addDataBtn:hover {
            transform: translateY(-2px);
        }

        /* --- Inner Card Section (My Course, Course For You) --- */
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

        /* --- Mentor Card --- */
        .mentor-card {
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: 0.3s ease;
        }

        .mentor-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .pagination nav svg {
            height: 1.25rem;
        }

        .pagination nav span,
        .pagination nav a {
            border-radius: 9999px;
            /* pill shape */
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a>
            </li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><span class="text-main-600 fw-normal text-15">Kursus</span></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-24 gap-3">
                <ul class="nav nav-pills gap-10 mb-0 p-1 bg-light rounded-3 shadow-sm" id="redeemTabs" role="tablist"
                    style="--bs-nav-pills-link-active-bg: #4f46e5;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active filter-tab rounded-pill px-16 py-6 fw-semibold" data-status="all"
                            type="button">Semua</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link filter-tab rounded-pill px-16 py-6 fw-semibold" data-status="draft"
                            type="button">Draft</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link filter-tab rounded-pill px-16 py-6 fw-semibold" data-status="published"
                            type="button">Published</button>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-10">
                    <div class="position-relative">
                        <input type="text" id="searchInput" class="form-control ps-20" placeholder="Cari data...">
                        <i
                            class="ph ph-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    </div>

                    {{-- Tombol Tambah Kursus - Hanya untuk Admin & Pengajar --}}
                    @if(canManageCourses())
                        <a href="{{ route('course.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
                            style="border-radius: 30px">
                            <i class="ph ph-plus-circle text-lg"></i> Tambah Kursus
                        </a>
                    @endif
                </div>
            </div>

            {{-- Layout untuk Admin --}}
            @if(isset($allCourses))
                <div class="card-section mt-24 p-3">
                    <div class="card-body">
                        <h4 class="mb-20">Semua Kursus</h4>
                        <div class="row g-20" id="courseContainer">
                            @forelse ($allCourses as $item)
                                @include('pages.course._partials.course-list', ['course' => $item])
                            @empty
                                <div class="text-center w-100 py-4">Belum ada kursus</div>
                            @endforelse
                        </div>
                        <div class="mt-10 d-flex justify-content-center">
                            {{ $allCourses->withQueryString()->onEachSide(1)->links('pages.course._partials.pagination') }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Layout untuk Pengajar --}}
            @if(isset($myCourses) && isset($otherCourses))
                <div class="card-section mt-24 p-3">
                    <div class="card-body">
                        <h4 class="mb-20">My Course</h4>
                        <div class="row g-20" id="courseContainer">
                            @forelse ($myCourses as $item)
                                @include('pages.course._partials.course-list', ['course' => $item])
                            @empty
                                <div class="text-center w-100 py-4">Belum ada kursus</div>
                            @endforelse
                        </div>
                        <div class="mt-10 d-flex justify-content-center">
                            {{ $myCourses->withQueryString()->onEachSide(1)->links('pages.course._partials.pagination') }}
                        </div>
                    </div>
                </div>

                <div class="card-section mt-24 p-3">
                    <div class="card-body">
                        <h4 class="mb-20">Course For You</h4>
                        <div class="row g-20">
                            @forelse ($otherCourses as $item)
                                @include('pages.course._partials.course-list', ['course' => $item])
                            @empty
                                <div class="text-center w-100 py-4">Belum ada kursus lain</div>
                            @endforelse
                        </div>
                        <div class="mt-10 d-flex justify-content-center">
                            {{ $otherCourses->withQueryString()->onEachSide(1)->links('pages.course._partials.pagination') }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Layout untuk Karyawan --}}
            @if(isset($availableCourses))
                <div class="card-section mt-24 p-3">
                    <div class="card-body">
                        <h4 class="mb-20">Kursus Tersedia</h4>
                        <div class="row g-20" id="courseContainer">
                            @forelse ($availableCourses as $item)
                                @include('pages.course._partials.course-list', ['course' => $item])
                            @empty
                                <div class="text-center w-100 py-4">Belum ada kursus tersedia</div>
                            @endforelse
                        </div>
                        <div class="mt-10 d-flex justify-content-center">
                            {{ $availableCourses->withQueryString()->onEachSide(1)->links('pages.course._partials.pagination') }}
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        let statusFilter = 'all';
        let searchQuery = '';


        $(document).on('click', '.filter-tab', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            statusFilter = $(this).data('status');
            loadCourses(1);
        });

        $('#searchInput').on('keyup', function() {
            searchQuery = $(this).val();
            loadCourses(1);
        });


        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadCourses(page);
        });

        function loadCourses(page = 1) {
            $.ajax({
                url: "{{ route('course') }}" +
                    "?page=" + page +
                    "&status=" + statusFilter +
                    "&search=" + searchQuery,
                type: "GET",
                beforeSend: function() {
                    // Update all course containers
                    $('.row.g-20').html('<div class="text-center p-5 text-muted">Loading...</div>');
                },
                success: function(response) {
                    // Replace entire card content to handle different role layouts
                    let newContent = $(response).find('.card-body').html();
                    $('.card .card-body').html(newContent);
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                },
                error: function() {
                    $('.row.g-20').html('<div class="text-center p-5 text-danger">Terjadi kesalahan saat memuat data.</div>');
                }
            });
        }
    });
</script>
@endsection
