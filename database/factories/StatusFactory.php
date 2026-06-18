<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' Status',
            'slug' => fake()->unique()->slug(1),
            'is_closed' => false,
        ];
    }
}
