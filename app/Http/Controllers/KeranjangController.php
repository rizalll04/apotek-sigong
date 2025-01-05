<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    // Menampilkan halaman keranjang
    public function index(Request $request)
    {
        $query = $request->input('q'); // Untuk pencarian produk
        $produk = null;

        if ($query) {
            $produk = Produk::where('nama_produk', 'like', '%' . $query . '%')->get();
        }

        $userId = 1; // ID user hardcoded untuk contoh, gunakan autentikasi untuk real
        $keranjang = Keranjang::where('user_id', $userId)->with('produk')->get();

        return view('keranjang.index', compact('produk', 'keranjang', 'query'));
    }

    // Tambahkan produk ke keranjang
    public function tambahKeKeranjang(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:tb_user,user_id',
            'produk_id' => 'required|exists:produk,id_produk',
            'jumlah' => 'required|integer|min:1',
        ]);

        $produk = Produk::findOrFail($validated['produk_id']);
        $hargaSatuan = $produk->harga_jual;

        // Periksa apakah produk sudah ada di keranjang
        $keranjang = Keranjang::where('user_id', $validated['user_id'])
            ->where('produk_id', $validated['produk_id'])
            ->first();

        if ($keranjang) {
            // Jika produk sudah ada, update jumlah
            $keranjang->jumlah += $validated['jumlah'];
            $keranjang->total_harga = $keranjang->jumlah * $hargaSatuan;
            $keranjang->save();
        } else {
            // Jika belum ada, tambahkan produk baru
            Keranjang::create([
                'user_id' => $validated['user_id'],
                'produk_id' => $validated['produk_id'],
                'jumlah' => $validated['jumlah'],
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $validated['jumlah'] * $hargaSatuan,
            ]);
        }

        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    // Ubah jumlah produk di keranjang
    public function ubahJumlah(Request $request, $idKeranjang)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $keranjang = Keranjang::findOrFail($idKeranjang);
        $keranjang->jumlah = $validated['jumlah'];
        $keranjang->total_harga = $keranjang->jumlah * $keranjang->harga_satuan;
        $keranjang->save();

        return redirect()->route('keranjang.index')->with('success', 'Jumlah produk berhasil diubah');
    }

    public function hapus($id)
    {
        // Cari item keranjang berdasarkan ID
        $keranjangItem = Keranjang::find($id);

        // Jika item tidak ditemukan, kembalikan error 404
        if (!$keranjangItem) {
            return redirect()->route('keranjang.index')->with('error', 'Item tidak ditemukan.');
        }

        // Hapus item dari keranjang
        $keranjangItem->delete();

        // Redirect kembali ke halaman keranjang dengan pesan sukses
        return redirect()->route('keranjang.index')->with('success', 'Item berhasil dihapus.');
    }
}
