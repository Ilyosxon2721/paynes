<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'time',
        'user_id',
        'amount',
        'reason',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i:s',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the fine
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
