<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Fields that can be mass assigned
    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'role',
        'is_verified',          // ← added
    ];

    // Fields hidden from JSON/array output
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'role'              => 'string',
        'is_verified'       => 'boolean',     // ← recommended: treats 0/1 as true/false
    ];

    // Helper methods for role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    // Optional: nice helper for verification status
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    public function markAsVerified(): void
    {
        $this->update(['is_verified' => true]);
    }
}