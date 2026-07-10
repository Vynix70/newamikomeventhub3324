<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tambahan Google Fonts & Bootstrap Icons biar lebih interaktif -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .sidebar {
            background-color: #1e293b !important; /* Navy Slate yang elegan */
            min-height: 100vh;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .nav-link-custom {
            color: #94a3b8;
            font-weight: 500;
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .nav-link-custom:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }
        /* Style untuk menandai menu yang sedang aktif dikunjungi */
        .nav-link-custom.active-menu {
            color: #fff;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8); /* Gradasi Biru */
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .logout-btn {
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
            transition: all 0.2s ease;
        }
        .logout-btn:hover {
            background-color: #ef4444;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-2 sidebar p-3 d-flex flex-column justify-content-between">
                <div>
                    <!-- Logo / Brand -->
                    <div class="text-center my-3">
                        <h4 class="fw-bold text-white mb-0" style="letter-spacing: -0.5px;">
                            EventHub <span class="text-warning">CMS</span>
                        </h4>
                        <p class="text-muted small" style="font-size: 0.75rem;">Report & Control Panel</p>
                    </div>
                    <hr class="text-secondary opacity-25">
                    
                    <!-- Menu Navigasi -->
                    <ul class="nav flex-column gap-1">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active-menu' : '' }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}" class="nav-link-custom {{ request()->routeIs('admin.categories.*') ? 'active-menu' : '' }}">
                                <i class="bi bi-tags"></i> Kategori Event
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.events.index') }}" class="nav-link-custom {{ request()->routeIs('admin.events.*') ? 'active-menu' : '' }}">
                                <i class="bi bi-calendar-event"></i> Kelola Event
                            </a>
                        </li>
                        
                        <!-- MENU BARU: TRANSAKSI MIDTRANS -->
                        <li class="nav-item">
                            <a href="{{ route('admin.transactions.index') }}" class="nav-link-custom {{ request()->routeIs('admin.transactions.*') ? 'active-menu' : '' }}">
                                <i class="bi bi-wallet2"></i> Transaksi Tiket
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Bagian Tombol Logout di Paling Bawah Sidebar -->
                <div class="mt-auto pt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn logout-btn btn-sm w-100 py-2 fw-semibold rounded-3">
                            <i class="bi bi-box-arrow-right"></i> Keluar Panel
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- MAIN CONTENT -->
            <div class="col-md-10 p-4 bg-light min-vh-100">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 py-2.5 mb-4 animate__animated animate__fadeIn" style="background-color: #d1fae5; color: #065f46;">
                        ✨ {{ session('success') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>