<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction; 
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
       // Menggunakan details.event untuk mengambil data event di dalam detail transaksi
$transactions = Transaction::with(['user', 'details.event'])
->latest()
->paginate(10);

        return view('admin.transactions.index', compact('transactions'));
    }
}