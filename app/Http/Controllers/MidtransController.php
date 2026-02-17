<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    
    public function bayar(Request $request)
    {
        Log::info('Form POST berhasil dikirim.', $request->all());
        // Ambil ID penjualan dari inputan form
        $ids = explode(',', $request->penjualan_ids);
        $ids = array_filter($ids); // buang elemen kosong/null
    
        $total = intval($request->amount); // pastikan bentuk integer
    
        // Validasi sederhana
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data penjualan yang dipilih.');
        }
    
        // Generate Order ID unik
        $orderId = 'ORDER-' . implode('-', $ids) . '-' . time();
    
        // Simpan Order ID dan ubah status pembayaran ke 'pending'
        Penjualan::whereIn('id_penjualan', $ids)->update([
            'midtrans_order_id' => $orderId,
            'payment_status' => 'pending',
        ]);
    
        // Midtrans config (ambil dari config/services.php)
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    
        // Detail customer
        $customerDetails = [
            'first_name' => Auth::user()->name,
            'email' => Auth::user()->email ?? 'dummy@email.com',
        ];
    
        // Data transaksi untuk Midtrans
        $transaction = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => $customerDetails,
            'callbacks' => [
                'finish' => route('bayar.finish', ['order_id' => $orderId]), // Optional
            ],
        ];
    
        try {
            // Buat Snap transaction
            $snapTransaction = \Midtrans\Snap::createTransaction($transaction);
    
            // Arahkan user ke halaman pembayaran Midtrans
            return redirect()->away($snapTransaction->redirect_url);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran.');
        }
    }
    
    public function finish(Request $request)
    {
        $data = $request->all();
    
        // Cek jika pembayaran berhasil
        if (isset($data['transaction_status']) && $data['transaction_status'] === 'settlement') {
            $orderId = $data['order_id'];
    
            Penjualan::where('midtrans_order_id', $orderId)
                ->update(['payment_status' => 'paid']);
    
            return redirect()->route('keranjang.index')->with('berhasil', 'Pembayaran berhasil diselesaikan.');
        }
    
        return redirect()->route('keranjang.index')->with('gagal', 'Status pembayaran tidak valid atau belum selesai.');
    }
    
}
