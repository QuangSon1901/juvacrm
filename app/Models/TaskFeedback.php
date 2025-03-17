<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFeedback extends Model
{
    protected $table = 'tbl_task_feedbacks';
    
    protected $fillable = [
        'task_id',
        'user_id',
        'rating',
        'needs_revision',
        'comment',
        'is_resolved',
        'resolved_by',
        'resolved_at',
        'severity_level', // Thêm mức độ nghiêm trọng (1-3)
        'affects_child_tasks', // Có ảnh hưởng các task con không
        'specific_child_tasks', // Lưu mảng các task con cụ thể cần sửa (JSON)
        'revision_type' // Loại chỉnh sửa: minor, normal, major
    ];

    protected $casts = [
        'needs_revision' => 'boolean',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'specific_child_tasks' => 'array'
    ];

    // Quan hệ
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Thêm các responses/replies cho feedback
    public function responses()
    {
        return $this->hasMany(TaskFeedbackResponse::class, 'feedback_id');
    }
}