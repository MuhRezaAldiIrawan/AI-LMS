@extends('layouts.main')

@section('content')

@section('css')
    <style>
        .mentor-card { border: 1px solid #e5e7eb; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s; }
        .mentor-card:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0,0,0,0.1); }
        .nav-pills .nav-link.active { background-color: #4f46e5; color: #fff !important; box-shadow: 0 2px 8px rgba(79,70,229,0.3); }
        .nav-pills .nav-link:hover { background-color: #e0e7ff; color: #4f46e5; }
        #searchInput { border-radius: 30px; padding-left: 36px; }
        #searchInput:focus { box-shadow: 0 0 0 3px rgba(79,70,229,0.25); border-color: #4f46e5; }
        .reward-card-hover:hover { box-shadow: 0 8px 32px 0 rgba(31,38,135,0.12); border-color: #2563eb; transition: box-shadow 0.2s, border-color 0.2s; }
    </style>
@endsection

<div class="breadcrumb mb-24">
    <ul class="flex-align gap-4">
        <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
        <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
        <li><span class="text-main-600 fw-normal text-15">Toko Reward</span></li>
    </ul>
</div>

<div class="card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); margin-top: 24px;">
    <div class="card-body">
        <ul class="nav nav-tabs mb-4" id="mainRewardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-shop" data-bs-toggle="tab" data-bs-target="#tabShopContent" type="button" role="tab">Toko Reward</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-history" data-bs-toggle="tab" data-bs-target="#tabHistoryContent" type="button" role="tab">Riwayat Penukaran</button>
            </li>
        </ul>
        <div class="tab-content" id="mainRewardTabsContent">
            {{-- Tab Toko Reward --}}
            <div class="tab-pane fade show active" id="tabShopContent" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-24 gap-3">
                    <ul class="nav nav-pills gap-10 mb-0 p-1 bg-light rounded-3 shadow-sm" id="rewardTabs" role="tablist" style="--bs-nav-pills-link-active-bg: #4f46e5;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active filter-tab rounded-pill px-16 py-6 fw-semibold" data-status="all" type="button">Semua</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link filter-tab rounded-pill px-16 py-6 fw-semibold" data-status="available" type="button">Tersedia</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link filter-tab rounded-pill px-16 py-6 fw-semibold" data-status="out" type="button">Habis</button>
                        </li>
                    </ul>
                    <div class="position-relative" style="min-width: 260px;">
                        <input type="text" id="searchInput" class="form-control ps-20" placeholder="Cari reward..." style="border-radius: 30px; padding-left: 36px;">
                        <i class="ph ph-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    </div>
                </div>
                <h4 class="mb-20">Reward Tersedia</h4>
                <div class="row g-20" id="rewardContainer">
                    @if(count($rewards) > 0)
                        @include('pages.rewards._partials.reward-list', ['rewards' => $rewards, 'userPoints' => $userPoints])
                    @else
                        <div class="text-center w-100 py-4" style="font-size: 16px; color: #888;">Belum ada reward tersedia</div>
                    @endif
                </div>
            </div>

            {{-- Tab Riwayat Penukaran --}}
            <div class="tab-pane fade" id="tabHistoryContent" role="tabpanel">
                <h4 class="mb-20">Riwayat Penukaran Reward</h4>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Reward</th>
                                <th>Poin</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($redemptions as $idx => $r)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $r->reward->name ?? '-' }}</td>
                                    <td>{{ $r->points_cost }}</td>
                                    <td>
                                        @if($r->status == 'pending')
                                            {{-- INI PERBAIKANNYA: Menambahkan text-dark --}}
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($r->status == 'processed')
                                            <span class="badge bg-info">Diproses</span>
                                        @elseif($r->status == 'completed')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($r->status == 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $r->status }}</span>
                                        @endif
                                    </td>
                                    <td style="max-width:260px; white-space:normal; word-wrap:break-word;">{{ $r->admin_notes ? $r->admin_notes : '-' }}</td>
                                    <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Belum ada riwayat penukaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Event delegation for dynamic form
    document.addEventListener('submit', function(e) {
        if (e.target.matches('form.reward-claim-form')) {
            e.preventDefault();
            const btn = e.target.querySelector('.reward-claim-btn');
            const rewardName = btn ? btn.getAttribute('data-reward-name') : '';
            Swal.fire({
                title: 'Klaim Reward?',
                text: rewardName ? `Apakah Anda yakin ingin menukar poin untuk reward "${rewardName}"?` : 'Apakah Anda yakin ingin menukar poin untuk reward ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, klaim!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        }
    });

    // Notifikasi setelah klaim (berdasarkan session flash)
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#2563eb',
        });
    @endif
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
        });
    @endif

    // Filter & search AJAX
    let statusFilter = 'all';
    let searchQuery = '';
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            statusFilter = this.getAttribute('data-status');
            loadRewards();
        });
    });
    document.getElementById('searchInput').addEventListener('keyup', function() {
        searchQuery = this.value;
        loadRewards();
    });
    function loadRewards() {
        fetch(`{{ url('rewards-shop') }}?status=${statusFilter}&search=${encodeURIComponent(searchQuery)}`)
            .then(res => res.text())
            .then(html => {
                // Ambil isi rewardContainer dari response
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newList = doc.getElementById('rewardContainer');
                document.getElementById('rewardContainer').innerHTML = newList ? newList.innerHTML : '';
            });
    }
</script>
@endsection