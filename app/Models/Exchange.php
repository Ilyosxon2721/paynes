<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exchange extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'time',
        'usd_amount',
        'uzs_amount',
        'type',
        'rate',
        'cashier_id',
        'cashier_shift_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'usd_amount' => 'decimal:2',
            'uzs_amount' => 'decimal:2',
            'rate' => 'decimal:2',
        ];
    }

    /**
     * Get the cashier that owns the exchange.
     */
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the cashier shift that owns the exchange.
     */
    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    /**
     * Scope a query to only include buy exchanges.
     */
    public function scopeBuy($query)
    {
        return $query->where('type', 'buy');
    }

    /**
     * Scope a query to only include sell exchanges.
     */
    public function scopeSell($query)
    {
        return $query->where('type', 'sell');
    }

    /**
     * Scope a query to only include exchanges from a specific date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
