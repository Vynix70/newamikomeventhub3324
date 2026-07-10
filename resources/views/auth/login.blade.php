@extends('layouts.auth')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-4">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4 fw-bold text-primary">EventHub Login</h3>
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">Masuk</button>
                    <p class="text-center text-muted small">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection