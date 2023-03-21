<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Basket>
 */
class BasketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'shop_id' => Shop::factory()->create()->id,
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'total' => fake()->randomFloat(2, 0, 1000),
            'receipt_id' => fake()->randomLetter() . fake()->randomNumber(9, true),
        ];
    }
}
