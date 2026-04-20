<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Admin Caysie',
            'email'    => 'admin@caysie.com',
            'password' => Hash::make('uluketel'),
            'role'     => 'admin',
            'phone'    => '08123456789',
        ]);
    }
}
