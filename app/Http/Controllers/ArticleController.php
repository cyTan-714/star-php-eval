<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

class ArticleController extends Controller
{
    /**
     * Show every published article, newest first.
     */
    public function index(): View
    {
        $articles = Article::query()
            ->published()
            ->with('category')
            ->latest('published_at')
            ->get();

        return view('articles.index', ['articles' => $articles]);
    }

    /**
     * Show a single published article.
     */
    public function show(Article $article): View
    {
        abort_unless($article->isPublished(), 404);

        $article->load('category');

        return view('articles.show', ['article' => $article]);
    }
}
