<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk'; // Nama tabel produk
    protected $primaryKey = 'id'; // Primary key

    protected $fillable = [
        'nama',
        'stok',
        'harga_beli',
        'harga_jual',
        'kategori',
        'keterangan',
        'tanggal_kadaluarsa', // Kolom baru
    ];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'produk_id'); // 'produk_id' disesuaikan dengan nama kolom yang ada di tabel penjualan
    }

    public function forecastParameter()
    {
        return $this->hasOne(ForecastParameter::class, 'product_id', 'id');
    }
}
