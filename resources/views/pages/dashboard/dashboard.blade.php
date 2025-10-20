@extends('layouts.main')


@section('content')
    <div class="row gy-4">
        <div class="col-lg-9">
            <!-- Grettings Box Start -->
            <div class="grettings-box position-relative rounded-16 overflow-hidden gap-16 flex-wrap z-1">
                <img src="assets/images/bg/welcome-bg1.png" alt="b-welcome"
                    class="position-absolute inset-block-start-0 inset-inline-start-0 z-n1 w-100 h-100 opacity-6">
                <div class="row gy-4">
                    <div class="col-sm-7">
                        <div class="grettings-box__content py-xl-4">
                            <h2 class="text-white mb-0">Hello, {{ Auth::user()->name}} </h2>
                            <p class="text-15 fw-light mt-4 text-white">Let’s learning something today</p>
                            <p class="text-lg fw-light mt-24 text-white">Set your study plan and growth with
                                community</p>
                        </div>
                    </div>
                    <div class="col-sm-5 d-sm-block d-none">
                        <div class="text-center h-100 d-flex justify-content-center align-items-end ">
                            <img src="assets/images/bg/welcome-imagelms.webp" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Grettings Box End -->

            <!-- Widgets Start -->
            <div class="row gy-4 mt-10">
                @if(function_exists('isAdmin') && isAdmin())
                    <!-- Admin Widgets: System-wide aggregates -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $adminTotalCourses ?? 0 }}</h4>
                                <span class="text-gray-600">Total Kursus</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-600 text-white text-2xl">
                                        <i class="ph-fill ph-book-open"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $adminTotalUsers ?? 0 }}</h4>
                                <span class="text-gray-600">Total Pengguna</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-two-600 text-white text-2xl">
                                        <i class="ph-fill ph-users-three"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $adminTotalCompletions ?? 0 }}</h4>
                                <span class="text-gray-600">Total Penyelesaian</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-purple-600 text-white text-2xl">
                                        <i class="ph-fill ph-graduation-cap"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $adminTotalInstructors ?? 0 }}</h4>
                                <span class="text-gray-600">Total Pengajar</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-600 text-white text-2xl">
                                        <i class="ph-fill ph-chalkboard-teacher"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(function_exists('isAdmin') && isPengajar())
                    <!-- Pengajar Widgets: Creator-centric -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $createdCoursesCount ?? 0 }}</h4>
                                <span class="text-gray-600">Created Courses</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-600 text-white text-2xl">
                                        <i class="ph-fill ph-book-open"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $assignedStudentsCount ?? 0 }}</h4>
                                <span class="text-gray-600">Assigned Students</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-two-600 text-white text-2xl">
                                        <i class="ph-fill ph-users-three"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $graduatedStudentsCount ?? 0 }}</h4>
                                <span class="text-gray-600">Lulusan Kelas</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-purple-600 text-white text-2xl">
                                        <i class="ph-fill ph-graduation-cap"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $createdStudyTime ?? '00:00' }}</h4>
                                <span class="text-gray-600">Total Waktu Pelajaran Dibuat</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-600 text-white text-2xl">
                                        <i class="ph-fill ph-clock"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Karyawan Widgets: Learner-centric -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $completedCoursesCount }}</h4>
                                <span class="text-gray-600">Completed Courses</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-600 text-white text-2xl"><i class="ph-fill ph-book-open"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $inProgressCoursesCount }}</h4>
                                <span class="text-gray-600">Course in Progress</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-two-600 text-white text-2xl"><i class="ph-fill ph-certificate"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $earnedCertificatesCount }}</h4>
                                <span class="text-gray-600">Earned Certificate</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-purple-600 text-white text-2xl">
                                        <i class="ph-fill ph-graduation-cap"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">{{ $studyTime }}</h4>
                                <span class="text-gray-600">Study Time</span>
                                <div class="flex-between gap-8 mt-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-600 text-white text-2xl"><i class="ph-fill ph-clock"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Widgets End -->

            <!-- Table Start -->
            <div class="card mt-24 overflow-hidden">
                <div class="card-header">
                    <div class="mb-0 flex-between flex-wrap gap-8">
                        @if(function_exists('isAdmin') && isAdmin())
                            <h4 class="mb-0">Aktivitas Kursus Terbaru</h4>
                        @elseif(function_exists('isAdmin') && isPengajar())
                            <h4 class="mb-0">Created Courses</h4>
                        @else
                            <h4 class="mb-0">My Courses</h4>
                            <a href="{{ route('course') }}"
                                class="text-13 fw-medium text-main-600 hover-text-decoration-underline">See All</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-16 py-0">
                    @if(function_exists('isAdmin') && isAdmin())
                        @php $list = $recentCourses ?? collect(); @endphp
                        @if($list->count() > 0)
                            <!-- Admin: Recent Course Activities -->
                            <div class="d-none d-lg-block">
                                <table class="table style-two mb-0 mycourses-table" style="background-color: white; width: 100%; table-layout: fixed;">
                                    <colgroup>
                                        <col style="width: 360px">
                                        <col style="width: 160px">
                                        <col style="width: 160px">
                                        <col style="width: 140px">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th class="text-center">Participants</th>
                                            <th>Created / Updated</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($list as $course)
                                            <tr>
                                                <td class="align-middle" style="white-space: normal !important;">
                                                    <div class="flex-align gap-8">
                                                        <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0 overflow-hidden">
                                                            <img src="{{ asset('storage' . '/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-100 h-100 object-fit-cover">
                                                        </div>
                                                        <div style="min-width: 0;">
                                                            <h6 class="mb-0" title="{{ $course['title'] }}" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient: vertical; overflow:hidden; text-overflow: ellipsis; white-space: normal; word-break: break-word; overflow-wrap: anywhere;">{{ $course['title'] }}</h6>
                                                            <div class="table-list">
                                                                <span class="text-13 text-gray-600">{{ $course['category'] }} | {{ $course['author'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">{{ number_format($course['participants']) }}</td>
                                                <td class="align-middle">
                                                    <div class="text-13 text-gray-600">
                                                        Dibuat: {{ \Carbon\Carbon::parse($course['created_at'])->format('d M Y') }}
                                                    </div>
                                                    <div class="text-13 text-gray-600">
                                                        Diperbarui: {{ \Carbon\Carbon::parse($course['updated_at'])->format('d M Y') }}
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @php $isDraft = ($course['status'] ?? 'draft') === 'draft'; @endphp
                                                    @if($isDraft)
                                                        <span class="text-13 py-2 px-8 bg-gray-50 text-gray-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                            <span class="w-6 h-6 bg-gray-400 rounded-circle flex-shrink-0"></span>
                                                            Draft
                                                        </span>
                                                    @else
                                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                            <span class="w-6 h-6 bg-success-600 rounded-circle flex-shrink-0"></span>
                                                            Publish
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile list -->
                            <div class="d-block d-lg-none">
                                @foreach($list as $course)
                                    <div class="p-16 border-bottom border-gray-100">
                                        <div class="d-flex align-items-center gap-12">
                                            <div class="w-48 h-48 rounded-12 bg-main-600 flex-center flex-shrink-0 overflow-hidden">
                                                <img src="{{ asset('storage' . '/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-100 h-100 object-fit-cover">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-4" title="{{ $course['title'] }}" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient: vertical; overflow:hidden; text-overflow: ellipsis; white-space: normal; word-break: break-word; overflow-wrap: anywhere;">{{ $course['title'] }}</h6>
                                                <div class="text-13 text-gray-600 mb-8">{{ $course['category'] }} | {{ $course['author'] }}</div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="badge bg-main-50 text-main-600">{{ number_format($course['participants']) }} Peserta</span>
                                                    @php $isDraft = ($course['status'] ?? 'draft') === 'draft'; @endphp
                                                    @if($isDraft)
                                                        <span class="text-13 py-2 px-8 bg-gray-50 text-gray-600 rounded-pill">Draft</span>
                                                    @else
                                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 rounded-pill">Publish</span>
                                                    @endif
                                                </div>
                                                <div class="text-12 text-gray-500 mt-8">
                                                    Dibuat: {{ \Carbon\Carbon::parse($course['created_at'])->format('d M Y') }} • Diperbarui: {{ \Carbon\Carbon::parse($course['updated_at'])->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-40 px-24">
                                <div class="w-80 h-80 bg-gray-50 rounded-circle mx-auto flex-center mb-16">
                                    <i class="ph ph-books text-48 text-gray-400"></i>
                                </div>
                                <h5 class="text-gray-600 mb-8">Belum ada aktivitas kursus</h5>
                                <p class="text-gray-500 mb-0">Kursus terbaru akan muncul di sini.</p>
                            </div>
                        @endif
                    @elseif(function_exists('isAdmin') && isPengajar())
                        @php $list = $createdCourses ?? collect(); @endphp
                        @if($list->count() > 0)
                            <!-- Admin/Pengajar: Created courses table -->
                            <div class="d-none d-lg-block">
                                <table class="table style-two mb-0 mycourses-table" style="background-color: white; width: 100%; table-layout: fixed;">
                                    <colgroup>
                                        <col style="width: 360px">
                                        <col style="width: 160px">
                                        <col style="width: 160px">
                                        <col style="width: 140px">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th class="text-center">Participants</th>
                                            <th>Created / Updated</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($list as $course)
                                            <tr>
                                                <td class="align-middle" style="white-space: normal !important;">
                                                    <div class="flex-align gap-8">
                                                        <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0 overflow-hidden">
                                                            <img src="{{ asset('storage' . '/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-100 h-100 object-fit-cover">
                                                        </div>
                                                        <div style="min-width: 0;">
                                                            <h6 class="mb-0" title="{{ $course['title'] }}" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient: vertical; overflow:hidden; text-overflow: ellipsis; white-space: normal; word-break: break-word; overflow-wrap: anywhere;">{{ $course['title'] }}</h6>
                                                            <div class="table-list">
                                                                <span class="text-13 text-gray-600">{{ $course['category'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">{{ number_format($course['participants']) }}</td>
                                                <td class="align-middle">
                                                    <div class="text-13 text-gray-600">
                                                        Dibuat: {{ \Carbon\Carbon::parse($course['created_at'])->format('d M Y') }}
                                                    </div>
                                                    <div class="text-13 text-gray-600">
                                                        Diperbarui: {{ \Carbon\Carbon::parse($course['updated_at'])->format('d M Y') }}
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @php $isDraft = ($course['status'] ?? 'draft') === 'draft'; @endphp
                                                    @if($isDraft)
                                                        <span class="text-13 py-2 px-8 bg-gray-50 text-gray-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                            <span class="w-6 h-6 bg-gray-400 rounded-circle flex-shrink-0"></span>
                                                            Draft
                                                        </span>
                                                    @else
                                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                            <span class="w-6 h-6 bg-success-600 rounded-circle flex-shrink-0"></span>
                                                            Publish
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile list -->
                            <div class="d-block d-lg-none">
                                @foreach($list as $course)
                                    <div class="p-16 border-bottom border-gray-100">
                                        <div class="d-flex align-items-center gap-12">
                                            <div class="w-48 h-48 rounded-12 bg-main-600 flex-center flex-shrink-0 overflow-hidden">
                                                <img src="{{ asset('storage' . '/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-100 h-100 object-fit-cover">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-4" title="{{ $course['title'] }}" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient: vertical; overflow:hidden; text-overflow: ellipsis; white-space: normal; word-break: break-word; overflow-wrap: anywhere;">{{ $course['title'] }}</h6>
                                                <div class="text-13 text-gray-600 mb-8">{{ $course['category'] }}</div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="badge bg-main-50 text-main-600">{{ number_format($course['participants']) }} Peserta</span>
                                                    @php $isDraft = ($course['status'] ?? 'draft') === 'draft'; @endphp
                                                    @if($isDraft)
                                                        <span class="text-13 py-2 px-8 bg-gray-50 text-gray-600 rounded-pill">Draft</span>
                                                    @else
                                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 rounded-pill">Publish</span>
                                                    @endif
                                                </div>
                                                <div class="text-12 text-gray-500 mt-8">
                                                    Dibuat: {{ \Carbon\Carbon::parse($course['created_at'])->format('d M Y') }} • Diperbarui: {{ \Carbon\Carbon::parse($course['updated_at'])->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-40 px-24">
                                <div class="w-80 h-80 bg-gray-50 rounded-circle mx-auto flex-center mb-16">
                                    <i class="ph ph-books text-48 text-gray-400"></i>
                                </div>
                                <h5 class="text-gray-600 mb-8">Belum ada kursus yang dibuat</h5>
                                <p class="text-gray-500 mb-0">Buat kursus pertama Anda dan mulai mengundang peserta.</p>
                            </div>
                        @endif
                    @elseif($enrolledCourses->count() > 0)
                        <!-- Desktop / Large screens: keep table -->
                        <div class="d-none d-lg-block">
                            <table class="table style-two mb-0 mycourses-table" style="background-color: white; width: 100%; table-layout: fixed;">
                                <colgroup>
                                    <col style="width: 320px">
                                    <col style="width: 170px">
                                    <col style="width: 170px">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Progress</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledCourses as $course)
                                        <tr>
                                            <td class="align-middle" style="white-space: normal !important;">
                                                <div class="flex-align gap-8">
                                                    <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0 overflow-hidden">
                                                        <img src="{{ asset('storage' . '/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-100 h-100 object-fit-cover">
                                                    </div>
                                                    <div style="min-width: 0;">
                                                        <h6 class="mb-0" title="{{ $course['title'] }}" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient: vertical; overflow:hidden; text-overflow: ellipsis; white-space: normal; word-break: break-word; overflow-wrap: anywhere;">{{ $course['title'] }}</h6>
                                                        <div class="table-list">
                                                            <span class="text-13 text-gray-600">{{ $course['category'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="white-space: nowrap; overflow: hidden; padding-right: 16px;">
                                                <div class="flex-align gap-8 mt-12" style="max-width: 170px;">
                                                    <div class="progress flex-shrink-0 bg-main-100 rounded-pill h-4" style="width: 100px;" role="progressbar"
                                                        aria-label="Progress" aria-valuenow="{{ $course['progress'] }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        <div class="progress-bar bg-main-600 rounded-pill" style="width: {{ $course['progress'] }}%"></div>
                                                    </div>
                                                    <span class="text-main-600 flex-shrink-0 text-13 fw-medium">{{ $course['progress'] }}%</span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle" style="padding-left: 16px;">
                                                <div class="flex-align justify-content-center gap-12">
                                                    @if($course['is_completed'])
                                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                            <span class="w-6 h-6 bg-success-600 rounded-circle flex-shrink-0"></span>
                                                            Completed
                                                        </span>
                                                    @else
                                                        <span class="text-13 py-2 px-8 bg-warning-50 text-warning-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                            <span class="w-6 h-6 bg-warning-600 rounded-circle flex-shrink-0"></span>
                                                            In Progress
                                                        </span>
                                                    @endif
                                                    <a href="{{ route('course.show', $course['id']) }}"
                                                        class="text-gray-900 hover-text-main-600 text-md d-flex">
                                                        <i class="ph ph-caret-right"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile / Small screens: card list, no horizontal scroll -->
                        <div class="d-block d-lg-none">
                            @foreach($enrolledCourses as $course)
                                <div class="p-16 border-bottom border-gray-100">
                                    <div class="d-flex align-items-center gap-12">
                                        <div class="w-48 h-48 rounded-12 bg-main-600 flex-center flex-shrink-0 overflow-hidden">
                                            <img src="{{ asset('storage' . '/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-4" title="{{ $course['title'] }}" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient: vertical; overflow:hidden; text-overflow: ellipsis; white-space: normal; word-break: break-word; overflow-wrap: anywhere;">{{ $course['title'] }}</h6>
                                            <div class="text-13 text-gray-600 mb-8">{{ $course['category'] }}</div>
                                            <div class="d-flex align-items-center gap-8 mt-8">
                                                <div class="progress w-100 bg-main-100 rounded-pill h-6" role="progressbar"
                                                    aria-label="Progress" aria-valuenow="{{ $course['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-main-600 rounded-pill" style="width: {{ $course['progress'] }}%"></div>
                                                </div>
                                                <span class="text-main-600 flex-shrink-0 text-13 fw-medium">{{ $course['progress'] }}%</span>
                                            </div>
                                            <div class="mt-10">
                                                @if($course['is_completed'])
                                                    <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                        <span class="w-6 h-6 bg-success-600 rounded-circle flex-shrink-0"></span>
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="text-13 py-2 px-8 bg-warning-50 text-warning-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                                        <span class="w-6 h-6 bg-warning-600 rounded-circle flex-shrink-0"></span>
                                                        In Progress
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('course.show', $course['id']) }}" class="ms-8 text-gray-900 hover-text-main-600 text-xl d-flex flex-shrink-0">
                                            <i class="ph ph-caret-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-40 px-24">
                            <div class="w-80 h-80 bg-gray-50 rounded-circle mx-auto flex-center mb-16">
                                <i class="ph ph-book-open text-48 text-gray-400"></i>
                            </div>
                            <h5 class="text-gray-600 mb-8">No Enrolled Courses Yet</h5>
                            <p class="text-gray-500 mb-20">Start your learning journey by enrolling in a course!</p>
                            <a href="{{ route('course') }}" class="btn btn-main px-24 py-12">
                                <i class="ph ph-book-open me-2"></i>
                                Browse Courses
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Table End -->


        </div>
        <div class="col-lg-3">

            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="cover-img position-relative">
                        <div class="avatar-upload">
                            <input type='file' id="coverImageUpload" accept=".png, .jpg, .jpeg">
                            <div class="avatar-preview">
                                <div id="coverImagePreview"
                                     style="background-image: url('{{ asset('assets/images/bg/Logo B-Learning-15.webp') }}');">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="setting-profile px-24">
                        <div class="d-flex align-items-end flex-wrap mb-32 gap-24">
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}"
                                 class="w-120 h-120 rounded-circle border border-white object-fit-cover"
                                 onerror="this.src='{{ Auth::user()->getProfilePhotoUrlAttribute() }}'">
                            <div>
                                <h4 class="mb-8">{{ Auth::user()->name}}</h4>
                                <div class="setting-profile__infos flex-align flex-wrap gap-16">
                                    @if(Auth::user()->position)
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i class="ph ph-briefcase"></i></span>
                                            <span class="text-gray-600 d-flex text-15">{{ Auth::user()->position }}</span>
                                        </div>
                                    @endif
                                    @if(Auth::user()->division)
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i class="ph ph-buildings"></i></span>
                                            <span class="text-gray-600 d-flex text-15">{{ Auth::user()->division }}</span>
                                        </div>
                                    @endif
                                    @if(Auth::user()->location)
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i class="ph ph-map-pin"></i></span>
                                            <span class="text-gray-600 d-flex text-15">{{ Auth::user()->location->name }}</span>
                                        </div>
                                    @endif
                                    @if(Auth::user()->join_date)
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i class="ph ph-calendar-dots"></i></span>
                                            <span class="text-gray-600 d-flex text-15">Join {{ \Carbon\Carbon::parse(Auth::user()->join_date)->format('F Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(function_exists('isAdmin') && isAdmin())
                <!-- Widget 1: Shortcut Cepat -->
                <div class="card mt-24">
                    <div class="card-header border-bottom border-gray-100 flex-between gap-8 flex-wrap">
                        <h5 class="mb-0">Shortcut Cepat</h5>
                    </div>
                    <div class="card-body">
                        @php
                            // Users create (existing: users.create)
                            $createUserUrl = \Illuminate\Support\Facades\Route::has('users.create')
                                ? route('users.create')
                                : (\Illuminate\Support\Facades\Route::has('admin.users.create')
                                    ? route('admin.users.create')
                                    : url('/users/create'));

                            // Course create shortcut di-nonaktifkan untuk Admin
                            $createCourseUrl = null;

                            // Reports index (may not exist yet)
                            $reportsRouteName = \Illuminate\Support\Facades\Route::has('admin.reports.index') ? 'admin.reports.index'
                                : (\Illuminate\Support\Facades\Route::has('reports.index') ? 'reports.index' : null);
                            $reportsUrl = $reportsRouteName ? route($reportsRouteName) : '#';
                            $reportsDisabled = $reportsRouteName === null;
                        @endphp
                        <div class="d-grid gap-12">
                            <a href="{{ $createUserUrl }}" class="btn btn-main w-100">
                                <i class="ph ph-user-plus me-2"></i>
                                + Buat Pengguna Baru
                            </a>
                            <button class="btn btn-success w-100" disabled title="Admin tidak dapat membuat kursus">
                                <i class="ph ph-book-open me-2"></i>
                                + Buat Kursus Baru
                            </button>
                            <a href="{{ $reportsUrl }}" class="btn btn-outline-main w-100 {{ $reportsDisabled ? 'disabled' : '' }}" {{ $reportsDisabled ? 'aria-disabled=true tabindex=-1 title=\'Halaman laporan belum tersedia\'' : '' }}>
                                <i class="ph ph-chart-line-up me-2"></i>
                                Lihat Laporan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Widget 2: Aktivitas Admin Terbaru -->
                <div class="card mt-24">
                    <div class="card-header border-bottom border-gray-100 flex-between gap-8 flex-wrap">
                        <h5 class="mb-0">Aktivitas Admin Terbaru</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $logs = ($recentLogs ?? collect());
                            // Helper to decide icon/color based on action
                            $resolveLogMeta = function($action) {
                                $map = [
                                    'user.created' => ['icon' => 'ph-user-plus', 'bg' => 'bg-main-50', 'fg' => 'text-main-600', 'badge' => 'User'],
                                    'user.updated' => ['icon' => 'ph-pencil', 'bg' => 'bg-warning-50', 'fg' => 'text-warning-600', 'badge' => 'User'],
                                    'user.deleted' => ['icon' => 'ph-trash', 'bg' => 'bg-danger-50', 'fg' => 'text-danger-600', 'badge' => 'User'],
                                    'course.created' => ['icon' => 'ph-book-open', 'bg' => 'bg-main-50', 'fg' => 'text-main-600', 'badge' => 'Course'],
                                    'course.updated' => ['icon' => 'ph-pencil-line', 'bg' => 'bg-warning-50', 'fg' => 'text-warning-600', 'badge' => 'Course'],
                                    'course.published' => ['icon' => 'ph-megaphone', 'bg' => 'bg-success-50', 'fg' => 'text-success-600', 'badge' => 'Course'],
                                    'course.unpublished' => ['icon' => 'ph-megaphone-slash', 'bg' => 'bg-gray-50', 'fg' => 'text-gray-600', 'badge' => 'Course'],
                                    'course.participants_updated' => ['icon' => 'ph-users-three', 'bg' => 'bg-purple-50', 'fg' => 'text-purple-600', 'badge' => 'Course'],
                                ];
                                return $map[$action] ?? ['icon' => 'ph-activity', 'bg' => 'bg-gray-50', 'fg' => 'text-gray-700', 'badge' => 'Activity'];
                            };
                        @endphp

                        @if($logs->count() > 0)
                            <div class="position-relative ps-24">
                                <span class="position-absolute start-8 top-0 bottom-0 w-2 bg-gray-100 rounded-pill"></span>
                                <ul class="list-unstyled mb-0">
                                    @foreach($logs as $log)
                                        @php $meta = $resolveLogMeta($log['action'] ?? ''); @endphp
                                        <li class="d-flex gap-12 align-items-start mb-16 position-relative">
                                            <span class="position-absolute start-0 translate-middle-x w-10 h-10 rounded-circle bg-white border border-gray-200"></span>
                                            <div class="flex-shrink-0 {{ $meta['bg'] }} rounded-12 w-36 h-36 d-flex align-items-center justify-content-center">
                                                <i class="ph {{ $meta['icon'] }} {{ $meta['fg'] }} text-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-8 flex-wrap">
                                                    @if(!empty($log['causer_name']))
                                                        <span class="fw-semibold text-gray-900 text-14">{{ $log['causer_name'] }}</span>
                                                    @endif
                                                    <span class="badge rounded-pill px-8 py-4 text-11 {{ $meta['bg'] }} {{ $meta['fg'] }}">{{ $meta['badge'] }}</span>
                                                    <span class="text-12 text-gray-500">{{ \Carbon\Carbon::parse($log['time'])->diffForHumans() }}</span>
                                                </div>
                                                <div class="text-14 text-gray-800 mt-4" style="word-break: break-word; overflow-wrap: anywhere;">
                                                    {{ $log['description'] ?? $log['text'] }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-56 h-56 bg-gray-50 rounded-circle mx-auto flex-center mb-10">
                                    <i class="ph ph-activity text-28 text-gray-400"></i>
                                </div>
                                <div class="text-gray-600">Belum ada aktivitas.</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Widget 3: Ringkasan Sistem -->
                <div class="card mt-24">
                    <div class="card-header border-bottom border-gray-100 flex-between gap-8 flex-wrap">
                        <h5 class="mb-0">Ringkasan Sistem</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-10">
                            <span class="text-gray-600">Online Users</span>
                            <span class="fw-semibold">{{ $onlineUserCount ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-10">
                            <span class="text-gray-600">Versi Aplikasi</span>
                            <span class="fw-semibold">{{ $appVersion ?? 'v1.0.0' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-gray-600">Status Server</span>
                            @php $status = ($serverStatus ?? 'Online'); @endphp
                            <span class="fw-semibold {{ strtolower($status) === 'online' ? 'text-success' : 'text-danger' }}">{{ $status }}</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Non-admin sidebar (tetap seperti sebelumnya: Points & IDP) -->
                <!-- Point Start -->
                <div class="card mt-24">
                    <div class="card-header border-bottom border-gray-100 flex-between gap-8 flex-wrap">
                        <h5 class="mb-0">My Learning Points</h5>
                        <span class="badge bg-warning-600 text-white px-12 py-6 rounded-pill">
                            <i class="ph-fill ph-trophy me-1"></i>
                            {{ number_format(Auth::user()->getTotalPoints()) }} Points
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Total Points Display -->
                        <div class="text-center mb-24">
                            <div class="w-120 h-120 bg-warning-50 rounded-circle mx-auto flex-center mb-16">
                                <i class="ph-fill ph-trophy text-64 text-warning-600"></i>
                            </div>
                            <h2 class="mb-8 text-warning-600">{{ number_format(Auth::user()->getTotalPoints()) }}</h2>
                            <p class="text-gray-600 mb-0">Total Learning Points</p>
                        </div>
                        @php
                            $user = Auth::user();
                            $lessonPoints = $user->pointLogs()->where('related_type', 'App\\Models\\Lesson')->sum('points_earned');
                            $quizPoints = $user->pointLogs()->where('related_type', 'App\\Models\\Quiz')->sum('points_earned');
                            $coursePoints = $user->pointLogs()->where('related_type', 'App\\Models\\Course')->sum('points_earned');
                            $totalTransactions = $user->pointLogs()->count();
                        @endphp
                        <div class="border-top border-gray-100 pt-20">
                            <h6 class="mb-16 text-gray-900">Point Activities</h6>
                            <div class="mb-12 flex-between">
                                <div class="flex-align gap-8">
                                    <span class="w-32 h-32 bg-main-50 rounded-circle flex-center text-main-600">
                                        <i class="ph-fill ph-book-open"></i>
                                    </span>
                                    <span class="text-gray-600 text-sm">Lessons Completed</span>
                                </div>
                                <span class="text-main-600 fw-bold">+{{ number_format($lessonPoints) }}</span>
                            </div>
                            <div class="mb-12 flex-between">
                                <div class="flex-align gap-8">
                                    <span class="w-32 h-32 bg-success-50 rounded-circle flex-center text-success-600">
                                        <i class="ph-fill ph-check-circle"></i>
                                    </span>
                                    <span class="text-gray-600 text-sm">Quizzes Passed</span>
                                </div>
                                <span class="text-success-600 fw-bold">+{{ number_format($quizPoints) }}</span>
                            </div>
                            <div class="mb-12 flex-between">
                                <div class="flex-align gap-8">
                                    <span class="w-32 h-32 bg-warning-50 rounded-circle flex-center text-warning-600">
                                        <i class="ph-fill ph-certificate"></i>
                                    </span>
                                    <span class="text-gray-600 text-sm">Courses Completed</span>
                                </div>
                                <span class="text-warning-600 fw-bold">+{{ number_format($coursePoints) }}</span>
                            </div>
                            <div class="border-top border-gray-100 mt-16 pt-16">
                                <div class="flex-between">
                                    <span class="text-gray-900 fw-semibold">Total Transactions</span>
                                    <span class="badge bg-primary-50 text-primary-600 px-12 py-4 rounded-pill">
                                        {{ $totalTransactions }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Point End -->

                <!-- IDP Start -->
                <div class="card mt-24">
                    <div class="card-header border-bottom border-gray-100 flex-between gap-8 flex-wrap">
                        <h5 class="mb-0">My IDP</h5>
                        <div class="dropdown flex-shrink-0">
                            <button class="text-gray-400 text-xl d-flex rounded-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ph-fill ph-dots-three-outline"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu--md border-0 bg-transparent p-0">
                                <div class="card border border-gray-100 rounded-12 box-shadow-custom">
                                    <div class="card-body p-12">
                                        <div class="max-h-200 overflow-y-auto scroll-sm pe-8">
                                            <ul>
                                                <li class="mb-0">
                                                    <a href="students.html" class="py-6 text-15 px-8 hover-bg-gray-50 text-gray-300 w-100 rounded-8 fw-normal text-xs d-block text-start">
                                                        <span class="text"> <i class="ph ph-user me-4"></i> View Profile</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex-center">
                            <div id="activityDonutChart" class="w-auto d-inline-block"></div>
                        </div>
                        <h2 style="text-align: center">IDP Needs Not Available</h2>
                    </div>
                </div>
                <!-- IDP End -->
            @endif



        </div>
    </div>
@endsection
