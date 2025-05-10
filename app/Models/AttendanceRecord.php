<?php

namespace App\Models;

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
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public static function calculateTotalHours($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            return 0;
        }
        
        $checkInTime = new \DateTime($checkIn);
        $checkOutTime = new \DateTime($checkOut);
        $interval = $checkInTime->diff($checkOutTime);
        
        return $interval->h + ($interval->i / 60);
    }
}