<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class WalletsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
           'wallet_name' => $this->faker->word(),
           'wallet_type' => $this->faker->word(),
           'user_id' => User::factory(),
           'wallet_code' => rand(1111111111, 9999999999),
           'monthly_interest_rate' => 1.5,
           'wallet_minimum_balance' => 500
        ];
    }
}
