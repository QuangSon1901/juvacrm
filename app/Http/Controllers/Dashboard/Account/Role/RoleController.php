<?php

namespace App\Http\Controllers\Dashboard\Account\Role;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Level;
use App\Models\UserDepartment;
use App\Services\PaginationService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function detail($level_id, $department_id) {
        $department = Department::find($department_id);
        if (!$department) {
            return abort(404, 'Phòng ban không tồn tại.');
        }

        $level = Level::find($level_id);
        if (!$level) {
            return abort(404, 'Chức vụ không tồn tại.');
        }

        return view("dashboard.account.employee.role.detail", [
            'details' => [
                'department' => $department->toArray(),
                'level' => $level,
            ],
        ]);
    }

    public function memberInRole(Request $request) {
        $currentPage = $request->input('page', 1);

        $query = UserDepartment::with(['user', 'level'])
            ->where('department_id', $request['filter']['department_id'])
            ->where('level_id', $request['filter']['level_id'])
            ->where('is_active', 1);

        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $query->get()->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->user->id,
                'name' => $item->user->name,
                'level' => [
                    'id' => $item->level->id,
                    'name' => $item->level->name,
                ],
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.employee.team.ajax-detail', ['data' => $result, 'department_id' => $request['filter']['department_id']])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }
}
