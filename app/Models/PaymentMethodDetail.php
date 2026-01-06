<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethodDetail extends Model
{
    protected $fillable = [
        'payment_id',
        'method',
        'amount',
        'payment_system',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Платеж, к которому относится деталь
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
