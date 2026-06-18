<?php

namespace Database\Factories;

use App\Models\HelpTopic;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class HelpTopicFactory extends Factory
{
    protected $model = HelpTopic::class;

    public function definition(): array
    {
        $department = Department::inRandomOrder()->first() ?? Department::factory()->create();

        return [
            'name' => fake()->unique()->word() . ' Issue',
            'department_id' => $department->id,
        ];
    }
}
