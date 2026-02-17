<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    // Menampilkan halaman keranjang
    public function index(Request $request)
    {
        $query = $request->input('q'); // Untuk pencarian produk

        // Jika ada query, lakukan pencarian; jika tidak, tampilkan daftar awal (mis. 20 produk pertama)
        if ($query) {
            $produk = Produk::where('nama', 'like', '%' . $query . '%')->get();
        } else {
            // Ambil daftar produk awal untuk ditampilkan tanpa perlu mencari
            $produk = Produk::orderBy('nama', 'asc')->limit(20)->get();
        }

        // Use authenticated user instead of hardcoded user_id
        $userId = auth()->id() ?? 1; // Fallback to 1 if not authenticated
        $keranjang = Keranjang::where('user_id', $userId)->with('produk')->get();

        return view('keranjang.index', compact('produk', 'keranjang', 'query'));
    }

    // Tambahkan produk ke keranjang
    public function tambahKeKeranjang(Request $request)
    {
        // Gunakan user_id dari form atau user yang sedang login
        $userId = $request->input('user_id') ?? auth()->id();
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $validated = $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
        ]);
        
        // Verifikasi user_id ada di tabel tb_user
        if (!User::where('user_id', $userId)->exists()) {
            return redirect()->route('keranjang.index')->with('error', 'User tidak valid');
        }

        $produk = Produk::findOrFail($validated['produk_id']);
        $hargaSatuan = $produk->harga_jual;

        // Periksa apakah produk sudah ada di keranjang
        $keranjang = Keranjang::where('user_id', $userId)
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
                'user_id' => $userId,
                'produk_id' => $validated['produk_id'],
                'jumlah' => $validated['jumlah'],
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $validated['jumlah'] * $hargaSatuan,
            ]);
        }

        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    /**
     * Ubah jumlah produk di keranjang
     *
     * Supports both AJAX and regular form submission:
     * - AJAX requests: Returns JSON response with updated totals
     * - Form submission: Returns redirect with success message
     */
    public function ubahJumlah(Request $request, $idKeranjang)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'jumlah' => 'required|integer|min:1',
            ]);

            // Find cart item with product details
            $keranjang = Keranjang::with('produk')->findOrFail($idKeranjang);

            // Check stock validation
            if ($validated['jumlah'] > $keranjang->produk->stok) {
                $message = "Stok tidak cukup. Stok tersedia: {$keranjang->produk->stok}";

                // Return JSON for AJAX requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                // Return redirect for regular form submission
                return redirect()->route('keranjang.index')->with('error', $message);
            }

            // Update quantity and total_harga
            $keranjang->jumlah = $validated['jumlah'];
            $keranjang->total_harga = $validated['jumlah'] * $keranjang->harga_satuan;
            $keranjang->save();

            // Calculate updated cart totals for authenticated user
            $userId = auth()->id() ?? 1;
            $cartItems = Keranjang::where('user_id', $userId)->get();
            $subtotal = (int) $cartItems->sum('total_harga');
            $discount = 0; // Adjust with discount logic if applicable
            $total = $subtotal - $discount;

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quantity berhasil diupdate',
                    'item_total' => (int) $keranjang->total_harga,
                    'grandtotal' => [
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'total' => $total
                    ]
                ]);
            }

            // Return redirect for regular form submission
            return redirect()->route('keranjang.index')->with('success', 'Jumlah produk berhasil diubah');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $message = 'Item keranjang tidak ditemukan';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 404);
            }

            return redirect()->route('keranjang.index')->with('error', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                // Return first validation error message
                $firstError = collect($e->errors())->flatten()->first() ?? 'Validasi gagal';
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            \Log::error('ubahJumlah error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat update quantity'
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat update quantity');
        }
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
