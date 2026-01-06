<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model
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
        'recipient',
        'account_number',
        'branch',
        'debit',
        'credit',
        'confirmed_by',
        'status',
        'cashier_id',
        'cashier_shift_id',
        'remaining_balance',
        'total_repaid',
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
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'total_repaid' => 'decimal:2',
        ];
    }

    /**
     * Get the cashier that owns the credit.
     */
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the cashier shift that owns the credit.
     */
    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    /**
     * Get the repayments for the credit.
     */
    public function repayments()
    {
        return $this->hasMany(CreditRepayment::class);
    }

    /**
     * Scope a query to only include pending credits.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed credits.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Generate an account number.
     *
     * @return string
     */
    public function generateAccountNumber()
    {
        $id = str_pad($this->id ?? 1, 5, '0', STR_PAD_LEFT);
        $date = date('Ymd');
        $time = date('His');

        return '29801000' . $id . $date . $time . '001';
    }
}
