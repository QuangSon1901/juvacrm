<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractPayment extends Model
{
    use HasFactory;
    protected $table = 'tbl_contract_payments';
    protected $fillable = [
        'contract_id', // Thêm trường này
        'name',
        'price',
        'currency_id',
        'method_id',
        'payment_stage',
        'status',
        'due_date',
        'created_id',
        'is_active',
    ];

    /**
     * Quan hệ với model Currency
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Quan hệ với model PaymentMethod (nếu cần cho $payment->method->name)
     */
    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'payment_id');
    }

    public function hasTransaction()
    {
        return $this->transaction()->exists();
    }

    /**
     * Tạo giao dịch từ biên nhận thanh toán
     * 
     * @return Transaction
     */
    public function createTransaction()
    {
        if ($this->hasTransaction()) {
            return $this->transaction;
        }
        
        $transactionType = $this->payment_stage == 3 ? 1 : 0; // Stage 3 (Khấu trừ) là chi, còn lại là thu
        
        $categoryName = $this->payment_stage == 0 ? 'Đặt cọc' : (
            $this->payment_stage == 1 ? 'Tiền thưởng' : (
                $this->payment_stage == 2 ? 'Thanh toán cuối cùng' : 'Khấu trừ'
            )
        );
        
        // Tìm hoặc tạo danh mục
        $category = TransactionCategory::firstOrCreate(
            ['type' => $transactionType, 'name' => $categoryName],
            ['note' => "Hạng mục cho {$categoryName}", 'is_active' => 1]
        );
        
        // Tạo giao dịch
        $transaction = Transaction::create([
            'type' => $transactionType,
            'category_id' => $category->id,
            'target_client_id' => $this->contract->provider_id,
            'payment_id' => $this->id,
            'amount' => $this->price,
            'paid_date' => $this->due_date ?? date('Y-m-d H:i:s'),
            'status' => $this->status, // Kết thừa trạng thái từ biên nhận
            'reason' => $this->name,
            'note' => "Tự động tạo từ biên nhận #{$this->id} của hợp đồng #{$this->contract->contract_number}",
        ]);
        
        return $transaction;
    }
    
    /**
     * Xóa giao dịch liên kết
     * 
     * @return bool
     */
    public function deleteTransaction()
    {
        if ($this->hasTransaction()) {
            return $this->transaction()->delete();
        }
        
        return true;
    }
    
    /**
     * Lấy text hiển thị cho giai đoạn thanh toán
     * 
     * @return string
     */
    public function getPaymentStageText()
    {
        switch ($this->payment_stage) {
            case 0:
                return 'Đặt cọc';
            case 1:
                return 'Tiền thưởng';
            case 2:
                return 'Thanh toán cuối cùng';
            case 3:
                return 'Trừ tiền';
            default:
                return 'Không xác định';
        }
    }
}
