<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = 'tbl_levels';

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'tbl_role_permissions', 'level_id', 'permission_id')
                    ->withPivot('department_id');
    }
}
