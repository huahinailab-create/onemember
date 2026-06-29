<?php

namespace App\Jobs;

use App\Models\Merchant;
use App\Services\AnalyticsService;
use App\Services\ImportService;
use App\Services\SecurityLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ImportMembersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 600; // 10 minutes

    public function __construct(
        private readonly int    $merchantId,
        private readonly int    $userId,
        private readonly string $tempFilePath,
        private readonly array  $headers,
        private readonly array  $mapping,
        private readonly int    $validRowCount,
    ) {}

    public function handle(ImportService $importService, AnalyticsService $analytics, SecurityLogger $security): void
    {
        $merchant = Merchant::find($this->merchantId);
        if (! $merchant) {
            return;
        }

        try {
            $absolutePath = $importService->tempFilePath($this->tempFilePath);
            $parsed       = $importService->parseCsv($absolutePath);
            $validation   = $importService->validate($parsed['rows'], $parsed['headers'], $this->mapping, $merchant);
            $result       = $importService->execute($validation['valid_rows'], $merchant);

            $analytics->track('import_completed', [
                'type'        => 'members',
                'imported'    => $result['imported'],
                'failed'      => $result['failed'],
                'duplicates'  => $validation['duplicates'],
                'queued'      => true,
            ], $this->userId, $this->merchantId);

            $security->importCompleted(
                $this->userId,
                $this->merchantId,
                'members',
                $result['imported'],
                $validation['duplicates'],
                $result['failed'],
            );
        } catch (Throwable $e) {
            $analytics->track('import_failed', [
                'type'  => 'members',
                'error' => $e->getMessage(),
            ], $this->userId, $this->merchantId);

            $security->importFailed($this->userId, $this->merchantId, 'members', $e->getMessage());

            throw $e;
        } finally {
            $importService->deleteTempFile($this->tempFilePath);
        }
    }
}
