<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customers')->insert([
            'name'         => 'John Doe',
            'email'        => 'customer@leydiboss.com',
            'phone_number' => '+1234567890',
            'password'     => Hash::make('password'),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}