<?php

namespace App\Http\Controllers;

use App\Models\GameServer;
use App\Models\NewsPost;
use App\Models\ServerCategory;
use Illuminate\View\View;

/** Renders the public landing page. */
class HomeController
{
    /** Shows current network highlights. */
    public function __invoke(): View
    {
        $servers = GameServer::published()->with('category')->orderByDesc('is_featured')->limit(6)->get();

        return view('home', ['servers' => $servers, 'categories' => ServerCategory::withCount(['servers' => fn ($query) => $query->published()])->get(), 'news' => NewsPost::published()->latest('published_at')->limit(3)->get()]);
    }
}
