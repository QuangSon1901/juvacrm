<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'tbl_permissions';
    protected $fillable = ['name', 'slug', 'description', 'module'];

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }
}