@extends('layouts.app')
@section('title', $category->exists ? __('ui.admin.edit_category') : __('ui.admin.new_category'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ $category->exists ? __('ui.admin.edit_category') : __('ui.admin.new_category') }}</h1><a href="{{ route('admin.categories.index') }}">{{ __('ui.admin.back') }}</a></div>
    @if($errors->any())<div class="error panel">{{ $errors->first() }}</div>@endif
    <form class="form panel" method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf @if($category->exists) @method('put') @endif
        <div class="form-grid">
            <label>{{ __('ui.admin.slug') }} <small>{{ __('ui.admin.slug_help') }}</small><input name="slug" value="{{ old('slug', $category->slug) }}" data-slug-target></label>
            <label>{{ __('ui.admin.accent_color') }}<input type="color" name="accent_color" value="{{ old('accent_color', $category->accent_color ?? '#f4b942') }}" required></label>
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
        <x-admin.locale-tabs :active-locale="$activeLocale" tab-id="category">
            @foreach(config('localization.locales') as $locale => $language)
            <fieldset id="locale-panel-category-{{ $locale }}" class="settings-language locale-panel" role="tabpanel" aria-labelledby="locale-tab-category-{{ $locale }}" data-locale-panel="{{ $locale }}" @if($locale !== $activeLocale) hidden @endif>
                <legend class="sr-only">{{ $language }}</legend>
                <div class="form-grid">
                    <label>{{ __('ui.common.name') }}<input name="name_translations[{{ $locale }}]" value="{{ old('name_translations.'.$locale, $category->name_translations[$locale] ?? ($locale === 'bg' ? $category->name : '')) }}" @if($locale === 'en') data-slug-source @endif required></label>
                    <label class="wide">{{ __('ui.admin.description') }}<textarea name="description_translations[{{ $locale }}]">{{ old('description_translations.'.$locale, $category->description_translations[$locale] ?? ($locale === 'bg' ? $category->description : '')) }}</textarea></label>
                </div>
            </fieldset>
            @endforeach
        </x-admin.locale-tabs>
        <button class="button">{{ __('ui.admin.save') }}</button>
    </form>
    @if($category->exists)
        <form class="danger-zone" method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('ui.admin.confirm_delete_category') }}')">@csrf @method('delete')<button class="button danger" type="submit">{{ __('ui.admin.delete_category') }}</button></form>
    @endif
</section>
@endsection
