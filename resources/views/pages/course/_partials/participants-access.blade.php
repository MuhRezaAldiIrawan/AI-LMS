@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

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
                <div class="participants-actions d-flex gap-2">
                    <button type="button" class="btn btn-outline-main btn-sm" id="selectAll">
                        <i class="ph-check-square me-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-outline-main btn-sm" id="deselectAll">
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
                                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="participant-avatar" />
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
                <div class="flex-align justify-content-end gap-8">
                    <button type="button" class="btn btn-outline-main rounded-pill py-9" id="btnBackToModule">Kembali</button>
                    <button type="button" class="btn btn-main rounded-pill py-9" id="btnSaveAndContinuePublish">Simpan & Lanjutkan</button>
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

    /* New: ensure real photo shows and has spacing */
    .participant-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 12px; /* give breathing room from name/email */
        flex-shrink: 0;
        background: #f3f4f6; /* subtle bg while loading */
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
        const participantsForm = document.getElementById('participantsForm');
        const btnBackToModule = document.getElementById('btnBackToModule');
        const btnSaveAndContinuePublish = document.getElementById('btnSaveAndContinuePublish');

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

        // Helper: submit via AJAX (promise)
        function submitParticipantsViaAjax(){
            return new Promise((resolve, reject) => {
                if(!participantsForm) return reject(new Error('Form tidak ditemukan'));
                const selected = [...document.querySelectorAll('.participant-checkbox:checked')].map(cb => cb.value);
                const fd = new FormData();
                fd.append('_token', document.querySelector('input[name="_token"]').value);
                fd.append('course_id', document.querySelector('input[name="course_id"]').value);
                selected.forEach(id => fd.append('participants[]', id));
                fetch(participantsForm.action, { method:'POST', body: fd, headers:{ 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' } })
                    .then(r => { if(!r.ok) return r.text().then(t=>{ throw new Error(t || 'Gagal simpan peserta'); }); return r.json().catch(()=>({})); })
                    .then(data => resolve(data))
                    .catch(err => reject(err));
            });
        }

        // Form submit: save then go publish
        if(participantsForm){
            participantsForm.addEventListener('submit', function(e){
                e.preventDefault();
                submitParticipantsViaAjax()
                    .then(data => {
                        if(window.Swal){ Swal.fire({ icon:'success', title:'Berhasil!', text:(data&&data.message)||'Peserta kursus berhasil diperbarui.', showConfirmButton:false, timer:1200 }).then(()=> navigateTo('publish')); }
                        else { navigateTo('publish'); }
                    })
                    .catch(err => showAjaxError(err));
            });
        }

        // Back and Save & Continue buttons (save first)
        if(btnBackToModule){
            btnBackToModule.addEventListener('click', function(){
                setLoading(this, true);
                submitParticipantsViaAjax().then(()=> navigateTo('module')).catch(err => showAjaxError(err)).finally(()=> setLoading(this,false));
            });
        }
        if(btnSaveAndContinuePublish){
            btnSaveAndContinuePublish.addEventListener('click', function(){
                setLoading(this, true);
                submitParticipantsViaAjax().then(()=> navigateTo('publish')).catch(err => showAjaxError(err)).finally(()=> setLoading(this,false));
            });
        }

        function setLoading(btn, state){
            if(!btn) return; if(state){ btn.dataset._text = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...'; } else { btn.disabled=false; btn.innerHTML = btn.dataset._text || btn.innerHTML; }
        }
        function showAjaxError(err){ console.error('Error:', err); if(window.Swal){ Swal.fire({ icon:'error', title:'Terjadi Kesalahan', text:'Gagal menyimpan perubahan peserta. Coba lagi.' }); } else { alert('Gagal menyimpan perubahan peserta. Coba lagi.'); } }
        function navigateTo(step){
            window.dispatchEvent(new CustomEvent('course:navigate', { detail: { to: step } }));
            if(window.activateCoursePane){ window.activateCoursePane(step); return; }
            const map = { details:'informasi-umum', module:'kurikulum', participants:'users', publish:'overview' };
            const paneId = map[step] || step;
            try{
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show','active'));
                const target = document.getElementById(paneId);
                if(target){ target.classList.add('show','active'); window.setCourseWizardStep && window.setCourseWizardStep(step); const hashMap = { 'informasi-umum':'#informasi-umum', 'kurikulum':'#kurikulum', 'users':'#users', 'overview':'#overview' }; history.replaceState(null,'',hashMap[paneId]||'#'); }
            }catch(e){ console.warn('Fallback navigate failed', e); }
        }
    });
</script>
