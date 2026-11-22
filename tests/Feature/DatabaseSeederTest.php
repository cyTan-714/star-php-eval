<?php

use App\Models\Article;
use App\Models\Category;

it('seeds 5 categories and 20 articles', function () {
    $this->seed();

    expect(Category::count())->toBe(5)
        ->and(Article::count())->toBe(20);
});
