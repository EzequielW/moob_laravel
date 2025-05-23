<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => fake()->randomElement(['discord', 'telegram', 'whatsapp', 'slack']),
            'recipient' => fake()->unique()->safeEmail(),
            'content' => fake()->text(50),
            'attachment_path' => null
        ];
    }
}
