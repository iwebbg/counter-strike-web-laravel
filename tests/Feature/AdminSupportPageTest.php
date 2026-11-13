<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the protected bilingual help and support page. */
class AdminSupportPageTest extends TestCase
{
    use RefreshDatabase;

    /** Seeds the administrator and shared site settings. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Shows the complete Bulgarian support page to administrators. */
    public function test_administrator_can_open_support_page(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();

        $this->actingAs($administrator)->get('/admin/support')
            ->assertOk()
            ->assertSee('Как можем да помогнем?')
            ->assertSee('Как да намеря моя Steam ID?')
            ->assertSee('Приложете диагностика')
            ->assertSee('https://github.com/iwebbg/counter-strike-web-laravel/issues/new', false)
            ->assertSee('https://ko-fi.com/interwebltd', false)
            ->assertSee('https://buymeacoffee.com/interweb.bg', false)
            ->assertSee('https://paypal.me/interwebltd', false)
            ->assertSee('data-copy="status"', false);
    }

    /** Renders the translated support content for an English session. */
    public function test_support_page_is_available_in_english(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();

        $this->actingAs($administrator)->withSession(['locale' => 'en'])->get('/admin/support')
            ->assertOk()
            ->assertSee('How can we help?')
            ->assertSee('How do I find my Steam ID?')
            ->assertSee('Include diagnostics');
    }

    /** Keeps support resources private to authenticated administrators. */
    public function test_guest_cannot_open_support_page(): void
    {
        $this->get('/admin/support')->assertRedirect('/admin/login');
    }
}
