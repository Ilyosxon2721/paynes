<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'inn',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all subscriptions for the client
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get all branches for the client
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get all users for the client
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all merchants for the client
     */
    public function merchants(): HasMany
    {
        return $this->hasMany(Merchant::class);
    }

    /**
     * Get all commissions for the client
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Get the active subscription
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest();
    }

    /**
     * Check if client has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Check if client can add more users
     */
    public function canAddUser(): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }

        $currentUserCount = $this->users()->count();
        return $currentUserCount < $subscription->max_users;
    }

    /**
     * Check if client can add more branches
     */
    public function canAddBranch(): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }

        $currentBranchCount = $this->branches()->count();
        return $currentBranchCount < $subscription->max_branches;
    }

    /**
     * Get remaining user slots
     */
    public function getRemainingUserSlots(): int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return 0;
        }

        $currentUserCount = $this->users()->count();
        return max(0, $subscription->max_users - $currentUserCount);
    }

    /**
     * Get remaining branch slots
     */
    public function getRemainingBranchSlots(): int
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return 0;
        }

        $currentBranchCount = $this->branches()->count();
        return max(0, $subscription->max_branches - $currentBranchCount);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'suspended' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}
