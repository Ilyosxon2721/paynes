<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'payment_type_id',
        'name',
        'type',
        'fixed_amount',
        'percentage',
        'min_amount',
        'max_amount',
        'applies_to',
        'merchant_id',
        'status',
        'valid_from',
        'valid_until',
        'notes',
    ];

    protected $casts = [
        'fixed_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the client that owns the commission
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the payment type this commission applies to
     */
    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    /**
     * Get the merchant this commission applies to
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Scope active commissions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    /**
     * Calculate commission for given amount
     */
    public function calculate(float $amount): float
    {
        $commission = 0;

        switch ($this->type) {
            case 'fixed':
                $commission = $this->fixed_amount ?? 0;
                break;

            case 'percentage':
                $commission = ($amount * ($this->percentage ?? 0)) / 100;
                break;

            case 'combined':
                $percentageAmount = ($amount * ($this->percentage ?? 0)) / 100;
                $commission = ($this->fixed_amount ?? 0) + $percentageAmount;
                break;
        }

        // Применяем минимум и максимум
        if ($this->min_amount && $commission < $this->min_amount) {
            $commission = $this->min_amount;
        }

        if ($this->max_amount && $commission > $this->max_amount) {
            $commission = $this->max_amount;
        }

        return round($commission, 2);
    }

    /**
     * Check if commission is currently valid
     */
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();

        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'fixed' => 'Фиксированная',
            'percentage' => 'Процент',
            'combined' => 'Комбинированная',
            default => $this->type,
        };
    }
}
