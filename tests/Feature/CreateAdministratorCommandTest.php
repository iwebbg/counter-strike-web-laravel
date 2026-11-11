<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Verifies safe interactive administrator provisioning. */
class CreateAdministratorCommandTest extends TestCase
{
    use RefreshDatabase;

    /** Creates an administrator with a validated strong password. */
    public function test_command_creates_an_administrator(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator name', 'Site Operator')
            ->expectsQuestion('Email address', 'operator@example.com')
            ->expectsQuestion('Password', 'StrongPassword1!')
            ->expectsQuestion('Confirm password', 'StrongPassword1!')
            ->expectsOutput('Administrator created.')
            ->assertSuccessful();

        $administrator = User::where('email', 'operator@example.com')->firstOrFail();
        $this->assertTrue($administrator->is_admin);
        $this->assertTrue(Hash::check('StrongPassword1!', $administrator->password));
    }

    /** Rejects weak deployment credentials. */
    public function test_command_rejects_a_weak_password(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator name', 'Site Operator')
            ->expectsQuestion('Email address', 'operator@example.com')
            ->expectsQuestion('Password', 'password')
            ->expectsQuestion('Confirm password', 'password')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'operator@example.com']);
    }
}
