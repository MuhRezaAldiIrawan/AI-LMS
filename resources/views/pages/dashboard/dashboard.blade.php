@extends('layouts.main')


@section('content')
    <div class="row gy-4">
        <div class="col-lg-9">
            <!-- Grettings Box Start -->
            <div class="grettings-box position-relative rounded-16 bg-main-600 overflow-hidden gap-16 flex-wrap z-1">
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
                <div class="col-xxl-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-2">{{ $completedCoursesCount }}</h4>
                            <span class="text-gray-600">Completed Courses</span>
                            <div class="flex-between gap-8 mt-16">
                                <span
                                    class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-600 text-white text-2xl"><i
                                        class="ph-fill ph-book-open"></i></span>
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
                                <span
                                    class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-two-600 text-white text-2xl"><i
                                        class="ph-fill ph-certificate"></i></span>
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
                                <span
                                    class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-purple-600 text-white text-2xl">
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
                                <span
                                    class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-600 text-white text-2xl"><i
                                        class="ph-fill ph-clock"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Widgets End -->

            <!-- Table Start -->
            <div class="card mt-24 overflow-hidden">
                <div class="card-header">
                    <div class="mb-0 flex-between flex-wrap gap-8">
                        <h4 class="mb-0">My Courses</h4>
                        <a href="{{ route('course') }}"
                            class="text-13 fw-medium text-main-600 hover-text-decoration-underline">See All</a>
                    </div>
                </div>
                <div class="card-body p-0 overflow-x-auto scroll-sm scroll-sm-horizontal">
                    @if($enrolledCourses->count() > 0)
                        <table class="table style-two mb-0" style="background-color: white;">
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
                                        <td>
                                            <div class="flex-align gap-8">
                                                <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0 overflow-hidden">
                                                    <img src="{{ asset('storage' . '/' . $course['thumbnail']) }}" alt="{{ $course['title'] }}" class="w-100 h-100 object-fit-cover">
                                                </div>
                                                <div class="">
                                                    <h6 class="mb-0">{{ $course['title'] }}</h6>
                                                    <div class="table-list">
                                                        <span class="text-13 text-gray-600">{{ $course['category'] }}</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-align gap-8 mt-12">
                                                <div class="progress w-100px bg-main-100 rounded-pill h-4" role="progressbar"
                                                    aria-label="Progress" aria-valuenow="{{ $course['progress'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    <div class="progress-bar bg-main-600 rounded-pill" style="width: {{ $course['progress'] }}%"></div>
                                                </div>
                                                <span class="text-main-600 flex-shrink-0 text-13 fw-medium">{{ $course['progress'] }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-align justify-content-center gap-16">
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
                                    style="background-image: url('assets/images/bg/bg-blearning.webp');">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="setting-profile px-24">
                        <div class="flex-between">
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
            </div>

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

                    <!-- Point Breakdown -->
                    <div class="border-top border-gray-100 pt-20">
                        <h6 class="mb-16 text-gray-900">Point Activities</h6>

                        @php
                            $user = Auth::user();
                            $lessonPoints = $user->pointLogs()->where('related_type', 'App\Models\Lesson')->sum('points_earned');
                            $quizPoints = $user->pointLogs()->where('related_type', 'App\Models\Quiz')->sum('points_earned');
                            $coursePoints = $user->pointLogs()->where('related_type', 'App\Models\Course')->sum('points_earned');
                            $totalTransactions = $user->pointLogs()->count();
                        @endphp

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

                    <!-- Point Earning Info -->
                    <div class="bg-main-50 rounded-8 p-16 mt-20">
                        <h6 class="text-sm mb-12 text-gray-900">
                            <i class="ph-fill ph-info text-main-600 me-2"></i>
                            How to Earn Points
                        </h6>
                        <ul class="list-unstyled mb-0 text-13 text-gray-600">
                            <li class="mb-8">
                                <i class="ph-fill ph-check-circle text-success-600 me-2"></i>
                                Complete 1 Lesson = <strong>5 points</strong>
                            </li>
                            <li class="mb-8">
                                <i class="ph-fill ph-check-circle text-success-600 me-2"></i>
                                Pass 1 Quiz = <strong>10 points</strong>
                            </li>
                            <li class="mb-0">
                                <i class="ph-fill ph-check-circle text-success-600 me-2"></i>
                                Complete Course = <strong>20 points</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Point End -->

            <!-- IDP Start -->
            <div class="card mt-24">
                <div class="card-header border-bottom border-gray-100 flex-between gap-8 flex-wrap">
                    <h5 class="mb-0">My IDP</h5>
                    <div class="dropdown flex-shrink-0">
                        <button class="text-gray-400 text-xl d-flex rounded-4" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="ph-fill ph-dots-three-outline"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu--md border-0 bg-transparent p-0">
                            <div class="card border border-gray-100 rounded-12 box-shadow-custom">
                                <div class="card-body p-12">
                                    <div class="max-h-200 overflow-y-auto scroll-sm pe-8">
                                        <ul>
                                            <li class="mb-0">
                                                <a href="students.html"
                                                    class="py-6 text-15 px-8 hover-bg-gray-50 text-gray-300 w-100 rounded-8 fw-normal text-xs d-block text-start">
                                                    <span class="text"> <i class="ph ph-user me-4"></i>
                                                        View Profile</span>
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



        </div>
    </div>
@endsection
