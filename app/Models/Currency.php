<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $table = 'tbl_currencies';
    protected $fillable = [
        'name',
        'currency_code',
        'symbol',
        'is_active'
    ];
    public function payments()
    {
        return $this->hasMany(ContractPayment::class);
    }
}
