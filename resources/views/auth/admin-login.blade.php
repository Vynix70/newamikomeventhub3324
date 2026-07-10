@extends('layouts.auth')

@section('content')
<!-- Tambahan Google Fonts untuk tampilan font yang lebih clean & professional -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body { 
        background: radial-gradient(circle at top right, #2c3e50, #0f172a) !important; 
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .admin-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
    }
    .form-control {
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        transition: all 0.2s ease;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #f59e0b; /* Warna warning/kuning premium */
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
    }
    .btn-admin {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: #fff;
        border: none;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .btn-admin:hover {
        background: linear-gradient(135deg, #334155, #1e293b);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
    }
    .back-link {
        transition: color 0.2s ease;
    }
    .back-link:hover {
        color: #f59e0b !important;
    }
</style>

<div class="row justify-content-center align-items-center min-vh-100 px-3">
    <div class="col-md-4 col-sm-8 col-12">
        
        <!-- Bagian Header/Logo -->
        <div class="text-center mb-4 animate__animated animate__fadeInDown">
            <h2 class="text-white fw-bold tracking-tight mb-1" style="letter-spacing: -0.5px;">
                EventHub <span class="text-warning px-2 py-0.5 rounded" style="background: rgba(245, 158, 11, 0.15); font-size: 0.85em;">CMS</span>
            </h2>
            <p class="text-white-50 small mb-0">Content Management System & Report Panel</p>
        </div>
        
        <!-- Notifikasi Sukses Setelah Logout -->
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 py-2.5 text-center small mb-3 animate__animated animate__fadeIn" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                ✨ {{ session('success') }}
            </div>
        @endif

        <!-- Kotak Formulir Login -->
        <div class="card admin-card shadow-lg border-0 animate__animated animate__fadeInUp">
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 text-center">
                    <h5 class="fw-bold mb-1 text-dark">Sign In Admin</h5>
                    <p class="text-muted small mb-0">Masukkan akun kredensial pusat Anda</p>
                </div>
                
                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    
                    <!-- Input Email -->
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small mb-1">Admin Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="name@eventhub.com" required autocomplete="email" autofocus>
                        @error('email')
                            <div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Input Password -->
                    <div class="mb-4">
                        <label class="form-label text-dark fw-semibold small mb-1">Secure Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    
                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-admin w-100 py-2.5 fw-bold shadow-sm">
                        Masuk Panel Admin
                    </button>
                </form>
            </div>
        </div>
        
       
</div>
@endsection