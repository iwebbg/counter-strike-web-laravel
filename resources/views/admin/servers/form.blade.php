@extends('layouts.app')
@section('title', $server->exists ? 'Редакция' : 'Нов сървър')
@section('content')
<section class="admin shell">
    <div class="heading"><h1>{{ $server->exists ? 'РЕДАКЦИЯ' : 'НОВ СЪРВЪР' }}</h1><a href="{{ route('admin.servers.index') }}">← Назад</a></div>
    <form class="form panel" method="post" action="{{ $server->exists ? route('admin.servers.update', $server) : route('admin.servers.store') }}">
        @csrf @if($server->exists) @method('put') @endif
        <div class="form-grid">
            <label>Име<input name="name" value="{{ old('name', $server->name) }}" data-slug-source required></label>
            <label>Slug <small>генерира се автоматично</small><input name="slug" value="{{ old('slug', $server->slug) }}" data-slug-target placeholder="автоматично-от-името"></label>
            <label>Категория<select name="server_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('server_category_id', $server->server_category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
            <label>Игра<select name="game"><option value="cs16" @selected(old('game', $server->game) === 'cs16')>CS 1.6</option><option value="csgo" @selected(old('game', $server->game) === 'csgo')>CS:GO</option></select></label>
            <label>Протокол<select name="protocol"><option value="goldsrc" @selected(old('protocol', $server->protocol) === 'goldsrc')>GoldSrc</option><option value="source" @selected(old('protocol', $server->protocol) === 'source')>Source</option></select></label>
            <label>Локация<input name="location" value="{{ old('location', $server->location) }}"></label>
            <label>Host<input name="host" value="{{ old('host', $server->host) }}" required></label>
            <label>Port<input type="number" name="port" value="{{ old('port', $server->port ?? 27015) }}" required></label>
            <label class="wide">Описание<textarea name="description">{{ old('description', $server->description) }}</textarea></label>
        </div>
        <p><label><input type="checkbox" name="is_featured" value="1" @checked($server->is_featured)> Featured</label> <label><input type="checkbox" name="is_published" value="1" @checked(! $server->exists || $server->is_published)> Публикуван</label></p>
        <button class="button">ЗАПАЗИ →</button>
    </form>
</section>
@endsection
