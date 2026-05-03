<?php

namespace Database\Seeders;

use App\Models\Einheit;
use App\Models\Mietvertrag;
use App\Models\Mieter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Idempotent: adds the WOW demo lease for existing "demo@example.com" installations
 * when DemoDataSeeder was skipped (user already existed).
 */
class WowDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'demo@example.com')->first();
        if ($user === null) {
            return;
        }

        $mandantId = $user->mandant_id;
        if ($mandantId === null) {
            return;
        }

        $anna = Mieter::query()
            ->where('mandant_id', $mandantId)
            ->where('email', 'anna.schmidt@example.org')
            ->first();

        $e1 = Einheit::query()
            ->where('mandant_id', $mandantId)
            ->where('name', 'WE 1 OG links')
            ->first();

        if ($anna === null || $e1 === null) {
            return;
        }

        $exists = Mietvertrag::query()
            ->where('mandant_id', $mandantId)
            ->where('mieter_id', $anna->id)
            ->where('einheit_id', $e1->id)
            ->exists();

        if ($exists) {
            return;
        }

        Mietvertrag::query()->create([
            'mandant_id' => $mandantId,
            'einheit_id' => $e1->id,
            'mieter_id' => $anna->id,
            'starts_on' => Carbon::parse('2024-01-01')->toDateString(),
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 95_000,
            'nebenkosten_vorauszahlung_cent' => 25_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => 3,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);
    }
}
