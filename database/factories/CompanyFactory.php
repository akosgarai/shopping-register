<?php

namespace Database\Factories;

use App\Models\Address;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $taxNumber = fake()->randomNumber(9, true);
        $taxNumber = $taxNumber . fake()->randomNumber(2, true);
        return [
            'name' => fake()->company(),
            'address_id' => Address::factory()->create()->id,
            'tax_number' => $taxNumber,
        ];
    }
}
