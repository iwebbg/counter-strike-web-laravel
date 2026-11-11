@props(['activeLocale', 'tabId'])

<div class="locale-tabs" data-locale-tabs data-active-locale="{{ $activeLocale }}">
    <div class="locale-tab-list" role="tablist" aria-label="{{ __('ui.admin.language_content') }}">
        @foreach(config('localization.locales') as $locale => $language)
            <button
                type="button"
                id="locale-tab-{{ $tabId }}-{{ $locale }}"
                class="locale-tab @if($locale === $activeLocale) is-active @endif"
                role="tab"
                aria-selected="{{ $locale === $activeLocale ? 'true' : 'false' }}"
                aria-controls="locale-panel-{{ $tabId }}-{{ $locale }}"
                tabindex="{{ $locale === $activeLocale ? '0' : '-1' }}"
                data-locale-tab="{{ $locale }}"
            >
                <span>{{ strtoupper($locale) }}</span>
                {{ $language }}
            </button>
        @endforeach
    </div>

    {{ $slot }}
</div>

