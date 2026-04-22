<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['cedula' => '0000000000'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin2026*'),
            ]
        );

        $admin->assignRole('admin');
    }
}
