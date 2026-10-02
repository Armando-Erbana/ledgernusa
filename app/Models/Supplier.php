<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, BelongsToCompany;
    protected $fillable = ['company_id','code','name','npwp','email','phone','address','payment_term_days','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function company() { return $this->belongsTo(Company::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }
}