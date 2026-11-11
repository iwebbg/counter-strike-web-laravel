<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#11130f">
    <title>@yield('title', 'FragZone') — Counter-Strike Servers</title>
    <meta name="description" content="{{ __('ui.meta_description') }}">
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
    <script src="{{ asset('assets/app.js') }}" defer></script>
</head>
<body>
<div class="ambient a1"></div><div class="ambient a2"></div>
<header class="site-header">
    <a class="brand" href="{{ route('home') }}"><span>FZ</span><b>FRAG<em>ZONE</em><small>COMMUNITY NETWORK</small></b></a>
    <button class="nav-toggle" data-nav-toggle aria-label="{{ __('ui.nav.menu') }}">☰</button>
    <nav data-nav>
        <a href="{{ route('home') }}">{{ __('ui.nav.home') }}</a>
        <a href="{{ route('servers.index') }}">{{ __('ui.nav.servers') }}</a>
        <a href="{{ route('news.index') }}">{{ __('ui.nav.news') }}</a>
        @auth<a href="{{ route('admin.dashboard') }}">{{ __('ui.nav.administration') }}</a>@endauth
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
    <div class="brand"><span>FZ</span><b>FRAG<em>ZONE</em></b></div>
    <p>{{ __('ui.footer_tagline') }}</p>
    <div><a href="{{ route('servers.index') }}">{{ __('ui.nav.servers') }}</a> · <a href="{{ route('news.index') }}">{{ __('ui.nav.news') }}</a> · <a href="{{ route('admin.login') }}">Admin</a></div>
</footer>
</body>
</html>
