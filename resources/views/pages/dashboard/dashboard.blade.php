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
                            <h2 class="text-white mb-0">Hello, Mohib! </h2>
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
                            <h4 class="mb-2">155+</h4>
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
                            <h4 class="mb-2">39+</h4>
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
                            <h4 class="mb-2">25+</h4>
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
                            <h4 class="mb-2">18k+</h4>
                            <span class="text-gray-600">Study Time</span>
                            <div class="flex-between gap-8 mt-16">
                                <span
                                    class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-600 text-white text-2xl"><i
                                        class="ph-fill ph-users-three"></i></span>

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
                        <a href="student-courses.html"
                            class="text-13 fw-medium text-main-600 hover-text-decoration-underline">See All</a>
                    </div>
                </div>
                <div class="card-body p-0 overflow-x-auto scroll-sm scroll-sm-horizontal">
                    <table class="table style-two mb-0" style="background-color: white;">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Progress</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="flex-align gap-8">
                                        <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0">
                                            <img src="assets/images/icons/course-name-icon1.png" alt="">
                                        </div>
                                        <div class="">
                                            <h6 class="mb-0">Design Accesibility</h6>
                                            <div class="table-list">
                                                <span class="text-13 text-gray-600">Advanced</span>
                                                <span class="text-13 text-gray-600">12 Hours</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-align gap-8 mt-12">
                                        <div class="progress w-100px  bg-main-100 rounded-pill h-4" role="progressbar"
                                            aria-label="Basic example" aria-valuenow="32" aria-valuemin="0"
                                            aria-valuemax="100">
                                            <div class="progress-bar bg-main-600 rounded-pill" style="width: 32%"></div>
                                        </div>
                                        <span class="text-main-600 flex-shrink-0 text-13 fw-medium">32%</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-align justify-content-center gap-16">
                                        <span
                                            class="text-13 py-2 px-8 bg-warning-50 text-warning-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                            <span class="w-6 h-6 bg-warning-600 rounded-circle flex-shrink-0"></span>
                                            In Progress
                                        </span>
                                        <a href="assignment.html"
                                            class="text-gray-900 hover-text-main-600 text-md d-flex"><i
                                                class="ph ph-caret-right"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                <img src="assets/images/thumbs/setting-profile-img.jpg" alt=""
                                    class="w-120 h-120 rounded-circle border border-white">
                                <div>
                                    <h4 class="mb-8">Mohid Khan</h4>
                                    <div class="setting-profile__infos flex-align flex-wrap gap-16">
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i
                                                    class="ph ph-swatches"></i></span>
                                            <span class="text-gray-600 d-flex text-15">UX Designer</span>
                                        </div>
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i
                                                    class="ph ph-map-pin"></i></span>
                                            <span class="text-gray-600 d-flex text-15">Sans Fransisco</span>
                                        </div>
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i
                                                    class="ph ph-calendar-dots"></i></span>
                                            <span class="text-gray-600 d-flex text-15">Join August 2024</span>
                                        </div>
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
                    <h5 class="mb-0">My Learning Point</h5>
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
                                                        View</span>
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
                    <h2 style="text-align: center">70%</h2>

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
