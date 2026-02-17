<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian'; // Nama tabel yang digunakan

    protected $primaryKey = 'id_pembelian'; // Primary key tabel

    public $timestamps = true; // Menyertakan created_at dan updated_at

    protected $fillable = [
        'id_produk',
        'tanggal',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'supplier',
    ];

    // Relasi dengan produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
