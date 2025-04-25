<?php

namespace App\Http\Controllers\Dashboard\Account\Task;

use App\Http\Controllers\Controller;
use App\Models\TaskMission;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class TaskMissionController extends Controller
{
    /**
     * Lấy tất cả nhiệm vụ
     */
    public function getAllMissions()
    {
        return TaskMission::orderBy('name')->get()->toArray();
    }
    
    /**
     * Lấy thông tin chi tiết của nhiệm vụ
     */
    public function show($id)
    {
        try {
            $mission = TaskMission::findOrFail($id);
            
            return response()->json([
                'status' => 200,
                'data' => $mission,
                'message' => 'Lấy thông tin nhiệm vụ thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy thông tin nhiệm vụ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cập nhật hoặc tạo mới nhiệm vụ
     */
    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|int',
            'name' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $mission = TaskMission::find($request['id']);
            $data = $request->only('name', 'salary', 'description');
            
            if ($mission) {
                // Cập nhật nhiệm vụ hiện có
                $mission->update($data);
                $logMessage = "Chỉnh sửa nhiệm vụ #{$mission->id}";
                $id = $mission->id;
            } else {
                // Tạo nhiệm vụ mới
                $data['is_active'] = 1;
                $mission = TaskMission::create($data);
                $logMessage = "Thêm nhiệm vụ #{$mission->id}";
                $id = $mission->id;
            }

            LogService::saveLog([
                'action' => 'TASK_MISSION_CONFIG',
                'ip' => $request->getClientIp(),
                'details' => $logMessage,
                'fk_key' => 'tbl_task_missions|id',
                'fk_value' => $id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => $mission->wasRecentlyCreated ? 'Thêm nhiệm vụ thành công.' : 'Cập nhật nhiệm vụ thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật nhiệm vụ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Thay đổi trạng thái nhiệm vụ (kích hoạt/vô hiệu hóa)
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
            $mission = TaskMission::find($request['id']);
            if (!$mission) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Nhiệm vụ không tồn tại.',
                ]);
            }
            
            $mission->update(['is_active' => $request['is_active']]);

            LogService::saveLog([
                'action' => 'TASK_MISSION_CONFIG',
                'ip' => $request->getClientIp(),
                'details' => "Vừa cập nhật trạng thái của nhiệm vụ #{$request['id']} thành " . 
                             ($request['is_active'] ? 'kích hoạt' : 'vô hiệu hóa'),
                'fk_key' => 'tbl_task_missions|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật trạng thái nhiệm vụ thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi thay đổi trạng thái: ' . $e->getMessage()
            ]);
        }
    }
}