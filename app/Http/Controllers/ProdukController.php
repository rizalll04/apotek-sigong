<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    /**
     * Menampilkan daftar produk
     */
    public function index()
    {
        $produk = Produk::with('kategori')->get(); // Mengambil data produk beserta kategori
        $kategori = Kategori::all(); // Mengambil semua kategori
        return view('produk.index', compact('produk', 'kategori'));
    }

    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kode_produk' => 'required|unique:produk',
                'nama_produk' => 'required',
                'satuan' => 'required',
                'kategori_produk' => 'required',
                'stok' => 'required|integer',
                'hpp' => 'required|numeric',
                'harga_jual' => 'required|numeric',
                'gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            ]);
    
            // Simpan gambar
            if ($request->hasFile('gambar')) {
                $validated['gambar'] = $request->file('gambar')->store('images', 'public');
            }
    
            Produk::create($validated);
            return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Terjadi kesalahan, produk gagal disimpan: ' . $e->getMessage());
        }
    }
    
    /**
     * Mengupdate produk
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'kode_produk' => 'required|unique:produk,kode_produk,' . $produk->id_produk . ',id_produk',
            'nama_produk' => 'required',
            'satuan' => 'required',
            'kategori_produk' => 'required',
            'stok' => 'required|integer',
            'hpp' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Hapus gambar lama jika ada gambar baru
        if ($request->hasFile('gambar')) {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('images', 'public');
        }

        $produk->update($validated);
        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Hapus gambar jika ada
        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus.');
    }
}
