@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

<div class="card mt-24">
    <div class="card-header border-bottom">
        <h4 class="mb-4">Kelola Peserta Kursus</h4>
        <p class="text-gray-600 text-15">Pilih karyawan yang akan mendapatkan akses ke kursus ini.</p>
    </div>
    <div class="card-body">
        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="position-relative">
                    <input type="text" class="form-control" id="searchUsers" placeholder="Cari nama atau email...">
                    <i class="ph-magnifying-glass position-absolute top-50 translate-middle-y" style="right: 12px;"></i>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="selectAll">
                        <i class="ph-check-square me-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAll">
                        <i class="ph-square me-1"></i> Batalkan Semua
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('course.update-participants', $course->id) }}" id="participantsForm">
            @csrf
            <input type="hidden" name="course_id" value="{{ $course->id }}">

            <!-- Selected Participants Summary -->
            <div class="alert alert-info d-none" id="selectedSummary">
                <i class="ph-info me-2"></i>
                <span id="selectedCount">0</span> peserta terpilih
            </div>

            <!-- Users List -->
            <div class="participants-list" id="participantsList">
                @if (isset($users) && $users->count() > 0)
                    @foreach ($users as $user)
                        <div class="participant-item border rounded" data-user-name="{{ strtolower($user->name) }}"
                            data-user-email="{{ strtolower($user->email) }}">
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input participant-checkbox" type="checkbox"
                                        name="participants[]" value="{{ $user->id }}" id="user_{{ $user->id }}"
                                        {{ in_array($user->id, $course->enrolledUsers->pluck('id')->toArray()) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="user_{{ $user->id }}"></label>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div
                                                class="avatar-sm-wrapper rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                            <p class="mb-0 text-muted small">{{ $user->email }}</p>
                                            @if ($user->roles->count() > 0)
                                                <div class="mt-1">
                                                    @foreach ($user->roles as $role)
                                                        <span
                                                            class="badge bg-light text-dark me-1">{{ $role->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if (in_array($user->id, $course->enrolledUsers->pluck('id')->toArray()))
                                        <span class="badge bg-success">Terdaftar</span>
                                    @else
                                        <span class="badge bg-light text-muted">Belum Terdaftar</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="ph-users text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">Tidak ada user ditemukan</h5>
                        <p class="text-muted">Belum ada user yang tersedia untuk ditambahkan ke kursus.</p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if (isset($users) && method_exists($users, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <div>
                    <small class="text-muted">
                        <i class="ph-info-circle me-1"></i>
                        Centang kotak di samping nama untuk memilih peserta
                    </small>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary me-2" onclick="window.location.reload()">
                        <i class="ph-arrow-clockwise me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-floppy-disk me-1"></i> Simpan Peserta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .participant-item {
        transition: all 0.2s ease;
        background: #fff;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 12px;
    }

    .participant-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .participant-item.selected {
        background: #f8f9ff;
        border-color: #6366f1;
    }

    .avatar-sm-wrapper {
        width: 40px;
        height: 40px;
        font-size: 14px;
        font-weight: 600;
    }

    .form-check-input:checked+.form-check-label {
        color: #6366f1;
    }

    .participants-list {
        max-height: 600px;
        overflow-y: auto;
    }

    .search-highlight {
        background-color: #fff3cd;
        padding: 1px 2px;
        border-radius: 2px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchUsers');
        const participantItems = document.querySelectorAll('.participant-item');
        const checkboxes = document.querySelectorAll('.participant-checkbox');
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        const selectedSummary = document.getElementById('selectedSummary');
        const selectedCount = document.getElementById('selectedCount');

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            participantItems.forEach(item => {
                const name = item.dataset.userName;
                const email = item.dataset.userEmail;

                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Update selected count
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.participant-checkbox:checked').length;
            selectedCount.textContent = checked;

            if (checked > 0) {
                selectedSummary.classList.remove('d-none');
            } else {
                selectedSummary.classList.add('d-none');
            }
        }

        // Checkbox change event
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const item = this.closest('.participant-item');
                if (this.checked) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
                updateSelectedCount();
            });
        });

        // Select all
        selectAllBtn.addEventListener('click', function() {
            const visibleCheckboxes = Array.from(checkboxes).filter(cb =>
                cb.closest('.participant-item').style.display !== 'none'
            );

            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
                checkbox.closest('.participant-item').classList.add('selected');
            });
            updateSelectedCount();
        });

        // Deselect all
        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.closest('.participant-item').classList.remove('selected');
            });
            updateSelectedCount();
        });

        // Initial count
        updateSelectedCount();

        // Set initial selected state
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.closest('.participant-item').classList.add('selected');
            }
        });

        // Handle form submission
        const participantsForm = document.getElementById('participantsForm');
        if (participantsForm) {
            participantsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Menyimpan...';
                
                // Get selected participants
                const selectedParticipants = [];
                const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
                checkedBoxes.forEach(checkbox => {
                    selectedParticipants.push(checkbox.value);
                });
                
                // Prepare form data
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('course_id', document.querySelector('input[name="course_id"]').value);
                
                // Add participants array
                selectedParticipants.forEach(participantId => {
                    formData.append('participants[]', participantId);
                });
                
                console.log('Submitting participants:', selectedParticipants);
                console.log('Form action:', this.action);
                console.log('Course ID:', document.querySelector('input[name="course_id"]').value);
                
                // Submit via AJAX
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Error response:', text);
                            throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Peserta kursus berhasil diperbarui.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Reload page to show updated data
                            window.location.reload();
                        });
                    } else {
                        alert(data.message || 'Peserta kursus berhasil diperbarui.');
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Show error message with option to try normal submit
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan AJAX',
                            text: 'Gagal memperbarui peserta kursus via AJAX. Coba submit form biasa?',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Coba Submit Biasa',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Try normal form submit as fallback
                                const form = document.getElementById('participantsForm');
                                form.removeEventListener('submit', arguments.callee);
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm('Gagal memperbarui peserta kursus via AJAX. Coba submit form biasa?')) {
                            // Try normal form submit as fallback
                            const form = document.getElementById('participantsForm');
                            form.removeEventListener('submit', arguments.callee);
                            form.submit();
                        }
                    }
                })
                .finally(() => {
                    // Restore button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            });
        }
    });
</script>
