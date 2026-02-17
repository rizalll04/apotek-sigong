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

// Route untuk menyimpan penjualan baru
Route::post('penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
Route::get('penjualan/{id_penjualan}/edit', [PenjualanController::class, 'edit'])->name('penjualan.edit');
Route::put('penjualan/{id_penjualan}', [PenjualanController::class, 'update'])->name('penjualan.update');
Route::delete('penjualan/{id_penjualan}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
Route::get('/penjualan/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');


// Pembayaran
Route::get('/penjualan/pembayaran', [PenjualanController::class, 'halamanPembayaranNonTunai'])->name('penjualan.pembayaran');
//pembayaran midtrans
use App\Http\Controllers\MidtransController;

Route::post('/bayar', [App\Http\Controllers\MidtransController::class, 'bayar'])->name('bayar.midtrans');

Route::get('/bayar-midtrans/snap/{token}', [MidtransController::class, 'snap'])->name('bayar.snap');
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);


Route::post('/orders/{id}/pay', [PenjualanController::class, 'processPayment'])->name('orders.processPayment');

// Rute untuk menerima callback dari Midtrans setelah transaksi selesai
// Rute untuk menerima callback dari Midtrans setelah transaksi selesai
Route::get('/bayar/success/{order_id}', [MidtransController::class, 'finish'])->name('bayar.finish');





Route::get('/penjualan/laporan', [PenjualanController::class, 'laporan'])->name('penjualan.laporan');
Route::post('/penjualan/simpan', [PenjualanController::class, 'simpanDariKeranjang'])->name('penjualan.simpan');


// Route untuk menampilkan daftar data persediaan (KHUSUS Apoteker)
Route::get('/persediaan', [PersediaanController::class, 'index'])->name('manajemen.index');
Route::get('/manajemen/prediksi/{id}', [PersediaanController::class, 'prediksi'])->name('manajemen.prediksi');




// KERANJANG
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/tambah', [KeranjangController::class, 'tambahKeKeranjang'])->name('keranjang.tambah');
Route::post('/keranjang/ubah-jumlah/{idKeranjang}', [KeranjangController::class, 'ubahJumlah'])->name('keranjang.ubah-jumlah');
Route::delete('/keranjang/hapus/{id}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');


Route::resource('produk', ProdukController::class);


// pembelian
Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
Route::get('/pencarian-produk', [PembelianController::class, 'searchProduk'])->name('produk.search');
Route::get('/riwayat-pembelian', [PembelianController::class, 'riwayat'])->name('pembelian.riwayat');






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


Route::get('/', [UserController::class, 'login'])->name('login');
Route::get('register', [UserController::class, 'register'])->name('register');
Route::post('register', [UserController::class, 'register_action'])->name('register.action');
Route::post('login', [UserController::class, 'login_action'])->name('login.action');
Route::get('password', [UserController::class, 'password'])->name('password');
Route::post('password', [UserController::class, 'password_action'])->name('password.action');
Route::get('logout', [UserController::class, 'logout'])->name('logout');





// Routes untuk admin
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
// Routes untuk user
Route::get('/landing', [PenggunaController::class, 'index'])->name('pengguna.index');


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


Route::get('import', [ProdukController::class, 'showImport'])->name('produk.import.form');
Route::post('/produk/import', [ProdukController::class, 'import'])->name('produk.import');


Route::get('importpenjualan', [PenjualanController::class, 'showImport'])->name('penjualan.import');
Route::post('/penjualan/import/process', [PenjualanController::class, 'import'])->name('penjualan.import.process');// Tambahkan route untuk deleteAll
Route::post('/penjualan/deleteAll', [PenjualanController::class, 'deleteAll'])->name('penjualan.deleteAll');
Route::post('/produk/deleteAll', [ProdukController::class, 'deleteAll'])->name('produk.deleteAll');