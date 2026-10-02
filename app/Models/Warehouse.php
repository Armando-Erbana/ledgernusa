<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use BelongsToCompany;
    protected $fillable = ['company_id','code','name','address','is_default','is_active'];
    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean'];

    public function movements() { return $this->hasMany(StockMovement::class); }
}