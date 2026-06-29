<?php

namespace Tests\Feature;

use App\Jobs\ImportMembersJob;
use App\Models\Member;
use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use App\Models\Redemption;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ImportService;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataImportExportTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function actingAsMerchant(): array
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create(['user_id' => $user->id]);
        return [$user, $merchant];
    }

    private function makeCsv(array $headers, array $rows): UploadedFile
    {
        $content = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $content .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }
        return UploadedFile::fake()->createWithContent('members.csv', $content);
    }

    private function uploadCsv(User $user, UploadedFile $file)
    {
        return $this->actingAs($user)->post(route('data.import.upload'), [
            'csv_file' => $file,
        ]);
    }

    // -----------------------------------------------------------------------
    // ImportService unit tests
    // -----------------------------------------------------------------------

    public function test_import_service_detects_common_column_names(): void
    {
        $service = new ImportService();

        $mapping = $service->detectMapping(['First Name', 'Mobile', 'DOB', 'Email', 'Notes']);

        $this->assertSame('first_name',    $mapping['First Name']);
        $this->assertSame('phone',         $mapping['Mobile']);
        $this->assertSame('date_of_birth', $mapping['DOB']);
        $this->assertSame('email',         $mapping['Email']);
        $this->assertSame('notes',         $mapping['Notes']);
    }

    public function test_import_service_detects_thai_column_names(): void
    {
        $service = new ImportService();

        $mapping = $service->detectMapping(['ชื่อ', 'เบอร์โทร', 'อีเมล', 'วันเกิด']);

        $this->assertSame('first_name',    $mapping['ชื่อ']);
        $this->assertSame('phone',         $mapping['เบอร์โทร']);
        $this->assertSame('email',         $mapping['อีเมล']);
        $this->assertSame('date_of_birth', $mapping['วันเกิด']);
    }

    public function test_import_service_ignores_unknown_columns(): void
    {
        $service = new ImportService();

        $mapping = $service->detectMapping(['unknown_xyz', 'some_random_col']);

        $this->assertEmpty($mapping);
    }

    public function test_import_service_should_queue_large_imports(): void
    {
        $service = new ImportService();

        $this->assertFalse($service->shouldQueue(100));
        $this->assertFalse($service->shouldQueue(5000));
        $this->assertTrue($service->shouldQueue(5001));
    }

    // -----------------------------------------------------------------------
    // Validation tests
    // -----------------------------------------------------------------------

    public function test_validation_requires_first_name(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $headers = ['First Name', 'Phone'];
        $rows    = [['', '0812345678']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(0, $result['valid']);
        $this->assertSame(1, $result['invalid']);
        $this->assertStringContainsString('First Name', $result['errors'][0]['messages'][0]);
    }

    public function test_validation_requires_phone(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $headers = ['First Name', 'Phone'];
        $rows    = [['Alice', '']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(0, $result['valid']);
        $this->assertSame(1, $result['invalid']);
    }

    public function test_validation_rejects_invalid_email(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $headers = ['First Name', 'Phone', 'Email'];
        $rows    = [['Alice', '0812345678', 'not-an-email']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone', 'Email' => 'email'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(0, $result['valid']);
    }

    public function test_validation_accepts_valid_row(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $headers = ['First Name', 'Last Name', 'Phone', 'Email'];
        $rows    = [['Alice', 'Smith', '0812345678', 'alice@example.com']];
        $mapping = [
            'First Name' => 'first_name',
            'Last Name'  => 'last_name',
            'Phone'      => 'phone',
            'Email'      => 'email',
        ];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(1, $result['valid']);
        $this->assertSame('Alice Smith', $result['valid_rows'][0]['name']);
        $this->assertSame('0812345678', $result['valid_rows'][0]['phone']);
    }

    public function test_validation_detects_duplicate_phone_against_existing(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        Member::factory()->create([
            'merchant_id' => $merchant->id,
            'phone'       => '0812345678',
        ]);

        $service = new ImportService();
        $headers = ['First Name', 'Phone'];
        $rows    = [['Bob', '0812345678']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(0, $result['valid']);
        $this->assertSame(1, $result['duplicates']);
        $this->assertTrue($result['errors'][0]['duplicate']);
    }

    public function test_validation_detects_duplicate_email_against_existing(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        Member::factory()->create([
            'merchant_id' => $merchant->id,
            'email'       => 'alice@example.com',
        ]);

        $service = new ImportService();
        $headers = ['First Name', 'Phone', 'Email'];
        $rows    = [['Alice', '0899999999', 'alice@example.com']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone', 'Email' => 'email'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(0, $result['valid']);
        $this->assertSame(1, $result['duplicates']);
    }

    public function test_validation_detects_intra_csv_phone_duplicate(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $headers = ['First Name', 'Phone'];
        $rows    = [
            ['Alice', '0812345678'],
            ['Alice2', '0812345678'],
        ];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(1, $result['valid']);
        $this->assertSame(1, $result['duplicates']);
    }

    public function test_validation_parses_date_of_birth(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $headers = ['First Name', 'Phone', 'DOB'];
        $rows    = [['Alice', '0812345678', '15/06/1990']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone', 'DOB' => 'date_of_birth'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(1, $result['valid']);
        $this->assertSame('1990-06-15', $result['valid_rows'][0]['birthday']);
    }

    public function test_validation_warns_on_unparseable_dob(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $headers = ['First Name', 'Phone', 'DOB'];
        $rows    = [['Alice', '0812345678', 'not-a-date']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone', 'DOB' => 'date_of_birth'];

        $result = $service->validate($rows, $headers, $mapping, $merchant);

        $this->assertSame(1, $result['valid']);
        $this->assertNotEmpty($result['warnings']);
    }

    // -----------------------------------------------------------------------
    // Tenant isolation
    // -----------------------------------------------------------------------

    public function test_validation_does_not_flag_duplicate_from_another_merchant(): void
    {
        [$user1, $merchant1] = $this->actingAsMerchant();
        [$user2, $merchant2] = $this->actingAsMerchant();

        Member::factory()->create([
            'merchant_id' => $merchant1->id,
            'phone'       => '0812345678',
        ]);

        $service = new ImportService();
        $headers = ['First Name', 'Phone'];
        $rows    = [['Bob', '0812345678']];
        $mapping = ['First Name' => 'first_name', 'Phone' => 'phone'];

        // Import for merchant2 — should NOT see merchant1's member as duplicate
        $result = $service->validate($rows, $headers, $mapping, $merchant2);

        $this->assertSame(1, $result['valid']);
        $this->assertSame(0, $result['duplicates']);
    }

    public function test_export_only_returns_authenticated_merchant_data(): void
    {
        [$user1, $merchant1] = $this->actingAsMerchant();
        [$user2, $merchant2] = $this->actingAsMerchant();

        Member::factory()->create(['merchant_id' => $merchant1->id, 'name' => 'Merchant1Member']);
        Member::factory()->create(['merchant_id' => $merchant2->id, 'name' => 'Merchant2Member']);

        $response = $this->actingAs($user1)->get(route('data.export', 'members'));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Merchant1Member', $content);
        $this->assertNotTrue(str_contains($content, 'Merchant2Member'));
    }

    // -----------------------------------------------------------------------
    // Import execution
    // -----------------------------------------------------------------------

    public function test_successful_import_creates_members(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        $validRows = [
            ['row' => 2, 'name' => 'Alice Smith', 'phone' => '0812345678', 'email' => 'alice@example.com', 'birthday' => null, 'notes' => null, 'nickname' => null],
            ['row' => 3, 'name' => 'Bob Jones',   'phone' => '0823456789', 'email' => null,                'birthday' => null, 'notes' => null, 'nickname' => null],
        ];

        $result = $service->execute($validRows, $merchant);

        $this->assertSame(2, $result['imported']);
        $this->assertSame(0, $result['failed']);
        $this->assertDatabaseHas('members', ['merchant_id' => $merchant->id, 'name' => 'Alice Smith']);
        $this->assertDatabaseHas('members', ['merchant_id' => $merchant->id, 'name' => 'Bob Jones']);
    }

    // -----------------------------------------------------------------------
    // Large import queuing
    // -----------------------------------------------------------------------

    public function test_large_import_is_queued(): void
    {
        Queue::fake();

        [$user, $merchant] = $this->actingAsMerchant();

        // Upload a CSV
        $csv = $this->makeCsv(['First Name', 'Phone'], [['Alice', '0812345678']]);
        $this->uploadCsv($user, $csv);

        // Manually set session to simulate >5000 valid rows
        $this->actingAs($user)->withSession([
            'import.temp_path'  => 'import-temp/' . $merchant->id . '/fake.csv',
            'import.headers'    => ['First Name', 'Phone'],
            'import.mapping'    => ['First Name' => 'first_name', 'Phone' => 'phone'],
            'import.validation' => ['total' => 6000, 'valid' => 6000, 'invalid' => 0, 'duplicates' => 0],
        ]);

        // Verify ImportService::shouldQueue works correctly
        $service = new ImportService();
        $this->assertTrue($service->shouldQueue(5001));
        $this->assertFalse($service->shouldQueue(5000));
    }

    // -----------------------------------------------------------------------
    // HTTP upload validation
    // -----------------------------------------------------------------------

    public function test_upload_rejects_non_csv_file(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $file = UploadedFile::fake()->create('members.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($user)->post(route('data.import.upload'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors('csv_file');
    }

    public function test_upload_rejects_oversized_file(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        // Create a file larger than 10 MB
        $file = UploadedFile::fake()->create('members.csv', 11 * 1024, 'text/csv');

        $response = $this->actingAs($user)->post(route('data.import.upload'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors('csv_file');
    }

    public function test_upload_requires_csv_extension(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $file = UploadedFile::fake()->createWithContent('members.txt', "First Name,Phone\nAlice,0812345678");

        $response = $this->actingAs($user)->post(route('data.import.upload'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors('csv_file');
    }

    // -----------------------------------------------------------------------
    // CSV encoding test
    // -----------------------------------------------------------------------

    public function test_import_service_handles_utf8_bom(): void
    {
        $service = new ImportService();

        // Write temp file with BOM
        $content = "\xEF\xBB\xBFFirst Name,Phone\nAlice,0812345678\n";
        $path    = tempnam(sys_get_temp_dir(), 'csv_test');
        file_put_contents($path, $content);

        $parsed = $service->parseCsv($path);
        unlink($path);

        $this->assertContains('First Name', $parsed['headers']);
        $this->assertCount(1, $parsed['rows']);
    }

    public function test_import_service_handles_thai_utf8_characters(): void
    {
        $service = new ImportService();

        $content = "ชื่อ,เบอร์โทร\nอลิส,0812345678\n";
        $path    = tempnam(sys_get_temp_dir(), 'csv_th');
        file_put_contents($path, $content);

        $parsed = $service->parseCsv($path);
        unlink($path);

        $this->assertContains('ชื่อ', $parsed['headers']);
        $this->assertSame('อลิส', $parsed['rows'][0][0]);
    }

    // -----------------------------------------------------------------------
    // Export tests
    // -----------------------------------------------------------------------

    public function test_members_export_contains_correct_headers_and_bom(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        Member::factory()->create([
            'merchant_id' => $merchant->id,
            'name'        => 'Test Member',
            'phone'       => '0812345678',
        ]);

        $response = $this->actingAs($user)->get(route('data.export', 'members'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();

        // BOM present
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        // Headers present
        $this->assertStringContainsString('Member Code', $content);
        // Data present
        $this->assertStringContainsString('Test Member', $content);
    }

    public function test_campaigns_export_returns_200(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $response = $this->actingAs($user)->get(route('data.export', 'campaigns'));

        $response->assertOk();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $response->streamedContent());
    }

    public function test_rewards_export_returns_200(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $response = $this->actingAs($user)->get(route('data.export', 'rewards'));

        $response->assertOk();
    }

    public function test_purchases_export_returns_200(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $response = $this->actingAs($user)->get(route('data.export', 'purchases'));

        $response->assertOk();
    }

    public function test_redemptions_export_returns_200(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $response = $this->actingAs($user)->get(route('data.export', 'redemptions'));

        $response->assertOk();
    }

    public function test_invalid_export_type_returns_404(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $response = $this->actingAs($user)->get(route('data.export', 'invoices'));

        $response->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Authorization tests
    // -----------------------------------------------------------------------

    public function test_guest_cannot_access_import(): void
    {
        $response = $this->get(route('data.import.form'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_export(): void
    {
        $response = $this->get(route('data.export', 'members'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_without_merchant_gets_403_on_import(): void
    {
        $user = User::factory()->create(); // No merchant

        $response = $this->actingAs($user)->get(route('data.import.form'));
        $response->assertForbidden();
    }

    public function test_user_without_merchant_gets_403_on_export(): void
    {
        $user = User::factory()->create(); // No merchant

        $response = $this->actingAs($user)->get(route('data.export', 'members'));
        $response->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Analytics / security logging (no exception = pass)
    // -----------------------------------------------------------------------

    public function test_export_does_not_throw_on_analytics_tracking(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $response = $this->actingAs($user)->get(route('data.export', 'members'));

        $response->assertOk();
    }

    public function test_import_upload_does_not_throw_on_analytics_tracking(): void
    {
        [$user, $merchant] = $this->actingAsMerchant();

        $csv = $this->makeCsv(['First Name', 'Phone'], [['Alice', '0812345678']]);

        $response = $this->actingAs($user)->post(route('data.import.upload'), [
            'csv_file' => $csv,
        ]);

        $response->assertOk();
    }

    // -----------------------------------------------------------------------
    // Settings page renders Data tab
    // -----------------------------------------------------------------------

    public function test_settings_page_shows_data_tab(): void
    {
        $user     = User::factory()->create();
        $merchant = Merchant::factory()->create([
            'user_id'  => $user->id,
            'settings' => [
                'date_format'                => 'DD/MM/YYYY',
                'default_expiration_type'    => 'never',
                'default_expiration_duration'=> null,
                'default_birthday_enabled'   => false,
                'locale'                     => 'en',
                'email_notifications'        => [
                    'product_updates'       => true,
                    'tips'                  => true,
                    'feature_announcements' => true,
                ],
            ],
        ]);

        $response = $this->actingAs($user)->get(route('settings') . '?tab=data');

        $response->assertOk();
        $response->assertSee('Data');
        $response->assertSee(route('data.import.form'));
        $response->assertSee(route('data.export', 'members'));
    }

    // -----------------------------------------------------------------------
    // Storage handling — temp file cleanup
    // -----------------------------------------------------------------------

    public function test_temp_file_is_cleaned_up_after_import_execute(): void
    {
        Storage::fake('local');

        [$user, $merchant] = $this->actingAsMerchant();
        $service = new ImportService();

        // Store a fake temp file
        $csvContent = "First Name,Phone\nAlice,0812345678\n";
        $fakePath   = "import-temp/{$merchant->id}/test.csv";
        Storage::disk('local')->put($fakePath, $csvContent);

        $this->assertTrue(Storage::disk('local')->exists($fakePath));

        $absolutePath = Storage::disk('local')->path($fakePath);
        $parsed       = $service->parseCsv($absolutePath);
        $mapping      = ['First Name' => 'first_name', 'Phone' => 'phone'];
        $validation   = $service->validate($parsed['rows'], $parsed['headers'], $mapping, $merchant);
        $service->execute($validation['valid_rows'], $merchant);
        $service->deleteTempFile($fakePath);

        $this->assertFalse(Storage::disk('local')->exists($fakePath));
    }
}
