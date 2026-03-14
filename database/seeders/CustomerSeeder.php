<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name'         => 'John Doe',
                'email'        => 'customer@leydiboss.com',
                'phone_number' => '+1234567890',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Jane Smith',
                'email'        => 'jane.smith@example.com',
                'phone_number' => '+1234567891',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Michael Johnson',
                'email'        => 'michael.johnson@example.com',
                'phone_number' => '+1234567892',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Emily Davis',
                'email'        => 'emily.davis@example.com',
                'phone_number' => '+1234567893',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Robert Brown',
                'email'        => 'robert.brown@example.com',
                'phone_number' => '+1234567894',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Sarah Wilson',
                'email'        => 'sarah.wilson@example.com',
                'phone_number' => '+1234567895',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'David Martinez',
                'email'        => 'david.martinez@example.com',
                'phone_number' => '+1234567896',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Laura Anderson',
                'email'        => 'laura.anderson@example.com',
                'phone_number' => '+1234567897',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'James Taylor',
                'email'        => 'james.taylor@example.com',
                'phone_number' => '+1234567898',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Olivia Thomas',
                'email'        => 'olivia.thomas@example.com',
                'phone_number' => '+1234567899',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'William Jackson',
                'email'        => 'william.jackson@example.com',
                'phone_number' => '+1234567800',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Sophia White',
                'email'        => 'sophia.white@example.com',
                'phone_number' => '+1234567801',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Charles Harris',
                'email'        => 'charles.harris@example.com',
                'phone_number' => '+1234567802',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Isabella Martin',
                'email'        => 'isabella.martin@example.com',
                'phone_number' => '+1234567803',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Daniel Garcia',
                'email'        => 'daniel.garcia@example.com',
                'phone_number' => '+1234567804',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Mia Thompson',
                'email'        => 'mia.thompson@example.com',
                'phone_number' => '+1234567805',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        DB::table('customers')->insert($customers);
    }
}