<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache TTL in seconds
     */
    const RATES_TTL = 3600; // 1 hour
    const PAYMENT_TYPES_TTL = 86400; // 24 hours
    const USER_TTL = 3600; // 1 hour

    /**
     * Get latest exchange rate with caching
     */
    public function getLatestRate()
    {
        return Cache::remember('latest_rate', self::RATES_TTL, function () {
            return \App\Models\Rate::latest()->first();
        });
    }

    /**
     * Get all active payment types with caching
     */
    public function getActivePaymentTypes()
    {
        return Cache::remember('active_payment_types', self::PAYMENT_TYPES_TTL, function () {
            return \App\Models\PaymentType::where('is_active', true)->get();
        });
    }

    /**
     * Clear all application caches
     */
    public function clearAll(): void
    {
        Cache::flush();
    }

    /**
     * Clear rates cache
     */
    public function clearRates(): void
    {
        Cache::forget('latest_rate');
        Cache::tags(['rates'])->flush();
    }

    /**
     * Clear payment types cache
     */
    public function clearPaymentTypes(): void
    {
        Cache::forget('active_payment_types');
        Cache::tags(['payment_types'])->flush();
    }
}
