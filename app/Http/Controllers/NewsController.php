<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\View\View;

/** Renders public news content. */
class NewsController
{
    /** Lists published posts. */
    public function index(): View
    {
        return view('news.index', ['posts' => NewsPost::published()->latest('published_at')->paginate(10)]);
    }

    /** Shows one published post. */
    public function show(NewsPost $post): View
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        return view('news.show', ['post' => $post]);
    }
}
