<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::Create([
            'full_name' => 'Admin Smartin',
            'email' => 'admin@gmail.com',
            'username' => 'admin1',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        User::Create([
            'full_name' => 'Owner Smartin',
            'email' => 'Owner@gmail.com',
            'username' => 'Owner',
            'role' => 'owner',
            'password' => bcrypt('password123'),
        ]);

        User::Create([
            'full_name' => 'Kasir Smartin',
            'email' => 'Kasir@gmail.com',
            'username' => 'Kasir1',
            'role' => 'kasir',
            'password' => bcrypt('password123'),
        ]);
    }
}
