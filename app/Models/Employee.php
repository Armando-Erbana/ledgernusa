<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'company_id','employee_number','name','npwp','ktp','position','department',
        'join_date','resign_date','employment_type','ptkp_status',
        'basic_salary','fixed_allowance','bank_name','bank_account_number','is_active',
    ];
    protected $casts = [
        'join_date' => 'date','resign_date' => 'date',
        'basic_salary' => 'decimal:2','fixed_allowance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function payrollItems() { return $this->hasMany(PayrollItem::class); }

    public function ptkpAmount(): float
    {
        return match ($this->ptkp_status) {
            'TK/0' => 54_000_000,
            'TK/1' => 58_500_000,
            'TK/2' => 63_000_000,
            'TK/3' => 67_500_000,
            'K/0'  => 58_500_000,
            'K/1'  => 63_000_000,
            'K/2'  => 67_500_000,
            'K/3'  => 72_000_000,
            default => 54_000_000,
        };
    }
}