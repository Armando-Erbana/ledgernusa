<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;
    protected $fillable = ['purchase_id','account_id','description','qty','price','discount','subtotal'];
    protected $casts = ['qty' => 'decimal:2','price' => 'decimal:2','discount' => 'decimal:2','subtotal' => 'decimal:2'];

    public function purchase() { return $this->belongsTo(Purchase::class); }
    public function account() { return $this->belongsTo(Account::class); }
} 