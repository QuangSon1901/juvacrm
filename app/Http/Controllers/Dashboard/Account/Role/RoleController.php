<?php

namespace App\Http\Controllers\Dashboard\Account\Role;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Level;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserDepartment;
use App\Services\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function getPermissions($level_id, $department_id)
    {
        $department = Department::find($department_id);
        if (!$department) {
            return abort(404, 'Phòng ban không tồn tại.');
        }

        $level = Level::find($level_id);
        if (!$level) {
            return abort(404, 'Chức vụ không tồn tại.');
        }

        // Lấy tất cả quyền và nhóm theo module
        $allPermissions = Permission::orderBy('module')->get();
        $permissions = $allPermissions->groupBy('module');

        // Lấy các quyền đã được gán cho chức vụ này trong phòng ban này
        $assignedPermissions = RolePermission::where('level_id', $level_id)
            ->where('department_id', $department_id)
            ->pluck('permission_id')
            ->toArray();

        return view("dashboard.account.employee.role.permissions", [
            'details' => [
                'department' => $department,
                'level' => $level,
            ],
            'permissions' => $permissions,
            'assignedPermissions' => $assignedPermissions
        ]);
    }

    public function savePermissions(Request $request, $level_id, $department_id)
    {
        $department = Department::find($department_id);
        if (!$department) {
            return response()->json(['status' => 404, 'message' => 'Phòng ban không tồn tại.']);
        }

        $level = Level::find($level_id);
        if (!$level) {
            return response()->json(['status' => 404, 'message' => 'Chức vụ không tồn tại.']);
        }

        try {
            // Xóa tất cả quyền hiện tại của role này
            RolePermission::where('level_id', $level_id)
                ->where('department_id', $department_id)
                ->delete();

            // Thêm quyền mới
            $permissions = $request->input('permissions', []);
            $permissionCount = count($permissions);
            
            // Log hành động
            Log::info("Cập nhật phân quyền cho chức vụ {$level->name} của phòng ban {$department->name}: {$permissionCount} quyền");
            
            foreach ($permissions as $permissionId) {
                RolePermission::create([
                    'level_id' => $level_id,
                    'department_id' => $department_id,
                    'permission_id' => $permissionId
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => "Phân quyền thành công! Đã cấp {$permissionCount} quyền cho chức vụ {$level->name}.",
            ]);
        } catch (\Exception $e) {
            Log::error("Lỗi khi phân quyền cho chức vụ {$level->name} của phòng ban {$department->name}: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi phân quyền: ' . $e->getMessage()
            ]);
        }
    }
}