<?php

namespace App\Console\Commands;

use App\Domain\Billing\GenerateMonthlyRentInvoices;
use Illuminate\Console\Command;
use InvalidArgumentException;

class RentsGenerateMonthlyCommand extends Command
{
    protected $signature = 'rents:generate-monthly
                            {--period= : Billing period YYYY-MM (default: current calendar month in app timezone)}
                            {--mandant= : Only leases for this mandant ID}';

    protected $description = 'Generate monthly rent invoices (Snapshot per lease via GenerateMonthlyRentInvoices).';

    public function handle(GenerateMonthlyRentInvoices $service): int
    {
        $tz = config('app.timezone');
        $period = $this->option('period') ?: now()->timezone((string) $tz)->format('Y-m');
        $mandantOpt = $this->option('mandant');

        $mandantId = $mandantOpt !== null && $mandantOpt !== ''
            ? (int) $mandantOpt
            : null;

        try {
            $created = $service->run($period, $mandantId);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf('Period %s: %d rent invoice(s) created.', $period, $created));

        return self::SUCCESS;
    }
}
