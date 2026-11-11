<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServerCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Manages server categories and their localized public content. */
class ServerCategoryController
{
    /** Lists configured categories. */
    public function index(): View
    {
        return view('admin.categories.index', ['categories' => ServerCategory::withCount('servers')->orderBy('name')->get()]);
    }

    /** Shows the create form. */
    public function create(): View
    {
        return view('admin.categories.form', ['category' => new ServerCategory]);
    }

    /** Stores a category. */
    public function store(Request $request): RedirectResponse
    {
        ServerCategory::create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('status', __('ui.admin.category_created'));
    }

    /** Shows the edit form. */
    public function edit(ServerCategory $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    /** Updates a category. */
    public function update(Request $request, ServerCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('status', __('ui.admin.category_updated'));
    }

    /** Deletes an unused category. */
    public function destroy(ServerCategory $category): RedirectResponse
    {
        if ($category->servers()->exists()) {
            return back()->withErrors(['category' => __('ui.admin.category_in_use')]);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', __('ui.admin.category_deleted'));
    }

    /** Validates and normalizes category input. */
    private function validated(Request $request, ?ServerCategory $category = null): array
    {
        $rules = [
            'slug' => ['nullable', 'alpha_dash', 'max:140', Rule::unique('server_categories')->ignore($category)],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];

        foreach (array_keys(config('localization.locales')) as $locale) {
            $rules['name_translations.'.$locale] = ['required', 'string', 'max:100'];
            $rules['description_translations.'.$locale] = ['nullable', 'string', 'max:1000'];
        }

        $data = $request->validate($rules);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name_translations']['en'] ?? $data['name_translations']['bg']);
        $data['name'] = $data['name_translations']['bg'];
        $data['description'] = $data['description_translations']['bg'] ?? null;

        return $data;
    }
}
