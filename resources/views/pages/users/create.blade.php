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
            <h5 class="mb-0">Buat User Baru</h5>
            <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Course Details">
                <i class="ph-fill ph-question"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row gy-20">
                <form id="createUserForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-20">
                        <div class="col-sm-6">
                            <label for="name" class="h5 mb-8 fw-semibold font-heading">Nama Lengkap
                            </label>
                            <div class="position-relative">
                                <input type="text" name="name" id="name" class="form-control py-9">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="nik" class="h5 mb-8 fw-semibold font-heading">NIK (Nomor Induk
                                Karyawan)</label>
                            <div class="position-relative">
                                <input type="text" name="nik" id="nik" class="form-control py-9">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="courseTime" class="h5 mb-8 fw-semibold font-heading">Email</label>
                            <div class="position-relative">
                                <input type="text" name="email" id="email" class="form-control py-9">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="join_date" class="h5 mb-8 fw-semibold font-heading">Tanggal Bergabung</label>
                            <div class="position-relative">
                                <input type="text" name="join_date" id="join_date" class="join_date form-control py-9">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="password" class="h5 mb-8 fw-semibold font-heading">Password</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="password" class="form-control py-9">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="position" class="h5 mb-8 fw-semibold font-heading">Posisi / Jabatan</label>
                            <div class="position-relative">
                                <input type="text" name="position" id="position" class="form-control py-9">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="role" class="h5 mb-8 fw-semibold font-heading">Role</label>
                            <div class="position-relative">
                                <select id="role" name="role" class="form-select py-9 placeholder-13 text-15"">
                                    <option value="" disabled selected>Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            @if (old('role') == $role->name) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="division" class="h5 mb-8 fw-semibold font-heading">Devisi</label>
                            <div class="position-relative">
                                <input type="text" name="division" id="division" class="form-control py-9">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="location_id" class="h5 mb-8 fw-semibold font-heading">Lokasi</label>
                            <div class="position-relative">
                                <select id="location_id" class="form-select py-9 placeholder-13 text-15" name="location_id">
                                    <option value="1" disabled selected>Pilih Lokasi</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}"
                                            @if (old('location_id') == $location->id) selected @endif>{{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="h5 fw-semibold font-heading mb-0">Foto Profile</label>
                            <div id="fileUpload" class="fileUpload image-upload" name="photo"></div>
                        </div>
                    </div>
                    <div class="flex-align justify-content-end gap-8 mt5">
                        <a href="{{ route('users') }}" class="btn btn-outline-main rounded-pill py-9">Batal</a>
                        <button class="btn btn-main rounded-pill py-9" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="{{ asset('assets/js/file-upload.js') }}"></script>
    <script src="{{ asset('assets/js/plyr.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $("#join_date").flatpickr({
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                allowInput: true,
            });

            if ($.fn.fileUpload) {
                $('.fileUpload').fileUpload();
                console.log("✅ File upload initialized successfully.");
            } else {
                console.error("❌ fileUpload plugin not found. Check file-upload.js loading order.");
            }
        });
    </script>

    {{-- Input Validation --}}
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                let isValid = true;

                $('.error-message').remove();
                $('.form-control, .form-select').removeClass('input-error');

                const name = $('#name').val().trim();
                const email = $('#email').val().trim();
                const password = $('#password').val().trim();
                const role = $('#role').val().trim();
                const location = $('#location_id').val();


                if (!name) showError('#name', 'Nama lengkap wajib diisi');
                if (!email) showError('#email', 'Email wajib diisi');
                else if (!validateEmail(email)) showError('#email', 'Format email tidak valid');
                if (!password) showError('#password', 'Password wajib diisi');
                if (!role) showError('#role', 'Role wajib diisi');
                if (!location || location === '1') showError('#location_id', 'Silakan pilih lokasi');

                if ($('.error-message').length > 0) {
                    e.preventDefault();
                    isValid = false;
                }

                return isValid;
            });


            function showError(selector, message) {
                const parent = $(selector).closest('.position-relative');
                $(selector).addClass('input-error');
                parent.append(`<div class="error-message">${message}</div>`);
            }


            function validateEmail(email) {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(email);
            }


            $('.form-control, .form-select').on('input change', function() {
                $(this).removeClass('input-error');
                $(this).closest('.position-relative').find('.error-message').remove();
            });
        });

        $('#createUserForm').on('submit', function(e) {
            e.preventDefault(); // cegah reload halaman

            // Bersihkan error lama
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            // Ambil data form
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('users.store') }}", // sesuaikan dengan route kamu
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data user berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // redirect atau reset form
                        window.location.href = '/users';
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
