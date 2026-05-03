<?php

namespace Tests\Feature;

use App\Models\Mieter;
use App\Models\Rechnung;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualRechnungTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_manual_invoices(): void
    {
        $this->get(route('manual-rechnungen.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_create_manual_invoice(): void
    {
        $user = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $user->mandant_id]);

        $this->actingAs($user)
            ->post(route('manual-rechnungen.store'), [
                'mieter_id' => $mieter->id,
                'einheit_id' => null,
                'betrag' => '123.45',
                'faellig_am' => '2026-06-01',
                'status' => Rechnung::STATUS_OFFEN,
                'bezahlt_am' => null,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('manual-rechnungen.index'));

        $this->assertDatabaseHas('rechnungen', [
            'mandant_id' => $user->mandant_id,
            'mieter_id' => $mieter->id,
            'typ' => Rechnung::TYP_MANUAL,
            'betrag_cent' => 12_345,
            'status' => Rechnung::STATUS_OFFEN,
        ]);
    }

    public function test_user_can_delete_open_manual_invoice_without_allocations(): void
    {
        $user = User::factory()->create();
        $mieter = Mieter::factory()->create(['mandant_id' => $user->mandant_id]);

        $rechnung = Rechnung::query()->create([
            'mandant_id' => $user->mandant_id,
            'mieter_id' => $mieter->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 10_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $this->actingAs($user)
            ->delete(route('manual-rechnungen.destroy', $rechnung))
            ->assertRedirect(route('manual-rechnungen.index'));

        $this->assertDatabaseMissing('rechnungen', ['id' => $rechnung->id]);
    }
}
