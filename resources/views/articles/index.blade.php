<x-layout title="Latest articles">
    <h1 class="mb-4">Latest articles</h1>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse ($articles as $article)
            <div class="col">
                <article class="card h-100 shadow-sm">
                    @if ($article->thumbnail_url)
                        <img src="{{ $article->thumbnail_url }}" class="card-img-top" alt="{{ $article->title }}" loading="lazy">
                    @endif

                    <div class="card-body">
                        <span class="badge text-bg-primary mb-2">{{ $article->category->name }}</span>
                        <h2 class="card-title h5">
                            <a href="{{ route('articles.show', $article) }}" class="stretched-link text-body-emphasis text-decoration-none">{{ $article->title }}</a>
                        </h2>
                        <p class="card-text text-body-secondary">{{ $article->summary }}</p>
                    </div>

                    <div class="card-footer bg-transparent small text-body-secondary">
                        {{ $article->author }} · {{ $article->published_at->format('j M Y') }}
                    </div>
                </article>
            </div>
        @empty
            {{-- w-100 stops the row-cols classes squeezing the message into one card-width column. --}}
            <p class="w-100 text-body-secondary">No articles have been published yet.</p>
        @endforelse
    </div>
</x-layout>
