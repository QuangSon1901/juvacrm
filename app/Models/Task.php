<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tbl_tasks';
    
    protected $fillable = [
        'name', 
        'description', 
        'note', 
        'contract_id', 
        'progress', 
        'service_id',
        'priority_id', 
        'status_id', 
        'issue_id',
        'estimate_time', 
        'spend_time', 
        'due_date',
        'is_active', 
        'qty_request', 
        'qty_completed',
        'type',
        'is_updated', 
        'contract_service_id',
        'parent_id', 
        'original_task_id',
        'assign_id', 
        'sub_name',
        'created_id', 
        'start_date', 
        'service_other',
        'bonus_amount', 
        'deduction_amount'
    ];

    /**
     * Scope query để lấy công việc của tôi
     */
    public function scopeMyTask($query, $my_task)
    {
        if ($my_task == 1) {
            $query->where('assign_id', session()->get(ACCOUNT_CURRENT_SESSION)['id']);
        }
        return $query;
    }

    /**
     * Scope query để lọc theo cấp độ công việc
     */
    public function scopeLevelTask($query, $level_task)
    {
        if ($level_task != '') {
            $query->where('type', $level_task);
        }
        return $query;
    }

    /**
     * Scope query để lọc theo độ ưu tiên
     */
    public function scopePriorityTask($query, $priority_task)
    {
        if ($priority_task != '') {
            $query->where('priority_id', $priority_task);
        }
        return $query;
    }

    /**
     * Scope query để lọc theo trạng thái
     */
    public function scopeStatusTask($query, $status_task)
    {
        if ($status_task != '') {
            $query->where('status_id', $status_task);
        }
        return $query;
    }

    /**
     * Scope để tìm kiếm
     */
    public function scopeSearch($query, $search)
    {
        if ($search != '') {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        return $query;
    }

    /**
     * Lấy thông tin độ ưu tiên
     */
    public function priority()
    {
        return $this->belongsTo(TaskConfig::class, 'priority_id');
    }

    /**
     * Lấy thông tin trạng thái
     */
    public function status()
    {
        return $this->belongsTo(TaskConfig::class, 'status_id');
    }

    /**
     * Lấy thông tin vấn đề
     */
    public function issue()
    {
        return $this->belongsTo(TaskConfig::class, 'issue_id');
    }

    /**
     * Lấy thông tin người được gán
     */
    public function assign()
    {
        return $this->belongsTo(User::class, 'assign_id');
    }

    /**
     * Lấy thông tin người tạo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_id');
    }

    /**
     * Lấy thông tin hợp đồng
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    /**
     * Lấy thông tin dịch vụ
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Lấy thông tin dịch vụ hợp đồng
     */
    public function contractService()
    {
        return $this->belongsTo(ContractService::class, 'contract_service_id');
    }

    /**
     * Lấy thông tin công việc cha
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Lấy thông tin các công việc con
     */
    public function childs()
    {
        return $this->hasMany(Task::class, 'parent_id')->where('is_active', 1);
    }

    /**
     * Lấy thông tin bình luận
     */
    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id')->orderBy('created_at', 'desc');
    }

    /**
     * Lấy thông tin đóng góp từ các nhân viên
     */
    public function contributions()
    {
        return $this->hasMany(TaskContribution::class, 'task_id')->where('is_active', 1);
    }

    /**
     * Lấy tất cả các tệp đính kèm
     */
    public function attachments()
    {
        return $this->hasMany(Upload::class, 'fk_value')
            ->where('fk_key', 'tbl_tasks|id')
            ->where('action', MEDIA_DRIVER_UPLOAD);
    }
    
    /**
     * Tính toán số lượng đã hoàn thành dựa trên các đóng góp
     */
    public function recalculateCompletedQuantity()
    {
        $totalCompleted = $this->contributions()->sum('quantity');
        $this->update(['qty_completed' => $totalCompleted]);
        
        // Nếu có giá trị qty_request, cập nhật tiến độ phần trăm
        if ($this->qty_request > 0) {
            $progress = min(100, round(($totalCompleted / $this->qty_request) * 100));
            $this->update(['progress' => $progress]);
        }
        
        return $totalCompleted;
    }

    /**
     * Get all mission assignments for this task
     */
    public function missionAssignments()
    {
        return $this->hasMany(TaskMissionAssignment::class, 'task_id');
    }

    /**
     * Get all feedbacks for this task
     */
    public function feedbacks()
    {
        return $this->hasMany(TaskFeedback::class, 'task_id');
    }

    /**
     * Check if task needs revision based on feedback
     */
    public function needsRevision()
    {
        return $this->feedbacks()
            ->where('needs_revision', true)
            ->where('is_resolved', false)
            ->exists();
    }

    /**
     * Get the completion percentage of all mission assignments
     */
    public function getMissionCompletionPercentage()
    {
        $assignments = $this->missionAssignments;
        
        if ($assignments->isEmpty()) {
            return 0;
        }
        
        $totalRequired = $assignments->sum('quantity_required');
        $totalCompleted = $assignments->sum('quantity_completed');
        
        if ($totalRequired == 0) {
            return 0;
        }
        
        return min(100, round(($totalCompleted / $totalRequired) * 100));
    }

     /**
     * Lấy task gốc
     */
    public function originalTask()
    {
        return $this->belongsTo(Task::class, 'original_task_id');
    }
    
    /**
     * Lấy các task bổ sung
     */
    public function supplementaryTasks()
    {
        return $this->hasMany(Task::class, 'original_task_id');
    }
    
    /**
     * Kiểm tra xem task có phải là task bổ sung không
     */
    public function isSupplementary()
    {
        return $this->original_task_id !== null;
    }
}