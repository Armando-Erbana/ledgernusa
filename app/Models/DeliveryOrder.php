<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'branch_id', 'sales_order_id', 'do_number', 'date', 'status', 'notes', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function items() { return $this->hasMany(DeliveryOrderItem::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}