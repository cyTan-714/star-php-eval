@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand bg-dark" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('articles.index') }}">{{ config('app.name') }}</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('articles.index') }}">Articles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('api.articles.index') }}">API</a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="container py-4">{{ $slot }}</main>

    @stack('scripts')
</body>
</html>
