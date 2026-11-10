<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'account_id', 
        'period_month', 'period_year', 'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Otomatis isi branch_id saat membuat budget baru (kecuali sedang mode konsolidasi)
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->branch_id)) {
                $model->branch_id = session('branch_id');
            }
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}