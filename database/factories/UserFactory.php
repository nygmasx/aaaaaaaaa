<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'role' => 'ROLE_USER',
            'balance' => fake()->randomFloat(2, 100, 5000),
            'iban' => $this->generateIban(),
            'is_active' => true,
        ];
    }

    /**
     * Generate a valid French IBAN
     */
    private function generateIban(): string
    {
        $bankCode = str_pad(fake()->numberBetween(10000, 99999), 5, '0', STR_PAD_LEFT);
        $branchCode = str_pad(fake()->numberBetween(10000, 99999), 5, '0', STR_PAD_LEFT);
        $accountNumber = str_pad(fake()->numberBetween(1, 99999999999), 11, '0', STR_PAD_LEFT);
        $key = str_pad(fake()->numberBetween(10, 99), 2, '0', STR_PAD_LEFT);

        return 'FR' . $key . $bankCode . $branchCode . $accountNumber;
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model does not have two-factor authentication configured.
     */
    public function withoutTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'ROLE_ADMIN',
            'balance' => fake()->randomFloat(2, 10000, 50000),
        ]);
    }

    /**
     * Create an inactive/blocked user
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a user with low balance
     */
    public function lowBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => fake()->randomFloat(2, 0, 50),
        ]);
    }

    /**
     * Create a user with high balance
     */
    public function highBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => fake()->randomFloat(2, 10000, 100000),
        ]);
    }

    /**
     * Create a user with 2FA enabled
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => Str::random(32),
            'two_factor_recovery_codes' => json_encode([
                Str::random(10),
                Str::random(10),
                Str::random(10),
                Str::random(10),
                Str::random(10),
                Str::random(10),
                Str::random(10),
                Str::random(10),
            ]),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
