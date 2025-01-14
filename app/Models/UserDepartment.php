<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDepartment extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_departments';

    protected $fillable = [
        'user_id',
        'department_id',
        'level_id',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Mối quan hệ tới User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Mối quan hệ tới Level
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
