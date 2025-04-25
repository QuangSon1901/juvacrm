<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'tbl_role_permissions';
    protected $fillable = ['level_id', 'department_id', 'permission_id'];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}