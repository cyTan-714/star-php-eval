<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'World',
                'Business',
                'Technology',
                'Sport',
                'Entertainment',
                'Health',
                'Science',
                'Lifestyle',
                'Travel',
                'Opinion',
            ]),
        ];
    }
}
