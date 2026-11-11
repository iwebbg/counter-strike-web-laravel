<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Verifies branded and localized HTTP error responses. */
class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    /** Seeds the website settings required by the web middleware. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Renders an unknown public route through the FragZone error layout. */
    public function test_not_found_page_uses_the_branded_layout(): void
    {
        $this->get('/missing-arena')
            ->assertNotFound()
            ->assertSee('class="error-page"', false)
            ->assertSee('404')
            ->assertSee('РУНДЪТ НЕ Е НАМЕРЕН')
            ->assertSee('href="https://interweb.bg"', false)
            ->assertDontSee(route('admin.login'), false);
    }

    /** Keeps exception pages localized after the visitor changes language. */
    public function test_error_page_uses_the_selected_english_locale(): void
    {
        $this->post('/locale', ['locale' => 'en'])->assertRedirect();

        $this->get('/missing-arena')
            ->assertNotFound()
            ->assertSee('ROUND NOT FOUND')
            ->assertSee('BACK TO HOME');
    }

    /** Uses the matching branded page for an explicitly forbidden request. */
    public function test_forbidden_response_uses_the_branded_layout(): void
    {
        Route::middleware('web')->get('/test-forbidden', fn () => abort(403));

        $this->get('/test-forbidden')
            ->assertForbidden()
            ->assertSee('НЯМАТЕ ДОСТЪП');
    }
}
