<?php

namespace Tests\Feature;

use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mietvertrag;
use App\Models\Mieter;
use App\Models\Objekt;
use App\Models\Rechnung;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirected_from_portal_dashboard(): void
    {
        $this->get(route('portal.dashboard'))->assertRedirect(route('portal.login'));
    }

    public function test_tenant_sees_only_own_open_invoices(): void
    {
        $mandant = Mandant::factory()->create();
        $user = User::factory()->create(['mandant_id' => $mandant->id]);

        $mieterA = Mieter::factory()->create([
            'mandant_id' => $mandant->id,
            'email' => 'tenant-a@example.org',
            'password' => 'secret1234',
        ]);
        $mieterB = Mieter::factory()->create([
            'mandant_id' => $mandant->id,
            'email' => 'tenant-b@example.org',
            'password' => 'secret5678',
        ]);

        Rechnung::query()->create([
            'mandant_id' => $mandant->id,
            'mieter_id' => $mieterA->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 50_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);
        Rechnung::query()->create([
            'mandant_id' => $mandant->id,
            'mieter_id' => $mieterB->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 99_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $this->actingAs($mieterA, 'mieter')
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('500,00', false)
            ->assertDontSee('990,00', false);
    }

    public function test_landlord_can_set_portal_password_and_tenant_can_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('mieter.store'), [
                'name' => 'Portal User',
                'email' => 'portal.user@example.org',
                'portal_password' => 'password12',
                'portal_password_confirmation' => 'password12',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('mieter.index'));

        $this->post(route('portal.login'), [
            'email' => 'portal.user@example.org',
            'password' => 'password12',
        ])->assertRedirect(route('portal.dashboard'));

        $this->actingAs(Mieter::query()->where('email', 'portal.user@example.org')->first(), 'mieter')
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee('Portal User', false);
    }

    public function test_active_lease_shows_on_dashboard(): void
    {
        $mandant = Mandant::factory()->create();
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create(['mandant_id' => $mandant->id, 'objekt_id' => $objekt->id]);
        $mieter = Mieter::factory()->create([
            'mandant_id' => $mandant->id,
            'email' => 'lease@example.org',
            'password' => 'lease-pass1',
        ]);
        Mietvertrag::query()->create([
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => now()->startOfMonth()->toDateString(),
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        $this->actingAs($mieter, 'mieter')
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee($objekt->name, false)
            ->assertSee($einheit->name, false);
    }
}
