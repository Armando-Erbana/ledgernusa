<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    protected $fillable = ['delivery_order_id','sales_order_item_id','product_id','warehouse_id','description','qty'];
    protected $casts = ['qty' => 'decimal:2'];

    public function deliveryOrder() { return $this->belongsTo(DeliveryOrder::class); }
    public function salesOrderItem() { return $this->belongsTo(SalesOrderItem::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}