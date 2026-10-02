<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'npwp',
        'is_pkp',
        'ppn_rate',
        'ppn_included',
        'tax_office',
        'efin',
        'currency',
        'fiscal_year_start',
        'address',
        'logo',
        'status',
    ];

    protected $casts = [
        'is_pkp' => 'boolean',
        'ppn_included' => 'boolean',
        'ppn_rate' => 'decimal:2',
        'fiscal_year_start' => 'date',
    ];

    // ============ Relasi ============

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}