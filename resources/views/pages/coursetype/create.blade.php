@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .input-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.1);
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 4px;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header border-bottom border-gray-100 flex-align gap-8">
            <h5 class="mb-0">{{ $action === 'create' ? 'Tambah Tipe Kursus' : 'Edit Tipe Kursus' }}</h5>
            <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Course Details">
                <i class="ph-fill ph-question"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row gy-20">
                <form id="{{ $action === 'create' ? 'createCourseTypeForm' : 'editCourseTypeForm' }}">
                    @csrf
                    <div class="row g-20">
                        <input type="hidden" name="tipekursusid" id="tipekursusid" value="{{ $coursetype->id ?? '' }}">

                        <div class="col-sm-12">
                            <label for="name" class="h7 mb-8 fw-semibold font-heading">Nama Tipe Kursus</label>
                            <div class="position-relative">
                                <input type="text" name="name" id="name" class="form-control py-9"
                                    value="{{ old('name', $coursetype->name ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="flex-align justify-content-end gap-8 mt-16">
                        <a href="{{ route('coursetype') }}" class="btn btn-outline-main rounded-pill py-9">Batal</a>
                        <button class="btn btn-main rounded-pill py-9" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $('#createCourseTypeForm').on('submit', function(e) {
            e.preventDefault(); // cegah reload halaman

            // Ambil data form
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('coursetype.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data Tipe Kursus berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/coursetype';
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

        $('#editCourseTypeForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let id = $('#tipekursusid').val();

            $.ajax({
                url: '/coursetype/' + id,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data Tipe Kursus berhasil diupdate.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/coursetype';
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });
    </script>
@endsection
