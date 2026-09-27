<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = ['name','npwp','currency','fiscal_year_start','address','logo','status'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users')
                    ->withPivot('role')->withTimestamps();
    }
    public function accounts() { return $this->hasMany(Account::class); }
    public function contacts() { return $this->hasMany(Contact::class); }
    public function journals() { return $this->hasMany(Journal::class); }
}