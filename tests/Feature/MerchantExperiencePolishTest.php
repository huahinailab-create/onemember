<?php

namespace Tests\Feature;

use App\Models\LoyaltyProgram;
use App\Models\Merchant;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MerchantExperiencePolishTest extends TestCase
{
    use RefreshDatabase;

    // ── LoyaltyProgram::settings() null-safe accessor (CTO-008) ──────────────

    public function test_loyalty_program_settings_returns_empty_array_when_null(): void
    {
        $program = LoyaltyProgram::factory()->create(['settings' => null]);

        $this->assertSame([], $program->settings);
    }

    public function test_loyalty_program_settings_returns_array_when_set(): void
    {
        $program = LoyaltyProgram::factory()->create([
            'settings' => ['points_per_thb' => 1, 'minimum_spend' => 50],
        ]);

        $this->assertSame(1, $program->settings['points_per_thb']);
        $this->assertSame(50, $program->settings['minimum_spend']);
    }

    public function test_loyalty_program_settings_does_not_raise_exception_on_null_key_access(): void
    {
        $program = LoyaltyProgram::factory()->create(['settings' => null]);

        // This was the bug: accessing a key on null raised ErrorException
        $value = $program->settings['missing_key'] ?? 'default';

        $this->assertSame('default', $value);
    }

    public function test_loyalty_program_settings_can_be_updated(): void
    {
        $program = LoyaltyProgram::factory()->create(['settings' => null]);

        $program->update(['settings' => ['birthday_enabled' => true]]);
        $program->refresh();

        $this->assertTrue($program->settings['birthday_enabled']);
    }

    public function test_loyalty_program_fresh_from_db_settings_null_returns_empty_array(): void
    {
        $program = LoyaltyProgram::factory()->create(['settings' => null]);

        // Re-fetch from DB to confirm accessor fires on hydration
        $fresh = LoyaltyProgram::find($program->id);

        $this->assertSame([], $fresh->settings);
        $this->assertIsArray($fresh->settings);
    }
}
