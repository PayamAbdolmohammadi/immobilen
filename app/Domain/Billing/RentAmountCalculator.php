<?php

namespace App\Domain\Billing;

use App\Models\Mietvertrag;

final class RentAmountCalculator
{
    /**
     * @return array{total_cent: int, source_data: array{kaltmiete_cent: int, nebenkosten_vorauszahlung_cent: int, total_cent: int}}
     */
    public function forLease(Mietvertrag $lease): array
    {
        $kalt = (int) $lease->kaltmiete_cent;
        $nk = (int) $lease->nebenkosten_vorauszahlung_cent;
        $total = $kalt + $nk;

        return [
            'total_cent' => $total,
            'source_data' => [
                'kaltmiete_cent' => $kalt,
                'nebenkosten_vorauszahlung_cent' => $nk,
                'total_cent' => $total,
            ],
        ];
    }
}
