<?php

namespace App\Http\Controllers\Dashboard\Account\Salary;

use App\Http\Controllers\Controller;
use App\Models\SalaryConfiguration;
use App\Models\User;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SalaryConfigurationController extends Controller
{
    public function index()
    {
        $users = User::where('is_active', 1)->get();
        $globalFulltimeConfig = SalaryConfiguration::getConfiguration(null, 'fulltime');
        $globalPartTimeConfig = SalaryConfiguration::getConfiguration(null, 'part-time');
        
        return view('dashboard.account.salary.configuration', compact('users', 'globalFulltimeConfig', 'globalPartTimeConfig'));
    }
    
    public function getUserConfig(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'user_id' => 'required|exists:tbl_users,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $fullTimeConfig = SalaryConfiguration::getConfiguration($request->user_id, 'fulltime');
        $partTimeConfig = SalaryConfiguration::getConfiguration($request->user_id, 'part-time');
        
        return response()->json([
            'status' => 200,
            'data' => [
                'fulltime' => $fullTimeConfig,
                'part-time' => $partTimeConfig
            ]
        ]);
    }
    
    public function saveConfig(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'user_id' => 'nullable|exists:tbl_users,id',
            'type' => 'required|in:fulltime,part-time',
            'hourly_rate' => 'required_if:type,part-time|nullable|numeric|min:0',
            'monthly_salary' => 'required_if:type,fulltime|nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'attendance_bonus_rate' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'insurance_rate' => 'nullable|numeric|min:0|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        // Tìm cấu hình hiện tại nếu có
        $config = SalaryConfiguration::where('type', $request->type);
        
        if ($request->filled('user_id')) {
            $config = $config->where('user_id', $request->user_id);
        } else {
            $config = $config->whereNull('user_id');
        }
        
        $config = $config->first();
        
        // Nếu không có, tạo mới
        if (!$config) {
            $config = new SalaryConfiguration();
            $config->type = $request->type;
            $config->user_id = $request->user_id;
        }
        
        // Cập nhật các trường
        if ($request->type === 'fulltime') {
            $config->monthly_salary = $request->monthly_salary;
            $config->hourly_rate = null;
        } else {
            $config->hourly_rate = $request->hourly_rate;
            $config->monthly_salary = null;
        }
        
        if ($request->filled('overtime_rate')) {
            $config->overtime_rate = $request->overtime_rate;
        }
        
        if ($request->filled('attendance_bonus_rate')) {
            $config->attendance_bonus_rate = $request->attendance_bonus_rate;
        }
        
        if ($request->filled('tax_rate')) {
            $config->tax_rate = $request->tax_rate;
        }
        
        if ($request->filled('insurance_rate')) {
            $config->insurance_rate = $request->insurance_rate;
        }
        
        $config->save();
        
        LogService::saveLog([
            'action' => 'SAVE_SALARY_CONFIG',
            'ip' => $request->getClientIp(),
            'details' => "Lưu cấu hình lương " . $request->type . ($request->user_id ? " cho nhân viên #" . $request->user_id : " toàn cục"),
            'fk_key' => 'tbl_salary_configurations|id',
            'fk_value' => $config->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Lưu cấu hình lương thành công',
            'data' => $config,
        ]);
    }
}