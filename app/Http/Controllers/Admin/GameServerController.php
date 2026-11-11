<?php

namespace App\Http\Controllers\Admin;

use App\Models\GameServer;
use App\Models\ServerCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Manages configured game servers. */
class GameServerController
{
    /** Lists managed servers. */
    public function index(): View
    {
        return view('admin.servers.index', ['servers' => GameServer::with('category')->latest()->paginate(20)]);
    }

    /** Shows the create form. */
    public function create(): View
    {
        return view('admin.servers.form', ['server' => new GameServer, 'categories' => ServerCategory::orderBy('name')->get()]);
    }

    /** Stores a server. */
    public function store(Request $request): RedirectResponse
    {
        GameServer::create($this->validated($request));

        return redirect()->route('admin.servers.index')->with('status', __('ui.admin.created'));
    }

    /** Shows the edit form. */
    public function edit(GameServer $server): View
    {
        return view('admin.servers.form', ['server' => $server, 'categories' => ServerCategory::orderBy('name')->get()]);
    }

    /** Updates a server. */
    public function update(Request $request, GameServer $server): RedirectResponse
    {
        $server->update($this->validated($request, $server));

        return redirect()->route('admin.servers.index')->with('status', __('ui.admin.updated'));
    }

    /** Deletes a server. */
    public function destroy(GameServer $server): RedirectResponse
    {
        $server->delete();

        return redirect()->route('admin.servers.index')->with('status', __('ui.admin.deleted'));
    }

    /** Validates and normalizes server form input. */
    private function validated(Request $request, ?GameServer $server = null): array
    {
        $rules = ['server_category_id' => ['required', 'exists:server_categories,id'], 'name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'alpha_dash', 'max:140', 'unique:game_servers,slug,'.($server?->id ?? 'NULL')], 'game' => ['required', 'in:cs16,csgo'], 'protocol' => ['required', 'in:goldsrc,source'], 'host' => ['required', 'string', 'max:253'], 'port' => ['required', 'integer', 'between:1,65535'], 'description' => ['nullable', 'string', 'max:3000'], 'location' => ['nullable', 'string', 'max:80']];
        foreach (array_keys(config('localization.locales')) as $locale) {
            $rules['description_translations.'.$locale] = ['nullable', 'string', 'max:3000'];
        }

        $data = $request->validate($rules);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        if (! empty($data['description_translations'])) {
            $data['description'] = $data['description_translations']['bg'] ?? array_values($data['description_translations'])[0];
        } elseif (isset($data['description'])) {
            $data['description_translations'] = ['bg' => $data['description'], 'en' => $data['description']];
        }
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
