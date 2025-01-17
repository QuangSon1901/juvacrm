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
}
