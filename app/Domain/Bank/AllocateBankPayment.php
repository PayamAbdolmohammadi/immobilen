<?php

namespace App\Domain\Bank;

use App\Models\BankTransaction;
use App\Models\Rechnung;
use App\Models\Zahlungszuordnung;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Persists a user-confirmed allocation only. There is no automatic matching or booking in this class.
 */
final class AllocateBankPayment
{
    public function assign(BankTransaction $transaction, Rechnung $rechnung, int $amountCent): Zahlungszuordnung
    {
        if ($amountCent <= 0) {
            throw new InvalidArgumentException('Amount must be positive.');
        }

        if ($transaction->mandant_id !== $rechnung->mandant_id) {
            throw new InvalidArgumentException('Mandant mismatch.');
        }

        if ($rechnung->status === Rechnung::STATUS_STORNIERT) {
            throw new InvalidArgumentException('Invoice was cancelled (Storno).');
        }

        $txnRemaining = $transaction->remainingPayableCent();
        $invoiceOpen = $rechnung->openAmountCent();

        if ($txnRemaining === 0) {
            throw new InvalidArgumentException('No remaining amount on this bank transaction.');
        }

        if ($invoiceOpen === 0) {
            throw new InvalidArgumentException('Invoice is already fully allocated.');
        }

        $apply = min($amountCent, $txnRemaining, $invoiceOpen);
        if ($apply <= 0) {
            throw new InvalidArgumentException('Nothing to allocate.');
        }

        return DB::transaction(function () use ($transaction, $rechnung, $apply): Zahlungszuordnung {
            $zuordnung = Zahlungszuordnung::query()->create([
                'mandant_id' => $transaction->mandant_id,
                'bank_transaction_id' => $transaction->id,
                'rechnung_id' => $rechnung->id,
                'betrag_cent' => $apply,
            ]);

            $transaction->refresh();
            $rechnung->refresh();

            if ($transaction->remainingPayableCent() === 0) {
                $transaction->update(['status' => BankTransaction::STATUS_ZUGEORDNET]);
            }

            if ($rechnung->openAmountCent() === 0 && $rechnung->status !== Rechnung::STATUS_BEZAHLT) {
                $rechnung->update([
                    'status' => Rechnung::STATUS_BEZAHLT,
                    'bezahlt_am' => now(),
                ]);
            }

            return $zuordnung;
        });
    }
}
