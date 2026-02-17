<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PersediaanController;
use App\Http\Controllers\PembelianController;
use App\Models\Penjualan;

// Penjualan Routes (Admin, Kasir, Owner)
Route::middleware(['auth', 'role:admin,kasir,owner'])->group(function () {
    Route::post('penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
});

// Penjualan Edit & Delete (Admin Only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('penjualan/{id_penjualan}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
    Route::put('penjualan/{id_penjualan}', [PenjualanController::class, 'update'])->name('penjualan.update');
    Route::delete('penjualan/{id_penjualan}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
});

// Simpan transaksi dari keranjang (Admin & Kasir)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::post('/penjualan/simpan', [PenjualanController::class, 'simpanDariKeranjang'])->name('penjualan.simpan');
});

// Penjualan Receipt (All Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/penjualan/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');
});
// Midtrans disabled for admin-only app (routes removed)






// Laporan & Dashboard (Admin, Owner, Apoteker)
Route::middleware(['auth', 'role:admin,owner,apoteker'])->get('/penjualan/laporan', [PenjualanController::class, 'laporan'])->name('penjualan.laporan');



// Manajemen Persediaan & Prediksi (Admin, Apoteker, Owner)
Route::middleware(['auth', 'role:admin,apoteker,owner'])->group(function () {
    Route::get('/persediaan', [PersediaanController::class, 'index'])->name('manajemen.index');
    Route::get('/manajemen/prediksi/{id}', [PersediaanController::class, 'prediksi'])->name('manajemen.prediksi');
});




// KERANJANG (Admin & Kasir Only)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang/tambah', [KeranjangController::class, 'tambahKeKeranjang'])->name('keranjang.tambah');
    Route::post('/keranjang/ubah-jumlah/{idKeranjang}', [KeranjangController::class, 'ubahJumlah'])->name('keranjang.ubah-jumlah');
    Route::delete('/keranjang/hapus/{id}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');
});

// PRODUK Management (Admin & Apoteker)
Route::middleware(['auth', 'role:admin,apoteker'])->group(function () {
    Route::resource('produk', ProdukController::class);
});


// // pembelian
// Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
// Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
// Route::get('/pencarian-produk', [PembelianController::class, 'searchProduk'])->name('produk.search');
// Route::get('/riwayat-pembelian', [PembelianController::class, 'riwayat'])->name('pembelian.riwayat');






/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// User Management (ADMIN ONLY)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('user.index');
    Route::get('users/create', [UserController::class, 'create'])->name('user.create');
    Route::post('users', [UserController::class, 'store'])->name('user.store');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('users/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});


Route::get('/', [UserController::class, 'login'])->name('login');
Route::get('register', [UserController::class, 'register'])->name('register');
Route::post('register', [UserController::class, 'register_action'])->name('register.action');
Route::post('login', [UserController::class, 'login_action'])->name('login.action');
Route::get('password', [UserController::class, 'password'])->name('password');
Route::post('password', [UserController::class, 'password_action'])->name('password.action');
Route::get('logout', [UserController::class, 'logout'])->name('logout');





// Dashboard (ADMIN ONLY)
Route::middleware(['auth', 'role:admin'])->get('/admin', [AdminController::class, 'index'])->name('admin.index');

// Owner Dashboard
Route::middleware(['auth', 'role:owner'])->get('/owner', [AdminController::class, 'ownerDashboard'])->name('owner.dashboard');

// Landing Page (All Authenticated Users)
Route::middleware(['auth'])->get('/landing', [PenggunaController::class, 'index'])->name('pengguna.index');


use App\Http\Controllers\ProfilController;

Route::get('profile', [ProfilController::class, 'show'])->name('profile.show');

// Route untuk menampilkan form create profil
Route::get('/profil/create', [ProfilController::class, 'create'])->name('profil.create');



// Route untuk menyimpan data profil
Route::post('/profil', [ProfilController::class, 'store'])->name('profil.store');
// Route untuk menampilkan form edit profil
Route::get('/profil/edit/{id}', [ProfilController::class, 'edit'])->name('profil.edit');
// Route untuk mengupdate profil
Route::put('/profil/update/{id}', [ProfilController::class, 'update'])->name('profil.update');


// Import Routes
Route::middleware(['auth', 'role:admin,apoteker'])->group(function () {
    Route::get('import', [ProdukController::class, 'showImport'])->name('produk.import.form');
    Route::post('/produk/import', [ProdukController::class, 'import'])->name('produk.import');
    Route::post('/produk/deleteAll', [ProdukController::class, 'deleteAll'])->name('produk.deleteAll');
});

// Import penjualan (Admin & Kasir), DeleteAll (Admin only)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('importpenjualan', [PenjualanController::class, 'showImport'])->name('penjualan.import');
    Route::post('/penjualan/import/process', [PenjualanController::class, 'import'])->name('penjualan.import.process');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/penjualan/deleteAll', [PenjualanController::class, 'deleteAll'])->name('penjualan.deleteAll');
});

// Catchall route - Redirect URL yang tidak ditemukan sesuai role
Route::fallback(function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.index');
        } elseif ($user->role === 'apoteker') {
            return redirect()->route('manajemen.index');
        } else {
            return redirect()->route('pengguna.index');
        }
    }
    
    return redirect()->route('login');
});