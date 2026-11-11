@extends('layouts.app')
@section('title', $server->exists ? __('ui.admin.edit_server') : __('ui.admin.new_server'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ $server->exists ? __('ui.admin.edit_server') : __('ui.admin.new_server') }}</h1><a href="{{ route('admin.servers.index') }}">{{ __('ui.admin.back') }}</a></div>
    <form class="form panel" method="post" action="{{ $server->exists ? route('admin.servers.update', $server) : route('admin.servers.store') }}">
        @csrf @if($server->exists) @method('put') @endif
        <div class="form-grid">
            <label>{{ __('ui.common.name') }}<input name="name" value="{{ old('name', $server->name) }}" data-slug-source required></label>
            <label>Slug <small>{{ __('ui.admin.slug_help') }}</small><input name="slug" value="{{ old('slug', $server->slug) }}" data-slug-target placeholder="{{ __('ui.admin.slug_placeholder') }}"></label>
            <label>{{ __('ui.admin.category') }}<select name="server_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('server_category_id', $server->server_category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
            <label>{{ __('ui.common.game') }}<select name="game"><option value="cs16" @selected(old('game', $server->game) === 'cs16')>CS 1.6</option><option value="csgo" @selected(old('game', $server->game) === 'csgo')>CS:GO</option></select></label>
            <label>{{ __('ui.admin.protocol') }}<select name="protocol"><option value="goldsrc" @selected(old('protocol', $server->protocol) === 'goldsrc')>GoldSrc</option><option value="source" @selected(old('protocol', $server->protocol) === 'source')>Source</option></select></label>
            <label>{{ __('ui.common.location') }}<input name="location" value="{{ old('location', $server->location) }}"></label>
            <label>Host<input name="host" value="{{ old('host', $server->host) }}" required></label>
            <label>Port<input type="number" name="port" value="{{ old('port', $server->port ?? 27015) }}" required></label>
            <label class="wide">{{ __('ui.admin.description') }}<textarea name="description">{{ old('description', $server->description) }}</textarea></label>
        </div>
        <p><label><input type="checkbox" name="is_featured" value="1" @checked($server->is_featured)> {{ __('ui.admin.featured') }}</label> <label><input type="checkbox" name="is_published" value="1" @checked(! $server->exists || $server->is_published)> {{ __('ui.admin.published') }}</label></p>
        <button class="button">{{ __('ui.admin.save') }}</button>
    </form>
</section>
@endsection
