<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenggunaController extends Controller
{
   
    public function index()
    {
        return view('pengguna.index');  // Halaman dashboard pengguna
    }

    public function profile()
    {
        // Fungsi untuk menampilkan profil pengguna
        return view('user.profile');
    }
}
