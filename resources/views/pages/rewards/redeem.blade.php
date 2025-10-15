@extends('layouts.main')

@section('content')
<div class="container">
    <h1>Tukar Poin Reward</h1>
    <div class="row">
        @forelse ($rewards as $reward)
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if($reward->image)
                        <img src="{{ asset('storage/' . $reward->image) }}" class="card-img-top" alt="{{ $reward->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $reward->name }}</h5>
                        <p class="card-text">{{ $reward->description }}</p>
                        <p><strong>Poin:</strong> {{ $reward->points_cost }}</p>
                        <p><strong>Stok:</strong> {{ $reward->stock == -1 ? 'Tidak Terbatas' : $reward->stock }}</p>
                        <button class="btn btn-primary" disabled>Tukar</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col">
                <p>Tidak ada reward yang tersedia saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
