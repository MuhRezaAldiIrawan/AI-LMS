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

        .add-user-btn .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .add-user-btn .btn:hover {
            background-color: #2563eb;
            /* biru lebih pekat saat hover */
            color: white;
            transform: translateY(-1px);
        }

        /* Avatar + Name cell */
        .user-cell {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
        <!-- Breadcrumb Start -->
        <div class="breadcrumb mb-24">
            <ul class="flex-align gap-4">
                <li>
                    <a href="#" class="text-gray-200 fw-normal text-15 hover-text-main-600">
                        Home
                    </a>
                </li>
                <li>
                    <span class="text-gray-500 fw-normal d-flex">
                        <i class="ph ph-caret-right"></i>
                    </span>
                </li>
                <li>
                    <span class="text-main-600 fw-normal text-15">Users</span>
                </li>
            </ul>
        </div>
        <!-- Breadcrumb End -->

        <!-- 🔹 Add User Button -->
        <div class="add-user-btn">
            <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="ph ph-plus-circle text-lg"></i> Tambah User
            </a>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-body p-0 overflow-x-auto">
            <table id="studentTable" class="table table-hover  align-middle">
                <thead>
                    <tr>
                        <th class="h6 text-center">No</th>
                        <th class="h6 text-center">Foto</th>
                        <th class="h6 text-center">Nama</th>
                        <th class="h6 text-center">Email</th>
                        <th class="h6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center" style="margin-bottom: 20px">
                </tbody>
            </table>
        </div>
    </div>
@endsection


@section('js')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
            $('#studentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.get-data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'profile_photo_url',
                        name: 'profile_photo_url',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            const photo = data || '';
                            const safeName = row.name || '';
                            // Fallback: first letter avatar if missing photo
                            if (!photo) {
                                const letter = (safeName || '?').substring(0,1).toUpperCase();
                                return `<div class="d-flex justify-content-center"><div class="user-avatar" style="background:#e5e7eb; display:flex; align-items:center; justify-content:center; color:#6b7280; font-weight:600;">${letter}</div></div>`;
                            }
                            return `<div class="d-flex justify-content-center"><img src="${photo}" alt="${safeName}" class="user-avatar" /></div>`;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function (data) {
                            const safeName = data || '';
                            return `<span>${safeName}</span>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email'
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
                columnDefs: [
                    { targets: 1, width: 70 }, // Foto column width
                ],
                language: {
                    paginate: {
                        previous: "<",
                        next: ">"
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                }
            });

        });

        $(document).on('click', '.btn-pengguna-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            let url = "{{ route('users.destroy', ':id') }}";
            url = url.replace(':id', id);

            Swal.fire({
                title: 'Apakah kamu ingin menghapus data ini?',
                text: "data tidak dapat dikembalikan lagi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, hapus data ini!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        type: "DELETE",
                        success: function(data) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Data User Telah berhasil dihapus.',
                                icon: 'success',
                                timer: 2000
                            });
                            $('#studentTable').DataTable().ajax.reload();
                        }
                    })
                }
            })
        })
    </script>
@endsection
