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

    /**
     * Lấy giao dịch thanh toán liên quan đến hợp đồng
     */
    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            ContractPayment::class,
            'contract_id', // Khóa ngoại trên bảng ContractPayment
            'payment_id',  // Khóa ngoại trên bảng Transaction
            'id',          // Khóa chính trên bảng Contract
            'id'           // Khóa chính trên bảng ContractPayment
        );
    }
    
    /**
     * Tính tổng giá trị đã thanh toán của hợp đồng
     * 
     * @return float
     */
    public function getTotalPaidAmount()
    {
        // Tổng các khoản thu - Tổng các khoản chi
        return $this->payments()
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('payment_stage', '!=', 3)
            ->sum('price') 
            - 
            $this->payments()
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('payment_stage', 3)
            ->sum('price');
    }
    
    /**
     * Tính số tiền còn lại phải thanh toán
     * 
     * @return float
     */
    public function getRemainingAmount()
    {
        $totalPaid = $this->getTotalPaidAmount();
        return max(0, $this->total_value - $totalPaid);
    }
    
    /**
     * Kiểm tra hợp đồng đã thanh toán đủ chưa
     * 
     * @return bool
     */
    public function isFullyPaid()
    {
        return $this->getRemainingAmount() == 0;
    }
}