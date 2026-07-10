@extends('layouts.admin')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Manajemen Event</h2>
        <p class="text-muted mb-0">Tambah, ubah, atau hapus acara/konser musik di EventHub</p>
    </div>
    <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addEventModal">+ Tambah Event Baru</button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Detail Acara</th>
                        <th>Kategori</th>
                        <th>Waktu & Tempat</th>
                        <th>Harga & Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td style="width: 100px;">
                            <img src="{{ asset('storage/' . $event->poster) }}" class="img-thumbnail rounded" style="width: 80px; height: 80px; object-fit: cover;">
                        </td>
                        <td>
                            <h6 class="fw-bold mb-1">{{ $event->title }}</h6>
                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $event->description }}</small>
                        </td>
                        <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $event->category->name }}</span></td>
                        <td>
                            <small class="d-block">📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y - H:i') }}</small>
                            <small class="text-muted d-block">📍 {{ $event->location }}</small>
                        </td>
                        <td>
                            <small class="d-block text-success fw-bold">Rp {{ number_format($event->price, 0, ',', '.') }}</small>
                            <small class="text-muted d-block">📦 Sisa: {{ $event->stock }} tiket</small>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning fw-semibold me-1" data-bs-toggle="modal" data-bs-target="#editEventModal{{ $event->id }}">Edit</button>
                            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger fw-semibold" onclick="return confirm('Hapus event ini? Semua data transaksi terkait akan ikut terhapus.')">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editEventModal{{ $event->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Event: {{ $event->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label small text-muted">Nama / Judul Event</label>
                                                <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Kategori</label>
                                                <select name="category_id" class="form-select" required>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $event->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-muted">Deskripsi Event</label>
                                                <textarea name="description" class="form-control" rows="3" required>{{ $event->description }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Lokasi Tempat Acara</label>
                                                <input type="text" name="location" class="form-control" value="{{ $event->location }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Tanggal & Jam Pelaksanaan</label>
                                                <input type="datetime-local" name="event_date" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($event->event_date)) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Harga Tiket (Rp)</label>
                                                <input type="number" name="price" class="form-control" value="{{ (int)$event->price }}" min="0" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Jumlah Kuota Ketersediaan Tiket</label>
                                                <input type="number" name="stock" class="form-control" value="{{ $event->stock }}" min="0" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-muted">Ganti Gambar Poster (Biarkan kosong jika tidak ingin diubah)</label>
                                                <input type="file" name="poster" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada event yang dibuat. Klik tombol Tambah Event untuk memulai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Buat Event Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small text-muted">Nama / Judul Event</label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Konser Pamungkas Jogja" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">Deskripsi Event</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Tulis rincian informasi acara..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Lokasi Tempat Acara</label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: JEC, Yogyakarta" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Tanggal & Jam Pelaksanaan</label>
                            <input type="datetime-local" name="event_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Harga Tiket (Rp)</label>
                            <input type="number" name="price" class="form-control" placeholder="Contoh: 150000" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Jumlah Kuota Ketersediaan Tiket</label>
                            <input type="number" name="stock" class="form-control" placeholder="Contoh: 500" min="0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">Upload Gambar Poster Event</label>
                            <input type="file" name="poster" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Publish Event</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection