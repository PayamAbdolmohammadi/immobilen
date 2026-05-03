<?php

namespace Tests\Feature\Mandant;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MandantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dashboard_redirects_when_user_has_no_mandant(): void
    {
        $user = User::factory()->withoutMandant()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('mandant.setup', absolute: false));
    }

    #[Test]
    public function mandant_setup_screen_is_visible_without_mandant(): void
    {
        $user = User::factory()->withoutMandant()->create();

        $response = $this->actingAs($user)->get(route('mandant.setup'));

        $response->assertOk();
    }

    #[Test]
    public function mandant_setup_stores_mandant_and_allows_dashboard(): void
    {
        $user = User::factory()->withoutMandant()->create();

        $this->actingAs($user)->post(route('mandant.setup.store'), [
            'mandanten_name' => 'Nachzug HV',
        ])->assertRedirect(route('dashboard', absolute: false));

        $user->refresh();
        $this->assertNotNull($user->mandant_id);
        $this->assertSame('Nachzug HV', $user->mandant->name);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }
}
