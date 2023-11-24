<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Occupation>
 */
class OccupationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gender'=>fake()->name(),
            'education'=>rand(1,7),
            'status_'=>fake()->name(),
            'dis_type'=>rand(1,9),
            'tool'=>rand(1,7),
            'keeper'=>fake()->name(),
            'invest'=>rand(10000,1000000),
            'loan'=>rand(10000,60000),
            'hobby'=>fake()->text(),
            'aptitude'=>fake()->text(),
            'commute'=>fake()->text(),
            'occupation'=>fake()->name()
        ];
    }
}
