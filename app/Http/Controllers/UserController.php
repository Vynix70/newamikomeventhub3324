<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $transactions = Transaction::with('details.event')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.dashboard', compact('transactions'));
    }
}