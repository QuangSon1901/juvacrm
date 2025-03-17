<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskMission extends Model
{
    protected $table = 'tbl_task_missions';
    
    protected $fillable = [
        'name',
        'description',
        'salary',
        'is_active'
    ];
    
    /**
     * Get all assignments of this mission
     */
    public function assignments()
    {
        return $this->hasMany(TaskMissionAssignment::class, 'mission_id');
    }
}