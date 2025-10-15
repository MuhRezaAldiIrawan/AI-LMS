@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
@endsection

@section('content')
    <!-- Breadcrumb Start -->
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li><a href="index.html" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><span class="text-main-600 fw-normal text-15">Setting</span></li>
        </ul>
    </div>
    <!-- Breadcrumb End -->

    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <div class="cover-img position-relative">
                <label for="coverImageUpload"
                    class="btn border-gray-200 text-gray-200 fw-normal hover-bg-gray-400 rounded-pill py-4 px-14 position-absolute inset-block-start-0 inset-inline-end-0 mt-24 me-24">Edit
                    Cover</label>
                <div class="avatar-upload">
                    <input type='file' id="coverImageUpload" accept=".png, .jpg, .jpeg">
                    <div class="avatar-preview">
                        <div id="coverImagePreview"
                            style="background-image: url({{ asset('assets/images/bg/welcome-bg1.png') }}); background-size: cover; background-position: center;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="setting-profile px-24">
                <div class="flex-between">
                    <div class="d-flex align-items-end flex-wrap mb-32 gap-24">
                        <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('assets/images/thumbs/setting-profile-img.jpg') }}" alt=""
                            class="w-120 h-120 rounded-circle border border-white">
                        <div>
                            <h4 class="mb-8">{{ $user->name }}</h4>
                            <div class="setting-profile__infos flex-align flex-wrap gap-16">
                                <div class="flex-align gap-6">
                                    <span class="text-gray-600 d-flex text-lg"><i class="ph ph-swatches"></i></span>
                                    <span class="text-gray-600 d-flex text-15">{{ $user->position ?? '-' }}</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-gray-600 d-flex text-lg"><i class="ph ph-map-pin"></i></span>
                                    <span class="text-gray-600 d-flex text-15">{{ optional($user->location)->name ?? '-' }}</span>
                                </div>
                                <div class="flex-align gap-6">
                                    <span class="text-gray-600 d-flex text-lg"><i class="ph ph-calendar-dots"></i></span>
                                    <span class="text-gray-600 d-flex text-15">Join
                                        {{ $user->created_at->format('F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav common-tab style-two nav-pills mb-0" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-details-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details"
                            aria-selected="true">My Details</button>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="tab-content" id="pills-tabContent">
        <!-- My Details Tab start -->
        <div class="tab-pane fade show active" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab"
            tabindex="0">
            <div class="card mt-24">
                <div class="card-header border-bottom">
                    <h4 class="mb-4">My Details</h4>
                    <p class="text-gray-600 text-15">Please fill full details about yourself</p>
                </div>
                <div class="card-body">
                    <form id="updateUserProfileForm">
                        @csrf
                        <div class="row gy-4">
                            <div class="col-sm-6 col-xs-6">
                                <label for="fname" class="form-label mb-8 h6">Nama Lengkap</label>
                                <input type="text" class="form-control py-11" id="fname" value="{{ $user->name }}"
                                    name="name" placeholder="Masukkan Nama Lengkap">
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <label for="email" class="form-label mb-8 h6">Email</label>
                                <input type="email" class="form-control py-11" id="email" value="{{ $user->email }}"
                                    name="email" placeholder="Masukkan Email">
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <label for="nik" class="form-label mb-8 h6">NIK</label>
                                <input type="text" class="form-control py-11" id="nik" value="{{ $user->nik }}"
                                    name="nik" placeholder="Masukkan NIK">
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <label for="position" class="form-label mb-8 h6">Posisi</label>
                                <input type="text" class="form-control py-11" id="position"
                                    value="{{ $user->position }}" name="position" placeholder="Masukkan Posisi">
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <label for="devisi" class="form-label mb-8 h6">Devisi</label>
                                <input type="text" class="form-control py-11" id="devisi"
                                    value="{{ $user->division }}" name="division" placeholder="Masukkan Devisi">
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <label for="lokasi" class="form-label mb-8 h6">Lokasi</label>
                                <select class="form-select py-11" id="lokasi" aria-label="Default select example"
                                    name="location_id">
                                    <option selected>Pilih Lokasi</option>
                                    @foreach ($lokasi as $lok)
                                        <option value="{{ $lok->id }}"
                                            {{ $lok->id == $user->location_id ? 'selected' : '' }}>{{ $lok->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="imageUpload" class="form-label mb-8 h6">Your Photo</label>
                                <div class="flex-align gap-22">
                                    <div class="avatar-upload flex-shrink-0">
                                        <input type='file' id="imageUpload" name="photo" accept=".png, .jpg, .jpeg">
                                        <div class="avatar-preview">
                                            <div id="profileImagePreview"
                                                style="background-image: url('assets/images/thumbs/setting-profile-img.jpg');">
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="avatar-upload-box text-center position-relative flex-grow-1 py-24 px-4 rounded-16 border border-main-300 border-dashed bg-main-50 hover-bg-main-100 hover-border-main-400 transition-2 cursor-pointer">
                                        <label for="imageUpload"
                                            class="position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 rounded-16 cursor-pointer z-1"></label>
                                        <span class="text-32 icon text-main-600 d-inline-flex"><i
                                                class="ph ph-upload"></i></span>
                                        <span class="text-13 d-block text-gray-400 text my-8">Click to upload or drag and
                                            drop</span>
                                        <span class="text-13 d-block text-main-600">SVG, PNG, JPEG OR GIF (max
                                            1080px1200px)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="flex-align justify-content-end gap-8">
                                    <button type="reset"
                                        class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9">Cancel</button>
                                    <button type="submit" class="btn btn-main rounded-pill py-9">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/file-upload.js') }}"></script>
    <script src="{{ asset('assets/js/plyr.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>

        $('#updateUserProfileForm').on('submit', function(e) {

            console.log("Form submitted");
            e.preventDefault(); // cegah reload halaman

            // Ambil data form
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('users.profile.update', $user->id) }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Profil berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // redirect atau reset form
                        window.location.href = '/profile';
                    });
                },
                error: function(xhr) {
                    console.log(xhr)
                    if (xhr.status === 422) {
                        // Ambil semua pesan error dari Laravel
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

    <script>
        // ============================= Avatar Upload js =============================
        function uploadImageFunction(imageId, previewId) {
            $(imageId).on('change', function() {
                var input = this; // 'this' is the DOM element here
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).css('background-image', 'url(' + e.target.result + ')');
                        $(previewId).hide();
                        $(previewId).fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            });
        }

        uploadImageFunction('#coverImageUpload', '#coverImagePreview');
        uploadImageFunction('#imageUpload', '#profileImagePreview');
        if ($.fn.fileUpload) {
            $('.fileUpload').fileUpload();
            console.log("✅ File upload initialized successfully.");
        } else {
            console.error("❌ fileUpload plugin not found. Check file-upload.js loading order.");
        }
    </script>
@endsection
