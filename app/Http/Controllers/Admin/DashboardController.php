<?php

namespace App\Http\Controllers; // Sesuaikan namespace jika ditaruh di dalam folder Admin

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data total user terregistrasi dengan role 'user'
        $totalUsers = User::where('role', 'user')->count();

        // Base query untuk transaksi yang sukses (settlement)
        $revenueQuery = Transaction::where('payment_status', 'settlement');
        $ticketQuery = TransactionDetail::whereHas('transaction', function ($query) {
            $query->where('payment_status', 'settlement');
        });

        // 2. Terapkan Filter Waktu berdasarkan Request
        $filter = $request->get('filter', 'all'); // default tampilkan semua
        
        if ($filter == 'today') {
            $revenueQuery->whereDate('created_at', Carbon::today());
            $ticketQuery->whereDate('created_at', Carbon::today());
        } elseif ($filter == 'week') {
            $revenueQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $ticketQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $revenueQuery->whereMonth('created_at', Carbon::now()->month)
                         ->whereYear('created_at', Carbon::now()->year);
            $ticketQuery->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);
        }

        // Hitung total pendapatan dan total tiket terjual setelah filter waktu
        $totalRevenue = $revenueQuery->sum('total_price');
        $totalTicketsSold = $ticketQuery->sum('quantity');

        // 3. Laporan Penjualan per Event (Menggunakan Group By)
        // Kita ingin tahu tiap event terjual berapa tiket dan menghasilkan berapa rupiah
        $eventReports = TransactionDetail::select(
                'event_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(quantity * price_per_ticket) as total_sales')
            )
            ->whereHas('transaction', function ($query) use ($filter) {
                $query->where('payment_status', 'settlement');
                
                if ($filter == 'today') {
                    $query->whereDate('created_at', Carbon::today());
                } elseif ($filter == 'week') {
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($filter == 'month') {
                    $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
                }
            })
            ->groupBy('event_id')
            ->with('event.category')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalRevenue', 
            'totalTicketsSold', 
            'eventReports', 
            'filter'
        ));
    }
}