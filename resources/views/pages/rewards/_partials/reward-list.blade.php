@forelse ($rewards as $reward)
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mentor-card rounded-8 card border border-gray-100 h-100 shadow-sm reward-card-hover">
            <div class="card-body p-8">
                <div class="bg-main-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8">
                    @if($reward->image)
                        <img src="{{ asset('storage/' . $reward->image) }}" alt="{{ $reward->name }}" class="w-100 h-100 object-fit-cover" style="border-radius: 8px; max-height: 140px; object-fit: contain;">
                    @endif
                </div>
                <span class="text-13 py-2 px-10 rounded-pill bg-info-50 text-info-600 mb-16">Reward</span>
                <h5 class="mb-0 fw-bold text-main-700" style="min-height: 28px;">{{ $reward->name }}</h5>
                <p class="text-muted mb-2 mt-2" style="min-height: 40px; font-size: 14px;">{{ $reward->description }}</p>
                <div class="d-flex flex-wrap gap-2 mb-2 mt-2">
                    <span class="badge bg-main-600 text-white">Poin: {{ $reward->points_cost }}</span>
                    <span class="badge bg-secondary">Stok: {{ $reward->stock == -1 ? 'Tidak Terbatas' : $reward->stock }}</span>
                </div>
                <form action="{{ route('rewards.redeem', $reward) }}" method="POST" class="mt-auto reward-claim-form">
                    @csrf
                    <button type="submit" class="btn btn-outline-main rounded-pill w-100 mt-16 reward-claim-btn" {{ $userPoints < $reward->points_cost ? 'disabled' : '' }} data-reward-name="{{ $reward->name }}">
                        {{ $userPoints < $reward->points_cost ? 'Poin Tidak Cukup' : 'Tukar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="text-center w-100 py-4">Belum ada reward tersedia</div>
@endforelse