<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Penjualan;
use App\Models\Produk;  

use Carbon\Carbon;
use App\Imports\PenjualanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;


class PenjualanController extends Controller
{



    public function showImport()
    {
        $penjualan = Penjualan::latest()->paginate(10);
        return view('penjualan.import', compact('penjualan'));
    }

    /**
     * Meng-handle proses import dari file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new PenjualanImport, $request->file('file'));

        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil diimport!');
    }
    public function laporan(Request $request)
    {
        $filter = $request->get('filter', 'bulanan'); // Default: bulanan
        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan', now()->month);
        $tanggal = $request->get('tanggal', now()->toDateString());
        $minggu = $request->get('minggu', null); // Minggu ke-
    
        $penjualan = Penjualan::with('produk');
    
        if ($filter == 'harian') {
            $penjualan = $penjualan->whereDate('tanggal', $tanggal);
        } elseif ($filter == 'mingguan') {
            if ($minggu !== null) {
                // Hitung range minggu
                $startOfWeek = now()->setISODate($tahun, $minggu)->startOfWeek();
                $endOfWeek = now()->setISODate($tahun, $minggu)->endOfWeek();
                $penjualan = $penjualan->whereBetween('tanggal', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($filter == 'bulanan') {
            $penjualan = $penjualan->whereYear('tanggal', $tahun)
                                   ->whereMonth('tanggal', $bulan);
        } elseif ($filter == 'tahunan') {
            $penjualan = $penjualan->whereYear('tanggal', $tahun);
        }
    
        $penjualan = $penjualan->get();
    
        $totalPenjualan = $penjualan->sum('total_harga');
        $totalJumlahProduk = $penjualan->sum('jumlah');
    
        $formattedTotalPenjualan = number_format($totalPenjualan, 2, ',', '.');
        $formattedTotalJumlahProduk = number_format($totalJumlahProduk, 0, ',', '.');
    
        return view('penjualan.laporan', compact(
            'penjualan',
            'formattedTotalPenjualan',
            'formattedTotalJumlahProduk',
            'filter',
            'tahun',
            'bulan',
            'tanggal',
            'minggu'
        ));
    }
    
    

    public function index(Request $request)
    {
        // Ambil input bulan dan tahun dari request
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        
        // Query dasar untuk mendapatkan data penjualan
        $penjualanQuery = Penjualan::with('produk')->latest();
        
        // Filter berdasarkan bulan jika ada
        if ($bulan) {
            $penjualanQuery->whereMonth('tanggal', $bulan);
        }
        
        // Filter berdasarkan tahun jika ada
        if ($tahun) {
            $penjualanQuery->whereYear('tanggal', $tahun);
        }
        
        // Filter berdasarkan role: kasir hanya melihat transaksi miliknya
        $user = auth()->user();
        if ($user && $user->role === 'kasir') {
            $penjualanQuery->where('user_id', $user->user_id);
        }

        // Ambil data penjualan yang sudah difilter
        $penjualan = $penjualanQuery->get();
        
        // Ambil bulan dan tahun yang unik dari data penjualan
        $months = $penjualan->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->tanggal)->format('m'); // Ambil bulan
        });
    
        $years = $penjualan->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->tanggal)->format('Y'); // Ambil tahun
        });
    
        // Tampilkan view dengan data penjualan dan filter bulan/tahun
        return view('penjualan.index', compact('penjualan', 'months', 'years'));
    }
    

    /**
     * Simpan data dari keranjang ke tabel penjualan.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simpanDariKeranjang(Request $request)
    {
        $metode = $request->input('metode_pembayaran');
        $uangDiterima = intval($request->input('uang_diterima', 0));
        $buktiPath = null;
        if ($metode === 'Non Tunai' && $request->hasFile('bukti_transfer_file')) {
            $request->validate([
                'bukti_transfer_file' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);
            $buktiPath = $request->file('bukti_transfer_file')->store('bukti_transfer', 'public');
        }
        
        $userId = auth()->user() ? auth()->user()->user_id : null; // Gunakan primary key tb_user
        $keranjangItems = Keranjang::where('user_id', $userId)->get();
    
        if ($keranjangItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang kosong.');
        }
    
        $totalTransaksi = 0;
        $penjualans = [];
    
        // Validate cash payment
        if ($metode === 'Cash' && $uangDiterima <= 0) {
            return redirect()->back()->with('error', 'Uang diterima harus diisi untuk pembayaran cash.');
        }
    
        foreach ($keranjangItems as $item) {
            $kembalian = 0;
            if ($metode === 'Cash') {
                $kembalian = $uangDiterima - $item->total_harga;
            }
            
            $penjualan = Penjualan::create([
                'user_id'            => $userId,
                'produk_id'          => $item->produk_id,
                'jumlah'             => $item->jumlah,
                'harga'              => $item->harga_satuan,
                'total_harga'        => $item->total_harga,
                'metode_pembayaran'  => $metode,
                'uang_diterima'      => $metode === 'Cash' ? $uangDiterima : null,
                'kembalian'          => $kembalian,
                'bukti_transfer'     => $metode === 'Non Tunai' ? $buktiPath : null,
                'tanggal'            => now(),
                'payment_status'     => 'paid',
            ]);
    
            $penjualans[] = $penjualan;
            $totalTransaksi += $item->total_harga;
    
            // Kurangi stok
            $produk = Produk::find($item->produk_id);
            if ($produk) {
                $produk->stok -= $item->jumlah;
                $produk->save();
            }
        }
    
        // Bersihkan keranjang
        Keranjang::where('user_id', $userId)->delete();
    
        // Semua metode (Cash & Non Tunai) langsung dianggap lunas dan menuju struk
        return redirect()->route('penjualan.struk')
                         ->with('penjualans', $penjualans)
                         ->with('totalTransaksi', $totalTransaksi)
                         ->with('success', 'Transaksi berhasil disimpan.');
    }


    public function halamanPembayaranNonTunai()
    {
        // Ambil semua data penjualan dengan status pending
        $penjualans = Penjualan::where('payment_status', 'pending')->get();
    
        // Hitung total transaksi dari yang pending saja
        $totalTransaksi = $penjualans->sum('total_harga');
    
        return view('penjualan.pembayaran', compact('penjualans', 'totalTransaksi'));
    }
    
    


    public function edit($id_penjualan)
    {
        $penjualan = Penjualan::findOrFail($id_penjualan);
    
        // Pastikan tanggal diubah menjadi objek Carbon jika belum
        $penjualan->tanggal = Carbon::parse($penjualan->tanggal);
    
        $produk = Produk::all(); // Ambil data produk yang tersedia
        return view('penjualan.edit', compact('penjualan', 'produk'));
    }
    

    public function update(Request $request, $id_penjualan)
    {
        // Validasi input
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|numeric',
            'harga' => 'required|numeric',
            'tanggal' => 'required|date', // Menambahkan validasi tanggal
        ]);
    
        // Cari data penjualan berdasarkan id_penjualan
        $penjualan = Penjualan::findOrFail($id_penjualan);
    
        // Update data penjualan
        $penjualan->produk_id = $request->produk_id;
        $penjualan->jumlah = $request->jumlah;
        $penjualan->harga = $request->harga;
        $penjualan->total_harga = $request->jumlah * $request->harga;
    
        // Memperbarui tanggal penjualan sesuai dengan request, pastikan formatnya benar
        $penjualan->tanggal = Carbon::parse($request->tanggal); // Menggunakan Carbon untuk mengonversi tanggal
    
        // Simpan perubahan
        $penjualan->save();
    
        // Redirect ke halaman penjualan dengan pesan sukses
        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui.');
    }
    
    

    public function destroy($id_penjualan)
    {
        $penjualan = Penjualan::findOrFail($id_penjualan);  // Menggunakan id_penjualan
        $penjualan->delete();
        return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil dihapus.');
    }

    public function struk()
    {
        // Mengambil waktu session sebelumnya
        $lastActivity = session('last_activity');
        $currentTime = now();
    
        // Mengecek apakah sudah lebih dari 2 menit sejak aktivitas terakhir
        if ($lastActivity && $currentTime->diffInMinutes($lastActivity) > 2) {
            // Menghapus session jika sudah lebih dari 2 menit
            session()->forget(['penjualans', 'totalTransaksi', 'last_activity']);
            return redirect()->route('keranjang.index')->with('error', 'Session telah kadaluarsa.');
        }
    
        // Memperbarui waktu aktivitas terakhir
        session(['last_activity' => $currentTime]);
    
        // Mengambil data dari session
        $penjualans = session('penjualans');
        $totalTransaksi = session('totalTransaksi');
    
        return view('penjualan.struk', compact('penjualans', 'totalTransaksi'));
    }


    public function deleteAll()
{
    // Menghapus semua data penjualan
    Penjualan::truncate();

    // Redirect dengan pesan sukses
    return redirect()->route('penjualan.index')->with('success', 'Semua transaksi telah dihapus.');
}






// // Pembayaran Midtrans

// public function processPayment($id)
//     {
//         $order = Order::findOrFail($id);
    
//         // Update order status
//         // $order->update(['payment_status' => 'pending']);
    
//         // Buat Order ID unik untuk Midtrans
//         $midtrans_order_id = 'ORDER-' . $order->id . '-' . time();
//         $order->update(['midtrans_order_id' => $midtrans_order_id]);
    
//         // Konfigurasi Midtrans
//         Config::$serverKey = config('services.midtrans.server_key');
//         Config::$isProduction = false;
//         Config::$isSanitized = true;
//         Config::$is3ds = true;
    
//         // Data pelanggan dari order
//         $customer_details = [
//             'first_name' => $order->customer_name,
//             'email' => $order->customer_email,
//             'phone' => $order->customer_phone ?? '081234567890',
//         ];
    
//         // Detail item
//         $item_details = [
//             [
//                 'id' => $order->product->id,
//                 'price' => (int) number_format($order->product->price, 0, '', ''),
//                 'quantity' => $order->quantity,
//                 'name' => $order->product->name,
//             ]
//         ];
    
//         // Data transaksi
//         $transaction = [
//             'transaction_details' => [
//                 'order_id' => $midtrans_order_id,
//                 'gross_amount' => (int) number_format($order->total_price, 0, '', ''),
//             ],
//             'customer_details' => $customer_details,
//             'item_details' => $item_details,
//             'callbacks' => [
//                 'finish' => route('orders.finish', ['id' => $order->id]),  // Pastikan menggunakan ID yang benar
//             ],
//         ];
    
//         try {
//             $snapTransaction = Snap::createTransaction($transaction);
//             // Redirect ke Midtrans untuk menyelesaikan pembayaran
//             return redirect()->away($snapTransaction->redirect_url);
//         } catch (\Exception $e) {
//             Log::error('Midtrans Payment Error: ' . $e->getMessage());
//             return redirect()->route('orders.index')->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
//         }
//     }
    
    // public function finish($id)
    // {
    //     // Mengambil order berdasarkan id
    //     $order = Order::findOrFail($id);
        
    //     // Cek status pembayaran yang diterima
    //     $transaction_status = request()->get('transaction_status');
    //     Log::info('Transaction Status: ' . $transaction_status);  // Memastikan status yang diterima
        
    //     // Periksa jika transaction_status ada
    //     if (!$transaction_status) {
    //         Log::error('Status transaksi tidak ditemukan untuk order ID: ' . $id);
    //         return redirect()->route('orders.index')->with('error', 'Status transaksi tidak ditemukan.');
    //     }
    
    //     // Update status pembayaran berdasarkan transaction_status
    //     try {
    //         switch ($transaction_status) {
    //             case 'settlement':
    //                 $order->payment_status = 'paid';
    //                 break;
    
    //             case 'pending':
    //                 $order->payment_status = 'pending';
    //                 break;
    
    //             case 'failed':
    //                 $order->payment_status = 'failed';
    //                 break;
    
    //             default:
    //                 $order->payment_status = 'cancelled';
    //                 break;
    //         }
    
    //         // Simpan perubahan status pembayaran
    //         if (!$order->save()) {
    //             Log::error('Gagal memperbarui status pembayaran untuk order ID: ' . $id);
    //             return redirect()->route('orders.index')->with('error', 'Gagal memperbarui status pembayaran.');
    //         }
    
    //         // Jika berhasil memperbarui
    //         Log::info('Status pembayaran berhasil diperbarui untuk order ID: ' . $id);
    //         return redirect()->route('orders.index')->with('success', 'Pembayaran diproses dengan status: ' . $transaction_status);
    
    //     } catch (\Exception $e) {
    //         Log::error('Error saat memperbarui status pembayaran untuk order ID: ' . $id . ' - ' . $e->getMessage());
    //         return redirect()->route('orders.index')->with('error', 'Terjadi kesalahan saat memperbarui status pembayaran.');
    //     }
    // }

}
