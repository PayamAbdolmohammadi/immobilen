<?php

namespace App\Providers;

use App\Models\BankTransaction;
use App\Models\Einheit;
use App\Models\Mietvertrag;
use App\Models\Mieter;
use App\Models\NkAbrechnung;
use App\Models\Objekt;
use App\Models\Rechnung;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('manual_rechnung', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return Rechnung::query()
                ->where('mandant_id', $user->mandant_id)
                ->where('typ', Rechnung::TYP_MANUAL)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('bank_transaction', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return BankTransaction::query()
                ->where('mandant_id', $user->mandant_id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('rechnung', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return Rechnung::query()
                ->where('mandant_id', $user->mandant_id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('mieter', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return Mieter::query()
                ->where('mandant_id', $user->mandant_id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('objekt', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return Objekt::query()
                ->where('mandant_id', $user->mandant_id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('einheit', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return Einheit::query()
                ->where('mandant_id', $user->mandant_id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('mietvertrag', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return Mietvertrag::query()
                ->where('mandant_id', $user->mandant_id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('nk_abrechnung', function (string $value) {
            $user = auth()->user();
            abort_unless($user?->mandant_id, 403);

            return NkAbrechnung::query()
                ->where('mandant_id', $user->mandant_id)
                ->whereKey($value)
                ->firstOrFail();
        });
    }
}
