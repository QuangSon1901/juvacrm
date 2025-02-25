<?php

namespace App\Http\Controllers\Dashboard\Customer\Client;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Customer;
use App\Models\CustomerClass;
use App\Models\CustomerLead;
use App\Models\Service;
use App\Services\LogService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function detail($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return abort(404, 'Khách hàng không tồn tại.');
        }

        $result = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'note' => $customer->note,
            'services' => $customer->getServicesArray(),
            'company' => $customer->company,
            'status' => [
                'id' => $customer->status->id,
                'name' => $customer->status->name,
                'color' => $customer->status->color,
            ],
            'classification' => [
                'id' => $customer->classification->id,
                'name' => $customer->classification->name,
                'color' => $customer->classification->color,
            ],
            'source' => $customer->source,
            'staff' => $customer->user,
            'updated_at' => $customer->updated_at,
            'created_at' => $customer->created_at,
            'contacts' => $customer->getContactsArray(),
        ];

        $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $classes = CustomerClass::select('id', 'name')->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();
        $leads = CustomerLead::select('id', 'name', 'type')->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();
        $sources = [];
        $contacts = [];
        $statuses = [];

        foreach ($leads as $lead) {
            switch ($lead['type']) {
                case 0:
                    array_push($contacts, $lead);
                    break;
                case 1:
                    array_push($sources, $lead);
                    break;
                case 2:
                    array_push($statuses, $lead);
                    break;
            }
        }

        $activity_logs = ActivityLogs::where('action', CUSTOMER_ENUM_LOG)->where('fk_key', 'tbl_customers|id')->where('fk_value', $id)->orderBy('created_at', 'desc')->limit(TABLE_PERPAGE_NUM)->get()->map(function ($log, $index) {
            return [
                'index' => $index,
                'id' => $log->id,
                'action' => $log->action,
                'ip' => $log->ip,
                'details' => $log->details,
                'user' => [
                    'id' => $log->user->id,
                    'name' => $log->user->name,
                ],
                'created_at' => $log->created_at,
            ];
        });

        return view("dashboard.customer.client.detail", [
            'details' => $result,
            'services' => $services,
            'classes' => $classes,
            'sources' => $sources,
            'contacts' => $contacts,
            'statuses' => $statuses,
            'activity_logs' => $activity_logs,
        ]);
    }

    public function createView()
    {
        $services = Service::where('is_active', 1)->get();
        $classes = CustomerClass::where('is_active', 1)->orderBy('sort', 'asc')->get();
        $leads = CustomerLead::where('is_active', 1)->orderBy('sort', 'asc')->get();
        $sources = [];
        $contacts = [];
        $status = [];

        foreach ($leads as $lead) {
            switch ($lead->type) {
                case 0:
                    array_push($contacts, $lead);
                    break;
                case 1:
                    array_push($sources, $lead);
                    break;
                case 2:
                    array_push($status, $lead);
                    break;
            }
        }

        return view("dashboard.customer.client.create", [
            'services' => $services,
            'classes' => $classes,
            'sources' => $sources,
            'contacts' => $contacts,
            'status' => $status,
        ]);
    }

    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:tbl_customers,email',
            'address' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'nullable|integer',
            'source_id' => 'nullable|integer|exists:tbl_customer_lead,id',
            'services' => 'nullable',
            'class_id' => 'nullable|integer|exists:tbl_customer_class,id',
            'status_id' => 'nullable|integer|exists:tbl_customer_lead,id',
            'contacts' => 'nullable',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $data = $request->all();
            $data['contact_methods'] = implode("|", $data['contacts'] ?? []);
            $data['services'] = implode("|", $data['services'] ?? []);
            $newCustomer = Customer::create($data);

            return response()->json([
                'status' => 200,
                'message' => 'Khách hàng đã được lưu thành công!',
                'data' => [
                    'id' => $newCustomer->id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã tạo khách hàng mới',
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $response->data->id,
            ]);
        });
    }

    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:tbl_customers,email',
            'address' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'services' => 'nullable',
            'source_id' => 'nullable|integer|exists:tbl_customer_lead,id',
            'class_id' => 'nullable|integer|exists:tbl_customer_class,id',
            'status_id' => 'nullable|integer|exists:tbl_customer_lead,id',
            'contacts' => 'nullable',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $customer = Customer::find($request['id']);

            if (!$customer) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Khách hàng không tồn tại.',
                ]);
            }

            $data = $request->only(['name', 'phone', 'email', 'address', 'company', 'services', 'source_id', 'class_id', 'status_id', 'note']);
            if (!empty($data['services'])) {
                $data['services'] = implode('|', $data['services']);
            }
            if (!empty($request['contacts'])) {
                $data['contact_methods'] = implode('|', $request['contacts']);
            }

            $customer->update($data);

            return response()->json([
                'status' => 200,
                'message' => 'Đã cập nhật ' . FIELD_VALIDATE[array_diff(array_keys($data), ['id'])[0]],
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => $response->message,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $request['id'],
            ]);
        });
    }

    public function blackList(Request $request)
    {
        return tryCatchHelper($request, function () use ($request) {
            $customer = Customer::find($request['id'] ?? 0);

            if (!$customer) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Khách hàng không tồn tại.'
                ]);
            }

            $beforeChange = $customer->is_active;

            $customer->update(['is_active' => !$beforeChange]);

            return response()->json([
                'status' => 200,
                'message' => $beforeChange ? 'Cho vào danh sách đen thành công.' : 'Gỡ khỏi danh sách đen thành công',
            ], 200);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => $response->message,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $request['id'],
            ]);
        });
    }
}
