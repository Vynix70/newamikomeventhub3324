@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Dashboard Ringkasan & Laporan</h2>
        <p class="text-muted mb-0">Pantau performa penjualan tiket EventHub Anda</p>
    </div>
    
    <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
        <label class="text-muted small text-nowrap mb-0">Periode Laporan:</label>
        <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Semua Waktu</option>
            <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Bulan Ini</option>
        </select>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white p-3">
            <div class="card-body">
                <h6 class="text-white-50 uppercase fw-bold small mb-2">Total Pendapatan (Lunas)</h6>
                <h3 class="fw-bold mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white p-3">
            <div class="card-body">
                <h6 class="text-white-50 uppercase fw-bold small mb-2">Total Tiket Terjual</h6>
                <h3 class="fw-bold mb-0">{{ number_format($totalTicketsSold, 0, ',', '.') }} Tiket</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-dark text-white p-3">
            <div class="card-body">
                <h6 class="text-white-50 uppercase fw-bold small mb-2">User Terregistrasi (Pembeli)</h6>
                <h3 class="fw-bold mb-0">{{ $totalUsers }} Pengguna</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0 pt-3">
        <h5 class="fw-bold mb-0 text-dark">Data Penjualan Per Event</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nama Event</th>
                        <th>Kategori</th>
                        <th>Harga Satuan</th>
                        <th class="text-center">Tiket Terjual</th>
                        <th>Sisa Stok Saat Ini</th>
                        <th>Total Omset</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventReports as $report)
                        <tr>
                            <td><strong>{{ $report->event->title }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ $report->event->category->name }}</span></td>
                            <td>Rp {{ number_format($report->event->price, 0, ',', '.') }}</td>
                            <td class="text-center fw-bold text-success">{{ $report->total_qty }}</td>
                            <td>
                                @if($report->event->stock == 0)
                                    <span class="text-danger fw-bold">Habis</span>
                                @else
                                    {{ $report->event->stock }} Pcs
                                @endif
                            </td>
                            <td class="fw-bold text-primary">Rp {{ number_format($report->total_sales, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada data penjualan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection