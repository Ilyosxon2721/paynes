<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    protected $fillable = [
        'cashier_id',
        'shift_date',
        'opened_at',
        'closed_at',
        'status',
        'opening_cash_uzs',
        'opening_cashless_uzs',
        'opening_card_uzs',
        'opening_p2p_uzs',
        'opening_cash_usd',
        'closing_cash_uzs',
        'closing_cashless_uzs',
        'closing_card_uzs',
        'closing_p2p_uzs',
        'closing_cash_usd',
        'opening_notes',
        'closing_notes',
    ];

    protected function casts(): array
    {
        return [
            'shift_date' => 'date',
            'opening_cash_uzs' => 'decimal:2',
            'opening_cashless_uzs' => 'decimal:2',
            'opening_card_uzs' => 'decimal:2',
            'opening_p2p_uzs' => 'decimal:2',
            'opening_cash_usd' => 'decimal:2',
            'closing_cash_uzs' => 'decimal:2',
            'closing_cashless_uzs' => 'decimal:2',
            'closing_card_uzs' => 'decimal:2',
            'closing_p2p_uzs' => 'decimal:2',
            'closing_cash_usd' => 'decimal:2',
        ];
    }

    /**
     * Кассир, владелец смены
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Платежи смены
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'cashier_shift_id');
    }

    /**
     * Обмены валют смены
     */
    public function exchanges(): HasMany
    {
        return $this->hasMany(Exchange::class, 'cashier_shift_id');
    }

    /**
     * Кредиты смены
     */
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class, 'cashier_shift_id');
    }

    /**
     * Инкассации смены
     */
    public function incashes(): HasMany
    {
        return $this->hasMany(Incash::class, 'cashier_shift_id');
    }

    /**
     * Погашения кредитов смены
     */
    public function creditRepayments(): HasMany
    {
        return $this->hasMany(CreditRepayment::class, 'cashier_shift_id');
    }

    /**
     * Scope: только открытые смены
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope: только закрытые смены
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope: по дате
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('shift_date', $date);
    }

    /**
     * Scope: по кассиру
     */
    public function scopeByCashier($query, $cashierId)
    {
        return $query->where('cashier_id', $cashierId);
    }

    /**
     * Расчет итоговых балансов при закрытии смены
     */
    public function calculateClosingBalances(): array
    {
        $balances = [
            'cash_uzs' => $this->opening_cash_uzs,
            'cashless_uzs' => $this->opening_cashless_uzs,
            'card_uzs' => $this->opening_card_uzs,
            'p2p_uzs' => $this->opening_p2p_uzs,
            'cash_usd' => $this->opening_cash_usd,
        ];

        // Платежи: суммируем по методам оплаты
        $payments = $this->payments()->confirmed()->with('methodDetails')->get();
        foreach ($payments as $payment) {
            foreach ($payment->methodDetails as $detail) {
                if ($detail->method === 'cash') {
                    $balances['cash_uzs'] += $detail->amount;
                } elseif ($detail->method === 'cashless') {
                    $balances['cashless_uzs'] += $detail->amount;
                } elseif ($detail->method === 'card') {
                    $balances['card_uzs'] += $detail->amount;
                } elseif ($detail->method === 'p2p') {
                    $balances['p2p_uzs'] += $detail->amount;
                }
            }
        }

        // Обмены: покупка уменьшает UZS и увеличивает USD, продажа наоборот
        $exchanges = $this->exchanges()->get();
        foreach ($exchanges as $exchange) {
            if ($exchange->type === 'buy') {
                $balances['cash_uzs'] -= $exchange->uzs_amount;
                $balances['cash_usd'] += $exchange->usd_amount;
            } else {
                $balances['cash_uzs'] += $exchange->uzs_amount;
                $balances['cash_usd'] -= $exchange->usd_amount;
            }
        }

        // Кредиты: выдача уменьшает наличные UZS
        $credits = $this->credits()->confirmed()->get();
        foreach ($credits as $credit) {
            $balances['cash_uzs'] -= $credit->debit;
        }

        // Погашения кредитов: увеличивают наличные UZS
        $repayments = $this->creditRepayments()->get();
        foreach ($repayments as $repayment) {
            $balances['cash_uzs'] += $repayment->amount;
        }

        // Инкассации: приход увеличивает, расход уменьшает
        $incashes = $this->incashes()->confirmed()->get();
        foreach ($incashes as $incash) {
            if ($incash->type === 'income') {
                $balances['cash_uzs'] += $incash->amount;
            } else {
                $balances['cash_uzs'] -= $incash->amount;
            }
        }

        return $balances;
    }

    /**
     * Проверка возможности закрытия смены
     */
    public function canClose(): array
    {
        $errors = [];

        // Проверяем, что все платежи подтверждены
        $pendingPayments = $this->payments()->pending()->count();
        if ($pendingPayments > 0) {
            $errors[] = "Есть {$pendingPayments} неподтвержденных платежей";
        }

        // Проверяем, что все кредиты подтверждены
        $pendingCredits = $this->credits()->pending()->count();
        if ($pendingCredits > 0) {
            $errors[] = "Есть {$pendingCredits} неподтвержденных кредитов";
        }

        // Проверяем, что все инкассации подтверждены
        $pendingIncashes = $this->incashes()->pending()->count();
        if ($pendingIncashes > 0) {
            $errors[] = "Есть {$pendingIncashes} неподтвержденных инкассаций";
        }

        return [
            'can_close' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Статистика по операциям смены
     */
    public function getTotalOperations(): array
    {
        return [
            'payments' => [
                'count' => $this->payments()->count(),
                'total' => $this->payments()->sum('total'),
            ],
            'exchanges' => [
                'count' => $this->exchanges()->count(),
                'total_uzs' => $this->exchanges()->sum('uzs_amount'),
            ],
            'credits' => [
                'count' => $this->credits()->count(),
                'total' => $this->credits()->sum('debit'),
            ],
            'incashes' => [
                'count' => $this->incashes()->count(),
                'total' => $this->incashes()->sum('amount'),
            ],
        ];
    }
}
