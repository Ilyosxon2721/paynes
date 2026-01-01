<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'organization',
        'bank',
        'account_number',
        'mfo',
        'inn',
        'treasury_account',
        'type',
        'commission_percentage',
        'commission_fixed',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'commission_percentage' => 'decimal:2',
            'commission_fixed' => 'decimal:2',
        ];
    }

    /**
     * Get the payments for the payment type.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate the commission for a given amount.
     *
     * @param float $amount
     * @return float
     */
    public function calculateCommission($amount)
    {
        $commission = 0;

        if ($this->commission_percentage) {
            $commission += ($amount * $this->commission_percentage) / 100;
        }

        if ($this->commission_fixed) {
            $commission += $this->commission_fixed;
        }

        return round($commission, 2);
    }
}
