<?php

namespace Tests\Feature;

use App\Models\BankTransaction;
use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use App\Models\Rechnung;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_staff_cannot_access_demo_flow(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('demo-flow.index'))->assertForbidden();
    }

    public function test_staff_cannot_post_demo_flow_routes(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)
            ->post(route('demo-flow.start-auto'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->post(route('demo-flow.generate-rent'), ['period' => '2026-05'])
            ->assertForbidden();

        $this->actingAs($staff)
            ->post(route('demo-flow.seed-bank-line'))
            ->assertForbidden();
    }

    public function test_owner_can_access_demo_flow(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)->get(route('demo-flow.index'))->assertOk();
    }

    public function test_owner_can_start_auto_demo(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-12'));

        $fixture = $this->createOwnerWithActiveLease();
        $this->actingAs($fixture['user'])
            ->post(route('demo-flow.start-auto'))
            ->assertRedirect(route('demo-flow.index'))
            ->assertSessionHas('status', 'demo-auto-prepared');
    }

    public function test_generate_rent_only_affects_current_mandant(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-15'));

        $fixtureA = $this->createOwnerWithActiveLease();
        $fixtureB = $this->createOwnerWithActiveLease();

        $this->actingAs($fixtureA['user'])
            ->post(route('demo-flow.generate-rent'), ['period' => '2026-05'])
            ->assertRedirect(route('demo-flow.index'));

        $this->assertSame(1, Rechnung::query()->rent()->where('mandant_id', $fixtureA['mandant']->id)->count());
        $this->assertSame(0, Rechnung::query()->rent()->where('mandant_id', $fixtureB['mandant']->id)->count());
    }

    public function test_seed_bank_line_does_not_create_transactions_for_other_mandants(): void
    {
        $fixtureA = $this->createOwnerWithActiveLease();
        $fixtureB = $this->createOwnerWithActiveLease();

        $beforeB = BankTransaction::query()->where('mandant_id', $fixtureB['mandant']->id)->count();

        $this->actingAs($fixtureA['user'])
            ->post(route('demo-flow.seed-bank-line'))
            ->assertRedirect(route('demo-flow.index'));

        $afterB = BankTransaction::query()->where('mandant_id', $fixtureB['mandant']->id)->count();
        $this->assertSame($beforeB, $afterB);

        $this->assertSame(1, BankTransaction::query()->where('mandant_id', $fixtureA['mandant']->id)->count());
    }

    /**
     * @return array{user: User, mandant: Mandant, lease: Mietvertrag}
     */
    private function createOwnerWithActiveLease(): array
    {
        $mandant = Mandant::factory()->create();
        $user = User::factory()->create(['mandant_id' => $mandant->id]);
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);

        $lease = Mietvertrag::query()->create([
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => '2026-01-01',
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        return ['user' => $user, 'mandant' => $mandant, 'lease' => $lease];
    }
}
