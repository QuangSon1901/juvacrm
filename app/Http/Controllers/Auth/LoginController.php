<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index() {
        return view("auth.login.index");
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ],
            [
                'username.required' => ':attribute không được để trống',
                'username.string' => ':attribute phải là dạng chuỗi',
                'username.max' => ':attribute không được vượt quá :max ký tự',

                'password.required' => ':attribute không được để trống',
                'password.string' => ':attribute phải là dạng chuỗi',
                'password.max' => ':attribute không được vượt quá :max ký tự',
            ],
            [
                'username' => 'Tên tài khoản',
                'password' => 'Mật khẩu',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $user = User::where('username', $request['username'])->first();
    
            if ($user && $user->is_active && Auth::attempt($request->only('username', 'password'))) {
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
                $request->session()->regenerate();
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Đăng nhập thành công.',
                ]);
            }
    
            return response()->json([
                'status' => 400,
                'message' => 'Tài khoản không hoạt động hoặc mật khẩu không chính xác.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Đã có lỗi xảy ra.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.login');
    }
}
