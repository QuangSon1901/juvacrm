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
        'attendance_bonus',
        'overtime_hours',
        'overtime_amount',
        'commission_amount',
        'task_mission_amount',
        'deductions',
        'tax_amount',
        'insurance_amount',
        'advance_payments',
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
    
    public function calculateFinalAmount()
    {
        $this->final_amount = $this->base_salary + 
                              $this->attendance_bonus + 
                              $this->overtime_amount + 
                              $this->commission_amount +
                              $this->task_mission_amount -
                              $this->deductions - 
                              $this->tax_amount - 
                              $this->insurance_amount - 
                              $this->advance_payments;
        
        return $this->final_amount;
    }
}