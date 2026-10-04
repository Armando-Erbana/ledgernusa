<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'company_id','so_number','customer_id','date','expected_date','status',
        'subtotal','discount','tax','total','notes','created_by','confirmed_by','confirmed_at',
    ];
    protected $casts = [
        'date' => 'date', 'expected_date' => 'date', 'confirmed_at' => 'datetime',
        'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(SalesOrderItem::class); }
    public function deliveryOrders() { return $this->hasMany(DeliveryOrder::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function isFullyDelivered(): bool
    {
        return $this->items->every(fn($i) => $i->delivered_qty >= $i->qty);
    }
}