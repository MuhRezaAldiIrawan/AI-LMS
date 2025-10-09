@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
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
        <!-- My Details Tab start -->
        <div class="tab-pane fade show active" id="informasi-umum" role="tabpanel" aria-labelledby="informasi-umum-tab"
            tabindex="0">
            <div class="card mt-24">
                <div class="card-header border-bottom">
                    <h4 class="mb-4">Detail Kursus</h4>
                </div>
                <div class="card-body">

                    <form id="editCourseForm" enctype="multipart/form-data">
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
                                <label for="description" class="h7 mb-8 fw-semibold font-heading">Deskripsi</label>
                                <div class="position-relative">
                                    <textarea name="description" id="description" cols="30" rows="10" class="form-control py-9">
                                    {{ old('description', $course->description ?? '') }}
                                </textarea>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <label for="thumbnail" class="h7 mb-8 fw-semibold font-heading">Thumbnail</label>
                                <div id="fileUpload" class="fileUpload image-upload" name="thumbnail"
                                    data-preview="{{ !empty($course->thumbnail) ? asset('storage/' . $course->thumbnail) : '' }}">
                                </div>
                            </div>

                        </div>

                        <div class="flex-align justify-content-end gap-8 mt-16">
                            <a href="{{ route('course') }}" class="btn btn-outline-main rounded-pill py-9">Batal</a>
                            <button class="btn btn-main rounded-pill py-9" type="submit">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- My Details Tab End -->

        <!-- Profile Tab Start -->
        <div class="tab-pane fade" id="kurikulum" role="tabpanel" aria-labelledby="kurikulum-tab" tabindex="0">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <div class="card mt-24">
                        <div class="card-body">
                            <h6 class="mb-12">About Me</h6>
                            <p class="text-gray-600 text-15 rounded-8 border border-gray-100 p-16">Lorem ipsum dolor sit
                                amet, consectetur adipiscing elit. Commodo pellentesque massa tellus ac augue. Lectus arcu
                                at in in rhoncus malesuada ipsum turpis.</p>
                        </div>
                    </div>
                    <div class="card mt-24">
                        <div class="card-body">
                            <h6 class="mb-12">Recent Messages</h6>

                            <div class="rounded-8 border border-gray-100 p-16 mb-16">
                                <div class="comments-box__content flex-between gap-8">
                                    <div class="flex-align align-items-start gap-12">
                                        <img src="assets/images/thumbs/user-img1.png"
                                            class="w-32 h-32 rounded-circle object-fit-cover flex-shrink-0"
                                            alt="User Image">
                                        <div>
                                            <h6 class="text-lg mb-8">Michel Smith</h6>
                                            <p class="text-gray-600 text-15">Lorem ipsum dolor sit amet, consectetur
                                                adipiscing elit. Commodo pellentesque massa </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="flex-shrink-0 fw-bold text-13 text-main-600 flex-align gap-8 hover-text-main-800">Reply
                                        <i class="ph ph-arrow-bend-up-left d-flex text-lg"></i> </button>
                                </div>
                            </div>

                            <div class="rounded-8 border border-gray-100 p-16 mb-16">
                                <div class="comments-box__content flex-between gap-8">
                                    <div class="flex-align align-items-start gap-12">
                                        <img src="assets/images/thumbs/user-img5.png"
                                            class="w-32 h-32 rounded-circle object-fit-cover flex-shrink-0"
                                            alt="User Image">
                                        <div>
                                            <h6 class="text-lg mb-8">Zara Maliha</h6>
                                            <p class="text-gray-600 text-15">Lorem ipsum dolor sit amet, consectetur
                                                adipiscing elit. Commodo pellentesque massa </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="flex-shrink-0 fw-bold text-13 text-main-600 flex-align gap-8 hover-text-main-800">Reply
                                        <i class="ph ph-arrow-bend-up-left d-flex text-lg"></i> </button>
                                </div>
                            </div>

                            <div class="rounded-8 border border-gray-100 p-16 mb-16">
                                <div class="comments-box__content flex-between gap-8">
                                    <div class="flex-align align-items-start gap-12">
                                        <img src="assets/images/thumbs/user-img3.png"
                                            class="w-32 h-32 rounded-circle object-fit-cover flex-shrink-0"
                                            alt="User Image">
                                        <div>
                                            <h6 class="text-lg mb-8">Simon Doe</h6>
                                            <p class="text-gray-600 text-15">Lorem ipsum dolor sit amet, consectetur
                                                adipiscing elit. Commodo pellentesque massa </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="flex-shrink-0 fw-bold text-13 text-main-600 flex-align gap-8 hover-text-main-800">Reply
                                        <i class="ph ph-arrow-bend-up-left d-flex text-lg"></i> </button>
                                </div>
                            </div>

                            <div class="rounded-8 border border-gray-100 p-16 mb-16">
                                <div class="comments-box__content flex-between gap-8">
                                    <div class="flex-align align-items-start gap-12">
                                        <img src="assets/images/thumbs/user-img4.png"
                                            class="w-32 h-32 rounded-circle object-fit-cover flex-shrink-0"
                                            alt="User Image">
                                        <div>
                                            <h6 class="text-lg mb-8">Elejabeth Jenny</h6>
                                            <p class="text-gray-600 text-15">Lorem ipsum dolor sit amet, consectetur
                                                adipiscing elit. Commodo pellentesque massa </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="flex-shrink-0 fw-bold text-13 text-main-600 flex-align gap-8 hover-text-main-800">Reply
                                        <i class="ph ph-arrow-bend-up-left d-flex text-lg"></i> </button>
                                </div>
                            </div>

                            <div class="rounded-8 border border-gray-100 p-16 mb-16">
                                <div class="flex-between gap-8">
                                    <div class="flex-align align-items-start gap-12">
                                        <img src="assets/images/thumbs/user-img8.png"
                                            class="w-32 h-32 rounded-circle object-fit-cover flex-shrink-0"
                                            alt="User Image">
                                        <div>
                                            <h6 class="text-lg mb-8">Ronald Doe</h6>
                                            <p class="text-gray-600 text-15">Lorem ipsum dolor sit amet, consectetur
                                                adipiscing elit. Commodo pellentesque massa </p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        class="flex-shrink-0 fw-bold text-13 text-main-600 flex-align gap-8 hover-text-main-800">Reply
                                        <i class="ph ph-arrow-bend-up-left d-flex text-lg"></i> </button>
                                </div>
                            </div>

                            <a href="#"
                                class="flex-shrink-0 fw-bold text-13 text-main-600 flex-align gap-8 hover-text-main-800 hover-text-decoration-underline">
                                View All <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card mt-24">
                        <div class="card-body">
                            <h6 class="mb-12">Social Media</h6>
                            <ul class="flex-align flex-wrap gap-8">
                                <li>
                                    <a href="https://www.facebook.com"
                                        class="flex-center w-36 h-36 border border-main-600 text-main-600 rounded-circle text-xl hover-bg-main-100 hover-border-main-800"><i
                                            class="ph ph-facebook-logo"></i></a>
                                </li>
                                <li>
                                    <a href="https://www.google.com"
                                        class="flex-center w-36 h-36 border border-main-600 text-main-600 rounded-circle text-xl hover-bg-main-100 hover-border-main-800">
                                        <i class="ph ph-twitter-logo"></i></a>
                                </li>
                                <li>
                                    <a href="https://www.twitter.com"
                                        class="flex-center w-36 h-36 border border-main-600 text-main-600 rounded-circle text-xl hover-bg-main-100 hover-border-main-800"><i
                                            class="ph ph-linkedin-logo"></i></a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com"
                                        class="flex-center w-36 h-36 border border-main-600 text-main-600 rounded-circle text-xl hover-bg-main-100 hover-border-main-800"><i
                                            class="ph ph-instagram-logo"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mt-24">
                        <div class="card-body">
                            <div class="row gy-4">
                                <div class="col-xxl-4 col-xl-6 col-md-4 col-sm-6">
                                    <div class="statistics-card p-xl-4 p-16 flex-align gap-10 rounded-8 bg-main-50">
                                        <span
                                            class="text-white bg-main-600 w-36 h-36 rounded-circle flex-center text-xl flex-shrink-0"><i
                                                class="ph ph-users-three"></i></span>
                                        <div>
                                            <h4 class="mb-0">450k</h4>
                                            <span class="fw-medium text-main-600">Followers</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-xl-6 col-md-4 col-sm-6">
                                    <div class="statistics-card p-xl-4 p-16 flex-align gap-10 rounded-8 bg-info-50">
                                        <span
                                            class="text-white bg-info-600 w-36 h-36 rounded-circle flex-center text-xl flex-shrink-0"><i
                                                class="ph ph-users-three"></i></span>
                                        <div>
                                            <h4 class="mb-0">289k</h4>
                                            <span class="fw-medium text-info-600">Following</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-xl-6 col-md-4 col-sm-6">
                                    <div class="statistics-card p-xl-4 p-16 flex-align gap-10 rounded-8 bg-purple-50">
                                        <span
                                            class="text-white bg-purple-600 w-36 h-36 rounded-circle flex-center text-xl flex-shrink-0"><i
                                                class="ph ph-thumbs-up"></i></span>
                                        <div>
                                            <h4 class="mb-0">1256k</h4>
                                            <span class="fw-medium text-purple-600">Likes</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-24">
                                <div class="flex-align gap-8 flex-wrap mb-16">
                                    <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl">
                                        <i class="ph ph-phone"></i>
                                    </span>
                                    <div class="flex-align gap-8 flex-wrap text-gray-600">
                                        <span>+00 123 456 789</span>
                                        <span>+00 123 456 789</span>
                                    </div>
                                </div>
                                <div class="flex-align gap-8 flex-wrap mb-16">
                                    <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl">
                                        <i class="ph ph-envelope-simple"></i>
                                    </span>
                                    <div class="flex-align gap-8 flex-wrap text-gray-600">
                                        <span>exampleinfo1@mail.com,</span>
                                        <span>exampleinfo2@mail.com</span>
                                    </div>
                                </div>
                                <div class="flex-align gap-8 flex-wrap mb-16">
                                    <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl">
                                        <i class="ph ph-map-pin"></i>
                                    </span>
                                    <div class="flex-align gap-8 flex-wrap text-gray-600">
                                        <span>Inner Circular Road, New York City, 0123</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-24">
                        <div class="card-body">
                            <h6 class="mb-12">About Me</h6>
                            <div class="recent-post rounded-8 border border-gray-100 p-16 d-flex gap-12 mb-16">
                                <div class="d-inline-flex w-100 max-w-130 flex-shrink-0">
                                    <img src="assets/images/thumbs/recent-post-img1.png" alt=""
                                        class="rounded-6 cover-img max-w-130">
                                </div>
                                <div>
                                    <p class="text-gray-600 text-line-3">Lorem ipsum dolor sit amet, consectetur adipiscing
                                        elit. Commodo pellentesque massa tellus ac augue. Lectus arcu at in in rhoncus
                                        malesuada ipsum turpis.</p>
                                    <div class="flex-align gap-8 mt-24">
                                        <img src="assets/images/thumbs/user-img1.png" alt=""
                                            class="w-32 h-32 rounded-circle cover-img">
                                        <span class="text-gray-600 text-13">Michel Bruice</span>
                                    </div>
                                </div>
                            </div>
                            <div class="recent-post rounded-8 border border-gray-100 p-16 d-flex gap-12 mb-16">
                                <div class="d-inline-flex w-100 max-w-130 flex-shrink-0">
                                    <img src="assets/images/thumbs/recent-post-img2.png" alt=""
                                        class="rounded-6 cover-img max-w-130">
                                </div>
                                <div>
                                    <p class="text-gray-600 text-line-3">Lorem ipsum dolor sit amet, consectetur adipiscing
                                        elit. Commodo pellentesque massa tellus ac augue. Lectus arcu at in in rhoncus
                                        malesuada ipsum turpis.</p>
                                    <div class="flex-align gap-8 mt-24">
                                        <img src="assets/images/thumbs/user-img2.png" alt=""
                                            class="w-32 h-32 rounded-circle cover-img">
                                        <span class="text-gray-600 text-13">Sara Smith</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="mb-12 mt-24">Add New Post</h6>
                            <div class="editor style-two">
                                <div id="editorTwo">
                                    <p>Write something new...</p>
                                </div>
                            </div>

                            <div class="flex-between flex-wrap gap-8 mt-24">
                                <div class="flex-align flex-wrap gap-8">
                                    <button type="button"
                                        class="flex-center w-26 h-26 text-gray-600 bg-gray-50 hover-bg-gray-100 rounded-circle text-md">
                                        <i class="ph ph-smiley"></i>
                                    </button>
                                    <button type="button"
                                        class="flex-center w-26 h-26 text-gray-600 bg-gray-50 hover-bg-gray-100 rounded-circle text-md">
                                        <i class="ph ph-camera"></i>
                                    </button>
                                    <button type="button"
                                        class="flex-center w-26 h-26 text-gray-600 bg-gray-50 hover-bg-gray-100 rounded-circle text-md">
                                        <i class="ph ph-image"></i>
                                    </button>
                                    <button type="button"
                                        class="flex-center w-26 h-26 text-gray-600 bg-gray-50 hover-bg-gray-100 rounded-circle text-md">
                                        <i class="ph ph-video-camera"></i>
                                    </button>
                                    <button type="button"
                                        class="flex-center w-26 h-26 text-gray-600 bg-gray-50 hover-bg-gray-100 rounded-circle text-md">
                                        <i class="ph ph-google-drive-logo"></i>
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-main rounded-pill py-9"> Post Now</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Profile Tab End -->

        <!-- Password Tab Start -->
        <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab" tabindex="0">
            <div class="card mt-24">
                <div class="card-header border-bottom">
                    <h4 class="mb-4">Password Settings</h4>
                    <p class="text-gray-600 text-15">Please fill full details about yourself</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="#">
                                <div class="row gy-4">
                                    <div class="col-12">
                                        <label for="current-password" class="form-label mb-8 h6">Current Password</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control py-11" id="current-password"
                                                placeholder="Enter Current Password">
                                            <span
                                                class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash"
                                                id="#current-password"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="new-password" class="form-label mb-8 h6">New Password</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control py-11" id="new-password"
                                                placeholder="Enter New Password">
                                            <span
                                                class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash"
                                                id="#new-password"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="confirm-password" class="form-label mb-8 h6">Confirm Password</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control py-11" id="confirm-password"
                                                placeholder="Enter Confirm Password">
                                            <span
                                                class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash"
                                                id="#confirm-password"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-8 h6">Password Requirements:</label>
                                        <ul class="list-inside">
                                            <li class="text-gray-600 mb-4">At least one lowercase character</li>
                                            <li class="text-gray-600 mb-4">Minimum 8 characters long - the more, the better
                                            </li>
                                            <li class="text-gray-300 mb-4">At least one number, symbol, or whitespace
                                                character</li>
                                        </ul>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label mb-8 h6">Two-Step Verification</label>
                                        <ul>
                                            <li class="text-gray-600 mb-4 fw-semibold">Two-factor authentication is not
                                                enabled yet.</li>
                                            <li class="text-gray-600 mb-4 fw-medium">Two-factor authentication adds a layer
                                                of security to your account by requiring more than just a password to log
                                                in. Learn more.</li>
                                        </ul>
                                        <button type="submit" class="btn btn-main rounded-pill py-9 mt-24">Enable
                                            two-factor authentication</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12">
                            <div class="flex-align justify-content-end gap-8">
                                <button type="reset"
                                    class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9">Cancel</button>
                                <button type="submit" class="btn btn-main rounded-pill py-9">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Password Tab End -->

        <!-- Plan Tab Start -->
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
        <!-- Plan Tab End -->

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
    </script>


@endsection
