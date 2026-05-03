<?php

use App\Http\Controllers\AccountingExportController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ContractPublicController;
use App\Http\Controllers\DemoFlowController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\EinheitController;
use App\Http\Controllers\MahnungController;
use App\Http\Controllers\MandantSetupController;
use App\Http\Controllers\ManualRechnungController;
use App\Http\Controllers\MietvertragController;
use App\Http\Controllers\MieterController;
use App\Http\Controllers\NkAbrechnungController;
use App\Http\Controllers\ObjektController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RechnungStornoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('login', [PortalAuthController::class, 'create'])->name('login');
    Route::post('login', [PortalAuthController::class, 'store']);
    Route::post('logout', [PortalAuthController::class, 'destroy'])->middleware('auth:mieter')->name('logout');
    Route::get('/', PortalDashboardController::class)->middleware('auth:mieter')->name('dashboard');
});

Route::middleware('throttle:120,1')->group(function (): void {
    Route::get('/contracts/{token}', [ContractPublicController::class, 'show'])
        ->where('token', '[a-fA-F0-9]{48}')
        ->name('contracts.public.show');
    Route::get('/contracts/{token}/pdf', [ContractPublicController::class, 'pdf'])
        ->where('token', '[a-fA-F0-9]{48}')
        ->name('contracts.public.pdf');
});

Route::post('/contracts/{token}/accept', [ContractPublicController::class, 'accept'])
    ->middleware('throttle:30,1')
    ->where('token', '[a-fA-F0-9]{48}')
    ->name('contracts.public.accept');

Route::middleware('auth')->group(function () {
    Route::get('/mandant/einrichten', [MandantSetupController::class, 'create'])
        ->name('mandant.setup');
    Route::post('/mandant/einrichten', [MandantSetupController::class, 'store'])
        ->name('mandant.setup.store');
});

Route::middleware(['auth', 'verified', 'mandant'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('mieter', MieterController::class);

    Route::resource('objekte', ObjektController::class)->parameters(['objekte' => 'objekt']);

    Route::resource('einheiten', EinheitController::class)->parameters(['einheiten' => 'einheit']);

    Route::resource('mietvertraege', MietvertragController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
        ->parameters(['mietvertraege' => 'mietvertrag']);

    Route::post('/mietvertraege/{mietvertrag}/contract/pdf', [MietvertragController::class, 'generatePdf'])
        ->name('mietvertraege.contract.pdf');
    Route::post('/mietvertraege/{mietvertrag}/contract/link', [MietvertragController::class, 'createContractLink'])
        ->name('mietvertraege.contract.link');
    Route::post('/mietvertraege/{mietvertrag}/contract/activate', [MietvertragController::class, 'activateContract'])
        ->name('mietvertraege.contract.activate');

    Route::resource('nk-abrechnungen', NkAbrechnungController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->parameters(['nk-abrechnungen' => 'nk_abrechnung']);

    Route::get('/nk-abrechnungen/{nk_abrechnung}/pdf', [NkAbrechnungController::class, 'pdf'])->name('nk-abrechnungen.pdf');

    Route::get('/mietvertraege/{mietvertrag}/beenden', [MietvertragController::class, 'end'])->name('mietvertraege.end');
    Route::post('/mietvertraege/{mietvertrag}/beenden', [MietvertragController::class, 'endStore'])->name('mietvertraege.end.store');

    Route::post('/rechnungen/{rechnung}/mahnung', [MahnungController::class, 'store'])->name('rechnungen.mahnung');

    Route::post('/rechnungen/{rechnung}/storno', [RechnungStornoController::class, 'store'])->name('rechnungen.storno');

    Route::get('/rechnungen/manual', [ManualRechnungController::class, 'index'])->name('manual-rechnungen.index');
    Route::get('/rechnungen/manual/create', [ManualRechnungController::class, 'create'])->name('manual-rechnungen.create');
    Route::post('/rechnungen/manual', [ManualRechnungController::class, 'store'])->name('manual-rechnungen.store');
    Route::get('/rechnungen/manual/{manual_rechnung}/edit', [ManualRechnungController::class, 'edit'])->name('manual-rechnungen.edit');
    Route::patch('/rechnungen/manual/{manual_rechnung}', [ManualRechnungController::class, 'update'])->name('manual-rechnungen.update');
    Route::delete('/rechnungen/manual/{manual_rechnung}', [ManualRechnungController::class, 'destroy'])->name('manual-rechnungen.destroy');

    Route::get('/bank', [BankController::class, 'index'])->name('bank.index');
    Route::get('/bank/matching', [BankController::class, 'matching'])->name('bank.matching');
    Route::post('/bank/import', [BankController::class, 'import'])->name('bank.import');
    Route::post('/bank/transactions/{bank_transaction}/zuordnen', [BankController::class, 'allocate'])->name('bank.allocate');

    Route::middleware('owner')->prefix('export/accounting')->name('export.accounting.')->group(function () {
        Route::get('/', [AccountingExportController::class, 'index'])->name('index');
        Route::post('/simple-csv', [AccountingExportController::class, 'exportSimpleCsv'])->name('simple-csv');
        Route::post('/datev-csv', [AccountingExportController::class, 'exportDatevCsv'])->name('datev-csv');
    });

    Route::middleware('owner')->prefix('demo-flow')->name('demo-flow.')->group(function () {
        Route::get('/', [DemoFlowController::class, 'index'])->name('index');
        Route::post('/start-auto', [DemoFlowController::class, 'startAutoDemo'])->name('start-auto');
        Route::post('/generate-rent', [DemoFlowController::class, 'generateRent'])->name('generate-rent');
        Route::get('/demo-bank-csv', [DemoFlowController::class, 'downloadDemoBankCsv'])->name('demo-bank-csv');
        Route::post('/seed-bank-line', [DemoFlowController::class, 'seedBankLine'])->name('seed-bank-line');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
