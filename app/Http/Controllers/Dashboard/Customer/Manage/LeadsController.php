<?php

namespace App\Http\Controllers\Dashboard\Customer\Manage;

use App\Http\Controllers\Controller;
use App\Models\CustomerLead;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class LeadsController extends Controller
{
    public function index() {
        $contact_methods = CustomerLead::where('type', 0)->orderBy('sort')->get()->toArray();
        $sources = CustomerLead::where('type', 1)->orderBy('sort')->get()->toArray();
        $statuses = CustomerLead::where('type', 2)->orderBy('sort')->get()->toArray();

        return view("dashboard.customer.manage.leads.index", ['contact_methods' => $contact_methods, 'sources' => $sources, 'statuses' => $statuses]);
    }
    
    public function leadsPost(Request $request) {
        $validator = ValidatorService::make($request, [
            'id' => 'required|int',
            'name' => 'required|string|max:255',
            'sort' => 'required|int',
            'type' => 'required|int',
            'color' => 'required|string|in:success,warning,primary,gray,danger,neutral',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $config = CustomerLead::find($request['id']);
            $data = $request->only('name', 'sort', 'color', 'type');
            if ($config) {
                $config->update($data);
            } else {
                $newTask = CustomerLead::create($data);
            }

            LogService::saveLog([
                'action' => CONFIG_TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => $config ? 'Chỉnh sửa lead khách hàng #' . $request['id'] : 'Thêm lead khách hàng #' . $newTask->id,
                'fk_key' => 'tbl_customer_lead|id',
                'fk_value' => $config ? $request['id'] : $newTask->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => $config ? 'Chỉnh sửa lead khách hàng thành công.' : 'Thêm lead khách hàng thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra.',
            ]);
        }
    }

    public function leadsChangeStatus(Request $request) {
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
            $config = CustomerLead::find($request['id']);
            if (!$config) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lead không tồn tại.',
                ]);
            }
            $config->update(['is_active' => $request['is_active']]);

            LogService::saveLog([
                'action' => CONFIG_TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => "Vừa cập nhật lại trạng thái của #" .$request['id'],
                'fk_key' => 'tbl_customer_lead|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật trạng thái thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra.',
            ]);
        }
    }
}
