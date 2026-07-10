@extends('layouts.user')

@section('content')
<!--  UPDATED: Header Dashboard Baru dengan Tombol Eksplorasi Event -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Riwayat Transaksi Anda</h3>
        <p class="text-muted small mb-0">Pantau status tiket yang telah Anda beli</p>
    </div>
    <a href="{{ route('home') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
        🎟️ Jelajahi Event Terbaru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-12">
        
        @forelse($transactions as $trx)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 pt-3">
                    <span class="text-muted small">No. Invoice: <strong>{{ $trx->reference_number }}</strong></span>
                    
                    @if($trx->payment_status == 'pending')
                        <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                    @elseif($trx->payment_status == 'settlement')
                        <span class="badge bg-success">Lunas</span>
                    @else
                        <span class="badge bg-danger">{{ strtoupper($trx->payment_status) }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @foreach($trx->details as $detail)
                        <div class="d-flex align-items-center mb-2 border-bottom pb-2">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0">{{ $detail->event->title }}</h6>
                                <span class="text-muted small">{{ $detail->quantity }} Tiket x Rp {{ number_format($detail->price_per_ticket, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($trx->payment_status == 'settlement' && $detail->qr_code)
                                <div class="text-center ms-3">
                                    <div class="mb-1">
                                        {!! QrCode::size(100)->backgroundColor(255, 255, 255)->generate($detail->qr_code) !!}
                                    </div>
                                    <small class="d-block text-muted style-code mt-1" style="font-size: 10px;">{{ $detail->qr_code }}</small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="text-muted small d-block">Total Bayar</span>
                            <span class="fw-bold text-primary fs-5">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($trx->payment_status == 'pending' && $trx->snap_token)
                            <button class="btn btn-primary btn-sm fw-bold px-4" id="pay-button-{{ $trx->id }}">Bayar Sekarang</button>
                            
                            <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
                            <script type="text/javascript">
                                document.getElementById('pay-button-{{ $trx->id }}').onclick = function(){
                                    snap.pay('{{ $trx->snap_token }}', {
                                        onSuccess: function(result){ location.reload(); },
                                        onPending: function(result){ location.reload(); },
                                        onError: function(result){ location.reload(); }
                                    });
                                };
                            </script>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-light text-center py-4 border-0 shadow-sm">Anda belum memiliki riwayat transaksi pembelian tiket.</div>
        @endforelse
    </div>
</div>
@endsection