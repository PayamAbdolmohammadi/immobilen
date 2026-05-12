<?php

namespace Tests\Feature\Bank;

use App\Domain\Bank\BankCsvImporter;
use App\Jobs\ProcessBankCsvImportJob;
use App\Models\BankImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ProcessBankCsvImportJobFailureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function missing_csv_file_creates_failed_import_for_correct_mandant(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $job = new ProcessBankCsvImportJob(
            relativeStoragePath: 'bank-upload-temp/missing.csv',
            mandantId: (int) $user->mandant_id,
            originalFilename: 'missing.csv',
            csvProfile: null,
        );

        $marker = 'SECRET_MARKER_SHOULD_NOT_APPEAR';
        $job->handle(new BankCsvImporter);

        $this->assertDatabaseHas('bank_imports', [
            'mandant_id' => $user->mandant_id,
            'original_filename' => 'missing.csv',
            'status' => BankImport::STATUS_FAILED,
            'row_count' => 0,
            'error_message' => 'Upload file was missing on the server.',
        ]);

        $import = BankImport::query()->where('mandant_id', $user->mandant_id)->latest('id')->first();
        $this->assertNotNull($import);
        $this->assertIsString($import->error_message);
        $this->assertStringNotContainsString($marker, $import->error_message);
    }

    #[Test]
    public function importer_failure_does_not_leak_raw_csv_and_deletes_temp_file(): void
    {
        Storage::fake('local');
        Log::spy();

        $user = User::factory()->create();
        $relativePath = 'bank-upload-temp/bad.csv';
        $secret = 'SECRET_MARKER_12345';

        // Invalid header (no booking date / amount) but includes secret marker.
        Storage::disk('local')->put($relativePath, "NOT_A_REAL_HEADER;{$secret}\n");

        $job = new ProcessBankCsvImportJob(
            relativeStoragePath: $relativePath,
            mandantId: (int) $user->mandant_id,
            originalFilename: 'bad.csv',
            csvProfile: null,
        );

        $job->handle(new BankCsvImporter);

        $this->assertDatabaseHas('bank_imports', [
            'mandant_id' => $user->mandant_id,
            'original_filename' => 'bad.csv',
            'status' => BankImport::STATUS_FAILED,
            'row_count' => 0,
        ]);

        $import = BankImport::query()->where('mandant_id', $user->mandant_id)->latest('id')->firstOrFail();
        $this->assertIsString($import->error_message);
        $this->assertStringNotContainsString($secret, $import->error_message);

        Log::shouldHaveReceived('warning')
            ->atLeast()
            ->once()
            ->withArgs(function (string $message, array $context) use ($secret, $user): bool {
                $this->assertSame('Bank CSV import failed', $message);
                $this->assertSame($user->mandant_id, $context['mandant_id'] ?? null);

                return ! str_contains(json_encode($context) ?: '', $secret);
            });

        Storage::disk('local')->assertMissing($relativePath);
    }
}

