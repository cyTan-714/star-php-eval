<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

it('returns articles as JSON with their category', function () {
    $category = Category::factory()->create(['name' => 'Technology']);
    $article = Article::factory()->for($category)->create([
        'title' => 'Laravel 13 released',
        'published_at' => '2026-03-14 09:05:00',
    ]);

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'id',
                'title',
                'summary',
                'body',
                'permalink',
                'author',
                'thumbnail_url',
                'published_at',
                'category' => ['id', 'name'],
            ]],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonPath('data.0.id', $article->id)
        ->assertJsonPath('data.0.title', 'Laravel 13 released')
        ->assertJsonPath('data.0.published_at', '2026-03-14T09:05:00+00:00')
        ->assertJsonPath('data.0.category', ['id' => $category->id, 'name' => 'Technology']);
});

it('leaves out drafts and scheduled articles', function () {
    $category = Category::factory()->create();
    $published = Article::factory()->for($category)->create();
    Article::factory()->for($category)->draft()->create();
    Article::factory()->for($category)->create(['published_at' => now()->addDay()]);

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $published->id);
});

it('returns articles newest first', function () {
    $category = Category::factory()->create();
    Article::factory()->for($category)->create(['title' => 'Three days ago', 'published_at' => now()->subDays(3)]);
    Article::factory()->for($category)->create(['title' => 'One day ago', 'published_at' => now()->subDays(1)]);
    Article::factory()->for($category)->create(['title' => 'Two days ago', 'published_at' => now()->subDays(2)]);

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonPath('data.*.title', ['One day ago', 'Two days ago', 'Three days ago']);
});

it('loads a page of articles and their categories in three queries', function () {
    $categories = Category::factory()->count(3)->create();
    Article::factory()->count(15)->recycle($categories)->create();

    DB::enableQueryLog();

    $this->getJson(route('api.articles.index'))
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 15);

    // One count for the paginator, one page of articles, one eager load of their categories.
    expect(DB::getQueryLog())->toHaveCount(3);
});
