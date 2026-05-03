<?php

namespace Tests\Feature;

use App\Mail\MahnungMail;
use App\Models\Mieter;
use App\Models\Rechnung;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MahnungTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_invoice_can_receive_reminder_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $mieter = Mieter::factory()->create([
            'mandant_id' => $user->mandant_id,
            'email' => 'tenant@example.org',
        ]);

        $rechnung = Rechnung::query()->create([
            'mandant_id' => $user->mandant_id,
            'mieter_id' => $mieter->id,
            'einheit_id' => null,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 50_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => now()->subDays(5)->toDateString(),
            'bezahlt_am' => null,
        ]);

        $this->actingAs($user)
            ->post(route('rechnungen.mahnung', $rechnung))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        Mail::assertSent(MahnungMail::class, function (MahnungMail $mail) use ($rechnung): bool {
            return $mail->rechnung->is($rechnung);
        });
    }
}
