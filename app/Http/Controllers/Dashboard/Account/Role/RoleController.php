<?php

namespace App\Http\Controllers\Dashboard\Account\Role;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\UserDepartment;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function detail($level_id, $department_id) {
        $department = Department::find($department_id);
        if (!$department) {
            return abort(404, 'Phòng ban không tồn tại.');
        }

        $usersDetail = UserDepartment::with(['user', 'level'])
            ->where('department_id', $department_id)
            ->where('is_active', 1)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                    'level' => [
                        'id' => $item->level->id,
                        'name' => $item->level->name,
                    ],
                ];
            });

        return view("dashboard.account.employee.role.detail", [
            'details' => [
                'department' => $department,
                'users' => $usersDetail,
            ],
        ]);
    }
}
