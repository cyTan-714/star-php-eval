# News Portal

A small news site built with Laravel 13 for a PHP/Laravel technical evaluation. It lists published articles with a title search that filters as you type, gives each article its own page, and serves the same articles as a paginated JSON API.

Repository: https://github.com/cyTan-714/star-php-eval

- **Data:** a `Category` has many `Article`s. Faker factories and a seeder create 5 categories with 4 articles each.
- **Web:** a Bootstrap card grid with live title search, and a detail page for each article.
- **API:** `GET /api/articles` returns published articles as JSON, newest first, 10 per page, with each article's category eager-loaded (3 queries per page).
- **Tests:** Pest feature tests for the API, the pages and the seeder.

## Requirements

- PHP 8.5 or later, with the `pdo_sqlite` extension
- Composer 2
- No Node.js or npm: there's no front-end build, and Bootstrap's CSS loads from the jsDelivr CDN.

The pages fetch Bootstrap and the article thumbnails (from picsum.photos) over the internet. Offline, the site still works, but it's unstyled and the images show their alt text.

## Setup

```bash
git clone https://github.com/cyTan-714/star-php-eval.git
cd star-php-eval
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Open http://localhost:8000. It redirects to the article list.

On Windows, Git Bash runs these commands as they are. In PowerShell or Command Prompt, use these instead of the `cp` and `touch` lines:

| Step | PowerShell | Command Prompt |
| --- | --- | --- |
| Copy the environment file | `Copy-Item .env.example .env` | `copy .env.example .env` |
| Create the SQLite database | `New-Item database\database.sqlite` | `type nul > database\database.sqlite` |

Using [Laravel Herd](https://herd.laravel.com)? Clone into your Herd folder and open http://star-php-eval.test instead of running `php artisan serve`.

To reset the data, run `php artisan migrate:fresh --seed`. Running `php artisan db:seed` on an already seeded database fails, because category names must be unique and the factory picks them from a fixed list of ten.

`package.json`, `vite.config.js` and the `composer run setup` and `composer run dev` scripts come with the Laravel skeleton and rely on npm. This project doesn't use them, so follow the steps above instead.

## Usage

| Method | URL | Returns |
| --- | --- | --- |
| GET | `/` | A redirect to `/articles` |
| GET | `/articles` | Every published article, newest first, with live title search |
| GET | `/articles/{permalink}` | One article: title, category, author, published date (UTC) and full body |
| GET | `/api/articles` | Published articles as JSON, newest first, 10 per page (add `?page=2` for the next page) |

On `/articles`, type in the search box to filter the cards by title. The filtering happens in the browser, with no requests to the server. Click a card to open the article.

Drafts (no `published_at`) and scheduled articles (a `published_at` in the future) never appear. The list and the API leave them out, and their permalinks return 404.

Each item in the API's `data` array looks like this. The summary and body are shortened here, and your data will differ because the seeder generates random text.

```json
{
  "id": 20,
  "title": "Dolor sit impedit similique saepe neque recusandae asperiores impedit",
  "summary": "Repellat est vel quod et sed fugiat. Vel eum pariatur facere tempore.",
  "body": "Voluptas aut laborum perspiciatis. Est est magni voluptas aut.\n\nAd fugiat molestiae voluptatem ipsum consequatur ipsa repellat.",
  "permalink": "dolor-sit-impedit-similique-saepe-neque-recusandae-asperiores-impedit",
  "author": "Mr. Sidney Wuckert",
  "thumbnail_url": "https://picsum.photos/seed/dolor-sit-impedit-similique-saepe-neque-recusandae-asperiores-impedit/800/450",
  "published_at": "2026-11-15T14:03:29+00:00",
  "category": {
    "id": 5,
    "name": "Entertainment"
  }
}
```

Dates are ISO 8601 in UTC. Every response also includes Laravel's standard pagination objects: `links` (`first`, `last`, `prev`, `next`) and `meta` (`current_page`, `from`, `last_page`, `path`, `per_page`, `to`, `total`, and a `links` array for page buttons).

## Tests

```bash
php artisan test
```

The tests run against an in-memory SQLite database, so they never touch your local data. They check:

- **API:** the JSON fields including the nested category, that drafts and scheduled articles are left out, newest-first order, and that listing 15 articles takes exactly 3 queries (a count for the paginator, one page of articles, and their categories).
- **Pages:** the `/` redirect, newest-first order, drafts hidden from the list, the detail page's title, body, category and date, and 404s for unknown, draft and scheduled permalinks.
- **Seeder:** 5 categories and 20 articles.

Code style follows Laravel Pint's default preset. Check it with `php vendor/bin/pint --test`.

## Design decisions

- **`published_at` decides what's live.** An article is published when `published_at` is now or in the past. `NULL` means a draft and a future date means scheduled, so there's no separate `status` column that could disagree with the date. The rule lives in one place: the `published()` query scope, with `isPublished()` for a single article.
- **No N+1 queries.** Both lists eager-load their categories with `with('category')`. Outside production, `Model::preventLazyLoading()` turns a missed eager load into an exception, so the mistake fails in development and tests instead of quietly adding queries. The environment check reads `config('app.env')`, which keeps working after `php artisan config:cache`, where `env()` would return null.
- **API Resources define the JSON.** `ArticleResource` lists its fields explicitly, so new columns can't leak into the API. It nests the category through `whenLoaded()`, so it can never trigger a lazy load itself.
- **The web list isn't paginated.** The search runs in the browser, so every published article has to be on the page. That suits a few dozen articles; a larger archive would need server-side search with pagination. The API paginates 10 per page.
- **Search is plain JavaScript.** It hides non-matching cards on every keystroke, ignores case and surrounding spaces, and announces "No articles match your search." through an `aria-live` region for screen readers.
- **Permalink URLs.** Route model binding on `{article:permalink}` looks articles up by a unique, readable slug. Drafts and scheduled articles return 404 rather than 403, so their URLs don't reveal that they exist.
- **No front-end build.** Bootstrap 5.3's CSS loads from jsDelivr with a Subresource Integrity hash. No component needs Bootstrap's JavaScript, so it isn't loaded.
- **Schema.** A unique index on `permalink`, an index on `published_at` (every list filters and sorts on it), and a foreign key that stops a category being deleted while it still has articles.
- **UTC throughout.** Dates are stored in UTC, the API returns them as ISO 8601 with `+00:00`, and the detail page labels its time "UTC".
- **Factories follow overrides.** The permalink and thumbnail URL are closures built from the article's final title, so a test that sets its own title gets a matching permalink. Thumbnails are picsum.photos images seeded by the permalink, so each article always shows the same picture and no image files are stored.

## Key files

| File | Purpose |
| --- | --- |
| [`app/Models/Article.php`](app/Models/Article.php) | `category()` relation, `published()` scope and `isPublished()` |
| [`app/Models/Category.php`](app/Models/Category.php) | `articles()` relation |
| [`app/Http/Controllers/ArticleController.php`](app/Http/Controllers/ArticleController.php) | List and detail pages |
| [`app/Http/Controllers/Api/ArticleController.php`](app/Http/Controllers/Api/ArticleController.php) | JSON endpoint |
| [`app/Http/Resources/`](app/Http/Resources/) | `ArticleResource` and `CategoryResource`, which define the API's fields |
| [`app/Providers/AppServiceProvider.php`](app/Providers/AppServiceProvider.php) | Lazy-loading guard |
| [`database/migrations/`](database/migrations/) | `categories` and `articles` tables |
| [`database/factories/`](database/factories/) | `ArticleFactory` (with a `draft()` state) and `CategoryFactory` |
| [`database/seeders/DatabaseSeeder.php`](database/seeders/DatabaseSeeder.php) | 5 categories with 4 articles each |
| [`resources/views/components/layout.blade.php`](resources/views/components/layout.blade.php) | Shared layout with Bootstrap and the navbar |
| [`resources/views/articles/`](resources/views/articles/) | List page (including the search script) and detail page |
| [`routes/web.php`](routes/web.php), [`routes/api.php`](routes/api.php) | Web and API routes |
| [`tests/Feature/`](tests/Feature/) | Pest tests |

## What I'd do next

- **Markdown article bodies.** The detail page splits the body into paragraphs on blank lines, which suits the generated text but not real articles. Storing Markdown and rendering it with `Str::markdown()`, with raw HTML stripped, would allow headings, links and lists safely.
- **Server-side search and pagination** for the web list once it outgrows one page, for example a `?q=` parameter on `/articles` backed by SQLite full-text search or Laravel Scout.
- **Category pages and filters:** `/categories/{category}` for readers and a `?category=` filter on the API.
- **More API endpoints:** `GET /api/articles/{permalink}` for a single article, plus rate limiting and HTTP caching.
- **An authoring area** behind a login, with form request validation and scheduled publishing.
- **Readers' local time.** Keep storing UTC, but show each reader the time in their own time zone.
- **Continuous integration:** a GitHub Actions workflow that runs Pint and the tests on every push.
