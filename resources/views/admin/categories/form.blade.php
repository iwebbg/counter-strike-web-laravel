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
        @foreach(config('localization.locales') as $locale => $language)
            <fieldset class="settings-language">
                <legend>{{ $language }}</legend>
                <div class="form-grid">
                    <label>{{ __('ui.common.name') }}<input name="name_translations[{{ $locale }}]" value="{{ old('name_translations.'.$locale, $category->name_translations[$locale] ?? ($locale === 'bg' ? $category->name : '')) }}" @if($locale === 'en') data-slug-source @endif required></label>
                    <label class="wide">{{ __('ui.admin.description') }}<textarea name="description_translations[{{ $locale }}]">{{ old('description_translations.'.$locale, $category->description_translations[$locale] ?? ($locale === 'bg' ? $category->description : '')) }}</textarea></label>
                </div>
            </fieldset>
        @endforeach
        <button class="button">{{ __('ui.admin.save') }}</button>
    </form>
    @if($category->exists)
        <form class="danger-zone" method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('ui.admin.confirm_delete_category') }}')">@csrf @method('delete')<button class="button danger" type="submit">{{ __('ui.admin.delete_category') }}</button></form>
    @endif
</section>
@endsection
