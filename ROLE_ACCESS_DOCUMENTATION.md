# Dokumentasi Role & Akses Sistem Apotek

## Struktur Role

Sistem ini memiliki 4 role utama dengan akses yang berbeda:

### 1. **ADMINISTRATOR** (Role: `admin`)
Memiliki akses penuh ke semua fitur sistem.

**Akses:**
- ✅ **Dashboard** - Lihat statistik dan metrik sistem
- ✅ **Kelola User** - Tambah, edit, hapus user
- ✅ **Kelola Obat/Produk** - Tambah, edit, hapus, import produk
- ✅ **Input Transaksi** - Buat transaksi penjualan baru
- ✅ **Lihat Riwayat Transaksi** - Lihat dan edit riwayat transaksi
- ✅ **Peramalan TES** - Akses peramalan permintaan produk
- ✅ **Lihat Laporan** - Akses laporan penjualan

**Routes yang Dapat Diakses:**
```
GET  /admin                          (Dashboard)
GET  /users                          (Manage Users)
POST /users                          (Create User)
PUT  /users/{id}                     (Update User)
DELETE /users/{id}                   (Delete User)

GET  /produk                         (Product List)
POST /produk                         (Create Product)
PUT  /produk/{id}                    (Update Product)
DELETE /produk/{id}                  (Delete Product)
GET  /import                         (Import Products)
POST /produk/import                  (Process Import)

POST /penjualan                      (Store Transaction)
GET  /penjualan                      (Transaction History)
GET  /penjualan/{id}/edit            (Edit Transaction)
PUT  /penjualan/{id}                 (Update Transaction)
DELETE /penjualan/{id}               (Delete Transaction)
POST /penjualan/simpan               (Save from Cart)

GET  /persediaan                     (Stock Management)
GET  /manajemen/prediksi/{id}        (Forecast)
GET  /penjualan/laporan              (Reports)
```

---

### 2. **KASIR** (Role: `kasir`)
Bertugas untuk input transaksi dan melihat riwayat.

**Akses:**
- ❌ Dashboard (TIDAK MEMILIKI AKSES)
- ❌ Kelola User (TIDAK MEMILIKI AKSES)
- ❌ Kelola Obat (TIDAK MEMILIKI AKSES)
- ✅ **Input Transaksi** - Buat transaksi penjualan (melalui keranjang)
- ✅ **Lihat Riwayat Transaksi** - Lihat riwayat transaksi
- ❌ Peramalan (TIDAK MEMILIKI AKSES)
- ❌ Laporan (TIDAK MEMILIKI AKSES)

**Routes yang Dapat Diakses:**
```
GET  /keranjang                      (Shopping Cart)
POST /keranjang/tambah               (Add to Cart)
POST /keranjang/ubah-jumlah/{id}     (Update Quantity)
DELETE /keranjang/hapus/{id}         (Remove from Cart)

POST /penjualan                      (Store Transaction)
GET  /penjualan                      (Transaction History)
GET  /penjualan/{id}/edit            (Edit Transaction)
PUT  /penjualan/{id}                 (Update Transaction)
DELETE /penjualan/{id}               (Delete Transaction)
POST /penjualan/simpan               (Save from Cart)

GET  /penjualan/struk                (Receipt)
GET  /penjualan/pembayaran           (Payment Page)
```

---

### 3. **OWNER** (Role: `owner`)
Melihat performa bisnis dan laporan penjualan.

**Akses:**
- ❌ Dashboard (TIDAK MEMILIKI AKSES - hanya statistik terbatas)
- ❌ Kelola User (TIDAK MEMILIKI AKSES)
- ❌ Kelola Obat (TIDAK MEMILIKI AKSES)
- ❌ Input Transaksi (TIDAK MEMILIKI AKSES)
- ✅ **Lihat Riwayat Transaksi** - Lihat saja, tidak bisa edit
- ✅ **Peramalan TES** - Prediksi permintaan
- ✅ **Lihat Laporan** - Analisis penjualan

