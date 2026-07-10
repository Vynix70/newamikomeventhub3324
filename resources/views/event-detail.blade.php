@extends('layouts.user')

@section('content')
<div class="row mt-4">
    <!-- UPDATED: Blok Penanganan Alert Sukses & Error -->
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                ✨ {{ session('success') }} 
                <a href="{{ route('cart.view') }}" class="fw-bold text-success text-decoration-underline ms-2">Lihat Keranjang Belanja</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                ❌ {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <!-- ----------------------------------------- -->

    <!-- Sisi Kiri: Poster Event -->
    <div class="col-md-5 mb-4">
        <img src="{{ asset('storage/' . $event->poster) }}" class="img-fluid rounded-3 shadow-sm w-100" alt="{{ $event->title }}">
    </div>

    <!-- Sisi Kanan: Informasi Detail Event -->
    <div class="col-md-7">
        <span class="badge bg-primary mb-2">{{ $event->category->name }}</span>
        <h1 class="fw-bold mb-3">{{ $event->title }}</h1>
        
        <div class="mb-4 bg-white p-3 rounded-3 shadow-sm">
            <p class="mb-2"><strong>📍 Lokasi:</strong> {{ $event->location }}</p>
            <p class="mb-2"><strong>📅 Waktu:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y, H:i') }} WIB</p>
            <p class="mb-0 text-danger"><strong>🔥 Tersisa:</strong> {{ $event->stock }} Tiket tersedia</p>
        </div>

        <h5 class="fw-bold">Deskripsi Event</h5>
        <p class="text-secondary mb-4">{{ $event->description }}</p>

        <!-- Form Pembelian / Input Keranjang Belanja -->
        <div class="card border-0 shadow-sm bg-white p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">Harga Satuan</span>
                <span class="fw-bold text-success fs-4">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
            </div>

            <form action="{{ route('cart.add', $event->id) }}" method="POST">
                @csrf
                <div class="row align-items-center g-2">
                    <div class="col-md-4">
                        <label class="small text-muted mb-1 d-block">Jumlah Tiket</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $event->stock }}" required>
                    </div>
                    <div class="col-md-8 pt-4">
                        @auth
                            <button type="submit" class="btn btn-primary w-100 py-2">Tambah Ke Keranjang</button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-secondary w-100 py-2">Login untuk Membeli</a>
                        @endauth
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection