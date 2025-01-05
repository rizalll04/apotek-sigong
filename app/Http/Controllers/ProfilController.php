<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    // Menampilkan form untuk membuat profil
    public function create()
    {
        return view('profil.create');
    }

    // Menyimpan data profil
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'alamat' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'foto' => 'nullable|image',
        ]);

        // Menyimpan data profil
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            // Jika ada foto, simpan file di storage
            $fotoPath = $request->file('foto')->store('images', 'public');
        }

        $profil = Profil::create([
            'user_id' => Auth::id(),
            'alamat' => $validated['alamat'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'foto' => $fotoPath,
        ]);

        // Menampilkan profil yang baru disimpan
        return view('profil.show', compact('profil'))->with('success', 'Profil berhasil disimpan!');
    }

    // Menampilkan profil berdasarkan user_id
    public function show($id)
    {
        // Cari profil berdasarkan user_id
        $profil = Profil::where('user_id', $id)->first();

        // Jika profil tidak ditemukan, tampilkan halaman create
        if (!$profil) {
            return view('profil.create')->with('error', 'Profil tidak ditemukan!'); 
        }

        // Tampilkan profil pada halaman show
        return view('profil.show', compact('profil'));
    }

    // Menampilkan form untuk mengedit profil
    public function edit($id)
    {
        // Cari profil berdasarkan user_id
        $profil = Profil::where('user_id', $id)->first();

        // Jika profil tidak ditemukan, tampilkan halaman create
        if (!$profil) {
            return view('profil.create')->with('error', 'Profil tidak ditemukan!');
        }

        // Tampilkan form edit dengan data profil
        return view('profil.edit', compact('profil'));
    }

    // Mengupdate profil
    public function update(Request $request, $id)
    {
        // Validasi data
        $request->validate([
            'alamat' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Cari profil berdasarkan user_id
        $profil = Profil::where('user_id', $id)->first();

        // Jika profil tidak ditemukan, tampilkan halaman create
        if (!$profil) {
            return view('profil.create')->with('error', 'Profil tidak ditemukan!');
        }

        // Menyimpan foto jika ada
        $fotoPath = $profil->foto;  // Default menggunakan foto yang lama
        if ($request->hasFile('foto')) {
            // Jika ada foto baru, hapus foto lama dan simpan yang baru
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('images', 'public');
        }

        // Update data profil
        $profil->alamat = $request->alamat;
        $profil->tanggal_lahir = $request->tanggal_lahir;
        $profil->foto = $fotoPath;
        $profil->save();

        // Tampilkan profil yang sudah diperbarui
        return view('profil.show', compact('profil'))->with('success', 'Profil berhasil diperbarui!');
    }
}
