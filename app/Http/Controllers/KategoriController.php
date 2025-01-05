<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $kategori = Kategori::all(); // Mengambil semua data kategori
        return view('kategori.index', compact('kategori'));
    }

    // Store or Update data
    public function storeOrUpdate(Request $request, $id = null)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        if ($id) {
            // Jika ada ID, maka update
            $kategori = Kategori::findOrFail($id);
            $kategori->update($validatedData);
            $message = 'Kategori berhasil diperbarui!';
        } else {
            // Jika tidak ada ID, maka buat baru
            Kategori::create($validatedData);
            $message = 'Kategori berhasil ditambahkan!';
        }

        return response()->json(['message' => $message]);
    }

    // Show data for edit
    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return response()->json($kategori); // Kirim data dalam bentuk JSON
    }

    // Delete data
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus!']);
    }
}
