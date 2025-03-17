<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFeedback extends Model
{
    protected $table = 'tbl_task_feedbacks';
    
    protected $fillable = [
        'task_id',      // ID của task hợp đồng
        'user_id',      // Người tạo feedback (sale/quản lý)
        'comment',      // Nội dung feedback
        'is_resolved',  // Đã giải quyết chưa?
        'resolved_by',  // Người xác nhận đã giải quyết
        'resolved_at',  // Thời điểm xác nhận giải quyết
        'status'        // Trạng thái: 0-Đang chờ, 1-Đã giải quyết, 2-Yêu cầu làm lại
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    // Quan hệ với Task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Quan hệ với User tạo feedback
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với User xác nhận giải quyết
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Các task cần sửa trong feedback này
    public function feedbackItems()
    {
        return $this->hasMany(TaskFeedbackItem::class, 'feedback_id');
    }
}