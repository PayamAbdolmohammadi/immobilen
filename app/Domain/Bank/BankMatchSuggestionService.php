<?php

namespace App\Domain\Bank;

use App\Models\BankTransaction;
use App\Models\Rechnung;
use Illuminate\Support\Collection;

/**
 * Read-only heuristics for bank–invoice matching. Does not write to the database and never finalises a match;
 * the user confirms via {@see AllocateBankPayment} / Zuordnen.
 */
final class BankMatchSuggestionService
{
    /**
     * Suggested open invoices per transaction, highest score first (max 5 per row).
     *
     * @param  Collection<int, BankTransaction>  $transactions
     * @param  Collection<int, Rechnung>  $openInvoices  pre-filtered to mandant, must load `mieter` for name matching
     * @return array<int, list<array{rechnung: Rechnung, score: int, reasons: list<string>}>>
     */
    public function forTransactions(Collection $transactions, Collection $openInvoices): array
    {
        $out = [];

        foreach ($transactions as $tx) {
            $remaining = $tx->remainingPayableCent();
            if ($remaining <= 0) {
                $out[$tx->id] = [];

                continue;
            }

            $candidates = [];
            $haystack = mb_strtolower(trim(($tx->verwendungszweck ?? '').' '.($tx->gegenpartei ?? '')));

            foreach ($openInvoices as $inv) {
                $open = $inv->openAmountCent();
                if ($open <= 0) {
                    continue;
                }

                $score = 0;
                $reasons = [];

                if ($remaining === $open) {
                    $score += 100;
                    $reasons[] = 'exact_open_amount';
                }

                $tenant = mb_strtolower(trim($inv->mieter->name));
                if ($tenant !== '' && str_contains($haystack, $tenant)) {
                    $score += 50;
                    $reasons[] = 'tenant_in_text';
                }

                if (str_contains($haystack, (string) $inv->id) || str_contains($haystack, '#'.$inv->id)) {
                    $score += 40;
                    $reasons[] = 'invoice_id_in_text';
                }

                if ($score > 0) {
                    $candidates[] = [
                        'rechnung' => $inv,
                        'score' => $score,
                        'reasons' => $reasons,
                    ];
                }
            }

            usort($candidates, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);
            $out[$tx->id] = array_slice($candidates, 0, 5);
        }

        return $out;
    }
}
