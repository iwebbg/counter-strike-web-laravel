<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies physical default-theme rendering and partial custom overrides. */
class ThemeRenderingTest extends TestCase
{
    use RefreshDatabase;

    /** Uses a custom home view and the FragZone fallback for missing pages/layouts. */
    public function test_partial_theme_falls_back_to_physical_fragzone_theme(): void
    {
        $filesystem = new Filesystem;
        $themePath = resource_path('views/themes/test-partial');
        $filesystem->ensureDirectoryExists($themePath);
        $filesystem->put($themePath.'/home.blade.php', "@extends('layouts.app')\n@section('content')<p>Custom theme marker</p>@endsection");

        try {
            $this->seed(DatabaseSeeder::class);
            SiteSetting::query()->firstOrFail()->update(['theme_slug' => 'test-partial']);

            $this->get('/')->assertOk()->assertSee('Custom theme marker');
            $this->get('/servers')->assertOk()->assertSee('ИЗБЕРИ СВОЯТА');
        } finally {
            $filesystem->deleteDirectory($themePath);
        }
    }
}
