<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PenjualanController;
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



/// Menampilkan daftar user
Route::get('users', [UserController::class, 'index'])->name('user.index');

// Menampilkan form edit user
Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('user.edit');

// Update user
Route::put('users/{id}', [UserController::class, 'update'])->name('user.update');

// Hapus user
Route::delete('users/{id}', [UserController::class, 'destroy'])->name('user.destroy');


use App\Http\Controllers\KeranjangController;
Route::post('/penjualan/simpan', [PenjualanController::class, 'simpanDariKeranjang'])->name('penjualan.simpan');
Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');
Route::get('/penjualan/laporan', [PenjualanController::class, 'laporan'])->name('penjualan.laporan');
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/tambah', [KeranjangController::class, 'tambahKeKeranjang'])->name('keranjang.tambah');
Route::post('/keranjang/ubah-jumlah/{idKeranjang}', [KeranjangController::class, 'ubahJumlah'])->name('keranjang.ubah-jumlah');
Route::delete('/keranjang/hapus/{id}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');
use App\Http\Controllers\KategoriController;

Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');

use App\Http\Controllers\ProdukController;

Route::resource('produk', ProdukController::class);

Route::post('kategori/store', [KategoriController::class, 'storeOrUpdate'])->name('kategori.store');
Route::post('kategori/update/{id}', [KategoriController::class, 'storeOrUpdate'])->name('kategori.update');
Route::get('kategori/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
Route::delete('kategori/delete/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

Route::get('/', [UserController::class, 'login'])->name('login');

Route::get('register', [UserController::class, 'register'])->name('register');
Route::post('register', [UserController::class, 'register_action'])->name('register.action');
Route::get('login', [UserController::class, 'login'])->name('login');
Route::post('login', [UserController::class, 'login_action'])->name('login.action');
Route::get('password', [UserController::class, 'password'])->name('password');
Route::post('password', [UserController::class, 'password_action'])->name('password.action');
Route::get('logout', [UserController::class, 'logout'])->name('logout');




// Routes untuk admin
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
// Routes untuk user
Route::get('/landing', [PenggunaController::class, 'index'])->name('pengguna.index');


use App\Http\Controllers\ProfilController;

Route::get('/profil/{id}', [ProfilController::class, 'show'])->name('profil.show');
// Route untuk menampilkan form create profil
Route::get('/profil/create', [ProfilController::class, 'create'])->name('profil.create');



// Route untuk menyimpan data profil
Route::post('/profil', [ProfilController::class, 'store'])->name('profil.store');
// Route untuk menampilkan form edit profil
Route::get('/profil/edit/{id}', [ProfilController::class, 'edit'])->name('profil.edit');
// Route untuk mengupdate profil
Route::put('/profil/update/{id}', [ProfilController::class, 'update'])->name('profil.update');