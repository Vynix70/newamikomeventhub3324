<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('category')->latest()->get();
        $categories = Category::all();
        return view('admin.events.index', compact('events', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'location' => 'required|string',
            'event_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'poster' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Proses Upload Poster ke folder storage/public/posters
        $path = $request->file('poster')->store('posters', 'public');

        Event::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'price' => $request->price,
            'stock' => $request->stock,
            'poster' => $path,
        ]);

        return back()->with('success', 'Event baru berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'location' => 'required|string',
            'event_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['category_id', 'title', 'description', 'location', 'event_date', 'price', 'stock']);

        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada
            if ($event->poster) {
                Storage::disk('public')->delete($event->poster);
            }
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $event->update($data);

        return back()->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        if ($event->poster) {
            Storage::disk('public')->delete($event->poster);
        }
        $event->delete();
        return back()->with('success', 'Event berhasil dihapus.');
    }
}