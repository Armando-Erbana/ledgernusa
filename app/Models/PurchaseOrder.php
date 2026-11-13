<?php
namespace App\Models;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'company_id','branch_id','po_number','supplier_id','date','expected_date','status','approval_status','current_approver_position_id',
        'subtotal','discount','tax','total','notes','created_by','confirmed_by','confirmed_at',
    ];
    protected $casts = [
        'date' => 'date', 'expected_date' => 'date', 'confirmed_at' => 'datetime',
        'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function goodsReceipts() { return $this->hasMany(GoodsReceipt::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approvalLogs() { return $this->morphMany(ApprovalLog::class, 'approvable'); }
    public function isFullyReceived(): bool { return $this->items->every(fn($i) => $i->received_qty >= $i->qty); }
}