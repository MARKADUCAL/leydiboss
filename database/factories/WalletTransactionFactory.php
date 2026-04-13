<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::inRandomOrder()->value('id'),
            'admin_id'    => Admin::inRandomOrder()->value('id'),
            'description' => $this->faker->sentence(),
            'type'        => $this->faker->randomElement(['debit', 'credit']),
            'value'       => $this->faker->randomFloat(2, 10, 5000),
        ];
    }

    public function debit(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'debit']);
    }

    public function credit(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'credit']);
    }
}
