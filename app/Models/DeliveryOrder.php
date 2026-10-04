<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'company_id','sales_order_id','do_number','customer_id','date',
        'shipping_address','driver_name','vehicle_number','status','notes',
        'delivered_by','delivered_at','invoice_id',
    ];
    protected $casts = ['date' => 'date','delivered_at' => 'datetime'];

    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(DeliveryOrderItem::class); }
    public function invoice() { return $this->belongsTo(Sale::class, 'invoice_id'); }
}