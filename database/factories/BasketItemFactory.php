<?php

namespace Database\Factories;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Item;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BasketItem>
 */
class BasketItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $basket = Basket::factory()->create();
        $quantity = fake()->randomFloat(2, 0, 500);
        return [
            'basket_id' => $basket->id,
            'item_id' => Item::factory()->create()->id,
            'quantity' => $quantity,
            'quantity_unit_id' => fake()->numberBetween(1, 3),
            'unit_price' => $basket->total / $quantity,
            'price' => $basket->total,
        ];
    }
}
