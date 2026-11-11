<?php

namespace App\Http\Controllers;

use App\Models\GameServer;
use App\Models\ServerCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Renders the public server directory. */
class ServerController
{
    /** Shows filterable servers. */
    public function index(Request $request): View
    {
        $servers = GameServer::published()->with('category')->when($request->filled('game'), fn ($query) => $query->where('game', $request->string('game')))->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category'))))->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))->orderByDesc('is_online')->orderByDesc('current_players')->paginate(12)->withQueryString();

        return view('servers.index', ['servers' => $servers, 'categories' => ServerCategory::orderBy('name')->get()]);
    }

    /** Shows one server and recent samples. */
    public function show(GameServer $server): View
    {
        abort_unless($server->is_published, 404);
        $latestSnapshot = $server->snapshots()->latest('queried_at')->first();

        return view('servers.show', [
            'server' => $server->load('category'),
            'snapshots' => $server->snapshots()->latest('queried_at')->limit(24)->get()->reverse(),
            'players' => collect($latestSnapshot?->player_data ?? []),
        ]);
    }
}
