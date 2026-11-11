<?php

namespace App\Http\Controllers\Admin;

use App\Models\GameServer;
use App\Models\NewsPost;
use Illuminate\View\View;

/** Renders administrator metrics. */
class DashboardController
{
    /** Shows network management summary. */
    public function __invoke(): View
    {
        return view('admin.dashboard', ['serverCount' => GameServer::count(), 'onlineCount' => GameServer::where('is_online', true)->count(), 'playerCount' => GameServer::sum('current_players'), 'newsCount' => NewsPost::count(), 'servers' => GameServer::latest('last_queried_at')->limit(8)->get()]);
    }
}
