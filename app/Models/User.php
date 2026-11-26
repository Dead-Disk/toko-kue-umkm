<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'can_chat'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Apakah user diizinkan mengakses dan membalas chat pembeli?
     * Owner & Admin selalu punya izin penuh; Staf tergantung flag can_chat.
     */
    public function canAccessChat(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return (bool) $this->can_chat;
    }

    /**
     * Apakah user adalah owner atau admin (pemilik toko)?
     */
    public function isOwner(): bool
    {
        return in_array($this->role, ['owner', 'admin'], true);
    }

    /**
     * Apakah user adalah admin (pemilik toko)?
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'owner'], true);
    }

    /**
     * Apakah user adalah staff?
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Riwayat aktivitas yang dilakukan pengguna ini.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_chat' => 'boolean',
        ];
    }
}
