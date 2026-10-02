<?php

namespace App\Models\Concerns;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        // Otomatis filter query berdasarkan company_id di session
        static::addGlobalScope('company', function ($query) {
            if (session('company_id')) {
                $query->where('company_id', session('company_id'));
            }
        });

        // Otomatis isi company_id saat create
        static::creating(function ($model) {
            if (session('company_id') && empty($model->company_id)) {
                $model->company_id = session('company_id');
            }
        });
    }
}