<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalRule extends Model {
    protected $fillable = ['company_id', 'document_type', 'min_amount', 'max_amount', 'position_id', 'level'];
    public function position() { return $this->belongsTo(Position::class); }
}