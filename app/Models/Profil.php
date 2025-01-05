<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak mengikuti konvensi Laravel (tabel plural)
    protected $table = 'profil';

    // Tentukan kolom yang dapat diisi
    protected $fillable = ['user_id', 'alamat', 'tanggal_lahir', 'foto'];

    // Cek tipe data yang sesuai untuk kolom tertentu (jika diperlukan)
    protected $casts = [
        'tanggal_lahir' => 'date',  // pastikan tanggal_lahir disimpan dalam format tanggal
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');  // Setiap profil milik satu user
    }
}
