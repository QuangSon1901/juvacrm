<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_consultation_logs';

    protected $fillable = [
        'consultation_id',
        'user_id',
        'message',
        'action',
        'follow_up_date', // Trường mới
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'follow_up_date' => 'datetime',
    ];
    
    // Quan hệ với Consultation
    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }
    
    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Lấy tệp đính kèm
    public function getAttachmentsArray()
    {
        return Upload::where('fk_key', 'tbl_consultation_logs|id')
            ->where('fk_value', $this->id)
            ->get()
            ->toArray();
    }
    
    // Các trạng thái của nhật ký tư vấn
    const ACTION_ASK_NEEDS = 0;       // Hỏi nhu cầu khách hàng
    const ACTION_CONSULT = 1;         // Tư vấn gói
    const ACTION_CONTRACT = 2;        // Lập hợp đồng
    const ACTION_PRICE_LIST = 3;      // Gửi bảng giá
    const ACTION_CUSTOMER_REJECT = 4; // Khách từ chối
    const ACTION_FOLLOW_UP = 5;       // Đặt lịch tư vấn lại
    
    // Lấy tên trạng thái
    public static function getActionName($action)
    {
        switch ($action) {
            case self::ACTION_ASK_NEEDS:
                return 'Hỏi nhu cầu khách hàng';
            case self::ACTION_CONSULT:
                return 'Tư vấn gói';
            case self::ACTION_CONTRACT:
                return 'Lập hợp đồng';
            case self::ACTION_PRICE_LIST:
                return 'Gửi bảng giá';
            case self::ACTION_CUSTOMER_REJECT:
                return 'Khách từ chối';
            case self::ACTION_FOLLOW_UP:
                return 'Đặt lịch tư vấn lại';
            default:
                return 'Không xác định';
        }
    }
    
    // Lấy màu tương ứng với trạng thái
    public static function getActionColor($action)
    {
        switch ($action) {
            case self::ACTION_ASK_NEEDS:
                return 'primary';
            case self::ACTION_CONSULT:
                return 'warning';
            case self::ACTION_CONTRACT:
                return 'success';
            case self::ACTION_PRICE_LIST:
                return 'info';
            case self::ACTION_CUSTOMER_REJECT:
                return 'danger';
            case self::ACTION_FOLLOW_UP:
                return 'gray';
            default:
                return 'neutral';
        }
    }

    public function getServicesArray()
    {
        if (empty($this->services)) {
            return [];
        }

        return Service::whereIn('id', explode('|', $this->services))->get()->toArray();
    }
}
