<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
    'purchase_id', 'product_id', 'warehouse_id', 'account_id', 'description',
    'qty', 'price', 'discount', 'subtotal',
];

public function product() { return $this->belongsTo(Product::class); }
public function warehouse() { return $this->belongsTo(Warehouse::class); }
} 