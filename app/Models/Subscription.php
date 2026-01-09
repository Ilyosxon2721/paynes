<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'plan_name',
        'monthly_price',
        'start_date',
        'end_date',
        'next_billing_date',
        'status',
        'max_users',
        'max_branches',
        'auto_renew',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'monthly_price' => 'decimal:2',
        'auto_renew' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the client that owns the subscription
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date >= now();
    }

    /**
     * Check if subscription is expiring soon (within 7 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->isActive() && $this->end_date->diffInDays(now()) <= 7;
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->end_date < now()) {
            return 0;
        }
        return now()->diffInDays($this->end_date);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'cancelled' => 'warning',
            'suspended' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Renew subscription for another month
     */
    public function renew(): void
    {
        $this->update([
            'start_date' => $this->end_date->addDay(),
            'end_date' => $this->end_date->addMonth(),
            'next_billing_date' => $this->end_date,
            'status' => 'active',
        ]);
    }
}
