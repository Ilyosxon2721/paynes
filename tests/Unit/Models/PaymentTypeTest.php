<?php

namespace Tests\Unit\Models;

use App\Models\PaymentType;
use Tests\TestCase;

class PaymentTypeTest extends TestCase
{
    public function test_calculates_commission_with_percentage_only(): void
    {
        $paymentType = new PaymentType([
            'commission_type' => 'percentage',
            'commission_percentage' => 2.5, // 2.5%
            'commission_fixed' => 0,
        ]);

        $commission = $paymentType->calculateCommission(1000);

        $this->assertEquals(25, $commission); // 2.5% of 1000
    }

    public function test_calculates_commission_with_fixed_only(): void
    {
        $paymentType = new PaymentType([
            'commission_type' => 'fixed',
            'commission_percentage' => 0,
            'commission_fixed' => 500,
        ]);

        $commission = $paymentType->calculateCommission(1000);

        $this->assertEquals(500, $commission);
    }

    public function test_calculates_commission_with_percentage_and_fixed(): void
    {
        $paymentType = new PaymentType([
            'commission_type' => 'both',
            'commission_percentage' => 2, // 2%
            'commission_fixed' => 300,
        ]);

        $commission = $paymentType->calculateCommission(1000);

        $this->assertEquals(320, $commission); // 2% of 1000 (20) + 300
    }

    public function test_calculates_zero_commission_when_no_commission_set(): void
    {
        $paymentType = new PaymentType([
            'commission_type' => 'none',
            'commission_percentage' => 0,
            'commission_fixed' => 0,
        ]);

        $commission = $paymentType->calculateCommission(1000);

        $this->assertEquals(0, $commission);
    }

    public function test_commission_is_rounded_correctly(): void
    {
        $paymentType = new PaymentType([
            'commission_type' => 'percentage',
            'commission_percentage' => 2.75, // 2.75%
            'commission_fixed' => 0,
        ]);

        $commission = $paymentType->calculateCommission(1000);

        // 2.75% of 1000 = 27.5, should round to 27.50
        $this->assertEquals(27.5, $commission);
    }
}
