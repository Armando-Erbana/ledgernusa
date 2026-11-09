<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['company_id', 'name', 'code', 'address', 'is_main'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}