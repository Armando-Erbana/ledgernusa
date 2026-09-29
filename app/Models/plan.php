<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'code','name','description','price_monthly','price_yearly',
        'max_users','max_journals_per_month','max_companies','features','is_active','sort_order',
    ];
    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function subscriptions() { return $this->hasMany(Subscription::class); }
}