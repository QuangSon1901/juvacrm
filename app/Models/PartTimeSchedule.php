<?php

namespace App\Models;

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
}