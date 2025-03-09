<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tbl_tasks';

    protected $fillable = [
        'name', 
        'sub_name', 
        'description', 
        'note', 
        'contract_id', 
        'service_id', 
        'service_other', 
        'priority_id', 
        'status_id', 
        'issue_id', 
        'parent_id', 
        'assign_id', 
        'created_id', 
        'progress', 
        'start_date', 
        'estimate_time', 
        'spend_time', 
        'due_date', 
        'bonus_amount', 
        'deduction_amount', 
        'is_active', 
        'qty_request', 
        'qty_completed', 
        'type', 
        'is_updated', 
        'contract_service_id', 
    ];

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) 
            return $query->where('name', 'like', "%$search%")
                ->orWhere('sub_name', 'like', "%$search%");
        return $query;
    }

    public function scopeMyTask($query, $type)
    {
        if ($type) {
            return $query->where('assign_id', Session::get(ACCOUNT_CURRENT_SESSION)['id']);
        };
        return $query;
    }

    public function scopeLevelTask($query, $type)
    {
        if ($type == 'max') {
            return $query->whereNull('parent_id');
        };
        
        return $query->whereDoesntHave('childs');
    }

    public function scopePriorityTask($query, $type)
    {
        if ($type) {
            return $query->where('priority_id', $type);
        };
        return $query;
    }

    public function scopeStatusTask($query, $type)
    {
        if ($type) {
            return $query->where('status_id', $type);
        };
        return $query;
    }

    public function priority() {
        return $this->belongsTo(TaskConfig::class, 'priority_id')->where('type', 0);
    }

    public function status() {
        return $this->belongsTo(TaskConfig::class, 'status_id')->where('type', 1);
    }

    public function contract() {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function issue() {
        return $this->belongsTo(TaskConfig::class, 'issue_id')->where('type', 2);
    }

    public function parent() {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subTasks()
    {
        return $this->hasMany(Task::class, 'parent_id')->where('is_active', 1);
    }

    public function comments() {
        return $this->hasMany(TaskComment::class);
    }

    public function childs() {
        return $this->hasMany(Task::class, 'parent_id')->where('is_active', 1);
    }

    public function assign() {
        return $this->belongsTo(User::class, 'assign_id');
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_id');
    }
}
