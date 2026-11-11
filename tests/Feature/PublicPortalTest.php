<?php

namespace Tests\Feature;

use App\Models\GameServer;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the main visitor journeys. */
class PublicPortalTest extends TestCase
{
    use RefreshDatabase;

    /** Seeds portal fixtures before each test. */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Shows live network content on the landing page. */
    public function test_home_page_shows_seeded_servers_and_news(): void
    {
        $this->get('/')->assertOk()->assertSee('ИГРАЙ.')->assertSee('ПОБЕДИ.')->assertSee('FragZone Classic #1')->assertSee('Retakes уикенд');
    }

    /** Stores the selected language and renders the English interface. */
    public function test_visitor_can_switch_interface_to_english(): void
    {
        $this->from('/')->post('/locale', ['locale' => 'en'])
            ->assertRedirect('/')
            ->assertSessionHas('locale', 'en');

        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('<select name="locale"', false)
            ->assertSee('<option value="en" selected>English</option>', false)
            ->assertSee('PLAY.')
            ->assertSee('WIN.')
            ->assertSee('BROWSE SERVERS')
            ->assertSee('Home');
    }

    /** Rejects locale codes that are not configured for the portal. */
    public function test_visitor_cannot_select_an_unsupported_locale(): void
    {
        $this->post('/locale', ['locale' => 'de'])->assertNotFound();
        $this->assertNull(session('locale'));
    }

    /** Filters the server directory by game. */
    public function test_server_directory_filters_by_game(): void
    {
        $this->get('/servers?game=csgo')->assertOk()->assertSee('FragZone Retakes')->assertDontSee('FragZone Classic #1');
    }

    /** Opens a server through its public slug. */
    public function test_server_detail_shows_connection_address(): void
    {
        $this->get('/servers/fragzone-classic')->assertOk()->assertSee('127.0.0.1:27015');
    }

    /** Shows persisted player details ordered by their queried score. */
    public function test_server_detail_shows_player_ranking(): void
    {
        $server = GameServer::where('slug', 'fragzone-classic')->firstOrFail();
        $server->snapshots()->create([
            'is_online' => true,
            'map' => 'de_dust2',
            'players' => 2,
            'max_players' => 32,
            'player_data' => [
                ['name' => 'Top Fragger', 'score' => 42, 'duration_seconds' => 720],
                ['name' => 'Second Player', 'score' => 18, 'duration_seconds' => 420],
            ],
            'queried_at' => now(),
        ]);

        $this->get('/servers/fragzone-classic')
            ->assertOk()
            ->assertSeeInOrder(['Top Fragger', '42', 'Second Player', '18']);
    }
}
