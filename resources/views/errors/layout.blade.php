@php
    $errorSettings = $siteSettings ?? null;
    $errorSiteName = $errorSettings?->site_name ?? config('app.name', 'FragZone');
    $errorAbbreviation = $errorSettings?->site_abbreviation ?? 'FZ';
    $errorBrandPrefix = $errorSettings?->brandPrefix() ?? mb_strtoupper(mb_substr($errorSiteName, 0, max(1, mb_strlen($errorSiteName) - 4)));
    $errorBrandAccent = $errorSettings?->brandAccent() ?? mb_strtoupper(mb_substr($errorSiteName, -4));
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#11130f">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title') — {{ $errorSiteName }}</title>
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
</head>
<body class="error-page">
<header class="error-header">
    <a class="brand" href="{{ url('/') }}"><span>{{ $errorAbbreviation }}</span><b>{{ $errorBrandPrefix }}<em>{{ $errorBrandAccent }}</em></b></a>
</header>
<main class="error-main">
    <section class="error-panel" aria-labelledby="error-title">
        <div class="error-code" aria-hidden="true">@yield('code')</div>
        <div class="error-content">
            <span class="eyebrow"><i></i>{{ __('ui.errors.eyebrow') }}</span>
            <h1 id="error-title">@yield('message')</h1>
            <p>@yield('description')</p>
            <div class="buttons">
                <a class="button" href="{{ url('/') }}">{{ __('ui.errors.home') }} →</a>
                <a class="button ghost" href="{{ url('/servers') }}">{{ __('ui.errors.servers') }}</a>
            </div>
        </div>
    </section>
</main>
<footer class="error-footer">
    <span>&copy; {{ now()->year }} {{ $errorSiteName }}</span>
    <span>{{ __('ui.footer.created_by') }} <a href="https://interweb.bg" target="_blank" rel="noopener noreferrer">InterWeb Ltd.</a></span>
</footer>
</body>
</html>
