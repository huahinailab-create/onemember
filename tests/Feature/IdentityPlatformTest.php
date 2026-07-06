<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Consent;
use App\Models\Customer;
use App\Models\CustomerMemberLink;
use App\Models\Member;
use App\Models\Merchant;
use App\Models\User;
use App\Services\IdentityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentityPlatformTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private Merchant $merchantA;
    private User $userB;
    private Merchant $merchantB;
    private IdentityService $identity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA     = User::factory()->create(['email_verified_at' => now()]);
        $this->merchantA = Merchant::factory()->create(['user_id' => $this->userA->id, 'onboarding_completed_at' => now()]);
        $this->userB     = User::factory()->create(['email_verified_at' => now()]);
        $this->merchantB = Merchant::factory()->create(['user_id' => $this->userB->id, 'onboarding_completed_at' => now()]);
        $this->identity  = app(IdentityService::class);
    }

    private function registerMemberAtA(array $overrides = []): Member
    {
        $this->actingAs($this->userA)->post(route('members.store'), array_merge([
            'name'        => 'Chelsea Test',
            'phone'       => '0811111111',
            'birthday'    => '1995-04-10',
            'email'       => 'chelsea@example.com',
            'postal_code' => '10110',
        ], $overrides));

        return Member::where('merchant_id', $this->merchantA->id)->latest('id')->firstOrFail();
    }

    // ── Identity creation ────────────────────────────────────────────────

    public function test_registering_a_member_creates_identity_and_link(): void
    {
        $member = $this->registerMemberAtA();

        $customer = Customer::where('phone', '0811111111')->first();
        $this->assertNotNull($customer);
        $this->assertMatchesRegularExpression('/^OM-[A-Z2-9]{4}-[A-Z2-9]{4}$/', $customer->onemember_id);

        $this->assertDatabaseHas('customer_member_links', [
            'customer_id' => $customer->id,
            'member_id'   => $member->id,
            'merchant_id' => $this->merchantA->id,
            'linked_via'  => 'registration',
        ]);
    }

    public function test_same_phone_at_second_merchant_reuses_identity(): void
    {
        $this->registerMemberAtA();

        $this->actingAs($this->userB)->post(route('members.store'), [
            'name'     => 'Chelsea At B',
            'phone'    => '0811111111',
            'birthday' => '1995-04-10',
        ]);

        $this->assertSame(1, Customer::where('phone', '0811111111')->count());
        $this->assertSame(2, CustomerMemberLink::whereNull('unlinked_at')->count());
    }

    public function test_identity_profile_is_not_overwritten_by_later_registration(): void
    {
        $this->registerMemberAtA(['name' => 'Original Name']);

        $this->actingAs($this->userB)->post(route('members.store'), [
            'name'     => 'Different Name At B',
            'phone'    => '0811111111',
            'birthday' => '1990-01-01',
        ]);

        $this->assertSame('Original Name', Customer::where('phone', '0811111111')->first()->name);
    }

    public function test_member_without_phone_gets_no_identity(): void
    {
        Member::factory()->create(['merchant_id' => $this->merchantA->id, 'phone' => null]);
        $member = Member::where('merchant_id', $this->merchantA->id)->first();

        $this->assertNull($this->identity->ensureIdentityForMember($member->fresh()));
        $this->assertSame(0, Customer::count());
    }

    public function test_feature_flag_off_disables_identity_creation(): void
    {
        config(['features.identity' => false]);

        $this->registerMemberAtA();

        $this->assertSame(0, Customer::count());
    }

    // ── QR generation & resolution ───────────────────────────────────────

    public function test_qr_payload_contains_no_personal_data(): void
    {
        $customer = Customer::factory()->create(['name' => 'Secret Name', 'phone' => '0899999999']);
        $payload  = $this->identity->qrPayload($customer);

        $this->assertStringNotContainsString('Secret', $payload);
        $this->assertStringNotContainsString('0899999999', $payload);
        $this->assertStringStartsWith('OM2:' . $customer->onemember_id . ':', $payload);
    }

    public function test_qr_resolves_to_customer(): void
    {
        $customer = Customer::factory()->create();

        $this->assertTrue($this->identity->resolveQr($this->identity->qrPayload($customer))->is($customer));
    }

    public function test_tampered_qr_is_rejected(): void
    {
        $customer = Customer::factory()->create();
        $other    = Customer::factory()->create();

        // Signature from one ID, body from another
        $forged = 'OM2:' . $other->onemember_id . ':' . explode(':', $this->identity->qrPayload($customer))[2];

        $this->assertNull($this->identity->resolveQr($forged));
        $this->assertNull($this->identity->resolveQr('OM2:OM-FAKE-FAKE:0000000000000000'));
        $this->assertNull($this->identity->resolveQr('garbage'));
        $this->assertNull($this->identity->resolveQr(null));
    }

    // ── OneMember Card ───────────────────────────────────────────────────

    public function test_card_page_renders_with_id_and_qr(): void
    {
        $customer = Customer::factory()->create(['locale' => 'en']);

        $this->get(route('identity.card', $customer->public_uuid, absolute: false))
            ->assertOk()
            ->assertSee($customer->onemember_id)
            ->assertSee($customer->name)
            ->assertSee('<svg', false)
            ->assertSee(__('identity.card_privacy', [], 'en'));
    }

    public function test_card_page_renders_thai_by_default(): void
    {
        $customer = Customer::factory()->create(['locale' => 'th']);

        $this->get(route('identity.card', $customer->public_uuid, absolute: false))
            ->assertOk()
            ->assertSee(__('identity.card_label', [], 'th'));
    }

    public function test_card_404_for_unknown_uuid_and_disabled_flag(): void
    {
        $this->get('/omid/' . fake()->uuid())->assertNotFound();

        config(['features.identity' => false]);
        $customer = Customer::factory()->create();
        $this->get(route('identity.card', $customer->public_uuid, absolute: false))->assertNotFound();
    }

    // ── Merchant scan-to-join workflow ───────────────────────────────────

    private function scanAtB(Customer $customer)
    {
        return $this->actingAs($this->userB)->post(route('members.identity.resolve'), [
            'qr_payload' => $this->identity->qrPayload($customer),
        ]);
    }

    public function test_scan_shows_consent_screen_with_masked_data_only(): void
    {
        $member   = $this->registerMemberAtA();
        $customer = Customer::where('phone', '0811111111')->first();

        $response = $this->actingAs($this->userB)
            ->withSession(['locale' => 'en'])
            ->post(route('members.identity.resolve'), [
                'qr_payload' => $this->identity->qrPayload($customer),
            ]);

        $response->assertOk()
            ->assertSee($customer->onemember_id)
            ->assertSee('081-xxx-1111')            // masked phone
            ->assertDontSee('chelsea@example.com'); // no raw profile before consent
    }

    public function test_invalid_scan_shows_error(): void
    {
        $this->actingAs($this->userB)
            ->from(route('members.identity.add'))
            ->post(route('members.identity.resolve'), ['qr_payload' => 'OM2:OM-XXXX-XXXX:badbadbadbadbad1'])
            ->assertRedirect(route('members.identity.add', absolute: false))
            ->assertSessionHasErrors(['qr_payload']);
    }

    public function test_consented_join_creates_member_with_approved_fields_only(): void
    {
        $this->registerMemberAtA();
        $customer = Customer::where('phone', '0811111111')->first();

        $this->actingAs($this->userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid,
            'fields'        => ['name', 'phone', 'birthday'],   // email + postal declined
        ])->assertRedirect();

        $member = Member::where('merchant_id', $this->merchantB->id)->first();
        $this->assertNotNull($member);
        $this->assertSame($customer->name, $member->name);
        $this->assertSame($customer->phone, $member->phone);
        $this->assertNotNull($member->birthday);
        $this->assertNull($member->email);          // declined
        $this->assertNull($member->postal_code);    // declined

        // Consent ledger records both grants and refusals
        $this->assertTrue(Consent::where('merchant_id', $this->merchantB->id)->where('data_type', 'email')->where('granted', false)->exists());
        $this->assertTrue(Consent::where('merchant_id', $this->merchantB->id)->where('data_type', 'name')->where('granted', true)->exists());
    }

    public function test_join_without_name_consent_is_rejected(): void
    {
        $this->registerMemberAtA();
        $customer = Customer::where('phone', '0811111111')->first();

        $this->actingAs($this->userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid,
            'fields'        => ['phone'],
        ])->assertSessionHasErrors(['qr_payload']);

        $this->assertSame(0, Member::where('merchant_id', $this->merchantB->id)->count());
    }

    // ── Duplicate prevention ─────────────────────────────────────────────

    public function test_duplicate_membership_is_prevented(): void
    {
        $this->registerMemberAtA();
        $customer = Customer::where('phone', '0811111111')->first();

        $this->scanAtB($customer);
        $this->actingAs($this->userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid,
            'fields'        => ['name', 'phone'],
        ]);

        // Second attempt at the same merchant
        $this->actingAs($this->userB)
            ->from(route('members.identity.add'))
            ->post(route('members.identity.resolve'), ['qr_payload' => $this->identity->qrPayload($customer)])
            ->assertSessionHasErrors(['qr_payload']);

        $this->assertSame(1, Member::where('merchant_id', $this->merchantB->id)->count());
        $this->assertSame(1, $customer->liveLinks()->where('merchant_id', $this->merchantB->id)->count());
    }

    public function test_join_connects_existing_unlinked_member_instead_of_duplicating(): void
    {
        $this->registerMemberAtA();
        $customer = Customer::where('phone', '0811111111')->first();

        // Merchant B already typed this customer in manually before identity existed
        config(['features.identity' => false]);
        $this->actingAs($this->userB)->post(route('members.store'), [
            'name'     => 'Manually Typed',
            'phone'    => '0811111111',
            'birthday' => '1995-04-10',
        ]);
        config(['features.identity' => true]);

        $this->actingAs($this->userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid,
            'fields'        => ['name', 'phone'],
        ]);

        // No second member created — the existing record was connected
        $this->assertSame(1, Member::where('merchant_id', $this->merchantB->id)->count());
        $this->assertTrue(
            CustomerMemberLink::where('merchant_id', $this->merchantB->id)
                ->where('customer_id', $customer->id)->whereNull('unlinked_at')->exists()
        );
    }

    public function test_duplicate_identity_impossible_for_same_phone(): void
    {
        $this->registerMemberAtA();
        $memberB = Member::factory()->create(['merchant_id' => $this->merchantB->id, 'phone' => '0811111111']);

        $this->identity->ensureIdentityForMember($memberB);

        $this->assertSame(1, Customer::where('phone', '0811111111')->count());
    }

    // ── Merchant isolation ───────────────────────────────────────────────

    public function test_loyalty_stays_separate_per_merchant(): void
    {
        $memberA = $this->registerMemberAtA();
        $memberA->update(['total_points' => 500]);
        $customer = Customer::where('phone', '0811111111')->first();

        $this->actingAs($this->userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid,
            'fields'        => ['name', 'phone'],
        ]);

        $memberB = Member::where('merchant_id', $this->merchantB->id)->first();

        // Identity shared; loyalty NOT shared (ADR-010)
        $this->assertSame(0, $memberB->total_points);
        $this->assertSame(500, $memberA->fresh()->total_points);
    }

    public function test_consent_screen_requires_authenticated_merchant(): void
    {
        $customer = Customer::factory()->create();

        $this->post(route('members.identity.resolve'), [
            'qr_payload' => $this->identity->qrPayload($customer),
        ])->assertRedirect(); // guest → login
    }

    // ── Audit logging ────────────────────────────────────────────────────

    public function test_identity_creation_and_join_are_audited(): void
    {
        $this->registerMemberAtA();
        $customer = Customer::where('phone', '0811111111')->first();

        $this->actingAs($this->userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid,
            'fields'        => ['name', 'phone'],
        ]);

        $this->assertTrue(AuditLog::where('event', 'identity.created')->exists());
        $this->assertTrue(AuditLog::where('event', 'identity.linked')->exists());
        $join = AuditLog::where('event', 'identity.scan_join')->where('merchant_id', $this->merchantB->id)->first();
        $this->assertNotNull($join);
        $this->assertSame(['name', 'phone'], $join->new_values['approved_fields']);
    }

    // ── Consent ledger integrity ─────────────────────────────────────────

    public function test_consent_rows_are_append_only(): void
    {
        $this->registerMemberAtA();
        $customer = Customer::where('phone', '0811111111')->first();
        $this->actingAs($this->userB)->post(route('members.identity.join'), [
            'customer_uuid' => $customer->public_uuid,
            'fields'        => ['name'],
        ]);

        $consent = Consent::first();

        $this->expectException(\LogicException::class);
        $consent->update(['granted' => false]);
    }

    // ── Localization ─────────────────────────────────────────────────────

    public function test_scan_page_defaults_to_thai_and_supports_english(): void
    {
        $this->actingAs($this->userB)
            ->get(route('members.identity.add', absolute: false))
            ->assertOk()
            ->assertSee(__('identity.scan_heading', [], 'th'));

        $this->actingAs($this->userB)
            ->withSession(['locale' => 'en'])
            ->get(route('members.identity.add', absolute: false))
            ->assertOk()
            ->assertSee(__('identity.scan_heading', [], 'en'));
    }
}
