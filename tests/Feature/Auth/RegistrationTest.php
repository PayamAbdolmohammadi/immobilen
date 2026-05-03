<?php

namespace Tests\Feature\Auth;

use App\Models\Mandant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'mandanten_name' => 'HV Demo GmbH',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::query()->where('email', 'test@example.com')->first();
        $this->assertNotNull($user->mandant_id);
        $this->assertSame('HV Demo GmbH', $user->mandant->name);

        $this->assertSame(1, Mandant::query()->count());
    }

    public function test_registration_without_mandanten_name_falls_back_to_display_name(): void
    {
        $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'jane@example.com')->firstOrFail();
        $this->assertSame('Jane Doe', $user->mandant->name);
    }
}
