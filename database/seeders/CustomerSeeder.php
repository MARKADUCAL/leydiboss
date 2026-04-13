<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 customers (one "role" group out of four, for a total of 40 users)
        Customer::factory()->count(10)->create();
    }
}
