<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFeedbackResponse extends Model
{
    protected $table = 'tbl_task_feedback_responses';
    
    protected $fillable = [
        'feedback_id',
        'user_id',
        'content',
        'attachments' // JSON array của các file ID
    ];

    protected $casts = [
        'attachments' => 'array'
    ];

    public function feedback()
    {
        return $this->belongsTo(TaskFeedback::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}