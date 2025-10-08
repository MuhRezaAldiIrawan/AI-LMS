@extends('layouts.main')

@section('content')
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><span class="text-main-600 fw-normal text-15">Penukaran Reward</span></li>
        </ul>
    </div>
    <!-- Breadcrumb End -->

    <!-- Course Tab Start -->
    <div class="card">
        <div class="card-body">
            <div class="mb-24">
                <ul class="nav nav-pills common-tab gap-20" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-redeemtion-tab" data-bs-toggle="pill"
                            data-bs-target="#all-redeemtion" type="button" role="tab" aria-controls="all-redeemtion"
                            aria-selected="true">Semua</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pending-redeemtion-tab" data-bs-toggle="pill"
                            data-bs-target="#pending-redeemtion" type="button" role="tab" aria-controls="pending-redeemtion"
                            aria-selected="false">Pending</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="proses-redeemtion-tab" data-bs-toggle="pill" data-bs-target="#proses-redeemtion"
                            type="button" role="tab" aria-controls="proses-redeemtion" aria-selected="false">Proses</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="selesai-redeemtion-tab" data-bs-toggle="pill"
                            data-bs-target="#selesai-redeemtion" type="button" role="tab" aria-controls="selesai-redeemtion"
                            aria-selected="false">Selesai</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ditolak-redeemtion-tab" data-bs-toggle="pill"
                            data-bs-target="#ditolak-redeemtion" type="button" role="tab" aria-controls="ditolak-redeemtion"
                            aria-selected="false">Ditolak</button>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="all-redeemtion" role="tabpanel" aria-labelledby="all-redeemtion-tab" tabindex="0">
                    <div class="row g-20">
                        <h1>Semua Penukaran</h1>
                    </div>
                </div>
                <div class="tab-pane fade" id="pending-redeemtion" role="tabpanel" aria-labelledby="pending-redeemtion-tab"
                    tabindex="0">
                    <div class="row g-20">
                        <h1>Pending Penukaran</h1>
                    </div>
                </div>
                <div class="tab-pane fade" id="proses-redeemtion" role="tabpanel" aria-labelledby="proses-redeemtion-tab"
                    tabindex="0">
                    <div class="row g-20">
                        <h1>Proses Penukaran</h1>
                    </div>
                </div>
                <div class="tab-pane fade" id="selesai-redeemtion" role="tabpanel" aria-labelledby="selesai-redeemtion-tab"
                    tabindex="0">
                    <div class="row g-20">
                        <h1>Selesai Penukaran</h1>
                    </div>
                </div>
                <div class="tab-pane fade" id="ditolak-redeemtion" role="tabpanel" aria-labelledby="ditolak-redeemtion-tab"
                    tabindex="0">
                    <div class="row g-20">
                        <h1>Ditolak Penukaran</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
