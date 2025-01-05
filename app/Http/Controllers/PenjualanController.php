<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Penjualan;
use App\Models\Produk;  

class PenjualanController extends Controller
{
    public function laporan(Request $request)
    {
        // Filter tahun
        $tahun = $request->get('tahun', now()->year); // Default tahun saat ini
    
        // Ambil data penjualan berdasarkan tahun
        $penjualan = Penjualan::with('produk')
            ->whereYear('created_at', $tahun)
            ->get();
    
        // Hitung total penjualan dan jumlah produk
        $totalPenjualan = $penjualan->sum('total_harga');
        $totalJumlahProduk = $penjualan->sum('jumlah');
    
        // Format angka
        $formattedTotalPenjualan = number_format($totalPenjualan, 2, ',', '.');
        $formattedTotalJumlahProduk = number_format($totalJumlahProduk, 0, ',', '.');
    
        // Kirim data ke view
        return view('penjualan.laporan', compact(
            'penjualan', 
            'formattedTotalPenjualan', 
            'formattedTotalJumlahProduk', 
            'tahun'
        ));
    }
    

    


    public function index()
    {
        // Ambil data penjualan dari database
        $penjualan = Penjualan::with('produk')->latest()->get();

        // Tampilkan view dengan data penjualan
        return view('penjualan.index', compact('penjualan'));
    }
    /**
     * Simpan data dari keranjang ke tabel penjualan.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simpanDariKeranjang()
    {
        // Ambil data keranjang untuk user tertentu
        $userId = 1; // Ganti dengan autentikasi pengguna jika diperlukan
        $keranjangItems = Keranjang::where('user_id', $userId)->get();
    
        if ($keranjangItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang kosong.');
        }
    
        $totalTransaksi = 0;
        $penjualans = [];
    
        foreach ($keranjangItems as $item) {
            // Simpan data dari keranjang ke tabel penjualan
            $penjualan = Penjualan::create([
                'produk_id'   => $item->produk_id,
                'jumlah'      => $item->jumlah,
                'harga'       => $item->harga_satuan,
                'total_harga' => $item->total_harga,
                'uang_diterima' => 0, // Diisi setelah pembayaran
                'kembalian'   => 0, // Diisi setelah pembayaran
            ]);
    
            // Masukkan ke dalam array penjualan untuk ditampilkan
            $penjualans[] = $penjualan;
    
            // Hitung total transaksi
            $totalTransaksi += $item->total_harga;
    
            // Kurangi stok produk yang terjual
            $produk = Produk::find($item->produk_id);
            if ($produk) {
                $produk->stok -= $item->jumlah;
                $produk->save();
            }
        }
    
        // Hapus semua item di keranjang setelah data dipindahkan
        Keranjang::where('user_id', $userId)->delete();
    
        // Kirim data penjualan dan total transaksi untuk ditampilkan di modal
        return redirect()->route('penjualan.struk')
                         ->with('penjualans', $penjualans)
                         ->with('totalTransaksi', $totalTransaksi)
                         ->with('success', 'Transaksi berhasil disimpan.');
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
    
    
}
