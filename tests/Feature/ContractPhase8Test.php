<?php

namespace Tests\Feature;

use App\Domain\Billing\GenerateMonthlyRentInvoices;
use App\Domain\Billing\RentAmountCalculator;
use App\Models\ContractEvent;
use App\Models\Einheit;
use App\Models\Mietvertrag;
use App\Models\Mieter;
use App\Models\Objekt;
use App\Models\User;
use App\Services\Contract\ContractLinkService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContractPhase8Test extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function contract_pdf_template_contains_required_sections(): void
    {
        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_DRAFT);

        $html = $this->renderContractPdfHtml($lease);

        $this->assertStringContainsString('Wohnraummietvertrag', $html);
        $this->assertStringContainsString($lease->fresh()->mieter->name, $html);
        $this->assertStringContainsString('Gesamtmiete', $html);
        $this->assertStringContainsString($lease->starts_on->format('d.m.Y'), $html);
    }

    #[Test]
    public function owner_can_generate_contract_pdf_and_path_is_stored(): void
    {
        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_DRAFT);

        $this->actingAs($user)
            ->post(route('mietvertraege.contract.pdf', $lease))
            ->assertRedirect(route('mietvertraege.show', $lease));

        $lease->refresh();
        $this->assertNotNull($lease->contract_pdf_path);
        $fullPath = Storage::disk('local')->path($lease->contract_pdf_path);
        $this->assertFileExists($fullPath);

        $this->assertDatabaseHas('contract_events', [
            'mietvertrag_id' => $lease->id,
            'event_type' => 'pdf_generated',
            'actor_type' => ContractEvent::ACTOR_OWNER,
        ]);
    }

    #[Test]
    public function token_is_stored_hashed_and_plain_token_opens_public_page(): void
    {
        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_DRAFT);

        $links = new ContractLinkService;
        $plain = $links->createToken($lease);
        $lease->refresh();

        $this->assertSame(48, strlen($plain));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{48}$/', $plain);
        $this->assertSame(hash('sha256', $plain), $lease->signature_token_hash);

        $this->get(route('contracts.public.show', ['token' => $plain]))
            ->assertOk()
            ->assertSee($lease->mieter->name, false);
    }

    #[Test]
    public function owner_post_create_link_logs_contract_event(): void
    {
        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_DRAFT);

        $this->actingAs($user)
            ->post(route('mietvertraege.contract.link', $lease))
            ->assertRedirect(route('mietvertraege.show', $lease));

        $this->assertDatabaseHas('contract_events', [
            'mietvertrag_id' => $lease->id,
            'event_type' => 'link_created',
            'actor_type' => ContractEvent::ACTOR_OWNER,
        ]);
    }

    #[Test]
    public function expired_token_returns_404(): void
    {
        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_DRAFT);

        $links = new ContractLinkService;
        $plain = $links->createToken($lease);

        $lease->refresh()->update(['token_expires_at' => now()->subMinute()]);

        $this->get(route('contracts.public.show', ['token' => $plain]))->assertNotFound();
    }

    #[Test]
    public function tenant_can_accept_contract_and_it_becomes_active(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10'));

        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_DRAFT);

        $links = new ContractLinkService;
        $plain = $links->createToken($lease);

        $this->post(route('contracts.public.accept', ['token' => $plain]), [
            'accepted' => '1',
        ])->assertRedirect(route('contracts.public.show', ['token' => $plain]));

        $lease->refresh();
        $this->assertSame(Mietvertrag::STATUS_ACTIVE, $lease->status);
        $this->assertNotNull($lease->accepted_at);
        $this->assertNotNull($lease->activated_at);

        $this->assertDatabaseHas('contract_events', [
            'mietvertrag_id' => $lease->id,
            'event_type' => 'accepted',
            'actor_type' => ContractEvent::ACTOR_TENANT,
        ]);

        Carbon::setTestNow();
    }

    #[Test]
    public function rent_invoices_are_not_generated_for_draft_leases(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-15'));

        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_DRAFT);

        $service = new GenerateMonthlyRentInvoices;
        $this->assertSame(0, $service->run('2026-05', mandantId: $user->mandant_id));

        Carbon::setTestNow();
    }

    #[Test]
    public function rent_invoices_skip_active_lease_that_started_phase8_workflow_without_activation(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-15'));

        $user = User::factory()->create();
        $lease = $this->createLease($user, Mietvertrag::STATUS_ACTIVE);
        $lease->update([
            'sent_at' => now(),
            'signature_token_hash' => hash('sha256', 'fake'),
        ]);

        $this->assertFalse($lease->fresh()->canGenerateInvoices());

        $service = new GenerateMonthlyRentInvoices;
        $this->assertSame(0, $service->run('2026-05', mandantId: $user->mandant_id));

        Carbon::setTestNow();
    }

    #[Test]
    public function owner_cannot_view_lease_from_other_mandant(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $leaseB = $this->createLease($userB, Mietvertrag::STATUS_DRAFT);

        $this->actingAs($userA)
            ->get(route('mietvertraege.show', ['mietvertrag' => $leaseB->id]))
            ->assertNotFound();
    }

    private function renderContractPdfHtml(Mietvertrag $lease): string
    {
        $lease->load(['mandant', 'mieter', 'einheit.objekt']);

        $snapshot = (new RentAmountCalculator)->forLease($lease);
        $totalEuro = number_format($snapshot['total_cent'] / 100, 2, ',', '.');

        return view('contracts.pdf', [
            'vertrag' => $lease,
            'totalEuro' => $totalEuro,
            'pdfGeneratedAt' => now(),
            'acceptedAuditEvent' => null,
            'kaution_cent' => null,
            'additional_notes' => null,
            'zimmeranzahl' => null,
            'mieter_adresse' => null,
            'objekt_adresse' => null,
        ])->render();
    }

    private function createLease(User $user, string $status): Mietvertrag
    {
        $mandantId = (int) $user->mandant_id;
        $objekt = Objekt::factory()->create(['mandant_id' => $mandantId]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandantId,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandantId]);

        /** @var Mietvertrag $lease */
        $lease = Mietvertrag::query()->create([
            'mandant_id' => $mandantId,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => '2026-01-01',
            'ends_on' => null,
            'status' => $status,
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        return $lease;
    }
}
