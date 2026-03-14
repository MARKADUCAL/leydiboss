<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
         
        ];

        foreach ($admins as $admin) {
            Admin::query()->updateOrCreate(
                ['email' => $admin['email']],
                array_merge($admin, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}