<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    // TAMBAHKAN 'branch_ids' DI SINI
    protected $fillable = [
        'company_id', 
        'email', 
        'role', 
        'branch_ids', // <-- Tambahan
        'token', 
        'invited_by', 
        'expires_at', 
        'accepted_at'
    ];

    // TAMBAHKAN CASTS UNTUK branch_ids
    protected $casts = [
        'branch_ids' => 'array', // <-- Tambahan (agar JSON otomatis jadi array)
        'expires_at' => 'datetime', 
        'accepted_at' => 'datetime'
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function inviter() { return $this->belongsTo(User::class, 'invited_by'); }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isAccepted();
    }
}