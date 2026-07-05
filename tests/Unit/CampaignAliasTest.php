<?php

namespace Tests\Unit;

use App\Models\Campaign;
use App\Models\LoyaltyProgram;
use Tests\TestCase;

class CampaignAliasTest extends TestCase
{
    public function test_campaign_is_an_alias_of_loyalty_program(): void
    {
        $this->assertTrue(class_exists(Campaign::class));
        $this->assertSame(LoyaltyProgram::class, (new \ReflectionClass(Campaign::class))->getName());
    }

    public function test_campaign_instance_is_a_loyalty_program(): void
    {
        $this->assertInstanceOf(LoyaltyProgram::class, new Campaign());
    }
}
