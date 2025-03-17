<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskMissionAssignment extends Model
{
    protected $table = 'tbl_task_mission_assignments';
    
    protected $fillable = [
        'task_id',
        'mission_id',
        'user_id',
        'quantity_required',
        'quantity_completed',
        'status'
    ];
    
    /**
     * Get the task that owns the assignment
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
    
    /**
     * Get the mission that owns the assignment
     */
    public function mission()
    {
        return $this->belongsTo(TaskMission::class, 'mission_id');
    }
    
    /**
     * Get the user that owns the assignment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the reports for this assignment
     */
    public function reports()
    {
        return $this->hasMany(TaskMissionReport::class, 'assignment_id');
    }
}