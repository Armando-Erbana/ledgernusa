<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'company_id','plan_id','status','billing_cycle',
        'trial_ends_at','starts_at','ends_at','cancelled_at',
    ];
    protected $casts = [
        'trial_ends_at' => 'date',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }

    public function isActive(): bool
    {
        if (in_array($this->status, ['expired', 'cancelled'])) return false;
        if ($this->status === 'trial') {
            return $this->trial_ends_at && $this->trial_ends_at->isFuture();
        }
        return $this->ends_at && $this->ends_at->isFuture();
    }

    public function daysRemaining(): int
    {
        $end = $this->status === 'trial' ? $this->trial_ends_at : $this->ends_at;
        return $end ? max(0, now()->diffInDays($end, false)) : 0;
    }
}