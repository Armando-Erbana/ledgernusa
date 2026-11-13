<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model {
    protected $fillable = ['company_id', 'approvable_id', 'approvable_type', 'user_id', 'action', 'notes'];
    public function user() { return $this->belongsTo(User::class); }
}