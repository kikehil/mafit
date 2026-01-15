<?php

namespace App\Jobs;

use App\Models\MafImportBatch;
use App\Services\MafImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMafImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public MafImportBatch $batch,
        public string $filePath
    ) {
    }

    public function handle(MafImportService $service): void
    {
        try {
            $service->importBatch($this->batch, $this->filePath);
        } catch (\Exception $e) {
            Log::error('Error procesando importación MAF', [
                'batch_id' => $this->batch->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}











