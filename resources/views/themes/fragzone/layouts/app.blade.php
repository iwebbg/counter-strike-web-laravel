<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#11130f">
    <title>@yield('title', $siteSettings->translated('meta_title')) — {{ $siteSettings->site_name }}</title>
    <meta name="description" content="{{ $siteSettings->translated('meta_description') }}">
    <meta name="keywords" content="{{ $siteSettings->translated('meta_keywords') }}">
    <meta property="og:site_name" content="{{ $siteSettings->site_name }}">
    <meta property="og:title" content="@yield('title', $siteSettings->translated('meta_title'))">
    <meta property="og:description" content="{{ $siteSettings->translated('meta_description') }}">
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}">
    <script src="{{ asset('assets/app.js') }}?v={{ filemtime(public_path('assets/app.js')) }}" defer></script>
</head>
<body data-theme="{{ $activeTheme['slug'] }}" data-copy-success="{{ __('ui.common.copied') }}">
<div class="ambient a1"></div><div class="ambient a2"></div>
<header class="site-header">
    <a class="brand" href="{{ route('home') }}"><span>{{ $siteSettings->site_abbreviation }}</span><b>{{ $siteSettings->brandPrefix() }}<em>{{ $siteSettings->brandAccent() }}</em><small>{{ $siteSettings->translated('network_label') }}</small></b></a>
    <button class="nav-toggle" data-nav-toggle aria-label="{{ __('ui.nav.menu') }}">☰</button>
    <nav data-nav>
        <a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a>
        <a href="{{ route('servers.index') }}">{{ __('ui.nav.servers') }}</a>
        <a href="{{ route('news.index') }}">{{ __('ui.nav.news') }}</a>
    </nav>
    <form class="locale-switcher" method="post" action="{{ route('locale.update') }}">
        @csrf
        <label>
            <span class="sr-only">{{ __('ui.common.language') }}</span>
            <select name="locale" aria-label="{{ __('ui.common.language') }}" onchange="this.form.submit()">
                @foreach(config('localization.locales') as $locale => $label)
                    <option value="{{ $locale }}" @selected(app()->getLocale() === $locale)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <noscript><button type="submit">OK</button></noscript>
    </form>
    <a class="live" href="{{ route('servers.index') }}"><i></i>{{ \App\Models\GameServer::where('is_online', true)->count() }} {{ __('ui.common.live') }}</a>
</header>
<main>@yield('content')</main>
<footer>
    <div class="brand"><span>{{ $siteSettings->site_abbreviation }}</span><b>{{ $siteSettings->brandPrefix() }}<em>{{ $siteSettings->brandAccent() }}</em></b></div>
    <div class="footer-copy"><p>{{ $siteSettings->translated('footer_text') }}</p><p class="footer-credit">{{ __('ui.footer.created_by') }} <a href="https://interweb.bg" target="_blank" rel="noopener noreferrer">InterWeb Ltd.</a></p></div>
    <div><a href="{{ route('servers.index') }}">{{ __('ui.nav.servers') }}</a> · <a href="{{ route('news.index') }}">{{ __('ui.nav.news') }}</a></div>
</footer>
</body>
</html>
