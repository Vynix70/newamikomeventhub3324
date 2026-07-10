@extends('layouts.app') <!-- Pastikan ini mengarah ke master layout frontend kamu -->

@section('content')
<div class="container py-5">
    
    <!-- NOTIFIKASI KERANJANG BELANJA -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <span class="fs-4 me-2">🛒</span>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <a href="/cart" class="alert-link ms-2 fw-bold text-decoration-underline">Lihat Keranjang Belanja →</a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <span class="fs-4 me-2">⚠️</span>
                <div>
                    <strong>Gagal!</strong> {{ $errors->first('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- HERO SECTION -->
    <div class="p-5 mb-5 bg-light rounded-3 shadow-sm text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container-fluid py-4 position-relative" style="z-index: 2;">
            <h1 class="display-5 fw-bold">Temukan Event Musik Terbaik di EventHub</h1>
            <p class="col-md-8 mx-auto fs-5 opacity-75">Pesan tiket konser, festival, dan panggung pertunjukan favoritmu dengan mudah, cepat, dan aman.</p>
        </div>
    </div>

    <!-- DAFTAR CARD EVENT -->
    <h3 class="fw-bold mb-4">✨ Daftar Event Tersedia</h3>
    
    <div class="row g-4">
        @forelse($events as $event)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden transition-card">
                    <!-- Poster Event -->
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $event->poster) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 240px; object-fit: cover;">
                        <span class="position-absolute top-0 end-0 bg-primary text-white px-3 py-1 m-3 rounded-pill small fw-bold shadow-sm">
                            {{ $event->category->name }}
                        </span>
                    </div>
                    
                    <!-- Detail Konten -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-dark mb-2">{{ $event->title }}</h5>
                        <p class="card-text text-muted small text-truncate-2 flex-grow-1 mb-3">{{ $event->description }}</p>
                        
                        <div class="mb-3 small text-secondary">
                            <div class="mb-1">📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y - H:i') }} WIB</div>
                            <div>📍 {{ $event->location }}</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">Harga Tiket</small>
                                <span class="text-success fw-bold fs-5">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block text-end" style="font-size: 11px;">Sisa Kuota</small>
                                <span class="badge {{ $event->stock > 10 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} fw-bold">
                                    {{ $event->stock }} Tiket
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="card-footer bg-white border-0 p-3 pt-0">
                        @if($event->stock > 0)
                            <form action="{{ route('cart.add', $event->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-2 py-2">
                                    🎟️ Beli & Tambah Keranjang
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary w-100 fw-bold rounded-2 py-2 disabled" disabled>
                                ❌ Tiket Habis Bis
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="fs-1 mb-2">🎈</div>
                <h5 class="text-muted">Belum ada event musik aktif saat ini. Kembali lagi nanti ya!</h5>
            </div>
        @endforelse
    </div>
</div>

<style>
    /* Efek hover halus pada card event */
    .transition-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>
@endsection