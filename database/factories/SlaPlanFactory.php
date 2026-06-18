<?php

namespace Database\Factories;

use App\Models\SlaPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SlaPlanFactory extends Factory
{
    protected $model = SlaPlan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' SLA',
            'grace_hours' => fake()->randomElement([4, 8, 24, 48, 72]),
        ];
    }
}
