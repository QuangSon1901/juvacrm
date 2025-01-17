<?php

namespace App\Http\Controllers\Dashboard\Account\Member;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use App\Services\PaginationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index()
    {
        return view("dashboard.account.employee.member.index");
    }

    public function createView()
    {
        return view("dashboard.account.employee.member.create");
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        $query = User::with(['departments' => function ($query) {
            $query->select('tbl_departments.id', 'tbl_departments.name', 'tbl_departments.keyword', 'tbl_departments.note');
        }])
            ->isActive((int)$request['filter']['is_active'] ?? 0)
            ->search($request['filter']['search'] ?? '');

        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($user, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'gender' => $user->gender,
                'status' => $user->status,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
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

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.employee.member.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function detail($id)
    {
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

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:tbl_users,email',
                'username' => 'required|string|max:100|unique:tbl_users,username',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:0,1',
                'cccd' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'salary' => 'nullable|numeric',
                'note' => 'nullable|string|max:255',
            ],
            [
                'name.required' => ':attribute không được để trống',
                'name.string' => ':attribute phải là dạng chuỗi',
                'name.max' => ':attribute không được vượt quá :max ký tự',
                
                'birth_date.required' => ':attribute không được để trống',
                'birth_date.date' => ':attribute phải là một ngày hợp lệ',
                
                'phone.string' => ':attribute phải là dạng chuỗi',
                'phone.max' => ':attribute không được vượt quá :max ký tự',
                
                'email.required' => ':attribute không được để trống',
                'email.email' => ':attribute phải là một email hợp lệ',
                'email.unique' => ':attribute đã tồn tại trong hệ thống',
                
                'address.string' => ':attribute phải là dạng chuỗi',
                'address.max' => ':attribute không được vượt quá :max ký tự',
                
                'gender.required' => ':attribute không được để trống',
                'gender.in' => ':attribute phải là 0 hoặc 1',
                
                'cccd.string' => ':attribute phải là dạng chuỗi',
                'cccd.max' => ':attribute không được vượt quá :max ký tự',
                
                'username.required' => ':attribute không được để trống',
                'username.string' => ':attribute phải là dạng chuỗi',
                'username.max' => ':attribute không được vượt quá :max ký tự',
                'username.unique' => ':attribute đã tồn tại trong hệ thống',
                
                'salary.numeric' => ':attribute phải là dạng số',
                
                'note.string' => ':attribute phải là dạng chuỗi',
                'note.max' => ':attribute không được vượt quá :max ký tự',
            ],
            [
                'name' => 'Tên người dùng',
                'birth_date' => 'Ngày sinh',
                'phone' => 'Số điện thoại',
                'email' => 'Địa chỉ email',
                'address' => 'Địa chỉ',
                'gender' => 'Giới tính',
                'cccd' => 'Căn cước công dân',
                'username' => 'Tên đăng nhập',
                'salary' => 'Mức lương',
                'note' => 'Ghi chú',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $data = $request->all();

            $password = Str::random(10);
            $data['password'] = Hash::make($password);

            User::create($data);

            return response()->json([
                'status' => 200,
                'message' => 'Nhân viên đã được lưu thành công.',
                'data' => [
                    'password' => $password
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e,
            ]);
        }
    }

    public function update(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:tbl_users,email',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:0,1',
                'cccd' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'salary' => 'nullable|numeric',
                'note' => 'nullable|string|max:255',
            ],
            [
                'name.required' => ':attribute không được để trống',
                'name.string' => ':attribute phải là dạng chuỗi',
                'name.max' => ':attribute không được vượt quá :max ký tự',
                
                'birth_date.required' => ':attribute không được để trống',
                'birth_date.date' => ':attribute phải là một ngày hợp lệ',
                
                'phone.string' => ':attribute phải là dạng chuỗi',
                'phone.max' => ':attribute không được vượt quá :max ký tự',
                
                'email.required' => ':attribute không được để trống',
                'email.email' => ':attribute phải là một email hợp lệ',
                'email.unique' => ':attribute đã tồn tại trong hệ thống',
                
                'address.string' => ':attribute phải là dạng chuỗi',
                'address.max' => ':attribute không được vượt quá :max ký tự',
                
                'gender.required' => ':attribute không được để trống',
                'gender.in' => ':attribute phải là 0 hoặc 1',
                
                'cccd.string' => ':attribute phải là dạng chuỗi',
                'cccd.max' => ':attribute không được vượt quá :max ký tự',
                
                'username.required' => ':attribute không được để trống',
                'username.string' => ':attribute phải là dạng chuỗi',
                'username.max' => ':attribute không được vượt quá :max ký tự',
                'username.unique' => ':attribute đã tồn tại trong hệ thống',
                
                'salary.numeric' => ':attribute phải là dạng số',
                
                'note.string' => ':attribute phải là dạng chuỗi',
                'note.max' => ':attribute không được vượt quá :max ký tự',
            ],
            [
                'name' => 'Tên người dùng',
                'birth_date' => 'Ngày sinh',
                'phone' => 'Số điện thoại',
                'email' => 'Địa chỉ email',
                'address' => 'Địa chỉ',
                'gender' => 'Giới tính',
                'cccd' => 'Căn cước công dân',
                'username' => 'Tên đăng nhập',
                'salary' => 'Mức lương',
                'note' => 'Ghi chú',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            $user = User::find($request['id']);

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Thành viên không tồn tại.',
                ], 404);
            }

            $data = $request->only(['name', 'email', 'phone', 'birth_date', 'gender', 'cccd', 'address', 'salary', 'note']);
            $user->update($data);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật thành viên thành công.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật thành viên.',
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $user = User::find($request['id'] ?? 0);

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Người dùng không tồn tại.'
                ]);
            }

            $newPass = Str::random(10);
            $user->update(['password' => Hash::make($newPass)]);

            return response()->json([
                'status' => 200,
                'message' => 'Đặt lại mật khẩu thành công.',
                'data' => [
                    'password' => $newPass
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Đã có lỗi xảy ra.',
            ], 400);
        }
    }

    public function lockAccount(Request $request)
    {
        try {
            $user = User::find($request['id'] ?? 0);

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Người dùng không tồn tại.'
                ]);
            }

            $beforeChange = $user->is_active;

            $user->update(['is_active' => !$beforeChange]);

            return response()->json([
                'status' => 200,
                'message' => $beforeChange ? 'Khoá tài khoản thành công.' : 'Mở khoá tài khoản thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Đã có lỗi xảy ra.',
            ], 400);
        }
    }
}
