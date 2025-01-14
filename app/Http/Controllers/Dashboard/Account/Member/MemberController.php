<?php

namespace App\Http\Controllers\Dashboard\Account\Member;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index() {
        return view("dashboard.account.employee.member.index");
    }

    public function createView() {
        return view("dashboard.account.employee.member.create");
    }

    public function data(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $isActive = $request->input('is_active', null);

            $users = User::with(['departments' => function ($query) {
                $query->select('tbl_departments.id', 'tbl_departments.name', 'tbl_departments.keyword', 'tbl_departments.note');
            }]);

            if (!empty($search)) {
                $users->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            }
    
            if ($isActive !== null) {
                $users->where('is_active', $isActive);
            }

            $result = $users->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'gender' => $user->gender,
                    'status' => $user->status,
                    'departments' => $user->departments->map(function ($department) {
                        return [
                            'id' => $department->id,
                            'name' => $department->name,
                            'keyword' => $department->keyword,
                            'note' => $department->note,
                        ];
                    })
                ];
            });

            return response()->json([ 'status' => 200, 'data' => $result ], 200);
        } catch (\Exception $e) {
            return response()->json([ 'status' => 500, 'message' => 'Lỗi trong quá trình xử lý.', 'error' => $e->getMessage() ], 500);
        }
    }

    public function detail($id) {
        $user = User::with(['departments' => function ($query) {
            $query->select('tbl_departments.id', 'tbl_departments.name', 'tbl_departments.keyword', 'tbl_departments.note')
                  ->withPivot('level_id');
        }])
        ->findOrFail($id);

        $result = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'birth_date' => $user->birth_date,
            'gender' => $user->gender,
            'cccd' => $user->cccd,
            'avatar' => $user->avatar,
            'username' => $user->username,
            'salary' => $user->salary,
            'status' => $user->status,
            'is_active' => $user->is_active,
            'last_login' => $user->last_login,
            'note' => $user->note,
            'created_at' => $user->created_at,
            'departments' => $user->departments->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                    'keyword' => $department->keyword,
                    'note' => $department->note,
                    'level_name' => (Level::find($department->pivot->level_id))->name,
                ];
            })
        ];

        return view("dashboard.account.employee.member.detail", ['details' => $result]);
    }

    public function create(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:tbl_users,email',
            'address' => 'nullable|string|max:255',
            'gender' => 'required|in:0,1',
            'cccd' => 'nullable|string|max:255',
            'username' => 'required|string|max:100|unique:tbl_users,username',
            'salary' => 'nullable|numeric',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            $password = Str::random(8);
            $validated['password'] = Hash::make($password);
            $validated['birth_date'] = Carbon::createFromFormat('d/m/Y', $validated['birth_date'])->format('Y-m-d');
            User::create($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Nhân viên đã được lưu thành công! Mật khẩu mới là: ' . $password,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => $th,
            ]);
        }
    }
}
