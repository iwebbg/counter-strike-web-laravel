@extends('layouts.app')
@section('title', __('ui.home.title'))
@section('content')
<section class="hero shell">
    <div>
        <span class="eyebrow"><i></i>{{ __('ui.home.eyebrow') }}</span>
        <h1>{{ __('ui.home.hero_1') }}<br><em>{{ __('ui.home.hero_2') }}</em><br>{{ __('ui.home.hero_3') }}</h1>
        <p>{{ __('ui.home.intro') }}</p>
        <p class="buttons"><a class="button" href="{{ route('servers.index') }}">{{ __('ui.home.browse') }}</a><a class="button ghost" href="#news">{{ __('ui.home.latest_news') }}</a></p>
    </div>
    <div class="radar"><div></div><b>{{ $servers->where('is_online', true)->sum('current_players') }}<small>{{ __('ui.home.players_online') }}</small></b></div>
</section>
<section class="strip">
    <div><b>{{ $servers->where('is_online', true)->count() }}</b>{{ __('ui.home.live_servers') }}</div>
    <div><b>{{ $servers->sum('max_players') }}</b>{{ __('ui.home.total_slots') }}</div>
    <div><b>{{ $categories->count() }}</b>{{ __('ui.home.game_modes') }}</div>
    <div><b>24/7</b>{{ __('ui.home.community') }}</div>
</section>
<section class="section shell">
    <div class="heading"><div><span class="eyebrow">{{ __('ui.home.live_network') }}</span><h2>{{ __('ui.home.active_servers') }}</h2></div><a href="{{ route('servers.index') }}">{{ __('ui.home.view_all') }}</a></div>
    <div class="server-grid">@foreach($servers as $server)<x-server-card :server="$server"/>@endforeach</div>
</section>
<section class="section shell">
    <div class="heading"><div><span class="eyebrow">{{ __('ui.home.find_mode') }}</span><h2>{{ __('ui.home.categories') }}</h2></div></div>
    <div class="categories">@foreach($categories as $category)<a href="{{ route('servers.index', ['category' => $category->slug]) }}" style="--accent:{{ $category->accent_color }}"><span>0{{ $loop->iteration }}</span><h3>{{ $category->name }}</h3><p>{{ $category->description }}</p><b>{{ trans_choice('ui.home.servers_count', $category->servers_count, ['count' => $category->servers_count]) }}</b></a>@endforeach</div>
</section>
<section id="news" class="section shell">
    <div class="heading"><div><span class="eyebrow">{{ __('ui.home.intel_feed') }}</span><h2>{{ __('ui.home.latest_news') }}</h2></div><a href="{{ route('news.index') }}">{{ __('ui.home.archive') }}</a></div>
    <div class="news-grid">@foreach($news as $post)<article><time>{{ $post->published_at->format('d.m.Y') }}</time><h3><a href="{{ route('news.show', $post) }}">{{ $post->title }}</a></h3><p>{{ $post->excerpt }}</p><a href="{{ route('news.show', $post) }}">{{ __('ui.home.read') }}</a></article>@endforeach</div>
</section>
@endsection
