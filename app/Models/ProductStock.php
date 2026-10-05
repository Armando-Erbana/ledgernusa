<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use BelongsToCompany;
    protected $fillable = ['company_id','product_id','warehouse_id','stock','min_stock'];
    protected $casts = ['stock' => 'decimal:4', 'min_stock' => 'decimal:4'];

    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}