<?php

namespace Database\Factories;

use App\Enums\GearCategory;
use App\Enums\GearCondition;
use App\Models\Gear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gear>
 */
class GearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucwords(fake()->words(2, true)).' '.fake()->numerify('##L'),
            'category' => fake()->randomElement(GearCategory::cases()),
            'brand' => fake()->randomElement(['Eiger', 'Consina', 'Arei', 'Rei', 'Naturehike', null]),
            'daily_price' => fake()->numberBetween(1, 20) * 5000,
            'stock' => fake()->numberBetween(0, 10),
            'condition' => GearCondition::Good,
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the gear needs repair.
     */
    public function needsRepair(): static
    {
        return $this->state(fn () => ['condition' => GearCondition::NeedsRepair]);
    }
}
