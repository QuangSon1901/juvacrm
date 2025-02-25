<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'tbl_transactions';

    protected $fillable = [
        'type', // 0: thu, 1: chi
        'category_id',
        'target_employee_id',
        'target_client_id',
        'target_other',
        'payment_id',
        'amount',
        'paid_date',
        'status', // 0: chờ, 1: hoàn tất, 2: đã hủy
        'note',
        'reason',
    ];

    protected $dates = ['paid_date'];

    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function payment()
    {
        return $this->belongsTo(ContractPayment::class, 'payment_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'target_employee_id');
    }

    public function client()
    {
        return $this->belongsTo(Customer::class, 'target_client_id');
    }
}