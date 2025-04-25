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

    protected $dates = [
        'paid_date',
        'created_at',
        'updated_at',
    ];

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
    
    public function targetClient()
    {
        return $this->belongsTo(Customer::class, 'target_client_id');
    }
    
    public function targetEmployee()
    {
        return $this->belongsTo(User::class, 'target_employee_id');
    }

    public static function totalIncome($from = null, $to = null)
    {
        $query = self::where('type', 0)->where('status', 1);
        
        if ($from) {
            $query->whereDate('paid_date', '>=', $from);
        }
        
        if ($to) {
            $query->whereDate('paid_date', '<=', $to);
        }
        
        return $query->sum('amount');
    }

    public static function totalExpense($from = null, $to = null)
    {
        $query = self::where('type', 1)->where('status', 1);
        
        if ($from) {
            $query->whereDate('paid_date', '>=', $from);
        }
        
        if ($to) {
            $query->whereDate('paid_date', '<=', $to);
        }
        
        return $query->sum('amount');
    }

    public static function balance($from = null, $to = null)
    {
        $totalIncome = self::totalIncome($from, $to);
        $totalExpense = self::totalExpense($from, $to);
        
        return $totalIncome - $totalExpense;
    }

    public static function incomeByCategory($from = null, $to = null)
    {
        $query = self::where('type', 0)
            ->where('status', 1)
            ->with('category');
        
        if ($from) {
            $query->whereDate('paid_date', '>=', $from);
        }
        
        if ($to) {
            $query->whereDate('paid_date', '<=', $to);
        }
        
        return $query->get()
            ->groupBy('category_id')
            ->map(function ($items, $categoryId) {
                $category = TransactionCategory::find($categoryId);
                return [
                    'category_id' => $categoryId,
                    'category_name' => $category ? $category->name : 'Unknown',
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            })
            ->values();
    }

    public static function expenseByCategory($from = null, $to = null)
    {
        $query = self::where('type', 1)
            ->where('status', 1)
            ->with('category');
        
        if ($from) {
            $query->whereDate('paid_date', '>=', $from);
        }
        
        if ($to) {
            $query->whereDate('paid_date', '<=', $to);
        }
        
        return $query->get()
            ->groupBy('category_id')
            ->map(function ($items, $categoryId) {
                $category = TransactionCategory::find($categoryId);
                return [
                    'category_id' => $categoryId,
                    'category_name' => $category ? $category->name : 'Unknown',
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            })
            ->values();
    }
}