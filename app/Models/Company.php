<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name','npwp','currency','fiscal_year_start','address','logo','status'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users')
                    ->withPivot('role')->withTimestamps();
    }
}