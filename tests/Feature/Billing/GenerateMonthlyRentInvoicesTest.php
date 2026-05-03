<?php

namespace Tests\Feature\Billing;

use App\Domain\Billing\GenerateMonthlyRentInvoices;
use App\Models\Mandant;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use App\Models\Rechnung;
use App\Models\Einheit;
use App\Models\Mieter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenerateMonthlyRentInvoicesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function running_twice_for_same_period_creates_single_rent_invoice(): void
    {
        $billing = Carbon::parse('2026-05-15');
        Carbon::setTestNow($billing);

        $fixture = $this->createFixtureLease(startsOn: '2026-01-01');

        $service = new GenerateMonthlyRentInvoices;

        $this->assertSame(1, $service->run('2026-05', mandantId: $fixture['mandant']->id));
        $this->assertSame(0, $service->run('2026-05', mandantId: $fixture['mandant']->id));

        $this->assertSame(1, Rechnung::query()->rent()->count());
        $invoice = Rechnung::query()->rent()->first();
        $this->assertSame(Rechnung::TYP_RENT, $invoice->typ);
        $this->assertSame('2026-05', $invoice->billing_period);
        $this->assertSame($fixture['lease']->id, $invoice->mietvertrag_id);
        $this->assertSame(100_000, (int) $invoice->betrag_cent);
        $this->assertSame([
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'total_cent' => 100_000,
        ], $invoice->source_data);
    }

    #[Test]
    public function two_active_leases_receive_one_invoice_each_for_same_month(): void
    {
        $billing = Carbon::parse('2026-05-02');
        Carbon::setTestNow($billing);

        $fixtureA = $this->createFixtureLease(startsOn: '2026-01-01');
        $fixtureB = $this->createFixtureLease(startsOn: '2026-01-01');

        $service = new GenerateMonthlyRentInvoices;

        $this->assertSame(2, $service->run('2026-05'));

        $this->assertSame(2, Rechnung::query()->rent()->where('billing_period', '2026-05')->count());
    }

    #[Test]
    public function skips_lease_not_active_in_that_month(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-10'));

        $fixture = $this->createFixtureLease(startsOn: '2026-06-01');

        $service = new GenerateMonthlyRentInvoices;

        $this->assertSame(0, $service->run('2026-05', mandantId: $fixture['mandant']->id));
        $this->assertSame(0, Rechnung::query()->rent()->count());
    }

    #[Test]
    public function after_storno_a_new_rent_invoice_can_be_generated_for_same_period(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-15'));

        $fixture = $this->createFixtureLease(startsOn: '2026-01-01');
        $service = new GenerateMonthlyRentInvoices;

        $this->assertSame(1, $service->run('2026-05', mandantId: $fixture['mandant']->id));

        $fixture['lease']->refresh();
        $expectedNextPeriod = $fixture['lease']->next_billing_period;

        $invoice = Rechnung::query()->rent()->where('billing_period', '2026-05')->firstOrFail();
        $invoice->update([
            'status' => Rechnung::STATUS_STORNIERT,
            'storniert_am' => now(),
        ]);

        $this->assertSame(1, $service->run('2026-05', mandantId: $fixture['mandant']->id));

        $this->assertSame(2, Rechnung::query()->rent()->where('billing_period', '2026-05')->count());
        $this->assertSame(1, Rechnung::query()->rent()->where('billing_period', '2026-05')->where('status', Rechnung::STATUS_OFFEN)->count());

        $fixture['lease']->refresh();
        $this->assertSame($expectedNextPeriod, $fixture['lease']->next_billing_period);
    }

    #[Test]
    public function manual_invoice_with_null_contract_does_not_affect_rent_idempotency(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-10'));

        $fixture = $this->createFixtureLease(startsOn: '2026-01-01');

        Rechnung::query()->create([
            'mandant_id' => $fixture['mandant']->id,
            'mieter_id' => $fixture['mieter']->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 10,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => null,
            'bezahlt_am' => null,
        ]);

        $service = new GenerateMonthlyRentInvoices;
        $this->assertSame(1, $service->run('2026-05', mandantId: $fixture['mandant']->id));
        $this->assertSame(0, $service->run('2026-05', mandantId: $fixture['mandant']->id));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * @return array{mandant: Mandant, mieter: Mieter, lease: Mietvertrag}
     */
    private function createFixtureLease(string $startsOn): array
    {
        $mandant = Mandant::factory()->create();
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
            'starts_on' => $startsOn,
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        return compact('mandant', 'mieter', 'lease');
    }
}
