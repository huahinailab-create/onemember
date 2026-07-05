<?php

namespace App\Services;

use App\Jobs\ImportMembersJob;
use App\Models\Member;
use App\Models\Merchant;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImportService
{
    const MAX_SYNC_ROWS  = 5000;
    const MAX_FILE_BYTES = 10 * 1024 * 1024; // 10 MB

    // Maps normalised CSV header → OneMember member field
    const FIELD_ALIASES = [
        'first_name'    => ['first name', 'firstname', 'first_name', 'given name', 'name', 'ชื่อ', 'ชื่อจริง'],
        'last_name'     => ['last name', 'lastname', 'last_name', 'surname', 'family name', 'นามสกุล'],
        'phone'         => ['phone', 'mobile', 'telephone', 'tel', 'contact', 'phone number', 'mobile number',
                            'เบอร์โทร', 'เบอร์', 'โทรศัพท์'],
        'email'         => ['email', 'e-mail', 'email address', 'mail', 'อีเมล', 'อีเมล์'],
        'date_of_birth' => ['dob', 'birthday', 'date of birth', 'birth date', 'birthdate', 'birth_date',
                            'วันเกิด', 'วันเดือนปีเกิด'],
        'gender'        => ['gender', 'sex', 'เพศ'],
        'notes'         => ['notes', 'note', 'comment', 'remarks', 'remark', 'memo', 'หมายเหตุ', 'โน้ต'],
        'nickname'      => ['nickname', 'nick', 'nick name', 'ชื่อเล่น'],
        'postal_code'   => ['postal code', 'postal_code', 'zip', 'zip code', 'zipcode', 'postcode', 'post code', 'รหัสไปรษณีย์'],
        'tags'          => ['tags', 'tag', 'labels', 'label', 'แท็ก'],
    ];

    // DB-writable fields (gender/tags are accepted but not stored)
    const WRITABLE_FIELDS = ['first_name', 'last_name', 'phone', 'email', 'date_of_birth', 'notes', 'nickname', 'postal_code'];

    // -----------------------------------------------------------------------
    // File storage
    // -----------------------------------------------------------------------

    public function storeTempFile(UploadedFile $file, int $merchantId): string
    {
        $dir      = "import-temp/{$merchantId}";
        $filename = Str::uuid() . '.csv';

        Storage::disk('local')->makeDirectory($dir);
        Storage::disk('local')->put("{$dir}/{$filename}", $file->get());

        return "{$dir}/{$filename}";
    }

    public function deleteTempFile(string $relativePath): void
    {
        Storage::disk('local')->delete($relativePath);
    }

    public function tempFilePath(string $relativePath): string
    {
        return Storage::disk('local')->path($relativePath);
    }

    // -----------------------------------------------------------------------
    // CSV parsing
    // -----------------------------------------------------------------------

    public function parseCsv(string $absolutePath): array
    {
        $handle = @fopen($absolutePath, 'r');
        if (! $handle) {
            throw new \RuntimeException('Cannot open CSV file.');
        }

        // Strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rows    = [];
        $headers = null;
        $lineNum = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $lineNum++;

            // Try semicolon if first row has only one column
            if ($lineNum === 1 && count($row) === 1 && str_contains($row[0], ';')) {
                rewind($handle);
                $bom2 = fread($handle, 3);
                if ($bom2 !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                $handle2 = $handle;
                $rows    = [];
                $headers = null;
                $lineNum = 0;
                while (($row2 = fgetcsv($handle2, 0, ';')) !== false) {
                    $lineNum++;
                    if ($lineNum === 1) {
                        $headers = array_map(fn ($h) => trim($h), $row2);
                        continue;
                    }
                    $rows[] = $row2;
                }
                fclose($handle);
                return ['headers' => $headers ?? [], 'rows' => $rows];
            }

            if ($lineNum === 1) {
                $headers = array_map(fn ($h) => trim($h), $row);
                continue;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return ['headers' => $headers ?? [], 'rows' => $rows];
    }

    // -----------------------------------------------------------------------
    // Column mapping auto-detection
    // -----------------------------------------------------------------------

    public function detectMapping(array $headers): array
    {
        $mapping = [];

        foreach ($headers as $csvHeader) {
            $normalised = strtolower(trim($csvHeader));

            foreach (self::FIELD_ALIASES as $field => $aliases) {
                if (in_array($normalised, $aliases, true)) {
                    $mapping[$csvHeader] = $field;
                    break;
                }
            }
        }

        return $mapping;
    }

    public function availableFields(): array
    {
        return array_keys(self::FIELD_ALIASES);
    }

    // -----------------------------------------------------------------------
    // Validation
    // -----------------------------------------------------------------------

    public function validate(array $rows, array $headers, array $mapping, Merchant $merchant): array
    {
        $valid       = [];
        $errors      = [];
        $warnings    = [];
        $duplicates  = 0;
        $totalRows   = count($rows);

        // Pre-load existing phones and emails for this merchant (for duplicate detection)
        $existingPhones = Member::where('merchant_id', $merchant->id)
            ->whereNotNull('phone')
            ->pluck('phone')
            ->map(fn ($p) => $this->normalisePhone($p))
            ->flip()
            ->toArray();

        $existingEmails = Member::where('merchant_id', $merchant->id)
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn ($e) => strtolower(trim($e)))
            ->flip()
            ->toArray();

        // Track phones/emails seen in this CSV to catch intra-CSV duplicates
        $seenPhones = [];
        $seenEmails = [];

        foreach ($rows as $index => $row) {
            $rowNum  = $index + 2; // 1-indexed, +1 for header
            $rowData = $this->mapRow($row, $headers, $mapping);
            $rowErrors = [];

            // Required: first_name → name
            $firstName = trim($rowData['first_name'] ?? '');
            $lastName  = trim($rowData['last_name'] ?? '');
            $name      = trim("$firstName $lastName");

            if (empty($firstName)) {
                $rowErrors[] = __('data.error_first_name_required');
            }

            // Required: phone
            $phone = $this->normalisePhone($rowData['phone'] ?? '');
            if (empty($phone)) {
                $rowErrors[] = __('data.error_phone_required');
            } elseif (! $this->isValidPhone($phone)) {
                $rowErrors[] = __('data.error_phone_invalid');
            }

            // Optional: email
            $email = strtolower(trim($rowData['email'] ?? ''));
            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $rowErrors[] = __('data.error_email_invalid');
            }

            // Optional: date of birth
            $birthday = null;
            $dobRaw   = trim($rowData['date_of_birth'] ?? '');
            if ($dobRaw !== '') {
                $birthday = $this->parseDate($dobRaw);
                if ($birthday === null) {
                    $warnings[] = ['row' => $rowNum, 'message' => __('data.warning_dob_skipped', ['row' => $rowNum])];
                }
            }

            // Row length mismatch warning
            if (count($row) !== count($headers)) {
                $warnings[] = ['row' => $rowNum, 'message' => __('data.warning_column_mismatch', ['row' => $rowNum])];
            }

            if (! empty($rowErrors)) {
                $errors[] = ['row' => $rowNum, 'messages' => $rowErrors, 'data' => $row];
                continue;
            }

            // Duplicate detection — existing records
            $isDuplicate = false;
            if ($phone && isset($existingPhones[$phone])) {
                $duplicates++;
                $errors[] = ['row' => $rowNum, 'messages' => [__('data.error_duplicate_phone')], 'data' => $row, 'duplicate' => true];
                $isDuplicate = true;
            } elseif ($email && isset($existingEmails[$email])) {
                $duplicates++;
                $errors[] = ['row' => $rowNum, 'messages' => [__('data.error_duplicate_email')], 'data' => $row, 'duplicate' => true];
                $isDuplicate = true;
            }

            // Duplicate detection — intra-CSV
            if (! $isDuplicate && $phone && isset($seenPhones[$phone])) {
                $duplicates++;
                $errors[] = ['row' => $rowNum, 'messages' => [__('data.error_csv_duplicate_phone')], 'data' => $row, 'duplicate' => true];
                $isDuplicate = true;
            } elseif (! $isDuplicate && $email && isset($seenEmails[$email])) {
                $duplicates++;
                $errors[] = ['row' => $rowNum, 'messages' => [__('data.error_csv_duplicate_email')], 'data' => $row, 'duplicate' => true];
                $isDuplicate = true;
            }

            if ($isDuplicate) {
                continue;
            }

            if ($phone) {
                $seenPhones[$phone] = true;
            }
            if ($email) {
                $seenEmails[$email] = true;
            }

            $valid[] = [
                'row'         => $rowNum,
                'name'        => $name,
                'phone'       => $phone,
                'email'       => $email ?: null,
                'birthday'    => $birthday,
                'notes'       => trim($rowData['notes'] ?? '') ?: null,
                'nickname'    => trim($rowData['nickname'] ?? '') ?: null,
                'postal_code' => trim($rowData['postal_code'] ?? '') ?: null,
            ];
        }

        return [
            'total'      => $totalRows,
            'valid'      => count($valid),
            'invalid'    => count($errors) - $duplicates,
            'duplicates' => $duplicates,
            'errors'     => $errors,
            'warnings'   => $warnings,
            'valid_rows' => $valid,
        ];
    }

    // -----------------------------------------------------------------------
    // Import execution
    // -----------------------------------------------------------------------

    public function execute(array $validRows, Merchant $merchant): array
    {
        $imported = 0;
        $failed   = 0;
        $failedRows = [];
        $startTime  = microtime(true);

        foreach ($validRows as $rowData) {
            try {
                DB::transaction(function () use ($rowData, $merchant) {
                    Member::create([
                        'merchant_id' => $merchant->id,
                        'name'        => $rowData['name'],
                        'phone'       => $rowData['phone'] ?: null,
                        'email'       => $rowData['email'] ?: null,
                        'birthday'    => $rowData['birthday'],
                        'notes'       => $rowData['notes'],
                        'nickname'    => $rowData['nickname'],
                        'postal_code' => $rowData['postal_code'] ?? null,
                        'status'      => \App\Enums\MemberStatus::Active,
                    ]);
                });
                $imported++;
            } catch (Throwable $e) {
                $failed++;
                $failedRows[] = ['row' => $rowData['row'], 'error' => $e->getMessage()];
                Log::warning('ImportService: row failed', [
                    'merchant_id' => $merchant->id,
                    'row'         => $rowData['row'],
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        return [
            'imported'    => $imported,
            'failed'      => $failed,
            'failed_rows' => $failedRows,
            'time_ms'     => (int) round((microtime(true) - $startTime) * 1000),
        ];
    }

    public function shouldQueue(int $rowCount): bool
    {
        return $rowCount > self::MAX_SYNC_ROWS;
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function mapRow(array $row, array $headers, array $mapping): array
    {
        $data = [];
        foreach ($headers as $idx => $header) {
            if (isset($mapping[$header])) {
                $data[$mapping[$header]] = $row[$idx] ?? '';
            }
        }
        return $data;
    }

    private function normalisePhone(string $phone): string
    {
        // Strip spaces, dashes, dots, parentheses
        return preg_replace('/[\s\-\.\(\)]+/', '', $phone);
    }

    private function isValidPhone(string $phone): bool
    {
        // Allow +prefix, digits; min 7 chars, max 20
        return (bool) preg_match('/^\+?[0-9]{7,20}$/', $phone);
    }

    private function parseDate(string $raw): ?string
    {
        $formats = [
            'Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'd.m.Y',
            'Y/m/d', 'd M Y', 'd F Y', 'j/n/Y', 'j-n-Y',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $raw);
                if ($date && $date->format($format) === $raw) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                continue;
            }
        }

        // Try Carbon's natural parse as fallback
        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
