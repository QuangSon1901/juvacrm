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

    /**
     * Lấy tất cả giao dịch thuộc danh mục này
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
    
    /**
     * Lấy danh sách danh mục thu
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getIncomeCategories()
    {
        return self::where('type', 0)->where('is_active', 1)->get();
    }
    
    /**
     * Lấy danh sách danh mục chi
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getExpenseCategories()
    {
        return self::where('type', 1)->where('is_active', 1)->get();
    }
}