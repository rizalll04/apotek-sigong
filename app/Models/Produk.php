<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'satuan',
        'kategori_produk',
        'stok',
        'hpp',
        'harga_jual',
        'keterangan',
        'gambar',
    ];

    /**
     * Relasi ke model Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_produk', 'id_kategori');
    }
}
