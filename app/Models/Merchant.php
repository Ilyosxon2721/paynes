<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Merchant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'code',
        'api_key',
        'api_secret',
        'callback_url',
        'status',
        'allowed_ips',
        'settings',
        'notes',
    ];

    protected $casts = [
        'allowed_ips' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'api_secret',
    ];

    /**
     * Boot function
     */
    protected static function boot()
    {
        parent::boot();

        // Автоматически генерируем код и API ключи при создании
        static::creating(function ($merchant) {
            if (empty($merchant->code)) {
                $merchant->code = 'M' . strtoupper(Str::random(8));
            }
            if (empty($merchant->api_key)) {
                $merchant->api_key = Str::random(32);
            }
            if (empty($merchant->api_secret)) {
                $merchant->api_secret = Str::random(64);
            }
        });
    }

    /**
     * Get the client that owns the merchant
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get commissions for this merchant
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Get payments for this merchant
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope active merchants
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if merchant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Regenerate API credentials
     */
    public function regenerateApiCredentials(): void
    {
        $this->update([
            'api_key' => Str::random(32),
            'api_secret' => Str::random(64),
        ]);
    }

    /**
     * Check if IP is allowed
     */
    public function isIpAllowed(string $ip): bool
    {
        if (empty($this->allowed_ips)) {
            return true; // Если нет ограничений, разрешаем все
        }

        return in_array($ip, $this->allowed_ips);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'suspended' => 'danger',
            default => 'secondary',
        };
    }
}
