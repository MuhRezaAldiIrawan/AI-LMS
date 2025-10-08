@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}">
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
            <h5 class="mb-0">{{ $action === 'create' ? 'Tambah Rewards' : 'Edit Rewards' }}</h5>
            <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="Course Details">
                <i class="ph-fill ph-question"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row gy-20">
                <form id="{{ $action === 'create' ? 'createRewardsForm' : 'editRewardsForm' }}">
                    @csrf
                    <div class="row g-20">
                        <input type="hidden" name="rewardsid" id="rewardsid" value="{{ $rewards->id ?? '' }}">

                        <div class="col-sm-12">
                            <label for="name" class="h7 mb-8 fw-semibold font-heading">Nama Rewards</label>
                            <div class="position-relative">
                                <input type="text" name="name" id="name" class="form-control py-9"
                                    value="{{ old('name', $rewards->name ?? '') }}">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label for="description" class="h7 mb-8 fw-semibold font-heading">Deskripsi</label>
                            <div class="position-relative">
                                <textarea name="description" id="description" class="form-control py-9" rows="3">{{ old('description', $rewards->description ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 30px">
                            <div class="col-sm-6">
                                <label for="points_cost" class="h7 mb-8 fw-semibold font-heading">Biaya Poin</label>
                                <div class="position-relative">
                                    <input type="text" inputmode="numeric" name="points_cost" id="points_cost"
                                        class="form-control py-9"
                                        value="{{ old('points_cost', $rewards->points_cost ?? '') }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="stock" class="h7 mb-8 fw-semibold font-heading">Stok</label>
                                <div class="position-relative">
                                    <input type="text" inputmode="numeric" name="stock" id="stock"
                                        class="form-control py-9" value="{{ old('stock', $rewards->stock === -1 ? '' : $rewards->stock) }}">
                                    <small
                                        style="color: gray; font-size: 12px; font-style: italic; font-weight: 400; line-height: 16px; margin-top: 4px; margin-bottom: 0px; text-align: left; width: 100%; height: 16px;">
                                        kosongkan jika stok tidak terbatas</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label class="fw-semibold font-heading mb-0">Gambar Reward (Optional)</label>
                            <div id="fileUpload" class="fileUpload image-upload" name="photo" accept="image/*"
                                data-preview="{{ !empty($rewards->image) ? asset('storage/' . $rewards->image) : '' }}">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label for="name" class="h7 mb-8 fw-semibold font-heading">Status</label>
                            <div class="position-relative">
                                <select name="is_active" id="is_active" class="form-select py-9">
                                    <option value="1" {{ $rewards->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $rewards->is_active == 0 ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="flex-align justify-content-end gap-8 mt-16">
                        <a href="{{ route('rewards') }}" class="btn btn-outline-main rounded-pill py-9">Batal</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            $('form').on('submit', function(e) {
                let isValid = true;

                $('.error-message').remove();
                $('.form-control, .form-select').removeClass('input-error');

                const name = $('#name').val().trim();
                const description = $('#description').val().trim();
                const points_cost = $('#points_cost').val();
                const stock = $('#stock').val().trim();
                const is_active = $('#is_active').val();


                if (!name) showError('#name', 'Nama lengkap wajib diisi');
                if (!description) showError('#description', 'description wajib diisi');
                if (!points_cost) showError('#points_cost', 'Biaya Poin wajib diisi');
                if (!stock) showError('#stock', 'Stock wajib diisi');
                if (!is_active || is_active === '1') showError('#is_active', 'Silakan pilih Status');

                if ($('.error-message').length > 0) {
                    e.preventDefault();
                    isValid = false;
                }

                return isValid;
            });

            if ($.fn.fileUpload) {
                $('.fileUpload').fileUpload();
                console.log("✅ File upload initialized successfully.");
            } else {
                console.error("❌ fileUpload plugin not found. Check file-upload.js loading order.");
            }
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const numericInputs = document.querySelectorAll('#points_cost, #stock');

            numericInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, ''); // hapus semua non-digit
                });
            });
        });
    </script>


    <script>
        $('#createRewardsForm').on('submit', function(e) {
            e.preventDefault(); // cegah reload halaman

            // Ambil data form
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('rewards.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data Rewards berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/rewards';
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

        $('#editRewardsForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let id = $('#rewardsid').val();

            $.ajax({
                url: '/rewards/' + id,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data Rewards berhasil diupdate.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/rewards';
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });
    </script>
@endsection
