<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements AuthenticatableContract
{
    use HasFactory;

    protected $table = 'tbl_users';

    protected $fillable = [
        'name',
        'birth_date',
        'phone',
        'email',
        'address',
        'gender',
        'cccd',
        'avatar',
        'username',
        'password',
        'salary',
        'status',
        'is_active',
        'last_login',
        'note',
        'login_attempts',
        'ended_at',
        'is_super_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id')
                    ->using(UserDepartment::class); // Dùng UserDepartment để kết nối thông qua pivot
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'tbl_user_departments', 'user_id', 'department_id')
            ->withPivot('level_id', 'is_active')
            ->wherePivot('is_active', 1);
    }

    
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) return $query->where('name', 'like', "%$search%")->orWhere('note', 'like', "%$search%");;
        return $query;
    }

    public function scopeIsActive($query, $type)
    {
        if ($type) {
            return $query->where('is_active', 1);
        };
        return $query;
    }

    /**
     * Lấy các hợp đồng do người dùng tạo
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Lấy các công việc được giao cho người dùng
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assign_id');
    }

    public function task_mission_reports()
    {
        return $this->hasMany(TaskMissionReport::class, 'user_id');
    }

    public function isSuperAdmin()
    {
        return (bool) $this->is_super_admin;
    }

    // Thêm phương thức này vào model User
    public function permissions()
    {
        return Permission::select('tbl_permissions.*')
            ->join('tbl_role_permissions', 'tbl_permissions.id', '=', 'tbl_role_permissions.permission_id')
            ->join('tbl_user_departments', function($join) {
                $join->on('tbl_role_permissions.level_id', '=', 'tbl_user_departments.level_id')
                    ->on('tbl_role_permissions.department_id', '=', 'tbl_user_departments.department_id');
            })
            ->where('tbl_user_departments.user_id', $this->id)
            ->where('tbl_user_departments.is_active', 1)
            ->distinct();
    }

    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists();
        }
        
        if (is_array($permission)) {
            return $this->permissions()->whereIn('slug', $permission)->exists();
        }
        
        return false;
    }
}
