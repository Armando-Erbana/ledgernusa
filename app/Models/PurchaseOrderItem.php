<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id','product_id','warehouse_id','account_id','description','qty','price','discount','subtotal','received_qty'];
    protected $casts = ['qty' => 'decimal:2','price' => 'decimal:2','discount' => 'decimal:2','subtotal' => 'decimal:2','received_qty' => 'decimal:2'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function account() { return $this->belongsTo(Account::class); }

    public function remainingQty(): float { return (float) $this->qty - (float) $this->received_qty; }
}