<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class EventHubController extends Controller
{
    // Halaman Landing Page / Katalog Utama
    public function index(Request $request)
    {
        $categories = Category::all();
        
        // Memulai query builder untuk model Event
        $query = Event::with('category')->where('stock', '>', 0);

        // Filter berdasarkan Pencarian Kata Kunci (Nama Event)
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan Lokasi
        if ($request->has('location') && $request->location != '') {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter berdasarkan Tanggal Event
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('event_date', $request->date);
        }

        // Ambil data event terbaru setelah difilter
        $events = $query->latest()->get();

        return view('welcome', compact('events', 'categories'));
    }

    // Halaman Detail Event
    public function show(Event $event)
    {
        return view('event-detail', compact('event'));
    }
}