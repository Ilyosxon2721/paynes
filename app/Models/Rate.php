<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency_from',
        'currency_to',
        'buy_rate',
        'sell_rate',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'buy_rate' => 'decimal:2',
            'sell_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the latest active rate.
     *
     * @return Rate|null
     */
    public static function getLatest()
    {
        return static::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Scope a query to only include active rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
