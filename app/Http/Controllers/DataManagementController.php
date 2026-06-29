<?php

namespace App\Http\Controllers;

use App\Jobs\ImportMembersJob;
use App\Services\AnalyticsService;
use App\Services\ExportService;
use App\Services\ImportService;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataManagementController extends Controller
{
    public function __construct(
        private readonly ImportService  $importService,
        private readonly ExportService  $exportService,
        private readonly AnalyticsService $analytics,
        private readonly SecurityLogger   $security,
    ) {}

    // -----------------------------------------------------------------------
    // Import wizard — step 1: upload
    // -----------------------------------------------------------------------

    public function importForm(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        return view('data.import-upload');
    }

    public function importUpload(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $validator = Validator::make($request->all(), [
            'csv_file' => [
                'required',
                'file',
                'max:10240',  // 10 MB
                'mimes:csv,txt',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if ($ext !== 'csv') {
                        $fail(__('data.error_csv_only'));
                    }
                    // Reject executables by MIME
                    $mimeType = $value->getMimeType();
                    $allowed  = ['text/plain', 'text/csv', 'application/csv', 'application/vnd.ms-excel'];
                    if (! in_array($mimeType, $allowed, true) && ! str_starts_with($mimeType, 'text/')) {
                        $fail(__('data.error_invalid_mime'));
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $file         = $request->file('csv_file');
        $tempPath     = $this->importService->storeTempFile($file, $merchant->id);
        $absolutePath = $this->importService->tempFilePath($tempPath);
        $parsed       = $this->importService->parseCsv($absolutePath);
        $detected     = $this->importService->detectMapping($parsed['headers']);

        $request->session()->put('import.temp_path',  $tempPath);
        $request->session()->put('import.headers',    $parsed['headers']);
        $request->session()->put('import.rows_count', count($parsed['rows']));

        $this->analytics->track('import_started', [
            'type'      => 'members',
            'row_count' => count($parsed['rows']),
        ], $request->user()->id, $merchant->id);

        $this->security->importAttempted($request->user()->id, $merchant->id, 'members', count($parsed['rows']));

        return view('data.import-mapping', [
            'headers'        => $parsed['headers'],
            'detectedMapping'=> $detected,
            'availableFields'=> $this->importService->availableFields(),
            'rowsCount'      => count($parsed['rows']),
        ]);
    }

    // -----------------------------------------------------------------------
    // Import wizard — step 2: validate & preview
    // -----------------------------------------------------------------------

    public function importPreview(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $tempPath = $request->session()->get('import.temp_path');
        $headers  = $request->session()->get('import.headers');

        if (! $tempPath || ! $headers) {
            return redirect()->route('data.import.form')
                ->withErrors([__('data.error_session_expired')]);
        }

        // Build mapping from POST data
        $mapping = [];
        foreach ($headers as $header) {
            $field = $request->input('mapping.' . base64_encode($header));
            if ($field && $field !== '__skip__') {
                $mapping[$header] = $field;
            }
        }

        $request->session()->put('import.mapping', $mapping);

        $absolutePath = $this->importService->tempFilePath($tempPath);
        $parsed       = $this->importService->parseCsv($absolutePath);
        $validation   = $this->importService->validate($parsed['rows'], $parsed['headers'], $mapping, $merchant);

        $request->session()->put('import.validation', [
            'total'      => $validation['total'],
            'valid'      => $validation['valid'],
            'invalid'    => $validation['invalid'],
            'duplicates' => $validation['duplicates'],
        ]);

        // Show first 20 valid rows as preview
        $previewRows = array_slice($validation['valid_rows'], 0, 20);

        $shouldQueue = $this->importService->shouldQueue($validation['valid']);

        return view('data.import-preview', [
            'validation'  => $validation,
            'previewRows' => $previewRows,
            'shouldQueue' => $shouldQueue,
            'mapping'     => $mapping,
        ]);
    }

    // -----------------------------------------------------------------------
    // Import wizard — step 3: execute
    // -----------------------------------------------------------------------

    public function importExecute(Request $request)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $tempPath   = $request->session()->get('import.temp_path');
        $headers    = $request->session()->get('import.headers');
        $mapping    = $request->session()->get('import.mapping', []);
        $validation = $request->session()->get('import.validation', []);

        if (! $tempPath || ! $headers) {
            return redirect()->route('data.import.form')
                ->withErrors([__('data.error_session_expired')]);
        }

        $absolutePath = $this->importService->tempFilePath($tempPath);
        $parsed       = $this->importService->parseCsv($absolutePath);
        $fullValidation = $this->importService->validate($parsed['rows'], $parsed['headers'], $mapping, $merchant);

        $shouldQueue  = $this->importService->shouldQueue($fullValidation['valid']);

        if ($shouldQueue) {
            ImportMembersJob::dispatch(
                $merchant->id,
                $request->user()->id,
                $tempPath,
                $headers,
                $mapping,
                $fullValidation['valid'],
            );

            $request->session()->forget(['import.temp_path', 'import.headers', 'import.mapping', 'import.validation', 'import.rows_count']);

            return view('data.import-result', [
                'queued'     => true,
                'rowsCount'  => $fullValidation['valid'],
                'result'     => null,
                'validation' => $fullValidation,
            ]);
        }

        $result = $this->importService->execute($fullValidation['valid_rows'], $merchant);

        $this->analytics->track('import_completed', [
            'type'       => 'members',
            'imported'   => $result['imported'],
            'failed'     => $result['failed'],
            'duplicates' => $fullValidation['duplicates'],
            'skipped'    => $fullValidation['invalid'],
        ], $request->user()->id, $merchant->id);

        $this->security->importCompleted(
            $request->user()->id,
            $merchant->id,
            'members',
            $result['imported'],
            $fullValidation['duplicates'],
            $result['failed'],
        );

        $this->importService->deleteTempFile($tempPath);
        $request->session()->forget(['import.temp_path', 'import.headers', 'import.mapping', 'import.validation', 'import.rows_count']);

        return view('data.import-result', [
            'queued'     => false,
            'rowsCount'  => null,
            'result'     => $result,
            'validation' => $fullValidation,
        ]);
    }

    // -----------------------------------------------------------------------
    // Exports
    // -----------------------------------------------------------------------

    public function export(Request $request, string $type)
    {
        $merchant = $request->user()->merchant;
        abort_unless($merchant, 403);

        $allowed = ['members', 'campaigns', 'rewards', 'purchases', 'redemptions'];
        abort_unless(in_array($type, $allowed, true), 404);

        $this->security->exportGenerated($request->user()->id, $merchant->id, $type);

        $this->analytics->track('export_generated', [
            'type' => $type,
        ], $request->user()->id, $merchant->id);

        return match ($type) {
            'members'     => $this->exportService->streamMembers($merchant),
            'campaigns'   => $this->exportService->streamCampaigns($merchant),
            'rewards'     => $this->exportService->streamRewards($merchant),
            'purchases'   => $this->exportService->streamPurchases($merchant),
            'redemptions' => $this->exportService->streamRedemptions($merchant),
        };
    }
}
