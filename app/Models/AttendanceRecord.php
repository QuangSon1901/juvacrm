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
        'late_minutes',
        'early_leave_minutes',
        'overtime_hours',
        'valid_hours',
        'status',
        'note',
        'late_reason',
        'early_leave_reason',
        'forgot_checkout',
        'forgot_checkout_reason',
        'schedule_id',
    ];
    
    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'work_date' => 'date',
        'forgot_checkout' => 'boolean',
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
    
    public function scopeIncomplete($query)
    {
        return $query->whereNotNull('check_in_time')
                    ->whereNull('check_out_time')
                    ->where('work_date', '<', Carbon::today()->toDateString());
    }
    
    // Helper methods
    public static function calculateTotalHours($checkIn, $checkOut, $breakTime = null)
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
        
        // Nếu có thời gian nghỉ trưa, trừ đi
        if ($breakTime) {
            $breakTimeInSeconds = 0;
            
            // Parse thời gian nghỉ từ định dạng '12:00-13:00'
            if (strpos($breakTime, '-') !== false) {
                list($startBreak, $endBreak) = explode('-', $breakTime);
                
                $breakStart = Carbon::parse($checkIn->format('Y-m-d') . ' ' . $startBreak);
                $breakEnd = Carbon::parse($checkIn->format('Y-m-d') . ' ' . $endBreak);
                
                // Kiểm tra thời gian checkin và checkout có giao với thời gian nghỉ
                if ($checkIn->lt($breakEnd) && $checkOut->gt($breakStart)) {
                    // Tính thời gian giao nhau
                    $overlapStart = $checkIn->gt($breakStart) ? $checkIn : $breakStart;
                    $overlapEnd = $checkOut->lt($breakEnd) ? $checkOut : $breakEnd;
                    
                    if ($overlapEnd->gt($overlapStart)) {
                        $breakTimeInSeconds = $overlapEnd->diffInSeconds($overlapStart);
                    }
                }
            }
            
            $diffInSeconds -= $breakTimeInSeconds;
        }
        
        // Chuyển giây thành giờ với hai chữ số thập phân
        return max(0, round($diffInSeconds / 3600, 2));
    }
    
    // Kiểm tra đi trễ
    public function isLate()
    {
        return $this->status === 'late' || $this->status === 'late_and_early_leave';
    }
    
    // Kiểm tra về sớm
    // Kiểm tra về sớm
    public function isEarlyLeave()
    {
        return $this->status === 'early_leave' || $this->status === 'late_and_early_leave';
    }
    
    // Kiểm tra có tăng ca không
    public function hasOvertime()
    {
        return $this->overtime_hours > 0;
    }
    
    // Kiểm tra quên checkout
    public function isForgotCheckout()
    {
        return $this->forgot_checkout;
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