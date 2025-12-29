<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'creator_id' => User::factory(),
            'title' => ['en' => fake()->sentence()],
            'description' => ['en' => fake()->paragraph()],
            'type' => fake()->randomElement([Task::TYPE_ROUTINE, Task::TYPE_SITUATIONAL]),
            'status' => Task::STATUS_PENDING,
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
