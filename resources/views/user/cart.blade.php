@extends('layouts.user')

@section('content')
<h2 class="fw-bold mb-4">Keranjang Tiket Anda</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    @if(count($cart) > 0)
        <div class="col-md-8 mb-4">
            @php $total = 0; @endphp
            @foreach($cart as $id => $details)
                @php $total += $details['price'] * $details['quantity']; @endphp
                <div class="card border-0 shadow-sm mb-3 p-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 col-3">
                            <img src="{{ asset('storage/' . $details['poster']) }}" class="img-fluid rounded shadow-sm" alt="poster">
                        </div>
                        <div class="col-md-6 col-9">
                            <h5 class="fw-bold mb-1">{{ $details['title'] }}</h5>
                            <p class="text-muted small mb-0">
    📅 {{ isset($details['date']) ? \Carbon\Carbon::parse($details['date'])->format('d M Y') : 'Tanggal tidak tersedia' }}
</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex justify-content-between align-items-center d-md-block">
                            <span class="fw-bold fs-5 text-dark d-block mb-md-2">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</span>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white sticky-top" style="top: 90px;">
                <h5 class="fw-bold mb-3">Total Belanja</h5>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Total Pembayaran</span>
                    <span class="fw-bold text-primary fs-4">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                
                <form action="{{ route('cart.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">Lanjut ke Pembayaran</button>
                </form>
                <a href="{{ route('home') }}" class="btn btn-light w-100 mt-2 btn-sm">Tambah Tiket Event Lain</a>
            </div>
        </div>
    @else
        <div class="col-12 text-center py-5">
            <h4 class="text-muted">Keranjang belanja Anda masih kosong.</h4>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Lihat Katalog Event</a>
        </div>
    @endif
</div>
@endsection