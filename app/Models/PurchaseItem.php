<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'warehouse_id',
        'account_id',
        'description',
        'qty',
        'price',
        'discount',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'float',
        'price' => 'float',
        'discount' => 'float',
        'subtotal' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // TAMBAHKAN METHOD INI AGAR ERROR HILANG
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}