@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card-header border-bottom border-gray-100 flex-align gap-8">
            <h5 class="mb-0">{{ $action === 'create' ? 'Tambah Kursus' : 'Edit Kursus' }}</h5>
            <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Course Details">
                <i class="ph-fill ph-question"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row gy-20">
                <form id="createCourseForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-20">
                        <input type="hidden" name="kursusid" id="kursusid" value="{{ $course->id ?? '' }}">

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
                                            {{ $category->id == $course->id ? 'selected' : '' }}>
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
                                            {{ $type->id == $course->id ? 'selected' : '' }}>
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
                                data-preview="{{ !empty($course->profile_photo_path) ? asset('storage/' . $course->profile_photo_path) : '' }}">
                            </div>
                        </div>

                    </div>

                    <div class="flex-align justify-content-end gap-8 mt-16">
                        <a href="{{ route('course') }}" class="btn btn-outline-main rounded-pill py-9">Batal</a>
                        <button class="btn btn-main rounded-pill py-9" type="submit">Simpan & Lanjutkan Ke
                            Kurikulum</button>
                    </div>
                </form>
            </div>
        </div>
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
    </script>

    <script>

        $('#createCourseForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('course.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data Course berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/course';
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
@endsection
