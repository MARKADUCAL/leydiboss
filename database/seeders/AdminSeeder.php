<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->insert([
            'name'         => 'Super Admin',
            'email'        => 'admin@leydiboss.com',
            'phone_number' => '+1234567890',
            'password'     => Hash::make('password'),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}