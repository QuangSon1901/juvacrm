<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskMissionReport extends Model
{
    protected $table = 'tbl_task_mission_reports';
    
    protected $fillable = [
        'assignment_id',
        'user_id',
        'quantity',
        'note',
        'date_completed'
    ];
    
    /**
     * Get the assignment that owns the report
     */
    public function assignment()
    {
        return $this->belongsTo(TaskMissionAssignment::class, 'assignment_id');
    }
    
    /**
     * Get the user that created the report
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}