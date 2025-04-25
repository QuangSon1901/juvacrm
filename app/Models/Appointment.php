<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'tbl_appointments';

    protected $fillable = [
        'name',
        'note',
        'start_time',
        'end_time',
        'color',
        'is_active',
        'is_completed', // Trường mới
        'user_id',
        'customer_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'is_completed' => 'boolean',
    ];
    
    // Quan hệ với Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Các màu hợp lệ
    const COLOR_SUCCESS = 'success';
    const COLOR_WARNING = 'warning'; 
    const COLOR_PRIMARY = 'primary';
    const COLOR_GRAY = 'gray';
    const COLOR_DANGER = 'danger';
    const COLOR_NEUTRAL = 'neutral';
    
    // Các trạng thái
    const STATUS_PENDING = 0;    // Chưa hoàn thành
    const STATUS_COMPLETED = 1;  // Đã hoàn thành
    
    // Kiểm tra xem lịch hẹn đã qua chưa
    public function isPast()
    {
        return $this->end_time->isPast();
    }
    
    // Kiểm tra xem lịch hẹn là hôm nay không
    public function isToday()
    {
        return $this->start_time->isToday();
    }
    
    // Đánh dấu lịch hẹn đã hoàn thành
    public function markAsCompleted()
    {
        $this->is_completed = true;
        $this->save();
        
        // Cập nhật lần tương tác cuối cho khách hàng
        if ($this->customer) {
            $this->customer->updateLastInteraction();
        }
        
        return $this;
    }
    
    // Lấy lịch hẹn sắp tới
    public static function getUpcoming()
    {
        return self::where('start_time', '>=', Carbon::now())
            ->where('is_active', true)
            ->where('is_completed', false)
            ->orderBy('start_time', 'asc');
    }
    
    // Lấy lịch hẹn trong ngày
    public static function getToday()
    {
        return self::whereDate('start_time', Carbon::today())
            ->where('is_active', true)
            ->orderBy('start_time', 'asc');
    }
}
