<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 10 admins per role (manager, admin, super_admin) = 30 admins
        $roles = array_keys(Admin::roles());
        $index = 1;

        foreach ($roles as $role) {
            for ($i = 1; $i <= 10; $i++, $index++) {
                Admin::query()->updateOrCreate(
                    ['email' => "admin{$index}@example.com"],
                    [
                        'name'         => "Admin {$index} ({$role})",
                        'phone_number' => '08000000000',
                        'password'     => Hash::make('password'),
                        'role'         => $role,
                        'balance'      => 0,
                    ]
                );
            }
        }
    }
}