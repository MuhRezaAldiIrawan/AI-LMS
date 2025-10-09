@extends('layouts.main')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <style>
        div.dt-container div.dt-info {
            margin-left: 20px;
            margin-bottom: 10px;
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a>
            </li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><span class="text-main-600 fw-normal text-15">Penukaran Reward</span></li>
        </ul>
    </div>
    <!-- Breadcrumb End -->

    <!-- Course Tab Start -->
    <div class="card">
        <div class="card-body">
            <div class="mb-24">
                <ul class="nav nav-pills common-tab gap-20" id="redeemTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active filter-tab" data-status="all" type="button">Semua</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link filter-tab" data-status="pending" type="button">Pending</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link filter-tab" data-status="processed" type="button">Proses</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link filter-tab" data-status="completed" type="button">Selesai</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link filter-tab" data-status="rejected" type="button">Ditolak</button>
                    </li>
                </ul>
            </div>

            <div class="table-responsive">
                <table id="redeemTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Reward</th>
                            <th>Poin</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let statusFilter = 'all';

            let table = $('#redeemTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('redeemtion.getData') }}",
                    data: function(d) {
                        d.status = statusFilter; // kirim status ke backend
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'reward_id',
                        name: 'reward_id'
                    },
                    {
                        data: 'points_cost',
                        name: 'points_cost'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'admin_notes',
                        name: 'admin_notes'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                searching: false,
                lengthChange: false,
                ordering: true,
                responsive: true,
                pagingType: "simple_numbers",
                language: {
                    paginate: {
                        previous: "<",
                        next: ">"
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                }
            });

            // Handle klik tab filter
            $('.filter-tab').on('click', function() {
                $('.filter-tab').removeClass('active');
                $(this).addClass('active');
                statusFilter = $(this).data('status');
                table.ajax.reload(); // reload DataTable dengan filter baru
            });
        });
    </script>
@endsection
