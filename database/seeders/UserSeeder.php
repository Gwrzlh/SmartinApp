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
       

        User::create([
            'full_name' => 'Owner Smartin',
            'email' => 'Owner@gmail.com',
            'username' => 'Owner',
            'role' => 'owner',
            'password' => bcrypt('password123'),
        ]);

        User::create([
            'full_name' => 'Admin Smartin',
            'email' => 'admin@gmail.com',
            'username' => 'Admin1',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        User::create([
            'full_name' => 'Kasir Smartin',
            'email' => 'kasir@gmail.com',
            'username' => 'Kasir1',
            'role' => 'kasir',
            'password' => bcrypt('password123'),
        ]);

      
    }
}
