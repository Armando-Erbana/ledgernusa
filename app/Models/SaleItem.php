<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'account_id', 'description', 'qty', 'price', 'discount', 'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function sale() { return $this->belongsTo(Sale::class); }
    public function account() { return $this->belongsTo(Account::class); }
}