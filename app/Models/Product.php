<?php
namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id','category_id','code','name','unit','description','image',
        'inventory_account_id','sales_account_id','cogs_account_id',
        'cost_price','sell_price','stock','min_stock','track_stock','is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'stock' => 'decimal:4',
        'min_stock' => 'decimal:4',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function category() { return $this->belongsTo(ProductCategory::class, 'category_id'); }
    public function inventoryAccount() { return $this->belongsTo(Account::class, 'inventory_account_id'); }
    public function salesAccount() { return $this->belongsTo(Account::class, 'sales_account_id'); }
    public function cogsAccount() { return $this->belongsTo(Account::class, 'cogs_account_id'); }
    public function movements() { return $this->hasMany(StockMovement::class); }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock <= $this->min_stock;
    }
}