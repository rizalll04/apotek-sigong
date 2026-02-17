<?php

namespace App\Http\Controllers;
use App\Imports\ProdukImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Produk;
use App\Models\Keranjang;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ProdukController extends Controller
{

    public function showImport()
    {
        return view('produk.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            Excel::import(new ProdukImport, $request->file('file'));
            return redirect()->route('produk.index')->with('success', 'Data produk berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('produk.index')->with('error', 'Gagal mengimport data! '.$e->getMessage());
        }
    }

    public function index(Request $request)
    {
        // Menangkap nilai pencarian
        $search = $request->input('search');
    
        // Jika ada kata kunci pencarian, filter produk berdasarkan nama
        $produk = Produk::when($search, function ($query, $search) {
            return $query->where('nama', 'like', '%' . $search . '%');
        })->get();
    
        return view('produk.index', compact('produk', 'search'));
    }
    
    // Simpan data produk baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tanggal_kadaluarsa' => 'nullable|date', // Validasi kolom tanggal_kadaluarsa
        ]);

        Produk::create($request->all());

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }

    // Update data produk
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tanggal_kadaluarsa' => 'nullable|date', // Validasi kolom tanggal_kadaluarsa
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update($request->all());

        return redirect()->back()->with('success', 'Produk berhasil diupdate!');
    }

    // Hapus data produk
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus!');
    }


    public function deleteAll()
{
    // Nonaktifkan constraint foreign key sementara
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Hapus semua data di keranjang
    Keranjang::whereNotNull('produk_id')->delete();

    // Hapus semua data produk
    Produk::truncate();

    // Aktifkan kembali constraint foreign key
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // Redirect ke halaman produk dengan pesan sukses
    return redirect()->route('produk.index')->with('success', 'Semua produk telah dihapus dan ID auto increment direset.');
}

    

}
