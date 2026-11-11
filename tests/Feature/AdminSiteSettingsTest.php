<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies administrator-managed branding and localized SEO metadata. */
class AdminSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** Seeds administration fixtures before each test. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Allows an administrator to update branding and both locales. */
    public function test_administrator_can_update_site_settings(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();
        $settings = SiteSetting::query()->firstOrFail();
        $payload = $settings->only(['site_name', 'site_abbreviation', 'network_label', 'meta_title', 'meta_description', 'meta_keywords', 'hero_eyebrow', 'hero_primary', 'hero_accent', 'home_description', 'footer_text']);
        $payload['site_name'] = 'StrikeHub';
        $payload['site_abbreviation'] = 'SH';
        $payload['meta_description']['bg'] = 'Ново SEO описание.';

        $this->actingAs($administrator)->get('/admin/settings')
            ->assertOk()
            ->assertSee('data-locale-tabs', false)
            ->assertSee('data-locale-panel="bg"', false)
            ->assertSee('data-locale-panel="en"', false);

        $this->actingAs($administrator)->put('/admin/settings', $payload)
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('site_settings', ['site_name' => 'StrikeHub', 'site_abbreviation' => 'SH']);
        $this->get('/')->assertOk()->assertSee('StrikeHub')->assertSee('Ново SEO описание.');
    }

    /** Prevents anonymous visitors from opening site settings. */
    public function test_guest_cannot_open_site_settings(): void
    {
        $this->get('/admin/settings')->assertRedirect('/admin/login');
    }
}
