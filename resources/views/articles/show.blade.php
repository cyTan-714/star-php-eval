<x-layout :title="$article->title">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Articles</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $article->title }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <article class="col-lg-8">
            <header class="mb-4">
                <span class="badge text-bg-primary mb-2">{{ $article->category->name }}</span>
                <h1 class="mb-2">{{ $article->title }}</h1>
                <p class="text-body-secondary mb-0">
                    By {{ $article->author }} ·
                    <time datetime="{{ $article->published_at->toIso8601String() }}">{{ $article->published_at->format('j F Y, g:i a T') }}</time>
                </p>
            </header>

            @if ($article->thumbnail_url)
                <img src="{{ $article->thumbnail_url }}" class="img-fluid rounded mb-4" alt="{{ $article->title }}">
            @endif

            <p class="lead">{{ $article->summary }}</p>

            @foreach (explode("\n\n", $article->body) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach

            <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary mt-3">&larr; Back to all articles</a>
        </article>
    </div>
</x-layout>
