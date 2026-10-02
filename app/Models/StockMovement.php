<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id','product_id','warehouse_id','date','type','reference','description',
        'qty','unit_cost','total_cost','stock_before','stock_after',
        'source_type','source_id','created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'qty' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'stock_before' => 'decimal:4',
        'stock_after' => 'decimal:4',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}