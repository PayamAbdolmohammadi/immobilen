<?php

namespace App\Domain\Billing;

use App\Models\Mietvertrag;
use App\Models\Rechnung;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class GenerateMonthlyRentInvoices
{
    public function __construct(
        private RentAmountCalculator $calculator = new RentAmountCalculator,
    ) {}

    /**
     * @throws \InvalidArgumentException
     */
    public function run(string $billingPeriod, ?int $mandantId = null): int
    {
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $billingPeriod)) {
            throw new \InvalidArgumentException('billing_period must be YYYY-MM.');
        }

        $periodStart = Carbon::createFromFormat('Y-m-d', $billingPeriod.'-01')->startOfMonth();
        $periodEnd = (clone $periodStart)->endOfMonth();

        $created = 0;

        $query = Mietvertrag::query()->where('status', Mietvertrag::STATUS_ACTIVE);
        if ($mandantId !== null) {
            $query->where('mandant_id', $mandantId);
        }

        foreach ($query->cursor() as $lease) {
            $created += DB::transaction(function () use ($lease, $billingPeriod, $periodStart, $periodEnd): int {
                /** @var Mietvertrag $locked */
                $locked = Mietvertrag::query()->whereKey($lease->id)->lockForUpdate()->firstOrFail();

                if (! $this->isEligible($locked, $periodStart, $periodEnd)) {
                    return 0;
                }

                $exists = Rechnung::query()
                    ->where('mietvertrag_id', $locked->id)
                    ->where('billing_period', $billingPeriod)
                    ->where('typ', Rechnung::TYP_RENT)
                    ->whereNot('status', Rechnung::STATUS_STORNIERT)
                    ->exists();

                if ($exists) {
                    return 0;
                }

                $computed = $this->calculator->forLease($locked);

                Rechnung::query()->create([
                    'mandant_id' => $locked->mandant_id,
                    'mieter_id' => $locked->mieter_id,
                    'einheit_id' => $locked->einheit_id,
                    'mietvertrag_id' => $locked->id,
                    'typ' => Rechnung::TYP_RENT,
                    'billing_period' => $billingPeriod,
                    'betrag_cent' => $computed['total_cent'],
                    'source_data' => $computed['source_data'],
                    'status' => Rechnung::STATUS_OFFEN,
                    'faellig_am' => $periodEnd->toDateString(),
                    'bezahlt_am' => null,
                ]);

                $nextPeriodHint = $locked->next_billing_period;
                // Avoid advancing lease counters when regenerating an older period (e.g. after Storno).
                if ($nextPeriodHint === null || $billingPeriod >= $nextPeriodHint) {
                    $locked->update([
                        'last_billed_at' => now(),
                        'next_billing_period' => $periodEnd->copy()->addDay()->startOfMonth()->format('Y-m'),
                    ]);
                }

                return 1;
            });
        }

        return $created;
    }

    private function isEligible(Mietvertrag $lease, Carbon $periodStart, Carbon $periodEnd): bool
    {
        if (! $lease->canGenerateInvoices()) {
            return false;
        }

        if ($lease->starts_on->greaterThan($periodEnd)) {
            return false;
        }

        if ($lease->ends_on !== null && $lease->ends_on->lessThan($periodStart)) {
            return false;
        }

        return true;
    }
}
