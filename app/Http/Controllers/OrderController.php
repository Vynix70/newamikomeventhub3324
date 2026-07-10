<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function __construct()
    {
        // Konfigurasi dasar Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
        Config::$is3ds = env('MIDTRANS_IS_3D_SECURE');
    }

    /**
     * Menampilkan halaman keranjang belanja
     */
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('user.cart', compact('cart'));
    }

    /**
     * UPDATED: Memasukkan tiket event ke dalam keranjang belanja session (Mendukung Kuantitas Kustom)
     */
    public function addToCart(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        // Tangkap quantity dari form input, pasang default 1 jika kosong
        $quantityRequested = $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            // Hitung total kuantitas baru jika digabung
            $newQuantity = $cart[$id]['quantity'] + $quantityRequested;

            if ($newQuantity <= $event->stock) {
                $cart[$id]['quantity'] = $newQuantity;
            } else {
                return back()->withErrors(['error' => 'Maaf, gabungan jumlah tiket di keranjang Anda melebihi batas kuota tersedia.']);
            }
        } else {
            // Pastikan input awal tidak langsung menjebol stok
            if ($quantityRequested <= $event->stock) {
                $cart[$id] = [
                    "title" => $event->title,
                    "quantity" => $quantityRequested, // <-- Memakai input dari form
                    "price" => $event->price,
                    "poster" => $event->poster,
                    "date" => $event->event_date // Sembari memperbaiki sinkronisasi tanggal
                ];
            } else {
                return back()->withErrors(['error' => 'Jumlah tiket yang Anda minta melebihi stok tersedia.']);
            }
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Tiket berhasil dimasukkan ke keranjang belanja!');
    }

    /**
     * Menghapus item tertentu dari keranjang belanja session
     */
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Tiket berhasil dihapus dari keranjang.');
    }

    /**
     * Memproses keranjang belanja ke sistem database lokal dan Midtrans Snap
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Keranjang belanja Anda kosong.');
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $itemsToBuy = [];
            $itemDetailsMidtrans = []; // Untuk payload Midtrans

            foreach ($cart as $eventId => $details) {
                $event = Event::where('id', $eventId)->lockForUpdate()->first();

                if (!$event || $event->stock < $details['quantity']) {
                    DB::rollBack();
                    return redirect()->route('cart.view')->with('error', "Maaf, stok untuk event '{$details['title']}' tidak mencukupi.");
                }

                $subtotal = $details['price'] * $details['quantity'];
                $totalPrice += $subtotal;
                
                $itemsToBuy[] = [
                    'event_model' => $event,
                    'quantity' => $details['quantity'],
                    'price_per_ticket' => $details['price']
                ];

                // Struktur item detail untuk Midtrans
                $itemDetailsMidtrans[] = [
                    'id' => $event->id,
                    'price' => (int) $details['price'],
                    'quantity' => $details['quantity'],
                    'name' => Str::limit($details['title'], 45)
                ];
            }

            $referenceNumber = 'EVH-' . strtoupper(Str::random(5)) . '-' . time();

            // 1. Simpan transaksi ke database lokal dulu
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'reference_number' => $referenceNumber,
                'total_price' => $totalPrice,
                'payment_status' => 'pending',
            ]);

            foreach ($itemsToBuy as $item) {
                $item['event_model']->decrement('stock', $item['quantity']);

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'event_id' => $item['event_model']->id,
                    'quantity' => $item['quantity'],
                    'price_per_ticket' => $item['price_per_ticket'],
                ]);
            }

            // 2. Buat Payload untuk Request ke Midtrans Snap
            $params = [
                'transaction_details' => [
                    'order_id' => $referenceNumber,
                    'gross_amount' => (int) $totalPrice,
                ],
                'item_details' => $itemDetailsMidtrans,
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
            ];

            // 3. Dapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // 4. Update transaksi lokal dengan Snap Token yang didapat
            $transaction->update(['snap_token' => $snapToken]);

            DB::commit();
            session()->forget('cart');

            return redirect()->route('user.dashboard')->with('success', 'Pesanan berhasil dibuat! Silakan klik tombol bayar.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.view')->with('error', 'Gagal memproses checkout: ' . $e->getMessage());
        }
    }
}