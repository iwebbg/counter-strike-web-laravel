<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Stores the visitor's preferred interface language. */
class LocaleController
{
    /** Change the active interface language. */
    public function __invoke(Request $request): RedirectResponse
    {
        $locale = (string) $request->input('locale');

        abort_unless(array_key_exists($locale, config('localization.locales')), 404);

        $request->session()->put('locale', $locale);

        return back();
    }
}
