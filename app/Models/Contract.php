<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'tbl_contracts';
    protected $fillable = [
        'contract_number', 'name', 'user_id', 'provider_id', 'category_id',
        'company_name', 'tax_code', 'company_address', 'customer_representative',
        'customer_tax_code', 'address', 'phone', 'sign_date', 'effective_date',
        'expiry_date', 'estimate_day', 'estimate_date', 'total_value', 'note',
        'terms_and_conditions', 'created_id', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provider()
    {
        return $this->belongsTo(Customer::class, 'provider_id');
    }

    public function services()
    {
        return $this->hasMany(ContractService::class, 'contract_id')->where('is_active', 1);
    }

    public function payments()
    {
        return $this->hasMany(ContractPayment::class, 'contract_id')->where('is_active', 1);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'contract_id')->where('is_active', 1);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_id');
    }
}