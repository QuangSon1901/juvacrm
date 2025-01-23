<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationLog;
use App\Models\Customer;
use App\Models\CustomerClass;
use App\Models\CustomerLead;
use App\Models\Service;
use App\Models\Upload;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerSupportController extends Controller
{
    public function index()
    {
        $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $statuses = CustomerLead::select('id', 'name', 'color')->where('type', 2)->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();
        $classes = CustomerClass::select('id', 'name', 'color')->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();

        return view("dashboard.customer.support.index", [
            'services' => $services,
            'statuses' => $statuses,
            'classes' => $classes,
        ]);
    }

    public function consultation($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return abort(404, 'Khách hàng không tồn tại.');
        }

        if ($customer->consultations->isEmpty()) {
            $consultations = [
                ['customer_id' => $id, 'name' => 'Nhật ký lần 1'],
                ['customer_id' => $id, 'name' => 'Nhật ký lần 2'],
                ['customer_id' => $id, 'name' => 'Nhật ký lần 3'],
            ];
            Consultation::insert($consultations);
        }

        $customer = Customer::find($id);
        $result = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'classification' => [
                'id' => $customer->classification->id,
                'name' => $customer->classification->name,
                'color' => $customer->classification->color
            ],
            'status' => [
                'id' => $customer->status->id,
                'name' => $customer->status->name,
                'color' => $customer->status->color
            ],
            'updated_at' => $customer->updated_at,
            'consultations' => $customer->consultations->map(function ($cons, $_) {
                return [
                    "index" => ++$_,
                    "id" => $cons->id,
                    "name" => $cons->name,
                    "updated_at" => $cons->updated_at
                ];
            })
        ];
        return view("dashboard.customer.support.consultation", ['details' => $result]);
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        $customersQuery = Customer::query()
            ->filterMyCustomer($request['filter']['my_customer'] ?? 0)
            ->filterBlackList($request['filter']['black_list'] ?? 1)
            ->filterByServices($request['filter']['services'] ?? '')
            ->filterByStatus($request['filter']['status_id'] ?? 0)
            ->filterByClass($request['filter']['class_id'] ?? 0)
            ->filterByType($request['filter']['lead'] ?? 1)
            ->search($request['filter']['search'] ?? '');

        $paginationResult = PaginationService::paginate($customersQuery, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($customer, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'services' => $customer->getServicesArray(),
                'company' => $customer->company,
                'status' => [
                    'id' => $customer->status->id ?? 0,
                    'name' => $customer->status->name ?? '',
                    'color' => $customer->status->color ?? '',
                ],
                'classification' => [
                    'id' => $customer->classification->id ?? 0,
                    'name' => $customer->classification->name ?? '',
                    'color' => $customer->classification->color ?? '',
                ],
                'source' => $customer->source,
                'staff' => [
                    'id' => $customer->user->id ?? 0,
                    'name' => $customer->user->name ?? '',
                ],
                'updated_at' => $customer->updated_at,
                'is_active' => $customer->is_active,
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.customer.support.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function consultationRemove(Request $request) {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_consultations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $consultation = Consultation::find($request['id']);
            $consultation->update(['is_deleted' => 1]);

            return response()->json([
                'status' => 200,
                'message' => 'Xoá thành công!',
                'data' => [
                    'customer_id' => $consultation->customer_id,
                    'name' => $consultation->name,
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã xoá nhật ký "' . $response->data->name .'" của khách hàng #' . $response->data->customer_id,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $response->data->customer_id,
            ]);
        });
    }

    public function consultationLog(Request $request) {
        $consultation = Consultation::find($request['id']);
        if (!$consultation) {
            return abort(404, 'Khách hàng không tồn tại.');
        }

        $consultationLogs = ConsultationLog::where('consultation_id', $consultation->id)->get()->map(function($log, $_) {
            return [
                'index' => ++$_,
                'id' => $log->id,
                'message' => $log->message,
                'status' => $log->action,
                'created_at' => $log->created_at,
                'attachments' => $log->getAttachmentsArray(),
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.customer.support.components.consultation_item', ['data' => $consultationLogs])->render(),
        ]);
    }

    public function consultationUpdate(Request $request) {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255',
            'id' => 'required|integer|exists:tbl_consultations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $consultation = Consultation::find($request['id']);
            $consultation->update(['name' => $request['name']]);

            return response()->json([
                'status' => 200,
                'message' => 'Chỉnh sửa thành công!',
                'data' => [
                    'customer_id' => $consultation->customer_id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã cập nhật tiêu đề nhật ký "' . $request['name'] .'" cho khách hàng #' . $response->data->customer_id,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $response->data->customer_id,
            ]);
        });
    }

    public function consultationCreate(Request $request)
    {

        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255',
            'customer_id' => 'required|integer|exists:tbl_customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {

            $data = $request->only('name', 'customer_id');
            $consultation = Consultation::create($data);

            return response()->json([
                'status' => 200,
                'message' => 'Lưu thành công!',
                'data' => [
                    'id' => $consultation->id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã tạo nhật ký "' . $request['name'] .'" cho khách hàng #' . $request['customer_id'],
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $request['customer_id'],
            ]);
        });
    }

    public function consultationAddLog(Request $request)
    {

        $validator = ValidatorService::make($request, [
            'message' => 'required|string|max:255',
            'action' => 'nullable|integer',
            'consultation_id' => 'required|integer|exists:tbl_consultations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        
        return tryCatchHelper($request, function () use ($request) {
            $data = $request->only('message', 'action', 'consultation_id');
            $data['user_id'] = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $consultationLog = ConsultationLog::create($data);

            if (isset($request['attachment']) && count($request['attachment']) > 0) {
                foreach ($request['attachment'] as $attachment) {
                    $upload = Upload::where('driver_id', $attachment)->first();
                    if ($upload) {
                        $upload->update(['fk_key' => 'tbl_consultation_logs|id', 'fk_value' => $consultationLog->id]);
                    }
                }
            }

            $consultation = Consultation::find($request['consultation_id']);

            return response()->json([
                'status' => 200,
                'message' => 'Lưu thành công!',
                'data' => [
                    'id' => $consultationLog->id,
                    'customer_id' => $consultation->customer_id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã đăng nhật ký #' . $response->data->id,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $response->data->customer_id,
            ]);
        });
    }
}
