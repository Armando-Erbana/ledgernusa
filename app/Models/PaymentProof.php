<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    protected $fillable = [
        'company_id', 'plan_id', 'user_id',
        'amount', 'transfer_date', 'bank_from', 'account_name',
        'proof_file', 'notes',
        'status', 'verified_by', 'verified_at', 'rejection_reason',
        'subscription_id',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'verified_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function verifier(): BelongsTo { return $this->belongsTo(User::class, 'verified_by'); }
    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}