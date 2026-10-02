<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

   protected $fillable = [
    'sale_id', 'product_id', 'warehouse_id', 'account_id', 'description',
    'qty', 'price', 'discount', 'subtotal', 'cost_price', 'total_cost',
];

protected $casts = [
    'qty' => 'decimal:2',
    'price' => 'decimal:2',
    'discount' => 'decimal:2',
    'subtotal' => 'decimal:2',
    'cost_price' => 'decimal:4',
    'total_cost' => 'decimal:2',
];

public function product() { return $this->belongsTo(Product::class); }
public function warehouse() { return $this->belongsTo(Warehouse::class); }

    public function sale() { return $this->belongsTo(Sale::class); }
    public function account() { return $this->belongsTo(Account::class); }
}