<?php

namespace App\Http\Controllers\Admin;

use App\Models\SiteSetting;
use App\Themes\ThemeManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Manages global branding, homepage copy and SEO metadata. */
class SiteSettingController
{
    public function __construct(private readonly ThemeManager $themes) {}

    /** Shows the settings form. */
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => SiteSetting::query()->firstOrFail(),
            'themes' => $this->themes->available(),
        ]);
    }

    /** Updates the singleton settings row. */
    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'site_name' => ['required', 'string', 'max:80'],
            'site_abbreviation' => ['required', 'string', 'max:12'],
            'theme_slug' => ['required', Rule::in(array_keys($this->themes->available()))],
        ];

        foreach (array_keys(config('localization.locales')) as $locale) {
            foreach (['network_label', 'meta_title', 'meta_description', 'meta_keywords', 'hero_eyebrow', 'hero_primary', 'hero_accent', 'home_description', 'footer_text'] as $attribute) {
                $rules[$attribute.'.'.$locale] = ['required', 'string', 'max:'.($attribute === 'meta_keywords' ? 500 : 255)];
            }
        }

        SiteSetting::query()->firstOrFail()->update($request->validate($rules));

        return back()->with('status', __('ui.admin.settings_saved'));
    }
}
