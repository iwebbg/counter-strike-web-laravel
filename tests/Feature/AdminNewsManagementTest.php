<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies localized rich-text news administration. */
class AdminNewsManagementTest extends TestCase
{
    use RefreshDatabase;

    /** Seeds administration fixtures before each test. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Allows an administrator to publish localized sanitized rich text. */
    public function test_administrator_can_create_localized_news(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();
        $this->actingAs($administrator)->get('/admin/news/create')
            ->assertOk()
            ->assertSee('data-locale-tabs', false)
            ->assertSee('data-rich-editor', false)
            ->assertSee('body_translations[bg]', false)
            ->assertSee('body_translations[en]', false);

        $payload = [
            'slug' => '',
            'title_translations' => ['bg' => 'Турнир', 'en' => 'Tournament'],
            'excerpt_translations' => ['bg' => 'Кратко описание.', 'en' => 'Short description.'],
            'body_translations' => [
                'bg' => '<h2 onclick="alert(1)">Финали</h2><script>alert(1)</script><p>Запиши се.</p>',
                'en' => '<h2>Finals</h2><p>Register now.</p>',
            ],
            'is_published' => '1',
        ];

        $this->actingAs($administrator)->post('/admin/news', $payload)
            ->assertRedirect('/admin/news');

        $post = NewsPost::where('slug', 'tournament')->firstOrFail();
        $this->assertSame('Tournament', $post->localized('title', 'en'));
        $this->assertStringNotContainsString('script', $post->localized('body', 'bg'));
        $this->assertStringNotContainsString('onclick', $post->localized('body', 'bg'));
        $this->assertTrue($post->is_published);

        $this->actingAs($administrator)->get('/admin/news')
            ->assertOk()
            ->assertSee('class="table-action"', false)
            ->assertSee(__('ui.common.edit'))
            ->assertDontSee(__('ui.common.delete'));
    }

    /** Allows an administrator to delete a post. */
    public function test_administrator_can_delete_news(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();
        $post = NewsPost::firstOrFail();

        $this->actingAs($administrator)->delete('/admin/news/'.$post->id)
            ->assertRedirect('/admin/news');

        $this->assertDatabaseMissing('news_posts', ['id' => $post->id]);
    }
}
