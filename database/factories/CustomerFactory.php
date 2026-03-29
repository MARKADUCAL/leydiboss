<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
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
            'phone_number' => fake()->phoneNumber(),
            'password' => Hash::make('password'),
            'profile_photo_path' => null,
        ];
    }

    /**
     * Indicate that the customer should have a profile photo avatar.
     */
    public function withAvatar(): static
    {
        return $this->state(function (array $attributes) {
            try {
                $file = \Illuminate\Http\UploadedFile::fake()->createWithContent(
                    'avatar.png',
                    file_get_contents('https://i.pravatar.cc/300?img=' . fake()->numberBetween(1, 70))
                );
                
                
                return [
                    'profile_photo_path' => $file->store('profile-photos', 'public'),
                ];
            } catch (\Exception $e) {
             
                return [];
            }
        });
    }
}
