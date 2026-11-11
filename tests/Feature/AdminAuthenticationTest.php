<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the seeded administrator login and isolated test database. */
class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** Authenticates the documented local administrator account. */
    public function test_seeded_administrator_can_log_in(): void
    {
        $this->seed(DatabaseSeeder::class);
        $administrator = User::where('email', 'admin@fragzone.local')->firstOrFail();

        $this->post('/admin/login', [
            'email' => 'admin@fragzone.local',
            'password' => 'ChangeMe123!',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($administrator);
    }

    /** Prevents the suite from ever using the local MariaDB database. */
    public function test_suite_uses_an_isolated_in_memory_database(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }
}
