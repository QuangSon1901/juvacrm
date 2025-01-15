<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'tbl_departments';

    protected $fillable = [
        'name',
        'note',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            UserDepartment::class,
            'department_id', // Foreign key on tbl_user_departments
            'id',            // Foreign key on tbl_users
            'id',            // Local key on tbl_departments
            'user_id'        // Local key on tbl_user_departments
        )->where('tbl_user_departments.is_active', 1);
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
}
