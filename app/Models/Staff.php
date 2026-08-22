<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'staff';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'role_id',
        'full_name',
        'email',
        'password_hash',
        'pin_hash',
        'remember_token',
        'active'
    ];

    protected $hidden = [
        'password_hash',
        'pin_hash',
        'remember_token',
    ];

    protected $casts = [
        'id' => 'string',
        'active' => 'boolean',
        'password_hash' => 'hashed',
    ];

    public function getAuthPassword() {
        return $this->password_hash;
    }

    public function initials(): string
    {
        $names = explode(' ', $this->full_name);
        $initials = '';
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        return $initials ?: '?';
    }

    public function role() { return $this->belongsTo(Role::class); }
    public function stores() { return $this->belongsToMany(Store::class, 'staff_stores')->withPivot('is_primary'); }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->role?->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'Super Admin';
    }

    protected $cachedActiveStore = false;

    /**
     * Get the active store for the staff member, with in-memory caching per request.
     */
    public function getActiveStore()
    {
        if ($this->cachedActiveStore !== false) {
            return $this->cachedActiveStore;
        }

        $primaryStore = $this->stores()->wherePivot('is_primary', true)->first();
        $this->cachedActiveStore = $primaryStore ?: $this->stores()->first();
        
        return $this->cachedActiveStore;
    }
}
