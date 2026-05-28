<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrator',       'email' => 'admin@desa.id',    'password' => 'admin123',    'role' => 'admin'],
            ['name' => 'Petugas Loket',        'email' => 'petugas@desa.id',  'password' => 'petugas123',  'role' => 'petugas'],
            ['name' => 'Kepala Desa (Viewer)', 'email' => 'kades@desa.id',    'password' => 'viewer123',   'role' => 'viewer'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make($u['password']), 'role' => $u['role']]
            );
        }
    }
}
