<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    protected $table = 'tbl_salary_advances';
    
    protected $fillable = [
        'user_id',
        'amount',
        'request_date',
        'reason',
        'status',
        'approver_id',
        'approval_date',
        'transaction_id',
        'note',
    ];
    
    protected $casts = [
        'request_date' => 'date',
        'approval_date' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}