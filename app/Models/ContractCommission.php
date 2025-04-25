<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractCommission extends Model
{
    protected $table = 'tbl_contract_commissions';
    
    protected $fillable = [
        'contract_id',
        'user_id',
        'commission_percentage',
        'commission_amount',
        'contract_value',
        'processed_at',
        'is_paid',
        'transaction_id'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
    
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}