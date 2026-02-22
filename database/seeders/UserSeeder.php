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
            'full_name' => 'Owner Smartin',
            'email' => 'Owner@gmail.com',
            'username' => 'Owner',
            'role' => 'owner',
            'password' => bcrypt('password123'),
        ]);

      
    }
}
