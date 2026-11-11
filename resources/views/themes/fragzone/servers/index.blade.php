@extends('layouts.app')
@section('title', __('ui.servers.title'))
@section('content')
<section class="page-hero shell"><span class="eyebrow">{{ __('ui.servers.browser') }}</span><h1>{{ __('ui.servers.choose') }} <em>{{ __('ui.servers.arena') }}</em></h1><p>{{ __('ui.servers.intro') }}</p></section>
<section class="shell">
    <form class="filters">
        <input name="q" value="{{ request('q') }}" placeholder="{{ __('ui.servers.search') }}">
        <select name="game"><option value="">{{ __('ui.servers.all_games') }}</option><option value="cs16" @selected(request('game') === 'cs16')>CS 1.6</option><option value="csgo" @selected(request('game') === 'csgo')>CS:GO</option></select>
        <select name="category"><option value="">{{ __('ui.servers.all_categories') }}</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->localizedName() }}</option>@endforeach</select>
        <button class="button">{{ __('ui.servers.filter') }}</button>
    </form>
    <div class="server-grid">@forelse($servers as $server)@include('components.server-card', ['server' => $server])@empty<p>{{ __('ui.servers.none') }}</p>@endforelse</div>
    {{ $servers->links() }}
</section>
@endsection
