<?php

namespace Tests\Feature;

use App\Models\ServerCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies administrator access and server management. */
class AdminServerManagementTest extends TestCase
{
    use RefreshDatabase;

    /** Seeds administration fixtures before each test. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Redirects anonymous visitors to the administrator login. */
    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    /** Allows an administrator to add a server. */
    public function test_administrator_can_create_server(): void
    {
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();
        $category = ServerCategory::firstOrFail();
        $response = $this->actingAs($administrator)->post('/admin/servers', ['server_category_id' => $category->id, 'name' => 'Test Arena', 'slug' => '', 'game' => 'cs16', 'protocol' => 'goldsrc', 'host' => '10.0.0.5', 'port' => 27015, 'description' => 'Fixture server', 'location' => 'Sofia', 'is_published' => '1']);
        $response->assertRedirect('/admin/servers');
        $this->assertDatabaseHas('game_servers', ['slug' => 'test-arena', 'is_published' => true]);
    }

    /** Rejects a signed-in non-administrator. */
    public function test_regular_user_cannot_open_administration(): void
    {
        $user = User::create(['name' => 'Player', 'email' => 'player@example.com', 'password' => 'secret-password', 'is_admin' => false]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
