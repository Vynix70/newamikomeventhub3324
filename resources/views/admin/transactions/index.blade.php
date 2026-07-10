@extends('layouts.admin') {{-- Sesuaikan dengan nama layout dashboard admin kamu --}}

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }
    .card-custom {
        border-radius: 14px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    .table thead th {
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 14px;
        border: none;
    }
    .table tbody td {
        padding: 16px 14px;
        vertical-align: middle;
        color: #334155;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }
    .badge-success { background-color: #d1fae5; color: #065f46; }
    .badge-pending { background-color: #fef3c7; color: #92400e; }
    .badge-failed { background-color: #fee2e2; color: #991b1b; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Daftar Transaksi Tiket</h4>
            <p class="text-muted small mb-0">Pantau dan kelola seluruh status pembayaran pesanan masuk</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2.5 small mb-3">
            ✨ {{ session('success') }}
        </div>
    @endif

    <div class="card card-custom bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Pelanggan</th>
                            <th>Event</th>
                            <th>Jumlah</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="fw-bold text-secondary">#{{ $tx->id }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $tx->user->name ?? 'User Terhapus' }}</div>
                                    <div class="text-muted text-xs small">{{ $tx->user->email ?? '-' }}</div>
                                </td>
                                
                                <!-- UPDATED: Menggunakan pengecekan detail relasi multi-item -->
                                <td class="fw-medium text-dark">
                                    @if($tx->details->isNotEmpty())
                                        {{ $tx->details->first()->event->title ?? 'Event Terhapus' }}
                                        @if($tx->details->count() > 1)
                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">+{{ $tx->details->count() - 1 }} Event Lain</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $tx->quantity }} Tiket</td>
                                <td class="fw-bold text-dark">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                
                        

                                <!-- Kolom Status Pembayaran Otomatis -->
                                <td>
                                    @if($tx->payment_status === 'success' || $tx->payment_status === 'settlement')
                                        <span class="badge-status badge-success">Paid / Settlement</span>
                                    @elseif($tx->payment_status === 'pending')
                                        <span class="badge-status badge-pending">Pending</span>
                                    @elseif($tx->payment_status === 'expire')
                                        <span class="badge-status badge-failed" style="background-color: #fee2e2; color: #991b1b;">Expired</span>
                                    @else
                                        <span class="badge-status badge-failed">Failed / Cancel</span>
                                    @endif
                                </td>
                                
                               
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-2">📭</div>
                                    Belum ada transaksi tiket yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Link -->
    <div class="d-flex justify-content-end mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection