<?php

namespace App\Http\Controllers\Dashboard\Customer\Client;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\ConsultationLog;
use App\Models\Contract;
use App\Models\ContractPayment;
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
    // Tìm khách hàng theo ID
    $customer = Customer::find($id);
    if (!$customer) {
        return abort(404, 'Khách hàng không tồn tại.');
    }

    // Chuẩn bị dữ liệu cơ bản của khách hàng
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

    // Lấy dữ liệu danh sách
    $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
    $classes = CustomerClass::select('id', 'name')->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();
    $leads = CustomerLead::select('id', 'name', 'type')->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();
    $sources = [];
    $contacts = [];
    $statuses = [];

    // Phân loại leads theo type
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

    // Lấy nhật ký hoạt động
    $activity_logs = ActivityLogs::where('action', CUSTOMER_ENUM_LOG)
        ->where('fk_key', 'tbl_customers|id')
        ->where('fk_value', $id)
        ->orderBy('created_at', 'desc')
        ->limit(TABLE_PERPAGE_NUM)
        ->get()
        ->map(function ($log, $index) {
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
        
    // ----- THỐNG KÊ KHÁCH HÀNG -----
    
    // 1. Thống kê hợp đồng
    $contracts = Contract::where('provider_id', $id)->get();
    $totalContracts = $contracts->count();
    $completedContracts = $contracts->where('status', 2)->count();
    $activeContracts = $contracts->where('status', 1)->count();
    $canceledContracts = $contracts->where('status', 3)->count();
    
    // 2. Thống kê tài chính
    $totalContractValue = $contracts->sum('total_value');
    
    // Tính tổng số tiền đã thanh toán
    $paidAmount = 0;
    $dueAmount = 0;
    
    foreach ($contracts as $contract) {
        // Số tiền đã thanh toán (không tính các khoản trừ, payment_stage != 3)
        $paid = ContractPayment::where('contract_id', $contract->id)
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('payment_stage', '!=', 3)
            ->sum('price');
            
        // Số tiền đã trừ (payment_stage = 3)
        $deducted = ContractPayment::where('contract_id', $contract->id)
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('payment_stage', 3)
            ->sum('price');
        
        $paidAmount += $paid;
        $dueAmount += ($contract->total_value - $paid + abs($deducted));
    }
    
    // 3. Thống kê phương thức thanh toán
    $paymentMethods = ContractPayment::whereIn('contract_id', $contracts->pluck('id'))
        ->where('status', 1)
        ->where('is_active', 1)
        ->with('method')
        ->get()
        ->groupBy(function($payment) {
            return $payment->method ? $payment->method->name : 'Không xác định';
        })
        ->map(function ($group) {
            return $group->count();
        });
    
    // 4. Danh sách hợp đồng gần đây, sắp xếp theo thời gian tạo
    $recentContracts = $contracts->sortByDesc('created_at')
        ->take(3)
        ->map(function($contract) {
            return [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'name' => $contract->name,
                'total_value' => $contract->total_value,
                'status' => $contract->status,
                'effective_date' => $contract->effective_date,
                'expiry_date' => $contract->expiry_date,
                'status_color' => $this->getContractStatusColor($contract->status),
                'status_text' => $this->getContractStatusText($contract->status)
            ];
        })->values();

    // Trả về view với tất cả dữ liệu
    return view("dashboard.customer.client.detail", [
        'details' => $result,
        'services' => $services,
        'classes' => $classes,
        'sources' => $sources,
        'contacts' => $contacts,
        'statuses' => $statuses,
        'activity_logs' => $activity_logs,
        
        // Dữ liệu thống kê mới
        'contract_stats' => [
            'total' => $totalContracts,
            'completed' => $completedContracts,
            'active' => $activeContracts,
            'canceled' => $canceledContracts
        ],
        'financial_stats' => [
            'total_value' => $totalContractValue,
            'paid_amount' => $paidAmount,
            'due_amount' => max(0, $dueAmount)  // Đảm bảo không hiển thị số âm
        ],
        'payment_methods' => $paymentMethods,
        'recent_contracts' => $recentContracts
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

    private function getConsultationActionText($action) {
        switch ($action) {
            case 0: return 'Hỏi nhu cầu KH';
            case 1: return 'Tư vấn gói';
            case 2: return 'Lập hợp đồng';
            case 3: return 'Gửi bảng giá';
            case 4: return 'Khách từ chối';
            case 5: return 'Đặt lịch tư vấn lại';
            default: return 'Tư vấn';
        }
    }

    /**
 * Lấy màu hiển thị cho trạng thái hợp đồng
 * 
 * @param int $status
 * @return string
 */
private function getContractStatusColor($status)
{
    switch ($status) {
        case 0: return 'gray';    // Chờ duyệt
        case 1: return 'primary'; // Đang triển khai
        case 2: return 'success'; // Hoàn thành
        case 3: return 'danger';  // Đã hủy
        default: return 'gray';
    }
}

/**
 * Lấy tên trạng thái hợp đồng
 * 
 * @param int $status
 * @return string
 */
private function getContractStatusText($status)
{
    switch ($status) {
        case 0: return 'Chờ duyệt';
        case 1: return 'Đang triển khai';
        case 2: return 'Hoàn thành';
        case 3: return 'Đã hủy';
        default: return 'Không xác định';
    }
}
}
