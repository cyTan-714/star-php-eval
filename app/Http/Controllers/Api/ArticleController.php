<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    /**
     * List published articles as JSON, newest first, 10 per page.
     */
    public function index(): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->published()
            ->with('category')
            ->latest('published_at')
            ->paginate(10);

        return ArticleResource::collection($articles);
    }
}
