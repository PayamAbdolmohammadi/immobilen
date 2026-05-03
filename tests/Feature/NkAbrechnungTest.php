<?php

namespace Tests\Feature;

use App\Models\Einheit;
use App\Models\NkAbrechnung;
use App\Models\Objekt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NkAbrechnungTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_nk_settlements(): void
    {
        $this->get(route('nk-abrechnungen.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_create_settlement_and_download_pdf(): void
    {
        $user = User::factory()->create();
        $objekt = Objekt::factory()->create(['mandant_id' => $user->mandant_id]);
        Einheit::factory()->create(['mandant_id' => $user->mandant_id, 'objekt_id' => $objekt->id]);
        Einheit::factory()->create(['mandant_id' => $user->mandant_id, 'objekt_id' => $objekt->id]);

        $this->actingAs($user)
            ->post(route('nk-abrechnungen.store'), [
                'objekt_id' => $objekt->id,
                'jahr' => 2025,
                'status' => NkAbrechnung::STATUS_DRAFT,
                'positionen' => [
                    ['beschreibung' => 'Heizung', 'betrag' => '100.00'],
                    ['beschreibung' => 'Wasser', 'betrag' => '50.50'],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $nk = NkAbrechnung::query()->first();
        $this->assertNotNull($nk);
        $this->assertSame(15_050, $nk->totalCent());

        $this->actingAs($user)
            ->get(route('nk-abrechnungen.pdf', $nk))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_duplicate_property_year_rejected(): void
    {
        $user = User::factory()->create();
        $objekt = Objekt::factory()->create(['mandant_id' => $user->mandant_id]);

        NkAbrechnung::query()->create([
            'mandant_id' => $user->mandant_id,
            'objekt_id' => $objekt->id,
            'jahr' => 2024,
            'verteilungs_art' => NkAbrechnung::VERTEILUNG_GLEICH_PRO_EINHEIT,
            'status' => NkAbrechnung::STATUS_DRAFT,
        ]);

        $this->actingAs($user)
            ->from(route('nk-abrechnungen.create'))
            ->post(route('nk-abrechnungen.store'), [
                'objekt_id' => $objekt->id,
                'jahr' => 2024,
                'status' => NkAbrechnung::STATUS_DRAFT,
                'positionen' => [
                    ['beschreibung' => 'x', 'betrag' => '1'],
                ],
            ])
            ->assertSessionHasErrors('jahr');
    }
}
