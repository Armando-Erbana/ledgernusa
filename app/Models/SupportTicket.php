<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'company_id', 
        'user_id', 
        'subject', 
        'message', 
        'status', 
        'reply'
    ];

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function company() 
    { 
        return $this->belongsTo(Company::class); 
    }
}