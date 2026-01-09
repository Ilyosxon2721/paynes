<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * Determine if the user can access the given panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Только администраторы могут получить доступ к админ-панели
        return $this->hasRole('admin') && $this->status === 'active';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'login',
        'email',
        'phone',
        'full_name',
        'password',
        'position',
        'status',
        'branch',
        'reward_percentage',
        'tariff',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'reward_percentage' => 'decimal:2',
        ];
    }

    /**
     * Get the user's payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'cashier_id');
    }

    /**
     * Get the user's exchanges.
     */
    public function exchanges()
    {
        return $this->hasMany(Exchange::class, 'cashier_id');
    }

    /**
     * Get the user's credits.
     */
    public function credits()
    {
        return $this->hasMany(Credit::class, 'cashier_id');
    }

    public function ticketsCreated()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function ticketsAssigned()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Get the user's incashes.
     */
    public function incashes()
    {
        return $this->hasMany(Incash::class, 'cashier_id');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    /**
     * Scope a query to only include cashier users.
     */
    public function scopeCashiers($query)
    {
        return $query->role('cashier');
    }

    /**
     * Get the is admin attribute.
     */
    public function getIsAdminAttribute()
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    /**
     * Filament display name for the user menu/avatar.
     */
    public function getFilamentName(): string
    {
        return $this->full_name ?: ($this->login ?: 'User');
    }
}
