<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\Pembelian;

class AdminController extends Controller
{
    public function index(Request $request)
{
    // Mendapatkan tahun yang dipilih, jika tidak ada maka default ke tahun sekarang
    $tahun = $request->input('tahun', \date('Y'));


    // Menghitung total produk
    $totalProduk = Produk::count();

    // Menghitung total penjualan
    $totalPenjualan = Penjualan::whereYear('tanggal', $tahun)->sum('total_harga');

    // Menghitung stok produk yang tersisa
    $stokTersisa = Produk::sum('stok');

    // Menghitung total jumlah produk yang terjual
    $totalProdukTerjual = Penjualan::whereYear('tanggal', $tahun)->sum('jumlah');

    $grafikPenjualanLabels = Penjualan::whereYear('tanggal', $tahun)
    ->selectRaw('MONTH(tanggal) as bulan')
    ->groupByRaw('MONTH(tanggal)')
    ->pluck('bulan')
    ->map(function ($bulan) {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $namaBulan[$bulan] ?? 'Tidak Diketahui';
    });



    
    $grafikPenjualanData = Penjualan::whereYear('tanggal', $tahun)
        ->selectRaw('SUM(total_harga) as total_penjualan')
        ->groupByRaw('MONTH(tanggal)')
        ->pluck('total_penjualan');

    // Mendapatkan produk terlaris berdasarkan jumlah terjual
    $produkTerlaris = Produk::withCount('penjualan') // Menghitung jumlah penjualan setiap produk
        ->orderByDesc('penjualan_count') // Mengurutkan berdasarkan jumlah penjualan
        ->take(5) // Menampilkan 5 produk terlaris
        ->get();

    // Kirim data ke view
    return view('admin.index', compact(
        'totalProduk', 
        'totalPenjualan', 
        'stokTersisa',
        'totalProdukTerjual',
        'grafikPenjualanLabels', 
        'grafikPenjualanData', 
        'produkTerlaris',
        'tahun' // Kirim tahun yang dipilih ke view
    ));
}

    
}
