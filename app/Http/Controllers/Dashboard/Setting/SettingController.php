<?php

namespace App\Http\Controllers\Dashboard\Setting;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use App\Models\ContractCommission;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index() {
        // Lấy tất cả cấu hình hệ thống
        $configs = SystemConfig::where('is_active', 1)->get();
        
        // Lấy tổng số tiền hoa hồng chưa chi
        $pendingCommissions = ContractCommission::where('is_paid', 0)->sum('commission_amount');
        $totalCommissions = ContractCommission::count();
        $paidCommissions = ContractCommission::where('is_paid', 1)->count();
        
        return view("dashboard.setting.index", compact('configs', 'pendingCommissions', 'totalCommissions', 'paidCommissions'));
    }
    
    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'config_key' => 'required|string|exists:tbl_system_config,config_key',
            'config_value' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $config = SystemConfig::where('config_key', $request->config_key)->first();
            $config->update(['config_value' => $request->config_value]);

            LogService::saveLog([
                'action' => 'UPDATE_SYSTEM_CONFIG',
                'ip' => $request->getClientIp(),
                'details' => "Đã cập nhật cấu hình " . $request->config_key . " thành " . $request->config_value,
                'fk_key' => 'tbl_system_config|id',
                'fk_value' => $config->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật cấu hình thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Đã xảy ra lỗi khi cập nhật cấu hình: ' . $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Hiển thị danh sách hoa hồng
     */
    public function commissions()
    {
        $commissions = ContractCommission::with(['contract', 'user'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
                      
        return view('dashboard.setting.commissions', compact('commissions'));
    }
}