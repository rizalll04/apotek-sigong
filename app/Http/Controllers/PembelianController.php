<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Produk;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function riwayat(Request $request)
    {
        // Ambil bulan dan tahun dari input request, jika tidak ada, gunakan nilai default
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
    
        // Query pembelian dengan filter berdasarkan bulan dan tahun
        $pembelian = Pembelian::query();
    
        if ($bulan) {
            // Jika bulan dipilih, filter berdasarkan bulan
            $pembelian->whereMonth('tanggal', $bulan);
        }
    
        if ($tahun) {
            // Jika tahun dipilih, filter berdasarkan tahun
            $pembelian->whereYear('tanggal', $tahun);
        }
    
        // Ambil data pembelian yang sudah difilter, urutkan berdasarkan tanggal terbaru
        $pembelian = $pembelian->orderBy('tanggal', 'desc')->get();
    
        // Kirim data pembelian ke view
        return view('pembelian.riwayat', compact('pembelian'));
    }
    
    public function index()
    {
        $produk = Produk::all(); // Mengambil semua data produk dari database
        return view('pembelian.index', compact('produk')); // Mengirimkan data produk ke view
    }

    public function searchProduk(Request $request)
    {
        $query = $request->get('query');
        $produk = Produk::where('nama', 'like', '%' . $query . '%')->get();
    
        return response()->json($produk); // Mengembalikan data produk dalam format JSON
    }
    

  // Fungsi untuk menyimpan data pembelian
public function store(Request $request)
{
    // Validasi inputan
    $request->validate([
        'id_produk' => 'required|exists:produk,id', // Pastikan id_produk ada di tabel produk
        'tanggal' => 'required|date', // Validasi tanggal
        'jumlah' => 'required|integer|min:1', // Validasi jumlah minimal 1
        'harga_satuan' => 'required|numeric|min:0', // Validasi harga satuan minimal 0
        'supplier' => 'required|string|max:255', // Validasi supplier (string dengan panjang maksimal 255)
    ]);

    // Ambil produk berdasarkan id_produk
    $produk = Produk::findOrFail($request->id_produk);

    // Hitung total harga pembelian
    $total_harga = $request->jumlah * $request->harga_satuan;

    // Simpan data pembelian
    Pembelian::create([
        'id_produk' => $produk->id,
        'tanggal' => $request->tanggal,
        'jumlah' => $request->jumlah,
        'harga_satuan' => $request->harga_satuan,
        'total_harga' => $total_harga,
        'supplier' => $request->supplier,
    ]);

    // Update stok produk setelah pembelian
    $produk->stok += $request->jumlah;
    $produk->save();

    // Redirect ke halaman yang sama dengan pesan sukses
    return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dan stok produk terupdate.');
}

}
