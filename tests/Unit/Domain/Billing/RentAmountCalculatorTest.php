<?php

namespace Tests\Unit\Domain\Billing;

use App\Domain\Billing\RentAmountCalculator;
use App\Models\Mietvertrag;
use PHPUnit\Framework\TestCase;

class RentAmountCalculatorTest extends TestCase
{
    public function test_source_data_and_total_use_integer_cents(): void
    {
        $lease = new Mietvertrag([
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
        ]);

        $calc = new RentAmountCalculator;
        $out = $calc->forLease($lease);

        $this->assertSame(100_000, $out['total_cent']);
        $this->assertSame([
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'total_cent' => 100_000,
        ], $out['source_data']);
    }
}
