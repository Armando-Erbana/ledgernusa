<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Division extends Model {
    protected $fillable = ['company_id', 'name'];
    public function positions() { return $this->hasMany(Position::class); }
}