<?php

use App\Models\Article;
use App\Models\Category;

it('redirects the home page to the article list', function () {
    $this->get('/')->assertRedirect(route('articles.index'));
});

it('lists articles newest first', function () {
    $category = Category::factory()->create();
    Article::factory()->for($category)->create(['title' => 'Three days ago', 'published_at' => now()->subDays(3)]);
    Article::factory()->for($category)->create(['title' => 'One day ago', 'published_at' => now()->subDays(1)]);
    Article::factory()->for($category)->create(['title' => 'Two days ago', 'published_at' => now()->subDays(2)]);

    $this->get(route('articles.index'))
        ->assertOk()
        ->assertSeeInOrder(['One day ago', 'Two days ago', 'Three days ago']);
});

it('lists published articles but hides drafts', function () {
    $category = Category::factory()->create();
    Article::factory()->for($category)->create(['title' => 'Published headline']);
    Article::factory()->for($category)->draft()->create(['title' => 'Draft headline']);

    $this->get(route('articles.index'))
        ->assertOk()
        ->assertSee('Published headline')
        ->assertDontSee('Draft headline');
});

it('shows the title, body, category and date on the detail page', function () {
    $category = Category::factory()->create(['name' => 'Science']);
    $article = Article::factory()->for($category)->create([
        'title' => 'Water found on Mars',
        'body' => "First paragraph.\n\nSecond paragraph.",
        'published_at' => '2026-03-14 09:05:00',
    ]);

    $this->get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('Water found on Mars')
        ->assertSee('Science')
        ->assertSee('14 March 2026, 9:05 am UTC')
        ->assertSeeInOrder(['<p>First paragraph.</p>', '<p>Second paragraph.</p>'], escape: false);
});

it('returns 404 for an unknown permalink', function () {
    $this->get('/articles/does-not-exist')->assertNotFound();
});

it('returns 404 for drafts and scheduled articles', function () {
    $category = Category::factory()->create();
    $draft = Article::factory()->for($category)->draft()->create();
    $scheduled = Article::factory()->for($category)->create(['published_at' => now()->addDay()]);

    $this->get(route('articles.show', $draft))->assertNotFound();
    $this->get(route('articles.show', $scheduled))->assertNotFound();
});
