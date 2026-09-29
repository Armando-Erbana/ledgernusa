<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_super_admin'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_super_admin' => 'boolean',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_users')
                    ->withPivot('role')->withTimestamps();
    }

    public function activeCompany()
    {
        return $this->companies()->where('companies.id', session('company_id'))->first();
    }

    public function roleInActiveCompany()
    {
        return $this->activeCompany()?->pivot->role;
    }

    public function isSuperAdmin(): bool
{
    return (bool) $this->is_super_admin;
}
}