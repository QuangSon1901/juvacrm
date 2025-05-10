<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PartTimeSchedule extends Model
{
    protected $table = 'tbl_part_time_schedules';
    
    protected $fillable = [
        'user_id',
        'schedule_date',
        'start_time',
        'end_time',
        'total_hours',
        'status',
        'note',
        'approver_id',
        'approval_time',
    ];
    
    protected $casts = [
        'schedule_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approval_time' => 'datetime',
    ];

    public function getStartTimeAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
    
    public static function calculateTotalHours($startTime, $endTime)
    {
        if (!$startTime || !$endTime) {
            return 0;
        }
        
        $start = new \DateTime($startTime);
        $end = new \DateTime($endTime);
        $interval = $start->diff($end);
        
        return $interval->h + ($interval->i / 60);
    }
    
    // Scope để lọc theo người dùng
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope để lọc theo trạng thái
    public function scopeByStatus($query, $status)
    {
        if(is_array($status)) {
            return $query->whereIn('status', $status);
        }
        return $query->where('status', $status);
    }

    // Scope để lọc theo khoảng thời gian
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        if($startDate) {
            $query->where('schedule_date', '>=', $startDate);
        }
        
        if($endDate) {
            $query->where('schedule_date', '<=', $endDate);
        }
        
        return $query;
    }

    // Scope để lấy lịch chờ duyệt
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope để lấy lịch đã duyệt
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope để lấy lịch đã từ chối
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Scope để lấy lịch yêu cầu hủy
    public function scopeCancelRequested($query)
    {
        return $query->where('status', 'cancel_requested');
    }

    // Scope để lấy lịch trong tương lai
    public function scopeInFuture($query)
    {
        return $query->where('schedule_date', '>=', now()->format('Y-m-d'));
    }

    // Scope để lấy lịch đã qua
    public function scopeInPast($query)
    {
        return $query->where('schedule_date', '<', now()->format('Y-m-d'));
    }
    
    // Lấy tên trạng thái hiển thị
    public function getStatusText()
    {
        switch ($this->status) {
            case 'pending':
                return 'Chờ duyệt';
            case 'approved':
                return 'Đã duyệt';
            case 'rejected':
                return 'Từ chối';
            case 'cancel_requested':
                return 'Yêu cầu hủy';
            default:
                return ucfirst($this->status);
        }
    }
    
    // Lấy class CSS cho badge trạng thái
    public function getStatusClass()
    {
        switch ($this->status) {
            case 'pending':
                return 'warning';
            case 'approved':
                return 'success';
            case 'rejected':
                return 'danger';
            case 'cancel_requested':
                return 'info';
            default:
                return 'gray';
        }
    }
    
    // Kiểm tra có thể hủy lịch không (chỉ cho trạng thái pending)
    public function canCancel()
    {
        return $this->status === 'pending';
    }
    
    // Kiểm tra có thể yêu cầu hủy lịch không (chỉ cho lịch đã duyệt và trong tương lai)
    public function canRequestCancel()
    {
        return $this->status === 'approved' && Carbon::parse($this->schedule_date)->gt(Carbon::today());
    }
}