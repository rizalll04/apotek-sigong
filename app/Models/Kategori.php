<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori'; // Nama tabel di database

    protected $primaryKey = 'id_kategori'; // Primary key tabel

    protected $fillable = [
        'nama', // Kolom yang dapat diisi
    ];
}
