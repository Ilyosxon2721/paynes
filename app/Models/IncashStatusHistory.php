<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncashStatusHistory extends Model
{
    protected $fillable = [
        'incash_id',
        'old_status',
        'new_status',
        'changed_by',
        'comment',
    ];

    public function incash(): BelongsTo
    {
        return $this->belongsTo(Incash::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
