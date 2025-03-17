<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFeedbackItem extends Model
{
    protected $table = 'tbl_task_feedback_items';
    
    protected $fillable = [
        'feedback_id',      // ID của feedback cha
        'task_id',          // ID của task cần sửa
        'is_resolved',      // Đã giải quyết chưa
        'resolved_by',      // Người đánh dấu đã giải quyết
        'resolved_at',      // Thời điểm đánh dấu đã giải quyết
        'resolver_comment'  // Ghi chú khi giải quyết (tùy chọn)
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    // Quan hệ với Feedback
    public function feedback()
    {
        return $this->belongsTo(TaskFeedback::class);
    }

    // Quan hệ với Task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Quan hệ với User giải quyết
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}