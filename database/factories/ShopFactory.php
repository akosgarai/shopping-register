<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Company;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->company(),
            'address_id' => Address::factory()->create()->id,
            'company_id' => Company::factory()->create()->id,
        ];
    }
}
