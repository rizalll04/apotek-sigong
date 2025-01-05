<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan'; // Nama tabel di database

    protected $primaryKey = 'id_penjualan'; // Primary key tabel

    protected $fillable = [
        'produk_id',
        'jumlah',
        'harga',
        'total_harga',
        'uang_diterima',
        'kembalian',
    ];

    /**
     * Relasi ke model Produk.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }
}
