<?php

namespace App\Http\Controllers\Admin;

use App\Models\NewsPost;
use App\Support\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Manages localized news posts. */
class NewsPostController
{
    public function __construct(private readonly RichTextSanitizer $sanitizer) {}

    /** Lists all news posts. */
    public function index(): View
    {
        return view('admin.news.index', ['posts' => NewsPost::latest('published_at')->paginate(20)]);
    }

    /** Shows the create form. */
    public function create(): View
    {
        return view('admin.news.form', ['post' => new NewsPost]);
    }

    /** Stores a news post. */
    public function store(Request $request): RedirectResponse
    {
        NewsPost::create($this->validated($request));

        return redirect()->route('admin.news.index')->with('status', __('ui.admin.news_created'));
    }

    /** Shows the edit form. */
    public function edit(NewsPost $news): View
    {
        return view('admin.news.form', ['post' => $news]);
    }

    /** Updates a news post. */
    public function update(Request $request, NewsPost $news): RedirectResponse
    {
        $news->update($this->validated($request, $news));

        return redirect()->route('admin.news.index')->with('status', __('ui.admin.news_updated'));
    }

    /** Deletes a news post. */
    public function destroy(NewsPost $news): RedirectResponse
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('status', __('ui.admin.news_deleted'));
    }

    /** Validates, localizes and sanitizes editorial input. */
    private function validated(Request $request, ?NewsPost $post = null): array
    {
        $rules = [
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('news_posts')->ignore($post)],
            'published_at' => ['nullable', 'date'],
        ];

        foreach (array_keys(config('localization.locales')) as $locale) {
            $rules['title_translations.'.$locale] = ['required', 'string', 'max:180'];
            $rules['excerpt_translations.'.$locale] = ['required', 'string', 'max:500'];
            $rules['body_translations.'.$locale] = ['required', 'string', 'max:50000'];
        }

        $data = $request->validate($rules);
        foreach ($data['body_translations'] as $locale => $body) {
            $data['body_translations'][$locale] = $this->sanitizer->sanitize($body);
        }

        $data['slug'] = $data['slug'] ?: Str::slug($data['title_translations']['en'] ?? $data['title_translations']['bg']);
        $data['title'] = $data['title_translations']['bg'];
        $data['excerpt'] = $data['excerpt_translations']['bg'];
        $data['body'] = $data['body_translations']['bg'];
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = ($data['published_at'] ?? null) ?: ($data['is_published'] ? now() : null);

        return $data;
    }
}
