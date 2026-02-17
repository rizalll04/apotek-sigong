<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan penamaan default
    protected $table = 'penjualan';

    // Tentukan primary key
    protected $primaryKey = 'id_penjualan';

    // Kolom-kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'produk_id',
        'jumlah',
        'harga',
        'total_harga',
        'uang_diterima',
        'kembalian',
        'metode_pembayaran',
        'midtrans_order_id',
        'payment_status',
        'bukti_transfer',
        'tanggal', // Tambahkan kolom 'tanggal' ke dalam $fillable
    ];

    // Relasi dengan tabel produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Method untuk menghitung total harga berdasarkan jumlah dan harga per unit
     *
     * @return float
     */
    public function calculateTotalHarga()
    {
        $this->total_harga = $this->jumlah * $this->harga;
        return $this->total_harga;
    }

    /**
     * Method untuk menghitung kembalian berdasarkan uang diterima
     *
     * @return float|null
     */
    public function calculateKembalian()
    {
        if ($this->uang_diterima >= $this->total_harga) {
            $this->kembalian = $this->uang_diterima - $this->total_harga;
        } else {
            $this->kembalian = null; // Jika uang diterima kurang, kembalian diset null
        }
        return $this->kembalian;
    }
}
