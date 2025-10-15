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
            <li><span class="text-main-600 fw-normal text-15">Manajemen Penukaran Reward</span></li>
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
                            <th>Nama Pengguna</th>
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
        let table;
        $(document).ready(function() {
            let statusFilter = 'all';

            table = $('#redeemTable').DataTable({
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
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'reward_name',
                        name: 'reward.name'
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                searching: true,
                lengthChange: true,
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

        function updateStatus(id, status) {
            // If rejecting, ask for an optional admin note (reason)
            if (status === 'rejected') {
                Swal.fire({
                    title: 'Tolak Penukaran',
                    input: 'textarea',
                    inputLabel: 'Alasan penolakan (opsional)',
                    inputPlaceholder: 'Masukkan alasan, mis. stok habis, data tidak lengkap, dll...',
                    inputAttributes: {
                        'aria-label': 'Alasan penolakan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const adminNotes = result.value || '';
                        $.ajax({
                            url: `/redeemtion/${id}/update-status`,
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                status: status,
                                admin_notes: adminNotes,
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    table.ajax.reload();
                                }
                            }
                        });
                    }
                });
                return;
            }

            // If completing, ask for an optional admin note (e.g. lokasi pengambilan)
            if (status === 'completed') {
                Swal.fire({
                    title: 'Selesaikan Penukaran',
                    input: 'textarea',
                    inputLabel: 'Catatan untuk pengguna (opsional)',
                    inputPlaceholder: 'Contoh: Ambil paket di kantor lantai 2, atas nama Budi, no. ext 123',
                    inputAttributes: {
                        'aria-label': 'Catatan untuk pengguna'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Selesaikan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const adminNotes = result.value || '';
                        $.ajax({
                            url: `/redeemtion/${id}/update-status`,
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                status: status,
                                admin_notes: adminNotes,
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    table.ajax.reload();
                                }
                            }
                        });
                    }
                });
                return;
            }

            // Default confirmation for other status changes
            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin mengubah status menjadi ${status}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ubah!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/redeemtion/${id}/update-status`,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.ajax.reload();
                            }
                        }
                    });
                }
            });
        }
    </script>
    <script>
        // Fetch and show details in a modal
        function showDetailModal(id) {
            $.ajax({
                url: `/redeemtion/${id}`,
                method: 'GET',
                success: function(data) {
                    let html = `
                        <div style="text-align:left">
                            <p><strong>Pengguna:</strong> ${data.user.name} &lt;${data.user.email}&gt;</p>
                            <p><strong>Reward:</strong> ${data.reward.name}</p>
                            <p><strong>Poin:</strong> ${data.points_cost}</p>
                            <p><strong>Status:</strong> ${data.status}</p>
                            <p><strong>Tanggal:</strong> ${data.created_at}</p>
                            <p><strong>Catatan Admin:</strong><br>${data.admin_notes ? data.admin_notes : '<em>-</em>'}</p>
                        </div>
                    `;

                    Swal.fire({
                        title: 'Detail Penukaran',
                        html: html,
                        width: 600,
                        confirmButtonText: 'Tutup',
                    });
                },
                error: function() {
                    Swal.fire('Gagal', 'Tidak dapat mengambil data. Coba lagi.', 'error');
                }
            });
        }
    </script>
@endsection
