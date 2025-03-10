<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskContribution extends Model
{
    protected $table = 'tbl_task_contributions';
    
    protected $fillable = [
        'task_id',
        'user_id',
        'quantity',
        'date_completed',
        'note',
        'is_active'
    ];

    /**
     * Mối quan hệ với model Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Mối quan hệ với model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}