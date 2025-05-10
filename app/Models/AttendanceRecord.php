<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $table = 'tbl_attendance_records';
    
    protected $fillable = [
        'user_id',
        'check_in_time',
        'check_out_time',
        'work_date',
        'total_hours',
        'status',
        'note',
        'schedule_id',
    ];
    
    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'work_date' => 'date',
    ];
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function schedule()
    {
        return $this->belongsTo(PartTimeSchedule::class, 'schedule_id');
    }
    
    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        if($startDate) {
            $query->where('work_date', '>=', $startDate);
        }
        
        if($endDate) {
            $query->where('work_date', '<=', $endDate);
        }
        
        return $query;
    }
    
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    public function scopeToday($query)
    {
        return $query->where('work_date', Carbon::today()->toDateString());
    }
    
    public function scopeThisMonth($query)
    {
        $firstDayOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $lastDayOfMonth = Carbon::now()->endOfMonth()->toDateString();
        
        return $query->whereBetween('work_date', [$firstDayOfMonth, $lastDayOfMonth]);
    }
    
    // Helper methods
    public static function calculateTotalHours($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            return 0;
        }
        
        if (is_string($checkIn)) {
            $checkIn = Carbon::parse($checkIn);
        }
        
        if (is_string($checkOut)) {
            $checkOut = Carbon::parse($checkOut);
        }
        
        // Đảm bảo checkout không sớm hơn checkin
        if ($checkOut->lt($checkIn)) {
            return 0;
        }
        
        $diffInSeconds = $checkOut->diffInSeconds($checkIn);
        
        // Chuyển đổi giây thành giờ với hai chữ số thập phân
        return round($diffInSeconds / 3600, 2);
    }
    
    // Kiểm tra đi trễ
    public function isLate()
    {
        return $this->status === 'late' || $this->status === 'late_and_early_leave';
    }
    
    // Kiểm tra về sớm
    public function isEarlyLeave()
    {
        return $this->status === 'early_leave' || $this->status === 'late_and_early_leave';
    }
    
    // Xác định trạng thái hiển thị
    public function getStatusText()
    {
        switch ($this->status) {
            case 'present':
                return 'Có mặt';
            case 'absent':
                return 'Vắng mặt';
            case 'late':
                return 'Đi trễ';
            case 'early_leave':
                return 'Về sớm';
            case 'late_and_early_leave':
                return 'Đi trễ và về sớm';
            default:
                return ucfirst($this->status);
        }
    }
    
    // Lấy class CSS cho hiển thị
    public function getStatusClass()
    {
        switch ($this->status) {
            case 'present':
                return 'success';
            case 'absent':
                return 'danger';
            case 'late':
                return 'warning';
            case 'early_leave':
                return 'info';
            case 'late_and_early_leave':
                return 'danger';
            default:
                return 'gray';
        }
    }
}