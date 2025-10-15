@extends('layouts.main')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="ph ph-shield-check me-2"></i>Verifikasi Sertifikat</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        Masukkan kode verifikasi yang tertera pada sertifikat untuk memverifikasi keasliannya.
                    </p>

                    <form action="{{ route('certificate.verify') }}" method="GET">
                        <div class="mb-3">
                            <label for="code" class="form-label fw-bold">Kode Verifikasi</label>
                            <input type="text"
                                   class="form-control form-control-lg"
                                   id="code"
                                   name="code"
                                   placeholder="Contoh: 1A2B3C4D5E6F7G8H"
                                   value="{{ request('code') }}"
                                   required>
                            <small class="form-text text-muted">
                                Kode verifikasi terdapat di bagian bawah sertifikat
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="ph ph-magnifying-glass me-2"></i>Verifikasi Sekarang
                        </button>
                    </form>

                    @if(isset($error) && $error)
                        <div class="alert alert-danger mt-4">
                            <i class="ph ph-warning-circle me-2"></i>{{ $error }}
                        </div>
                    @endif

                    @if(isset($certificate) && $certificate)
                        <div class="alert alert-success mt-4">
                            <div class="d-flex align-items-start">
                                <i class="ph ph-check-circle" style="font-size: 48px;"></i>
                                <div class="ms-3 flex-grow-1">
                                    <h5 class="mb-2"><i class="ph ph-seal-check me-2"></i>Sertifikat Valid!</h5>
                                    <p class="mb-0">Sertifikat ini sah dan telah terverifikasi.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Detail Sertifikat</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold" width="200">Nomor Sertifikat:</td>
                                        <td>{{ $certificate->certificate_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Penerima:</td>
                                        <td>{{ $certificate->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Kursus:</td>
                                        <td>{{ $certificate->course->title }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Tanggal Terbit:</td>
                                        <td>{{ $certificate->issued_date->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Diterbitkan Oleh:</td>
                                        <td>{{ $certificate->issued_by }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="ph ph-info me-2"></i>Informasi</h6>
                    <ul class="text-muted small mb-0">
                        <li>Setiap sertifikat memiliki kode verifikasi unik</li>
                        <li>Kode verifikasi dapat digunakan untuk memastikan keaslian sertifikat</li>
                        <li>Jika kode tidak valid, hubungi administrator LMS Bosowa</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
