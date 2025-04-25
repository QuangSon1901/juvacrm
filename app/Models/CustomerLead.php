<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLead extends Model
{
    use HasFactory;

    protected $table = 'tbl_customer_lead';
    protected $fillable = [
        'name',
        'description',
        'type',
        'sort',
        'color',
        'is_active',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'status_id');
    }
}
