<?php

namespace Tests\Feature;

use App\Models\Mieter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MieterCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_tenant(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('mieter.store'), [
                'name' => 'Max Mustermann',
                'email' => 'max@example.org',
            ])
            ->assertRedirect(route('mieter.index'));

        $this->assertDatabaseHas('mieter', [
            'mandant_id' => $user->mandant_id,
            'name' => 'Max Mustermann',
            'email' => 'max@example.org',
        ]);
    }

    public function test_user_can_update_tenant(): void
    {
        $user = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $user->mandant_id, 'name' => 'Old']);

        $this->actingAs($user)
            ->patch(route('mieter.update', $mieter), [
                'name' => 'New Name',
                'email' => null,
            ])
            ->assertRedirect(route('mieter.index'));

        $this->assertSame('New Name', $mieter->fresh()->name);
    }
}
