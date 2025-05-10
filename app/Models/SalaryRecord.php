<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $table = 'tbl_salary_records';
    
    protected $fillable = [
        'user_id',
        'period_month',
        'period_year',
        'base_salary',
        'commission_amount',
        'task_mission_amount',
        'tax_amount',
        'insurance_amount',
        'deductions',
        'final_amount',
        'status',
        'transaction_id',
        'created_by',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Tính tổng thu nhập cuối cùng
     */
    public function calculateFinalAmount()
    {
        $this->deductions = $this->tax_amount + $this->insurance_amount;
        
        $this->final_amount = $this->base_salary + 
                              $this->commission_amount +
                              $this->task_mission_amount -
                              $this->deductions;
        
        return $this->final_amount;
    }
}