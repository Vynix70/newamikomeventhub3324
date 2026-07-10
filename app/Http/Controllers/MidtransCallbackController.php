<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransCallbackController extends Controller
{
    public function handleNotification(Request $request)
    {
        // 1. Validasi SHA512 signature key dari Midtrans
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type;

        // 2. Cari transaksi berdasarkan Nomor Invoice / Reference Number
        $transaction = Transaction::where('reference_number', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // 3. Cegah proses berulang jika statusnya di database sudah settlement/success
        if ($transaction->payment_status === 'settlement' || $transaction->payment_status === 'success') {
            return response()->json(['message' => 'Transaction already processed']);
        }

        DB::beginTransaction();
        try {
            // Selalu perbarui tipe pembayaran yang digunakan pengguna
            $transaction->update(['payment_type' => $paymentType]);

            // 4. Logika Penerjemahan Status Midtrans API ke Database
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                
                // Set status ke settlement agar sinkron dengan Blade User & Admin
                $transaction->update(['payment_status' => 'settlement']);

                // Generate Kode QR unik untuk setiap detail tiket yang dibeli
                foreach ($transaction->details as $detail) {
                    $uniqueTicketCode = 'TIC-' . strtoupper(Str::random(4)) . '-' . $detail->id . '-' . time();
                    $detail->update(['qr_code' => $uniqueTicketCode]);
                    
                    // Potong stok per event di detail item secara langsung
                    $event = $detail->event;
                    if ($event && $event->stock > 0) {
                        $event->decrement('stock', $detail->quantity);
                    }
                }

            } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'])) {
                
                $transaction->update(['payment_status' => $transactionStatus]);

                // Mengembalikan stok tiket ke event semula dengan aman
                foreach ($transaction->details as $detail) {
                    $event = $detail->event;
                    if ($event) {
                        $event->increment('stock', $detail->quantity);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Callback handled successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Midtrans callback: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing callback: ' . $e->getMessage()], 500);
        }
    }
}