<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'tb_user'; // Pastikan nama tabel sesuai dengan migrasi
    protected $primaryKey = 'user_id'; // Sesuaikan primary key
    protected $fillable = ['name', 'username', 'password', 'role'];
}
