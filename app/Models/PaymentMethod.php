<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'tbl_payment_methods';
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public function payments()
    {
        return $this->hasMany(ContractPayment::class, 'method_id');
    }

    public static function getActivePaymentMethods()
    {
        return self::where('is_active', 1)->get();
    }
}
