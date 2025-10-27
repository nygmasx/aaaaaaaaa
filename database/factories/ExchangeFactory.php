<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exchange>
 */
class ExchangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencies = ['EUR', 'USD', 'GBP', 'JPY', 'CAD', 'CHF'];
        $currency = fake()->randomElement($currencies);

        $exchangeRates = [
            'EUR' => 1.0,
            'USD' => 1.08,
            'GBP' => 0.86,
            'JPY' => 161.50,
            'CAD' => 1.49,
            'CHF' => 0.93,
        ];

        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 10, 2000),
            'currency' => $currency,
            'exchange_rate' => $exchangeRates[$currency] + fake()->randomFloat(4, -0.05, 0.05),
            'message' => fake()->optional(0.7)->sentence(),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Create a small amount exchange
     */
    public function smallAmount(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => fake()->randomFloat(2, 1, 50),
        ]);
    }

    /**
     * Create a large amount exchange
     */
    public function largeAmount(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => fake()->randomFloat(2, 1000, 10000),
        ]);
    }

    /**
     * Create an exchange in EUR
     */
    public function eur(): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => 'EUR',
            'exchange_rate' => 1.0,
        ]);
    }

    /**
     * Create an exchange in USD
     */
    public function usd(): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => 'USD',
            'exchange_rate' => fake()->randomFloat(4, 1.05, 1.12),
        ]);
    }

    /**
     * Create an exchange with message
     */
    public function withMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => fake()->sentence(),
        ]);
    }

    /**
     * Create an exchange without message
     */
    public function withoutMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'message' => null,
        ]);
    }

    /**
     * Create a recent exchange
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create an old exchange
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-2 years', '-6 months'),
        ]);
    }
}
