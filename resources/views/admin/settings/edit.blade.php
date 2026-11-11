@extends('layouts.app')
@section('title', __('ui.admin.seo_settings'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><div><span class="eyebrow">{{ __('ui.admin.branding_and_seo') }}</span><h1>{{ __('ui.admin.seo_settings') }}</h1></div></div>
    @if(session('status'))<p class="flash">{{ session('status') }}</p>@endif
    <form class="form panel" method="post" action="{{ route('admin.settings.update') }}">
        @csrf @method('put')
        <h2>{{ __('ui.admin.branding') }}</h2>
        <div class="form-grid">
            <label>{{ __('ui.admin.site_name') }}<input name="site_name" value="{{ old('site_name', $settings->site_name) }}" required></label>
            <label>{{ __('ui.admin.site_abbreviation') }}<input name="site_abbreviation" value="{{ old('site_abbreviation', $settings->site_abbreviation) }}" required></label>
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
        <x-admin.locale-tabs :active-locale="$activeLocale" tab-id="settings">
            @foreach(config('localization.locales') as $locale => $language)
            <fieldset id="locale-panel-settings-{{ $locale }}" class="settings-language locale-panel" role="tabpanel" aria-labelledby="locale-tab-settings-{{ $locale }}" data-locale-panel="{{ $locale }}" @if($locale !== $activeLocale) hidden @endif>
                <legend class="sr-only">{{ $language }}</legend>
                <div class="form-grid">
                    <label>{{ __('ui.admin.network_label') }}<input name="network_label[{{ $locale }}]" value="{{ old('network_label.'.$locale, $settings->network_label[$locale] ?? '') }}" required></label>
                    <label>{{ __('ui.admin.meta_title') }}<input name="meta_title[{{ $locale }}]" value="{{ old('meta_title.'.$locale, $settings->meta_title[$locale] ?? '') }}" required></label>
                    <label class="wide">{{ __('ui.admin.meta_description') }}<textarea name="meta_description[{{ $locale }}]" required>{{ old('meta_description.'.$locale, $settings->meta_description[$locale] ?? '') }}</textarea></label>
                    <label class="wide">{{ __('ui.admin.meta_keywords') }}<input name="meta_keywords[{{ $locale }}]" value="{{ old('meta_keywords.'.$locale, $settings->meta_keywords[$locale] ?? '') }}" required></label>
                    <label>{{ __('ui.admin.hero_eyebrow') }}<input name="hero_eyebrow[{{ $locale }}]" value="{{ old('hero_eyebrow.'.$locale, $settings->hero_eyebrow[$locale] ?? '') }}" required></label>
                    <label>{{ __('ui.admin.hero_primary') }}<input name="hero_primary[{{ $locale }}]" value="{{ old('hero_primary.'.$locale, $settings->hero_primary[$locale] ?? '') }}" required></label>
                    <label>{{ __('ui.admin.hero_accent') }}<input name="hero_accent[{{ $locale }}]" value="{{ old('hero_accent.'.$locale, $settings->hero_accent[$locale] ?? '') }}" required></label>
                    <label class="wide">{{ __('ui.admin.home_description') }}<textarea name="home_description[{{ $locale }}]" required>{{ old('home_description.'.$locale, $settings->home_description[$locale] ?? '') }}</textarea></label>
                    <label class="wide">{{ __('ui.admin.footer_text') }}<input name="footer_text[{{ $locale }}]" value="{{ old('footer_text.'.$locale, $settings->footer_text[$locale] ?? '') }}" required></label>
                </div>
            </fieldset>
            @endforeach
        </x-admin.locale-tabs>
        <button class="button">{{ __('ui.admin.save') }}</button>
    </form>
</section>
@endsection
