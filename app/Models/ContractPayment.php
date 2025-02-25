<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractPayment extends Model
{
    use HasFactory;
    protected $table = 'tbl_contract_payments';
    protected $fillable = [
        'contract_id', // Thêm trường này
        'name',
        'price',
        'currency_id',
        'method_id',
        'payment_stage',
        'status',
        'due_date',
        'created_id',
        'is_active',
    ];

    /**
     * Quan hệ với model Currency
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Quan hệ với model PaymentMethod (nếu cần cho $payment->method->name)
     */
    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
