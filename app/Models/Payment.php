<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'list_number',
        'random_number',
        'share_token',
        'date',
        'time',
        'payment_type_id',
        'payer_name',
        'purpose',
        'amount',
        'commission',
        'total',
        'payment_method',
        'currency',
        'status',
        'cashier_id',
        'cashier_shift_id',
        'city',
        'region',
        'cash_back',
        'agent_id',
        'payment_system',
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
            'amount' => 'decimal:2',
            'commission' => 'decimal:2',
            'total' => 'decimal:2',
            'cash_back' => 'decimal:2',
        ];
    }

    /**
     * Get the payment type that owns the payment.
     */
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    /**
     * Get the cashier that owns the payment.
     */
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the agent that owns the payment.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the cashier shift that owns the payment.
     */
    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    /**
     * Get the method details for the payment.
     */
    public function methodDetails()
    {
        return $this->hasMany(PaymentMethodDetail::class);
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed payments.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include payments from a specific date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to only include payments by a specific cashier.
     */
    public function scopeByCashier($query, $cashierId)
    {
        return $query->where('cashier_id', $cashierId);
    }

    /**
     * Get the formatted total attribute.
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2, '.', ' ');
    }

    /**
     * Generate unique share token for public access
     *
     * @return string
     */
    public static function generateShareToken(): string
    {
        do {
            $token = hash('sha256', uniqid(mt_rand(), true) . microtime());
        } while (self::where('share_token', $token)->exists());

        return $token;
    }
}
