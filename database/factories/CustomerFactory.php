<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->name(),
            // Legacy national format — pre-CUSTOMER-001A records look like
            // this; new registrations store E.164 (+66…). Both must coexist.
            'phone'       => '08' . fake()->unique()->numerify('########'),
            'email'       => fake()->unique()->safeEmail(),
            'birthday'    => fake()->date('Y-m-d', '-18 years'),
            'postal_code' => fake()->numerify('#####'),
            'locale'      => 'th',
            'country'     => 'TH',
            'status'      => Customer::STATUS_ACTIVE,
        ];
    }

    /** A CUSTOMER-001A account: E.164 phone, verified, with a password. */
    public function account(): static
    {
        return $this->state(fn () => [
            'phone'             => '+668' . fake()->unique()->numerify('########'),
            'password'          => 'Secret!Password99',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }

    /** An account that signs in with OTP only (no password set). */
    public function otpOnly(): static
    {
        return $this->state(fn () => [
            'phone'             => '+668' . fake()->unique()->numerify('########'),
            'password'          => null,
            'phone_verified_at' => now(),
        ]);
    }
}
