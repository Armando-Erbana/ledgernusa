<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    protected $fillable = [
        'payroll_id','employee_id','basic_salary','allowance','other_income','gross_salary',
        'pph21','bpjs_kesehatan','bpjs_ketenagakerjaan','other_deduction','total_deduction','net_salary','notes',
    ];
    protected $casts = [
        'basic_salary' => 'decimal:2','allowance' => 'decimal:2','other_income' => 'decimal:2',
        'gross_salary' => 'decimal:2','pph21' => 'decimal:2',
        'bpjs_kesehatan' => 'decimal:2','bpjs_ketenagakerjaan' => 'decimal:2',
        'other_deduction' => 'decimal:2','total_deduction' => 'decimal:2','net_salary' => 'decimal:2',
    ];

    public function payroll() { return $this->belongsTo(Payroll::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}