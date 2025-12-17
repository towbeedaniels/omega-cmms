<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'employee_id',
        'phone',
        'facility_scope',
        'access_level',
        'last_login_at',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'facility_scope' => 'array',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function workOrdersAssigned()
    {
        return $this->hasMany(WorkOrder::class, 'assigned_to');
    }

    public function workOrdersRequested()
    {
        return $this->hasMany(WorkOrder::class, 'requested_by');
    }

    public function workOrderCompletions()
    {
        return $this->hasMany(WorkOrderCompletion::class, 'completed_by');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'created_by');
    }

    public function hasAccessToFacility($facilityId): bool
    {
        if ($this->access_level === 'admin') {
            return true;
        }

        $userFacilities = $this->facility_scope ?? [];
        return in_array($facilityId, $userFacilities);
    }

    public function isAdmin(): bool
    {
        return $this->access_level === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role_id === 2 || $this->access_level === 'edit';
    }

    public function isTechnician(): bool
    {
        return $this->role_id === 3;
    }
}