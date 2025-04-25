<?php

namespace App\Http\Controllers\Dashboard\Account\Task;

use App\Http\Controllers\Controller;
use App\Models\TaskConfig;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class TaskConfigController extends Controller
{
    /**
     * Hiển thị trang thiết lập công việc
     */
    public function index()
    {
        $priorities = TaskConfig::where('type', 0)->orderBy('sort')->get()->toArray();
        $statuses = TaskConfig::where('type', 1)->orderBy('sort')->get()->toArray();
        $issues = TaskConfig::where('type', 2)->orderBy('sort')->get()->toArray();
        
        // Lấy danh sách nhiệm vụ từ TaskMissionController
        $missions = app(TaskMissionController::class)->getAllMissions();

        return view("dashboard.account.task.config", [
            'priorities' => $priorities, 
            'statuses' => $statuses, 
            'issues' => $issues,
            'missions' => $missions
        ]);
    }

    /**
     * Cập nhật hoặc tạo mới cấu hình task
     */
    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|int',
            'name' => 'required|string|max:255',
            'sort' => 'required|int',
            'type' => 'required|int',
            'color' => 'required|string|in:success,warning,primary,gray,danger,neutral,info',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $config = TaskConfig::find($request['id']);
            $data = $request->only('name', 'sort', 'color', 'type');
            if ($config) {
                $config->update($data);
                $action = 'Chỉnh sửa cấu hình #' . $request['id'];
                $configId = $request['id'];
            } else {
                $newConfig = TaskConfig::create($data);
                $action = 'Thêm cấu hình #' . $newConfig->id;
                $configId = $newConfig->id;
            }

            LogService::saveLog([
                'action' => CONFIG_TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => $action,
                'fk_key' => 'tbl_task_config|id',
                'fk_value' => $configId,
            ]);

            return response()->json([
                'status' => 200,
                'message' => $config ? 'Chỉnh sửa cấu hình thành công.' : 'Thêm cấu hình thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Thay đổi trạng thái (kích hoạt/vô hiệu hóa)
     */
    public function changeStatus(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|int',
            'is_active' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $config = TaskConfig::find($request['id']);
            if (!$config) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Cấu hình không tồn tại.',
                ]);
            }
            $config->update(['is_active' => $request['is_active']]);

            LogService::saveLog([
                'action' => CONFIG_TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => "Vừa cập nhật trạng thái của #" . $request['id'],
                'fk_key' => 'tbl_task_config|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật trạng thái thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }
}