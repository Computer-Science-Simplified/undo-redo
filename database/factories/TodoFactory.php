<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentences(5, true),
            'user_id' => User::factory(),
            'assignee_id' => User::factory(),
            'due_at' => now()->subDays(rand(-10, 30)),
        ];
    }
}
