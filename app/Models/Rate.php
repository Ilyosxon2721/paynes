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
        'buy_rate',
        'sell_rate',
        'date',
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
            'buy_rate' => 'decimal:2',
            'sell_rate' => 'decimal:2',
        ];
    }

    /**
     * Get the latest rate.
     *
     * @return Rate|null
     */
    public static function getLatest()
    {
        return static::orderBy('date', 'desc')->orderBy('created_at', 'desc')->first();
    }
}
