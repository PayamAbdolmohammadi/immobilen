<?php

namespace App\Domain\Leasing;

use App\Models\Mietvertrag;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class MietvertragService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createLease(array $attributes): Mietvertrag
    {
        $einheitId = $attributes['einheit_id'] ?? null;
        if ($einheitId === null) {
            throw new \InvalidArgumentException('einheit_id ist erforderlich.');
        }

        $exists = Mietvertrag::query()
            ->where('einheit_id', $einheitId)
            ->whereIn('status', [
                Mietvertrag::STATUS_ACTIVE,
                Mietvertrag::STATUS_DRAFT,
                Mietvertrag::STATUS_SENT,
            ])
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException('Einheit hat bereits einen Mietvertrag in Bearbeitung oder aktiv.');
        }

        /** @var Mietvertrag $lease */
        $lease = Mietvertrag::query()->create($attributes);

        return $lease;
    }

    /**
     * Ends an active lease. Open rent invoices stay open until settled or adjusted elsewhere (no automatic write‑off).
     */
    public function endContract(Mietvertrag $lease, \DateTimeInterface $endedOn): void
    {
        DB::transaction(function () use ($lease, $endedOn): void {
            /** @var Mietvertrag $locked */
            $locked = Mietvertrag::query()->whereKey($lease->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== Mietvertrag::STATUS_ACTIVE) {
                throw new \InvalidArgumentException('Only active leases can be ended.');
            }

            $end = $this->toCarbonDay($endedOn);
            $startDay = Carbon::parse((string) $locked->starts_on)->startOfDay();
            if ($end->lt($startDay)) {
                throw new \InvalidArgumentException('End date cannot be before lease start.');
            }

            $locked->update([
                'status' => Mietvertrag::STATUS_ENDED,
                'ends_on' => $end->toDateString(),
            ]);
        });
    }

    /**
     * Updates amounts and operational fields on an active lease. Changes affect future billing snapshots only;
     * existing {@see \App\Models\Rechnung} rows remain immutable.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function updateContract(Mietvertrag $lease, array $attributes): void
    {
        $allowed = ['kaltmiete_cent', 'nebenkosten_vorauszahlung_cent', 'faelligkeit_tag', 'zahlungsintervall', 'ends_on'];
        $payload = array_intersect_key($attributes, array_flip($allowed));

        if ($payload === []) {
            return;
        }

        DB::transaction(function () use ($lease, $payload): void {
            /** @var Mietvertrag $locked */
            $locked = Mietvertrag::query()->whereKey($lease->id)->lockForUpdate()->firstOrFail();

            if (! in_array($locked->status, [
                Mietvertrag::STATUS_ACTIVE,
                Mietvertrag::STATUS_DRAFT,
                Mietvertrag::STATUS_SENT,
            ], true)) {
                throw new \InvalidArgumentException('Der Vertrag kann in diesem Status nicht bearbeitet werden.');
            }

            if (isset($payload['kaltmiete_cent'])) {
                $v = (int) $payload['kaltmiete_cent'];
                if ($v < 0) {
                    throw new \InvalidArgumentException('kaltmiete_cent must be non-negative.');
                }
                $payload['kaltmiete_cent'] = $v;
            }

            if (isset($payload['nebenkosten_vorauszahlung_cent'])) {
                $v = (int) $payload['nebenkosten_vorauszahlung_cent'];
                if ($v < 0) {
                    throw new \InvalidArgumentException('nebenkosten_vorauszahlung_cent must be non-negative.');
                }
                $payload['nebenkosten_vorauszahlung_cent'] = $v;
            }

            if (array_key_exists('ends_on', $payload) && $payload['ends_on'] !== null) {
                $end = Carbon::parse((string) $payload['ends_on'])->startOfDay();
                $startDay = Carbon::parse((string) $locked->starts_on)->startOfDay();
                if ($end->lt($startDay)) {
                    throw new \InvalidArgumentException('ends_on cannot be before starts_on.');
                }
                $payload['ends_on'] = $end->toDateString();
            }

            $locked->fill($payload);
            $locked->save();
        });
    }

    private function toCarbonDay(\DateTimeInterface $value): CarbonInterface
    {
        if ($value instanceof CarbonInterface) {
            return $value->copy()->startOfDay();
        }

        return Carbon::instance(\DateTimeImmutable::createFromInterface($value))->startOfDay();
    }
}
