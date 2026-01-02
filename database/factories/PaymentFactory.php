<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $amount = fake()->numberBetween(1000, 100000);
        $commission = fake()->numberBetween(100, 1000);

        return [
            'date' => fake()->date(),
            'time' => fake()->time(),
            'payment_type_id' => PaymentType::factory(),
            'payer_name' => fake()->name(),
            'purpose' => fake()->sentence(),
            'random_number' => str_pad(fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT),
            'account_number' => fake()->optional()->numerify('##########'),
            'amount' => $amount,
            'commission' => $commission,
            'total' => $amount + $commission,
            'payment_method' => fake()->randomElement(['cash', 'card']),
            'currency' => 'UZS',
            'status' => fake()->randomElement(['pending', 'confirmed', 'processed']),
            'is_duplicate' => false,
            'cashier_id' => User::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
        ]);
    }
}
