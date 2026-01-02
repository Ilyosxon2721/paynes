<?php

namespace Database\Factories;

use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentType>
 */
class PaymentTypeFactory extends Factory
{
    protected $model = PaymentType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'code' => fake()->unique()->numerify('PT###'),
            'commission_type' => fake()->randomElement(['percentage', 'fixed', 'both', 'none']),
            'commission_percentage' => fake()->randomFloat(2, 0, 5),
            'commission_fixed' => fake()->numberBetween(0, 1000),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
