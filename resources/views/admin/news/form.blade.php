@extends('layouts.admin')
@section('title', $post->exists ? __('ui.admin.edit_news') : __('ui.admin.new_news'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ $post->exists ? __('ui.admin.edit_news') : __('ui.admin.new_news') }}</h1><a href="{{ route('admin.news.index') }}">{{ __('ui.admin.back') }}</a></div>
    @if($errors->any())<div class="error panel">{{ $errors->first() }}</div>@endif
    <form class="form panel" method="post" action="{{ $post->exists ? route('admin.news.update', $post) : route('admin.news.store') }}">
        @csrf @if($post->exists) @method('put') @endif
        <div class="form-grid">
            <label>{{ __('ui.admin.slug') }} <small>{{ __('ui.admin.slug_help') }}</small><input name="slug" value="{{ old('slug', $post->slug) }}" data-slug-target></label>
            <label>{{ __('ui.admin.publication_date') }}<input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"></label>
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
        <x-admin.locale-tabs :active-locale="$activeLocale" tab-id="news">
            @foreach(config('localization.locales') as $locale => $language)
            @php($body = app(\App\Support\RichTextSanitizer::class)->sanitize((string) old('body_translations.'.$locale, $post->body_translations[$locale] ?? ($locale === 'bg' ? $post->body : ''))))
            <fieldset id="locale-panel-news-{{ $locale }}" class="settings-language locale-panel" role="tabpanel" aria-labelledby="locale-tab-news-{{ $locale }}" data-locale-panel="{{ $locale }}" @if($locale !== $activeLocale) hidden @endif>
                <legend class="sr-only">{{ $language }}</legend>
                <div class="form-grid">
                    <label>{{ __('ui.admin.title') }}<input name="title_translations[{{ $locale }}]" value="{{ old('title_translations.'.$locale, $post->title_translations[$locale] ?? ($locale === 'bg' ? $post->title : '')) }}" @if($locale === 'en') data-slug-source @endif required></label>
                    <label class="wide">{{ __('ui.admin.excerpt') }}<textarea name="excerpt_translations[{{ $locale }}]" required>{{ old('excerpt_translations.'.$locale, $post->excerpt_translations[$locale] ?? ($locale === 'bg' ? $post->excerpt : '')) }}</textarea></label>
                    <div class="wide rich-editor" data-rich-editor data-link-prompt="{{ __('ui.admin.link_prompt') }}">
                        <span class="editor-label">{{ __('ui.admin.content') }}</span>
                        <div class="editor-toolbar" role="toolbar" aria-label="{{ __('ui.admin.editor_toolbar') }}">
                            <button type="button" data-editor-command="bold"><strong>B</strong></button>
                            <button type="button" data-editor-command="italic"><em>I</em></button>
                            <button type="button" data-editor-command="formatBlock" data-editor-value="h2">H2</button>
                            <button type="button" data-editor-command="insertUnorderedList">• {{ __('ui.admin.list') }}</button>
                            <button type="button" data-editor-command="createLink">↗ {{ __('ui.admin.link') }}</button>
                        </div>
                        <div class="editor-content" contenteditable="true">{!! $body !!}</div>
                        <textarea class="editor-source" name="body_translations[{{ $locale }}]" required>{{ $body }}</textarea>
                    </div>
                </div>
            </fieldset>
            @endforeach
        </x-admin.locale-tabs>
        <div class="form-options"><label class="check-row"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published))><span>{{ __('ui.admin.published') }}</span></label></div>
        <button class="button">{{ __('ui.admin.save') }}</button>
    </form>
    @if($post->exists)
        <form class="danger-zone" method="post" action="{{ route('admin.news.destroy', $post) }}" onsubmit="return confirm('{{ __('ui.admin.confirm_delete_news') }}')">@csrf @method('delete')<button class="button danger" type="submit">{{ __('ui.admin.delete_news') }}</button></form>
    @endif
</section>
@endsection
