<?php

namespace App\Http\Controllers\Dashboard\Account\Team;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Level;
use App\Models\User;
use App\Models\UserDepartment;
use App\Services\PaginationService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index() {
        return view("dashboard.account.employee.team.index");
    }

    public function data(Request $request) {
        $currentPage = $request->input('page', 1);
        $query = Department::query()
            ->isActive((int)$request['filter']['is_active'] ?? 0)
            ->search($request['filter']['search'] ?? '');
            
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($department, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $department->id,
                'name' => $department->name,
                'updated_at' => $department->updated_at->format('d/m/Y H:i:s'),
                'description' => $department->note,
                'member_count' => $department->users->count(),
                'status' => $department->is_active,
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.employee.team.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function dataOld(Request $request)
    {
        $search = $request->input('search', '');
        $isActive = $request->input('is_active', null);

        $query = Department::with(['users']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('note', 'like', "%$search%");
            });
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        $departments = $query->get()->map(function ($department) {
            return [
                'id' => $department->id,
                'name' => $department->name,
                'updated_at' => $department->updated_at->format('d/m/Y H:i:s'),
                'description' => $department->note,
                'member_count' => $department->users->count(),
                'status' => $department->is_active,
            ];
        });

        return [
            "status" => 200,
            "data" => $departments
        ];
    }

    public function employeeByDepartment($id, Request $request) {
        $search = $request->input('search', '');
        $isActive = $request->input('is_active', null);

        $query = UserDepartment::with(['user', 'level'])
            ->where('department_id', $id)
            ->where('is_active', 1);
            

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('note', 'like', "%$search%");
            });
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }


        $users = $query->get()->map(function ($item) {
            return [
                'id' => $item->user->id,
                'name' => $item->user->name,
                'level' => [
                    'id' => $item->level->id,
                    'name' => $item->level->name,
                ],
            ];
        });

        return [
            "status" => 200,
            "data" => $users
        ];
    }

    public function detail($id)
    {
        $department = Department::find($id);
        if (!$department) {
            return abort(404, 'Phòng ban không tồn tại.');
        }

        $usersDetail = UserDepartment::with(['user', 'level'])
            ->where('department_id', $id)
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

        $levelsDetail = UserDepartment::selectRaw('department_id, level_id, COUNT(*) as total')
        ->where('department_id', $id)
        ->where('is_active', 1)
        ->groupBy('level_id', 'department_id')
        ->with(['level', 'department'])
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->level->id,
                'department_id' => $item->department->id,
                'name' => $item->level->name,
                'total' => $item->total,
            ];
        });

        $users = User::select('id', 'name')->get();
        $levels = Level::select('id', 'name')->get();

        // Trả về view với dữ liệu
        return view('dashboard.account.employee.team.detail', [
            'details' => [
                'department' => $department,
                'levels' => $levelsDetail,
                'users' => $usersDetail
            ],
            'levels' => $levels,
            'users' => $users
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name')->get();
        $levels = Level::select('id', 'name')->get();

        return view("dashboard.account.employee.team.create", ["users" => $users, "levels" => $levels]);
    }

    public function createPost(Request $request) {
        $department = Department::create([
            'name' => $request['name'],
            'note' => $request['note'] ?? null,
            'is_active' => 1,
        ]);

        foreach ($request['users'] as $user) {
            $levelId = $user['level']['id'];

            if ($levelId == -1) {
                $level = Level::create([
                    'name' => $user['level']['name']
                ]);
                $levelId = $level->id;
            }

            UserDepartment::create([
                'user_id' => $user['id'],
                'department_id' => $department->id,
                'level_id' => $levelId,
                'is_active' => 1,
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Phòng ban và người dùng đã được lưu thành công!',
            'department' => $department,
        ]);
    }

    public function addMemberView($id) {
        $users = User::select('id', 'name')->get();
        $levels = Level::select('id', 'name')->get();

        $department = Department::find($id);

        return view("dashboard.account.employee.team.addmember", ["users" => $users, "levels" => $levels, 'department' => $department]);
    }

    public function addMemberSave(Request $request) {
        $department = Department::find($request['id']);
        if (!$department) {
            return response()->json(['status' => 404, 'message' => 'Phòng ban không tồn tại.'], 404);
        }

        if (!isset($request['users']) || count($request['users']) == 0) {
            return response()->json(['status' => 404, 'message' => 'Chưa chọn thành viên.'], 404);
        }

        foreach ($request['users'] as $user) {
            $levelId = $user['level']['id'];

            if ($levelId == -1) {
                $level = Level::create([
                    'name' => $user['level']['name']
                ]);
                $levelId = $level->id;
            }

            UserDepartment::create([
                'user_id' => $user['id'],
                'department_id' => $department->id,
                'level_id' => $levelId,
                'is_active' => 1,
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Thêm thành viên thành công!',
            'department' => $department,
        ]);
    }

    public function removeMember(Request $request) {

        try {
            $userDepartment = UserDepartment::where('user_id', $request->user_id)
                ->where('department_id', $request->department_id)
                ->first();
            if (!$userDepartment) {
                return response()->json(['status' => 404, 'message' => 'Phòng ban không tồn tại.'], 404);
            }

            $userDepartment->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Đã gỡ thành viên khỏi phòng ban thành công.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi gỡ thành viên.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request) {
        try {
            // Tìm phòng ban theo ID
            $department = Department::find($request['id']);

            if (!$department) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Phòng ban không tồn tại.',
                ], 404);
            }

            // Cập nhật thông tin theo các trường có trong request
            $data = $request->only(['name', 'note']);
            $department->update($data);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật phòng ban thành công.',
                'data' => $department,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật phòng ban.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus($id) {
        $department = Department::find($id);
        if (!$department) {
            return response()->json(['status' => 404, 'message' => 'Phòng ban không tồn tại.'], 404);
        }

        $department->is_active = !$department->is_active;
        $department->save();
        return response()->json([
            'status' => 200,
            'message' => 'Trạng thái đã được thay đổi thành công.',
        ]);
    }
}
