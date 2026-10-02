<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'code', 'name', 'npwp', 'email', 'phone',
        'address', 'credit_limit', 'payment_term_days', 'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function sales() { return $this->hasMany(Sale::class); }
}