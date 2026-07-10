<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman registrasi user
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses registrasi user baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default pendaftar baru adalah pembeli/user
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Menampilkan halaman login user biasa
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses autentikasi login user biasa (Proteksi khusus dari admin)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Jika admin nyasar lewat sini, tetap kita kick
            if (Auth::user()->role === 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Admin dilarang login melalui pintu khusus user.'
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            // UPDATED: Langsung tembak ke rute /dashboard tanpa intended()
            return redirect('/dashboard'); 
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.'
        ])->onlyInput('email');
    }

    // Menampilkan halaman login khusus admin
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    // Memproses autentikasi login khusus admin (Proteksi dari user biasa)
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Cek jika ternyata yang login adalah user biasa, kita kick/logout
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses ditolak. Halaman ini hanya untuk Super Admin.'
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial admin tidak cocok.'
        ])->onlyInput('email');
    }

    // Memproses logout (Berlaku untuk User maupun Admin)
   // Memproses logout (Berlaku untuk User maupun Admin)
   public function logout(Request $request)
   {
       // 1. Cek dulu role yang sedang login SEBELUM logout dilakukan
       $isAdmin = Auth::check() && Auth::user()->role === 'admin';

       // 2. Jalankan proses logout dan hancurkan session
       Auth::logout();
       $request->session()->invalidate();
       $request->session()->regenerateToken();

       // 3. Kondisi pengalihan rute berdasarkan role tadi
       if ($isAdmin) {
           // Jika dia admin, kembalikan ke halaman login admin
           return redirect()->route('admin.login')->with('success', 'Admin telah berhasil logout.');
       }

       // Jika user biasa, kembalikan ke beranda depan proyek
       return redirect('/')->with('success', 'Anda telah berhasil logout.');
   }
}