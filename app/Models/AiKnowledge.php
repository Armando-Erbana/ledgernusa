<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiKnowledge extends Model
{
    use HasFactory;

    protected $table = 'ai_knowledge';

    protected $fillable = [
        'keywords', 'question', 'answer',
        'action_url', 'action_label', 'category',
        'hits', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
    ];
}