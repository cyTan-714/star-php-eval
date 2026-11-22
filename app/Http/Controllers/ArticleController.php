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
}