**Routes yang Dapat Diakses:**
```
POST /penjualan                      (View only)
GET  /penjualan                      (Transaction History)

GET  /persediaan                     (Stock Status)
GET  /manajemen/prediksi/{id}        (Forecast)
GET  /penjualan/laporan              (Reports & Analytics)

GET  /penjualan/struk                (Receipt)
GET  /penjualan/pembayaran           (Payment Info)
```

---

### 4. **APOTEKER** (Role: `apoteker`)
Mengelola stok obat dan peramalan.

**Akses:**
- ❌ Dashboard (TIDAK MEMILIKI AKSES)
- ❌ Kelola User (TIDAK MEMILIKI AKSES)
- ✅ **Kelola Obat/Produk** - Kelola stok, informasi obat
- ❌ Input Transaksi (TIDAK MEMILIKI AKSES)
- ❌ Lihat Riwayat (TIDAK MEMILIKI AKSES)
- ✅ **Peramalan TES** - Prediksi stok
- ✅ **Lihat Laporan** - Laporan stok

**Routes yang Dapat Diakses:**
```
GET  /produk                         (Product List)
POST /produk                         (Create Product)
PUT  /produk/{id}                    (Update Product)
DELETE /produk/{id}                  (Delete Product)
GET  /import                         (Import Products)
POST /produk/import                  (Process Import)

GET  /persediaan                     (Stock Management)
GET  /manajemen/prediksi/{id}        (Forecast)
GET  /penjualan/laporan              (Stock Reports)
```

---

## Tabel Perbandingan Akses

| Fitur | Admin | Kasir | Owner | Apoteker |
|-------|:-----:|:-----:|:-----:|:--------:|
| Dashboard | ✅ | ❌ | ❌ | ❌ |
| Kelola User | ✅ | ❌ | ❌ | ❌ |
| Kelola Produk | ✅ | ❌ | ❌ | ✅ |
| Input Transaksi | ✅ | ✅ | ❌ | ❌ |
| Lihat Riwayat | ✅ | ✅ | ✅ | ❌ |
| Edit/Hapus Transaksi | ✅ | ✅ | ❌ | ❌ |
| Peramalan | ✅ | ❌ | ✅ | ✅ |
| Laporan | ✅ | ❌ | ✅ | ✅ |

---

## Implementasi Middleware

Semua routes dilindungi dengan middleware `auth` dan `role`:

```php
// Contoh: Hanya Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Contoh: Admin atau Kasir
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index']);
});

// Contoh: Admin, Owner, atau Apoteker
Route::middleware(['auth', 'role:admin,owner,apoteker'])->group(function () {
    Route::get('/penjualan/laporan', [PenjualanController::class, 'laporan']);
});
```

---

## Error Handling

Jika user mencoba mengakses route yang tidak diizinkan, mereka akan mendapatkan:
- **HTTP 403 Forbidden** - Akses ditolak dengan pesan "Anda tidak memiliki izin untuk mengakses halaman ini"
- Sistem akan redirect ke halaman default sesuai role mereka

---

## Catatan Implementasi

1. **Middleware dipasang di `app/Http/Kernel.php`** dengan alias `role`
2. **CheckRole middleware** melakukan validasi role dari `auth()->user()->role`
3. **Fallback route** mengarahkan user yang belum login ke halaman login
4. **Redirect otomatis** mengarahkan user autentik ke dashboard sesuai role mereka

---

## Testing Akses

Untuk menguji akses, login dengan user yang memiliki role berbeda:

```bash
# Test Admin
- Username: admin, Password: sesuai
- Akses: /admin, /users, /produk, /penjualan, /persediaan

# Test Kasir  
- Username: kasir, Password: sesuai
- Akses: /keranjang, /penjualan

# Test Owner
- Username: owner, Password: sesuai
- Akses: /penjualan (readonly), /persediaan, /penjualan/laporan

# Test Apoteker
- Username: apoteker, Password: sesuai
- Akses: /produk, /persediaan, /penjualan/laporan
```

