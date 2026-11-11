@extends('layouts.app')
@section('title', $server->exists ? __('ui.admin.edit_server') : __('ui.admin.new_server'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ $server->exists ? __('ui.admin.edit_server') : __('ui.admin.new_server') }}</h1><a href="{{ route('admin.servers.index') }}">{{ __('ui.admin.back') }}</a></div>
    @if($errors->any())<div class="error panel">{{ $errors->first() }}</div>@endif
    <form class="form panel" method="post" action="{{ $server->exists ? route('admin.servers.update', $server) : route('admin.servers.store') }}">
        @csrf @if($server->exists) @method('put') @endif
        <div class="form-grid">
            <label>{{ __('ui.common.name') }}<input name="name" value="{{ old('name', $server->name) }}" data-slug-source required></label>
            <label>{{ __('ui.admin.slug') }} <small>{{ __('ui.admin.slug_help') }}</small><input name="slug" value="{{ old('slug', $server->slug) }}" data-slug-target placeholder="{{ __('ui.admin.slug_placeholder') }}"></label>
            <label>{{ __('ui.admin.category') }}<select name="server_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('server_category_id', $server->server_category_id) == $category->id)>{{ $category->localizedName() }}</option>@endforeach</select></label>
            <label>{{ __('ui.common.game') }}<select name="game"><option value="cs16" @selected(old('game', $server->game) === 'cs16')>CS 1.6</option><option value="csgo" @selected(old('game', $server->game) === 'csgo')>CS:GO</option></select></label>
            <label>{{ __('ui.admin.protocol') }}<select name="protocol"><option value="goldsrc" @selected(old('protocol', $server->protocol) === 'goldsrc')>GoldSrc</option><option value="source" @selected(old('protocol', $server->protocol) === 'source')>Source</option></select></label>
            <label>{{ __('ui.common.location') }}<input name="location" value="{{ old('location', $server->location) }}"></label>
            <label>{{ __('ui.admin.host') }}<input name="host" value="{{ old('host', $server->host) }}" required></label>
            <label>{{ __('ui.admin.port') }}<input type="number" name="port" value="{{ old('port', $server->port ?? 27015) }}" required></label>
        </div>
        @php
            $activeLocale = array_key_exists(app()->getLocale(), config('localization.locales')) ? app()->getLocale() : array_key_first(config('localization.locales'));
            foreach (array_keys(config('localization.locales')) as $candidateLocale) {
                if (collect($errors->keys())->contains(fn (string $key): bool => str_ends_with($key, '.'.$candidateLocale))) {
                    $activeLocale = $candidateLocale;
                    break;
                }
            }
        @endphp
        <x-admin.locale-tabs :active-locale="$activeLocale" tab-id="server">
            @foreach(config('localization.locales') as $locale => $language)
            <fieldset id="locale-panel-server-{{ $locale }}" class="settings-language locale-panel" role="tabpanel" aria-labelledby="locale-tab-server-{{ $locale }}" data-locale-panel="{{ $locale }}" @if($locale !== $activeLocale) hidden @endif>
                <legend class="sr-only">{{ $language }}</legend>
                <label>{{ __('ui.admin.description') }}<textarea name="description_translations[{{ $locale }}]">{{ old('description_translations.'.$locale, $server->description_translations[$locale] ?? ($locale === 'bg' ? $server->description : '')) }}</textarea></label>
            </fieldset>
            @endforeach
        </x-admin.locale-tabs>
        <div class="form-options">
            <label class="check-row"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $server->is_featured))><span>{{ __('ui.admin.featured') }}</span></label>
            <label class="check-row"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', ! $server->exists || $server->is_published))><span>{{ __('ui.admin.published') }}</span></label>
        </div>
        <button class="button">{{ __('ui.admin.save') }}</button>
    </form>
    @if($server->exists)
        <form class="danger-zone" method="post" action="{{ route('admin.servers.destroy', $server) }}" onsubmit="return confirm('{{ __('ui.admin.confirm_delete_server') }}')">@csrf @method('delete')<button class="button danger" type="submit">{{ __('ui.admin.delete_server') }}</button></form>
    @endif
</section>
@endsection
