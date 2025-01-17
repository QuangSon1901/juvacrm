<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index() {
        $user = User::with(['departments' => function ($query) {
            $query->select('tbl_departments.id', 'tbl_departments.name', 'tbl_departments.keyword', 'tbl_departments.note')
                ->withPivot('level_id');
        }])
            ->findOrFail(Session::get(ACCOUNT_CURRENT_SESSION)['id']);

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

        return view("dashboard.profile.index", ['details' => $result]);
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
                'note' => 'nullable|string|max:255',
                'password' => 'nullable|string|max:255',
            ],
            [
                'name.required' => ':attribute không được để trống',
                'name.string' => ':attribute phải là dạng chuỗi',
                'name.max' => ':attribute không được vượt quá :max ký tự',

                'password.required' => ':attribute không được để trống',
                'password.string' => ':attribute phải là dạng chuỗi',
                'password.max' => ':attribute không được vượt quá :max ký tự',
                
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
                'password' => 'Mật khẩu',
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
            $user = User::find(Session::get(ACCOUNT_CURRENT_SESSION)['id']);

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Thành viên không tồn tại.',
                ], 404);
            }

            $data = $request->only(['name', 'email', 'phone', 'birth_date', 'gender', 'cccd', 'address', 'password', 'note']);
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $user->update($data);
            $request->session()->put(ACCOUNT_CURRENT_SESSION, [
                'id' => $user->id,
                'name' => $user->name,
                'birth_date' => $user->birth_date,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $user->address,
                'gender' => $user->gender,
                'cccd' => $user->cccd,
                'avatar' => $user->avatar,
                'username' => $user->username,
                'salary' => $user->salary,
                'status' => $user->status,
                'is_active' => $user->is_active,
                'note' => $user->note,
                'last_login' => $user->last_login,
                'login_attempts' => $user->login_attempts,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);

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
}
