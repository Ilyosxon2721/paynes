<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $fillable = [
        'login',
        'password',
        'full_name',
        'added_by',
        'position',
        'reward_percentage',
        'branch',
        'status',
        'phone',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'reward_percentage' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Get payments created by this agent
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'agent_id');
    }
}
