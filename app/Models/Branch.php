<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'code',
        'address',
        'phone',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the client that owns the branch
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get users belonging to this branch (cashiers)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get cashiers of this branch
     */
    public function cashiers(): HasMany
    {
        return $this->hasMany(User::class)->where('position', 'cashier');
    }

    /**
     * Get managers of this branch (many-to-many)
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'branch_manager', 'branch_id', 'user_id')
            ->withTimestamps()
            ->where('position', 'manager');
    }

    /**
     * Scope active branches
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
