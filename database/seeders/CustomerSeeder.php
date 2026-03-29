<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating 100 customers with avatars (this may take a moment to download)...');
        Customer::factory()->count(100)->withAvatar()->create();
        
        $this->command->info('Creating 100 customers without avatars...');
        Customer::factory()->count(100)->create([
            'profile_photo_path' => null,
        ]);

        $this->command->info('200 customers seeded successfully!');
    }
}