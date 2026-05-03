<?php

namespace App\Jobs;

use App\Domain\Bank\BankCsvImporter;
use App\Models\BankImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class ProcessBankCsvImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $relativeStoragePath,
        public int $mandantId,
        public string $originalFilename,
        public ?string $csvProfile,
    ) {}

    public function handle(BankCsvImporter $importer): void
    {
        $disk = Storage::disk('local');

        if (! $disk->exists($this->relativeStoragePath)) {
            BankImport::query()->create([
                'mandant_id' => $this->mandantId,
                'original_filename' => $this->originalFilename,
                'csv_profile' => $this->csvProfile,
                'row_count' => 0,
                'status' => BankImport::STATUS_FAILED,
                'error_message' => 'Upload file was missing on the server.',
            ]);

            return;
        }

        $raw = $disk->get($this->relativeStoragePath);

        try {
            $importer->importFromContents($raw, $this->mandantId, $this->originalFilename, $this->csvProfile);
        } catch (Throwable $e) {
            BankImport::query()->create([
                'mandant_id' => $this->mandantId,
                'original_filename' => $this->originalFilename,
                'csv_profile' => $this->csvProfile,
                'row_count' => 0,
                'status' => BankImport::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            Log::warning('Bank CSV import failed', [
                'mandant_id' => $this->mandantId,
                'file' => $this->originalFilename,
                'exception' => $e->getMessage(),
            ]);
        } finally {
            $disk->delete($this->relativeStoragePath);
        }
    }
}
