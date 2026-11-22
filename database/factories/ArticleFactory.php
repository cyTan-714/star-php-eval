<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => rtrim(fake()->unique()->sentence(6), '.'),     // a 6-word Faker sentence with its trailing full stop removed.
            'summary' => fake()->text(200),
            'body' => fake()->paragraphs(5, true),
            'permalink' => fn (array $attributes) => Str::slug($attributes['title']),
            'author' => fake()->name(),
            'thumbnail_url' => fn (array $attributes) => "https://picsum.photos/seed/{$attributes['permalink']}/800/450",
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),      // a random date in the last 6 months
        ];
    }

    /**
     * Indicate that the article is an unpublished draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
