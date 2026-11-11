<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Handles administrator sessions. */
class SessionController
{
    /** Shows the sign-in form. */
    public function create(): View
    {
        return view('admin.login');
    }

    /** Authenticates an administrator. */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('ui.auth.invalid')])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /** Ends the active session. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
