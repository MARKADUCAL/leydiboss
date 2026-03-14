<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name'         => 'John Admin',
                'email'        => 'john.admin@leydiboss.com',
                'phone_number' => '+1234567891',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Sarah Admin',
                'email'        => 'sarah.admin@leydiboss.com',
                'phone_number' => '+1234567892',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Michael Admin',
                'email'        => 'michael.admin@leydiboss.com',
                'phone_number' => '+1234567893',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Emily Admin',
                'email'        => 'emily.admin@leydiboss.com',
                'phone_number' => '+1234567894',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'David Admin',
                'email'        => 'david.admin@leydiboss.com',
                'phone_number' => '+1234567895',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Jessica Admin',
                'email'        => 'jessica.admin@leydiboss.com',
                'phone_number' => '+1234567896',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Robert Admin',
                'email'        => 'robert.admin@leydiboss.com',
                'phone_number' => '+1234567897',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Laura Admin',
                'email'        => 'laura.admin@leydiboss.com',
                'phone_number' => '+1234567898',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'James Admin',
                'email'        => 'james.admin@leydiboss.com',
                'phone_number' => '+1234567899',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Olivia Admin',
                'email'        => 'olivia.admin@leydiboss.com',
                'phone_number' => '+1234567810',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'William Admin',
                'email'        => 'william.admin@leydiboss.com',
                'phone_number' => '+1234567811',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Sophia Admin',
                'email'        => 'sophia.admin@leydiboss.com',
                'phone_number' => '+1234567812',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Charles Admin',
                'email'        => 'charles.admin@leydiboss.com',
                'phone_number' => '+1234567813',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Isabella Admin',
                'email'        => 'isabella.admin@leydiboss.com',
                'phone_number' => '+1234567814',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Daniel Admin',
                'email'        => 'daniel.admin@leydiboss.com',
                'phone_number' => '+1234567815',
                'password'     => Hash::make('password'),
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        DB::table('admins')->insert($admins);
    }
}