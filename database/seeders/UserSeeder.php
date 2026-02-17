<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => 'Rivian1207',
                'role' => 'admin',
            ],
            [
                'name' => 'Owner User',
                'username' => 'owner',
                'password' => 'Rivian1207',
                'role' => 'owner',
            ],
            [
                'name' => 'Kasir User',
                'username' => 'kasir',
                'password' => 'Rivian1207',
                'role' => 'kasir',
            ],
            [
                'name' => 'Apoteker User',
                'username' => 'apoteker',
                'password' => 'Rivian1207',
                'role' => 'apoteker',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'username' => $user['username'],
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
            ]);
        }
    }
}
