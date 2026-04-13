<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => Admin::ROLE_ADMIN,
            'phone_number' => fake()->phoneNumber(),
        ];
    }

    /**
     * Create a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Admin::ROLE_SUPER_ADMIN,
        ]);
    }

    /**
     * Create a manager role admin.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Admin::ROLE_MANAGER,
        ]);
    }
}
