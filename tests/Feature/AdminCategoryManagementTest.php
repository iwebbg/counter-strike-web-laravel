<?php

namespace Tests\Feature;

use App\Models\ServerCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies localized category administration. */
class AdminCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    /** Seeds administration fixtures before each test. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Allows an administrator to create and delete an unused category. */
    public function test_administrator_can_manage_category(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();
        $payload = [
            'slug' => '',
            'accent_color' => '#abcdef',
            'name_translations' => ['bg' => 'Сърф', 'en' => 'Surf'],
            'description_translations' => ['bg' => 'Сърф карти.', 'en' => 'Surf maps.'],
        ];

        $this->actingAs($administrator)->get('/admin/categories/create')
            ->assertOk()
            ->assertSee('data-locale-tabs', false)
            ->assertSee('data-locale-panel="bg"', false)
            ->assertSee('data-locale-panel="en"', false);

        $this->actingAs($administrator)->post('/admin/categories', $payload)
            ->assertRedirect('/admin/categories');

        $category = ServerCategory::where('slug', 'surf')->firstOrFail();
        $this->assertSame('Сърф', $category->localizedName('bg'));
        $this->assertSame('Surf', $category->localizedName('en'));

        $this->actingAs($administrator)->get('/admin/categories')
            ->assertOk()
            ->assertSee('class="table-action"', false)
            ->assertSee(__('ui.common.edit'))
            ->assertDontSee(__('ui.common.delete'));

        $this->actingAs($administrator)->delete('/admin/categories/'.$category->id)
            ->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('server_categories', ['id' => $category->id]);
    }

    /** Prevents deletion of a category that still contains servers. */
    public function test_category_with_servers_cannot_be_deleted(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();
        $category = ServerCategory::has('servers')->firstOrFail();

        $this->actingAs($administrator)->delete('/admin/categories/'.$category->id)
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('server_categories', ['id' => $category->id]);
    }
}
