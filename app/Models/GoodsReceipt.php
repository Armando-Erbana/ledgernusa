<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'company_id','purchase_order_id','gr_number','supplier_id','date',
        'received_by_name','status','notes','received_by','received_at','bill_id',
    ];
    protected $casts = ['date' => 'date','received_at' => 'datetime'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(GoodsReceiptItem::class); }
    public function bill() { return $this->belongsTo(Purchase::class, 'bill_id'); }
}