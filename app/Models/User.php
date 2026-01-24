<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        if ($this->status !== 'active') {
            return false;
        }

        // Админ-панель Paynes (супер-админы системы)
        if ($panel->getId() === 'admin') {
            return $this->position === 'admin' && $this->hasRole('admin');
        }

        // Merchant-панель (для всех ролей компании)
        if ($panel->getId() === 'client') {
            // Должен быть привязан к компании
            if ($this->client_id === null) {
                return false;
            }

            // Проверяем подписку компании
            if (!$this->client || !$this->client->hasActiveSubscription()) {
                return false;
            }

            // Разрешаем доступ для: client_admin, manager, cashier
            return in_array($this->position, ['client_admin', 'manager', 'cashier']);
        }

        return false;
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
        'client_id',
        'branch_id',
        'is_client_admin',
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
            'is_client_admin' => 'boolean',
        ];
    }

    /**
     * Get the client that owns the user
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the branch that the user belongs to (для кассиров)
     */
    public function branchRelation(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the branches that the manager manages (many-to-many для менеджеров)
     */
    public function managedBranches()
    {
        return $this->belongsToMany(Branch::class, 'branch_manager', 'user_id', 'branch_id')
            ->withTimestamps();
    }

    /**
     * Get the user's cashier shifts.
     */
    public function shifts()
    {
        return $this->hasMany(CashierShift::class, 'cashier_id');
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
     * Scope a query to only include manager users.
     */
    public function scopeManagers($query)
    {
        return $query->role('manager');
    }

    /**
     * Scope a query to only include client admin users.
     */
    public function scopeClientAdmins($query)
    {
        return $query->role('client_admin');
    }

    /**
     * Get the is admin attribute.
     */
    public function getIsAdminAttribute()
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the is client admin attribute.
     */
    public function getIsClientAdminAttribute()
    {
        return $this->hasRole('client_admin') || ($this->attributes['is_client_admin'] ?? false);
    }

    /**
     * Get the is manager attribute.
     */
    public function getIsManagerAttribute()
    {
        return $this->hasRole('manager');
    }

    /**
     * Get the is cashier attribute.
     */
    public function getIsCashierAttribute()
    {
        return $this->hasRole('cashier');
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
