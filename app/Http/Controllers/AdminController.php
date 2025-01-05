<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produk;
use App\Models\Penjualan;

class AdminController extends Controller
{
    public function index()
    {
        // Menghitung total user
        $totalUser = User::count();

        // Menghitung total produk
        $totalProduk = Produk::count();

        // Menghitung total pendapatan
        $totalPendapatan = Penjualan::sum('total_harga');

        // Return ke view dashboard
        return view('admin.index', compact('totalUser', 'totalProduk', 'totalPendapatan'));
    }
}
