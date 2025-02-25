<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    protected $table = 'tbl_transaction_categories';

    protected $fillable = [
        'type', // 0: thu, 1: chi
        'name',
        'note',
        'is_active',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}