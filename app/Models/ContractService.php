<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractService extends Model
{
    protected $table = 'tbl_contract_services';
    protected $fillable = ['contract_id', 'service_id', 'quantity', 'price', 'note', 'is_active'];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}