@extends('layouts.user')

@section('content')
<div class="card border-0 shadow-sm p-4 mb-4 bg-white">
    <h5 class="fw-bold mb-3">Cari Event Seru Favoritmu</h5>
    <form action="{{ route('home') }}" method="GET" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari nama event..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="location" class="form-control" placeholder="Lokasi..." value="{{ request('location') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
    </form>
</div>

<h4 class="fw-bold mb-3 text-secondary">Event Terbaru</h4>
<div class="row row-cols-1 row-cols-md-3 g-4">
    @forelse($events as $event)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                <img src="{{ asset('storage/' . $event->poster) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 220px; object-fit: cover;">
                <div class="card-body">
                    <span class="badge bg-primary-subtle text-primary mb-2">{{ $event->category->name }}</span>
                    <h5 class="card-title fw-bold text-dark">{{ $event->title }}</h5>
                    <p class="text-muted small mb-1">📍 {{ $event->location }}</p>
                    <p class="text-muted small mb-3">📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y - H:i') }} WIB</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success fs-5">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                        <a href="{{ route('event.detail', $event->id) }}" class="btn btn-sm btn-outline-primary">Detail Tiket</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Tidak ada event yang cocok dengan pencarian Anda.</p>
        </div>
    @endforelse
</div>
@endsection