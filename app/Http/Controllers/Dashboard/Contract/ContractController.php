<?php

namespace App\Http\Controllers\Dashboard\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\ContractService;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    public function index() {
        return view("dashboard.contract.index");
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        // Xây dựng query cơ bản
        $query = Contract::query()
            ->with(['user', 'customer']) // Load relationships
            ->when($request->input('filter.search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('contract_number', 'like', "%{$search}%");
            })
            ->when($request->input('filter.my_contract'), function ($query) use ($request) {
                $query->where('user_id', auth()->id()); // Lọc hợp đồng của tôi
            })
            ->when($request->input('filter.status'), function ($query, $status) {
                $query->where('status', $status); // Lọc theo trạng thái nếu có
            });

        // Phân trang
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        // Format dữ liệu trả về
        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'contract_number' => $item->contract_number,
                'name' => $item->name,
                'user' => [
                    'id' => $item->user->id ?? 0,
                    'name' => $item->user->name ?? 'N/A',
                ],
                'customer' => [
                    'id' => $item->customer->id ?? 0,
                    'name' => $item->customer->name ?? 'N/A',
                ],
                'sign_date' => $item->sign_date,
                'effective_date' => $item->effective_date,
                'expiry_date' => $item->expiry_date,
                'total_value' => $item->total_value,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.contract.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function createView(Request $request) {
        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $customers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();
        $categories = ServiceCategory::where('is_active', 1)->get()->toArray();
        $services = Service::where('is_active', 1)->get()->toArray();
        $payments = PaymentMethod::where('is_active', 1)->get()->toArray();
        $currencies = Currency::where('is_active', 1)->get()->toArray();

        $details = [
            'users' => $users,
            'customers' => $customers,
            'categories' => $categories,
            'services' => $services,
            'payments' => $payments,
            'currencies' => $currencies,
        ];

        if (isset($request['customer_id'])) {
            $customer = Customer::select('id', 'name', 'phone', 'email', 'address')->find($request['customer_id']);
            if ($customer) {
                $details['customer'] = $customer;
            }
        }

        return view("dashboard.contract.create", ['details' => $details]);
    }

    public function create(Request $request)
    {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            // 1. Validate và lưu thông tin hợp đồng
            $contractValidator = ValidatorService::make($request, [
                'name' => 'required|string|max:255',
                'user_id' => 'required|integer|exists:tbl_users,id',
                'provider_id' => 'required|integer|exists:tbl_customers,id',
                'category_id' => 'nullable|integer',
                'company_name' => 'required|string|max:255',
                'tax_code' => 'nullable|string|max:50',
                'company_address' => 'nullable|string|max:255',
                'customer_representative' => 'nullable|string|max:255',
                'customer_tax_code' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'sign_date' => 'nullable|date',
                'effective_date' => 'nullable|date',
                'expiry_date' => 'nullable|date|after:effective_date',
                'estimate_day' => 'nullable|integer',
                'estimate_date' => 'nullable|date',
                'total_value' => 'nullable|numeric',
                'note' => 'nullable|string|max:500',
                'terms_and_conditions' => 'nullable|string|max:5000',
                'status' => 'required|in:0,1',
            ]);

            if ($contractValidator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $contractValidator->errors()->first()
                ]);
            }

            $contractData = $request->only([
                'name', 'user_id', 'provider_id', 'category_id',
                'company_name', 'tax_code', 'company_address', 'customer_representative',
                'customer_tax_code', 'address', 'phone', 'sign_date', 'effective_date',
                'expiry_date', 'estimate_day', 'estimate_date', 'total_value', 'note',
                'terms_and_conditions', 'status'
            ]);

            $lastContract = Contract::orderBy('id', 'desc')->first();
            $nextNumber = $lastContract ? (int)substr($lastContract->contract_number, -3) + 1 : 1;
            $contractData['contract_number'] = 'HD-2025-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Format ngày tháng
            if (!empty($contractData['sign_date'])) {
                $contractData['sign_date'] = formatDateTime($contractData['sign_date'], 'Y-m-d', 'd-m-Y');
            }
            if (!empty($contractData['effective_date'])) {
                $contractData['effective_date'] = formatDateTime($contractData['effective_date'], 'Y-m-d', 'd-m-Y');
            }
            if (!empty($contractData['expiry_date'])) {
                $contractData['expiry_date'] = formatDateTime($contractData['expiry_date'], 'Y-m-d', 'd-m-Y');
            }
            if (!empty($contractData['estimate_date'])) {
                $contractData['estimate_date'] = formatDateTime($contractData['estimate_date'], 'Y-m-d', 'd-m-Y');
            }

            $contractData['created_id'] = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $contract = Contract::create($contractData);

            // Logging
            LogService::saveLog([
                'action' => 'CREATE_CONTRACT',
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo hợp đồng #' . $contract->contract_number,
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);

            // Chỉ tạo task nếu status = 1 (Đang triển khai)
            if ($contract->status == 1) {
                // 2. Tạo task chính cho hợp đồng
                $mainTaskData = [
                    'name' => "Hợp đồng #$contract->contract_number - $contract->name",
                    'status_id' => 1, // Giả định trạng thái mặc định
                    'priority_id' => 1, // Giả định độ ưu tiên mặc định
                    'assign_id' => $contract->user_id, // Gán cho nhân viên phụ trách
                    'start_date' => $contract->effective_date,
                    'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                    'estimate_time' => $contract->estimate_day * 24, // Quy đổi ngày thành giờ
                    'description' => "Công việc tổng thể cho hợp đồng #$contract->contract_number",
                    'qty_request' => 1, // Task chính không cần số lượng cụ thể
                    'contract_id' => $contract->id,
                    'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                ];

                $mainTask = Task::create($mainTaskData);

                LogService::saveLog([
                    'action' => 'TASK_ENUM_LOG',
                    'ip' => $request->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task chính cho hợp đồng #' . $contract->contract_number . ' - ' . $contract->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $mainTask->id,
                ]);
            }

            // 3. Lưu thông tin dịch vụ và tạo task con dựa trên parent_id
            $services = $request->input('service_ids', []);
            $quantities = $request->input('service_quantity', []);
            $prices = $request->input('service_price', []);
            $notes = $request->input('service_note', []);

            if (empty($services)) {
                throw new \Exception('Vui lòng thêm ít nhất một dịch vụ cho hợp đồng.');
            }

            foreach ($services as $index => $serviceId) {
                // Validate dữ liệu dịch vụ
                $serviceValidator = Validator::make([
                    'service_id' => $serviceId,
                    'quantity' => $quantities[$index] ?? null,
                    'price' => $prices[$index] ?? null,
                    'note' => $notes[$index] ?? null,
                ], [
                    'service_id' => 'required|integer|exists:tbl_services,id',
                    'quantity' => 'required|integer|min:1',
                    'price' => 'required|numeric|min:0',
                    'note' => 'nullable|string|max:255',
                ]);

                if ($serviceValidator->fails()) {
                    throw new \Exception('Dữ liệu dịch vụ không hợp lệ: ' . $serviceValidator->errors()->first());
                }

                // Lưu thông tin dịch vụ vào bảng contract_services
                ContractService::create([
                    'contract_id' => $contract->id,
                    'service_id' => $serviceId,
                    'quantity' => $quantities[$index],
                    'price' => $prices[$index],
                    'note' => $notes[$index] ?? null,
                ]);

                // Chỉ tạo task nếu status = 1 (Đang triển khai)
                if ($contract->status == 1) {
                    $service = Service::find($serviceId);
                    $serviceName = $service ? $service->name : "Dịch vụ #$serviceId";

                    // Tạo task con cho từng dịch vụ
                    $subTaskData = [
                        'name' => "$serviceName",
                        'status_id' => 1, // Giả định trạng thái mặc định
                        'priority_id' => 1, // Giả định độ ưu tiên mặc định
                        'assign_id' => $contract->user_id, // Gán cho nhân viên phụ trách
                        'start_date' => $contract->effective_date,
                        'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                        'estimate_time' => $contract->estimate_day * 24, // Quy đổi ngày thành giờ
                        'description' => "Công việc thực hiện $serviceName cho hợp đồng #$contract->contract_number",
                        'qty_request' => $quantities[$index],
                        'contract_id' => $contract->id,
                        'service_id' => $serviceId,
                        'parent_id' => $mainTask->id, // Liên kết với task chính
                        'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                    ];

                    $subTask = Task::create($subTaskData);

                    LogService::saveLog([
                        'action' => 'TASK_ENUM_LOG',
                        'ip' => $request->getClientIp(),
                        'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task con cho dịch vụ #' . $serviceId,
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $subTask->id,
                    ]);
                }
            }

            // 4. Lưu thông tin biên nhận (thanh toán)
            $paymentNames = $request->input('payment_name', []);
            $prices = $request->input('payment_price', []);
            $currencies = $request->input('payment_currencies', []);
            $methods = $request->input('payment_methods', []);
            $dates = $request->input('payment_due_dates', []);
            $stages = $request->input('payment_stage', []); // Thêm payment_stage
            $statuses = $request->input('payment_status', []); // Thêm payment_status từ checkbox

            if (!empty($paymentNames)) {
                foreach ($paymentNames as $index => $paymentName) {
                    $paymentValidator = Validator::make([
                        'name' => $paymentName,
                        'price' => $prices[$index] ?? null,
                        'currency_id' => $currencies[$index] ?? null,
                        'method_id' => $methods[$index] ?? null,
                        'due_date' => $dates[$index] ?? '',
                        'stage' => $stages[$index] ?? null,
                        'status' => $statuses[$index] ?? 0,
                    ], [
                        'name' => 'required|string|max:255',
                        'price' => 'required|numeric|min:0',
                        'currency_id' => 'required|integer|exists:tbl_currencies,id',
                        'method_id' => 'required|integer|exists:tbl_payment_methods,id',
                        'due_date' => 'required|date_format:d-m-Y H:i:s',
                        'stage' => 'required|in:0,1,2,3', // Validate payment_stage
                        'status' => 'nullable|in:0,1',
                    ]);

                    if ($paymentValidator->fails()) {
                        throw new \Exception('Dữ liệu biên nhận không hợp lệ: ' . $paymentValidator->errors()->first());
                    }

                    $pushData = [
                        'contract_id' => $contract->id,
                        'name' => $paymentName,
                        'price' => $prices[$index],
                        'currency_id' => $currencies[$index],
                        'method_id' => $methods[$index],
                        'payment_stage' => $stages[$index],
                        'status' => $statuses[$index] ?? 0,
                        'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                    ];

                    if (!empty($dates[$index])) {
                        $pushData['due_date'] = formatDateTime($dates[$index], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
                    }

                    $payment = ContractPayment::create($pushData);

                    if ($payment->status == 1) {
                        $transactionType = $stages[$index] == 3 ? 1 : 0; // Deduction là chi (1), các loại khác là thu (0)
                        $categoryName = $stages[$index] == 0 ? 'Deposit' : ($stages[$index] == 1 ? 'Bonus' : ($stages[$index] == 2 ? 'Final Payment' : 'Deduction'));

                        // Tìm hoặc tạo hạng mục thu/chi
                        $category = TransactionCategory::firstOrCreate(
                            ['type' => $transactionType, 'name' => $categoryName],
                            ['note' => "Hạng mục cho $categoryName", 'is_active' => 1]
                        );

                        Transaction::create([
                            'type' => $transactionType,
                            'category_id' => $category->id,
                            'target_client_id' => $contract->provider_id, // Đối tượng là khách hàng của hợp đồng
                            'payment_id' => $payment->id,
                            'amount' => $payment->price,
                            'paid_date' => $payment->due_date,
                            'status' => 1, // Hoàn tất vì đã thanh toán
                            'note' => "Tự động tạo từ biên nhận #{$payment->id} của hợp đồng #{$contract->contract_number}",
                            'reason' => $payment->name,
                        ]);
                    }
                }
            }

            // Commit transaction nếu mọi thứ thành công
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Hợp đồng đã được tạo thành công.',
            ]);
        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage() ?? 'Đã xảy ra lỗi khi tạo hợp đồng.',
            ]);
        }
    }

    public function detail($id)
    {
        $contract = Contract::with(['user', 'customer', 'services.service', 'payments', 'tasks'])
            ->findOrFail($id);

        $details = [
            'id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'name' => $contract->name,
            'user' => [
                'id' => $contract->user->id ?? 0,
                'name' => $contract->user->name ?? 'N/A',
            ],
            'customer' => [
                'id' => $contract->customer->id ?? 0,
                'name' => $contract->customer->name ?? 'N/A',
            ],
            'sign_date' => $contract->sign_date,
            'effective_date' => $contract->effective_date,
            'expiry_date' => $contract->expiry_date,
            'total_value' => $contract->total_value,
            'status' => $contract->status ?? 'Chờ duyệt',
            'note' => $contract->note,
            'terms_and_conditions' => $contract->terms_and_conditions,
            'created_at' => $contract->created_at,
            'updated_at' => $contract->updated_at,
            'services' => $contract->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'service_id' => $service->service_id,
                    'name' => $service->service->name ?? 'N/A',
                    'quantity' => $service->quantity,
                    'price' => $service->price,
                    'note' => $service->note,
                    'is_active' => $service->is_active,
                ];
            })->toArray(),
            'payments' => $contract->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'name' => $payment->name,
                    'percentage' => $payment->percentage,
                    'price' => $payment->price,
                    'currency' => $payment->currency->currency_code ?? 'N/A',
                    'method' => $payment->method->name ?? 'N/A',
                    'due_date' => $payment->due_date ? formatDateTime($payment->due_date, 'd-m-Y H:i:s') : '',
                    'payment_stage' => $payment->payment_stage,
                    'status' => $payment->status,
                    'currency_id' => $payment->currency_id,
                    'method_id' => $payment->method_id,
                ];
            })->toArray(),
            'tasks' => $contract->tasks->filter(function ($task) {
                return is_null($task->service_id); // Chỉ lấy công việc chính (service_id là null)
            })->map(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'status' => [
                        'name' => $task->status->name ?? 'N/A',
                        'color' => $task->status->color ?? 'gray',
                    ],
                    'assign' => [
                        'id' => $task->assign->id ?? 0,
                        'name' => $task->assign->name ?? 'N/A',
                    ],
                    'start_date' => $task->start_date,
                    'due_date' => $task->due_date,
                    'qty_request' => $task->qty_request,
                    'qty_completed' => $task->qty_completed,
                ];
            })->toArray(),
        ];

        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $customers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();
        $categories = ServiceCategory::where('is_active', 1)->get()->toArray();
        $services = Service::where('is_active', 1)->get()->toArray();
        $payment_methods = PaymentMethod::where('is_active', 1)->get()->toArray();
        $currencies = Currency::where('is_active', 1)->get()->toArray();

        return view('dashboard.contract.detail', compact('details', 'users', 'customers', 'categories', 'services', 'payment_methods', 'currencies'));
    }

    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_contracts,id',
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|integer|in:1,2', // Giả định các trạng thái hợp lệ
            'user_id' => 'nullable|integer|exists:tbl_users,id',
            'provider_id' => 'nullable|integer|exists:tbl_customers,id',
            'category_id' => 'nullable|integer|exists:tbl_categories,id',
            'sign_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'note' => 'nullable|string|max:500',
            'terms_and_conditions' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $contract = Contract::find($request['id']);

            if (!$contract) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Hợp đồng không tồn tại.',
                ]);
            }

            $data = $request->only([
                'name',
                'status',
                'user_id',
                'provider_id',
                'category_id',
                'sign_date',
                'effective_date',
                'expiry_date',
                'note',
                'terms_and_conditions',
            ]);

            // Format ngày tháng nếu có
            if (!empty($data['sign_date'])) {
                $data['sign_date'] = formatDateTime($data['sign_date'], 'Y-m-d', 'd-m-Y');
            }
            if (!empty($data['effective_date'])) {
                $data['effective_date'] = formatDateTime($data['effective_date'], 'Y-m-d', 'd-m-Y');
            }
            if (!empty($data['expiry_date'])) {
                $data['expiry_date'] = formatDateTime($data['expiry_date'], 'Y-m-d', 'd-m-Y');
            }

            // Loại bỏ các trường null để không ghi đè dữ liệu cũ bằng null
            $data = array_filter($data, function ($value) {
                return !is_null($value);
            });

            if (isset($data['status']) && $data['status'] == 1 && $contract->status == 0) {
                // Tạo task chính
                $mainTaskData = [
                    'name' => "Hợp đồng #$contract->contract_number - $contract->name",
                    'status_id' => 1,
                    'priority_id' => 1,
                    'assign_id' => $contract->user_id,
                    'start_date' => $contract->effective_date,
                    'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                    'estimate_time' => $contract->estimate_day * 24,
                    'description' => "Công việc tổng thể cho hợp đồng #{$contract->contract_number}",
                    'qty_request' => 1,
                    'contract_id' => $contract->id,
                    'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                ];
                $mainTask = Task::create($mainTaskData);

                LogService::saveLog([
                    'action' => 'TASK_ENUM_LOG',
                    'ip' => $request->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo công việc chính cho hợp đồng #' . $contract->contract_number,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $mainTask->id,
                ]);

                // Tạo task dịch vụ dựa trên danh sách dịch vụ hiện có
                foreach ($contract->services()->where('is_active', 1)->get() as $service) {
                    $serviceName = $service->service ? $service->service->name : "Dịch vụ #{$service->service_id}";
                    $serviceTaskData = [
                        'name' => "$serviceName",
                        'status_id' => 1,
                        'priority_id' => 1,
                        'assign_id' => $contract->user_id,
                        'start_date' => $contract->effective_date,
                        'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                        'estimate_time' => $contract->estimate_day * 24,
                        'description' => "Công việc thực hiện $serviceName cho hợp đồng #{$contract->contract_number}",
                        'qty_request' => $service->quantity,
                        'contract_id' => $contract->id,
                        'service_id' => $service->service_id,
                        'parent_id' => $mainTask->id,
                        'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                    ];
                    $serviceTask = Task::create($serviceTaskData);

                    LogService::saveLog([
                        'action' => 'TASK_ENUM_LOG',
                        'ip' => $request->getClientIp(),
                        'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo công việc cho dịch vụ #' . $service->service_id,
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $serviceTask->id,
                    ]);
                }
            }

            $contract->update($data);

            // Ghi log (tương tự cách bạn làm)
            $updatedField = array_key_first($data); // Lấy trường đầu tiên được cập nhật
            $fieldNames = [
                'name' => 'tên hợp đồng',
                'status' => 'trạng thái',
                'user_id' => 'nhân viên phụ trách',
                'provider_id' => 'khách hàng',
                'category_id' => 'loại dịch vụ',
                'sign_date' => 'ngày ký',
                'effective_date' => 'ngày hiệu lực',
                'expiry_date' => 'ngày hết hạn',
                'note' => 'ghi chú',
                'terms_and_conditions' => 'điều khoản chung',
            ];

            LogService::saveLog([
                'action' => 'UPDATE_CONTRACT', // Bạn có thể định nghĩa hằng số nếu cần
                'ip' => $request->getClientIp(),
                'details' => 'Đã cập nhật ' . ($fieldNames[$updatedField] ?? 'thông tin hợp đồng'),
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật hợp đồng thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật hợp đồng: ' . $e->getMessage(),
            ]);
        }
    }

    public function addService(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'contract_id' => 'required|integer|exists:tbl_contracts,id',
            'service_id' => 'required|integer|exists:tbl_services,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($request->contract_id);

            $service = ContractService::create([
                'contract_id' => $request->contract_id,
                'service_id' => $request->service_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'note' => $request->note,
                'is_active' => 1,
            ]);

            if ($contract->status != 0) {
                // Tạo task dịch vụ
                $serviceModel = Service::find($request->service_id);
                $taskData = [
                    'name' => "{$serviceModel->name}",
                    'status_id' => 1,
                    'priority_id' => 1,
                    'assign_id' => $contract->user_id,
                    'start_date' => $contract->effective_date,
                    'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                    'estimate_time' => $contract->estimate_day * 24,
                    'description' => "Công việc thực hiện {$serviceModel->name} cho hợp đồng #{$contract->contract_number}",
                    'qty_request' => $request->quantity,
                    'contract_id' => $contract->id,
                    'service_id' => $request->service_id,
                    'parent_id' => $contract->tasks()->whereNull('service_id')->first()->id, // Task chính
                    'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                ];
                Task::create($taskData);
            }

            // Cập nhật tổng giá trị hợp đồng
            $this->updateContractTotalValue($contract);

            LogService::saveLog([
                'action' => 'ADD_CONTRACT_SERVICE',
                'ip' => $request->getClientIp(),
                'details' => "Đã thêm dịch vụ #{$service->id} vào hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contract_services|id',
                'fk_value' => $service->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Thêm dịch vụ thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi thêm dịch vụ: ' . $e->getMessage(),
            ]);
        }
    }

    // Chỉnh sửa dịch vụ
    public function updateService(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_contract_services,id',
            'service_id' => 'nullable|integer|exists:tbl_services,id',
            'quantity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $service = ContractService::findOrFail($request->id);
            $contract = $service->contract;

            $data = $request->only(['service_id', 'quantity', 'price', 'note']);
            $data = array_filter($data, fn($value) => !is_null($value));
            $service->update($data);

            if ($contract->status != 0) {
                // Cập nhật task liên quan
                $task = Task::where('contract_id', $contract->id)
                            ->where('service_id', $service->service_id)
                            ->first();
                if ($task) {
                    $serviceModel = Service::find($service->service_id);
                    $task->update([
                        'name' => "{$serviceModel->name} - Hợp đồng #{$contract->contract_number}",
                        'qty_request' => $service->quantity,
                    ]);
                }
            }

            // Cập nhật tổng giá trị hợp đồng
            $this->updateContractTotalValue($contract);

            LogService::saveLog([
                'action' => 'UPDATE_CONTRACT_SERVICE',
                'ip' => $request->getClientIp(),
                'details' => "Đã cập nhật dịch vụ #{$service->id} trong hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contract_services|id',
                'fk_value' => $service->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật dịch vụ thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật dịch vụ: ' . $e->getMessage(),
            ]);
        }
    }

    // Hủy bỏ dịch vụ
    public function cancelService(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_contract_services,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $service = ContractService::findOrFail($request->id);
            $contract = $service->contract;

            // Hủy dịch vụ
            $service->update(['is_active' => 0]);

            // Hủy task liên quan
            $task = Task::where('contract_id', $contract->id)
                        ->where('service_id', $service->service_id)
                        ->first();
            if ($task) {
                $task->update(['is_active' => 0]);
            }

            // Cập nhật tổng giá trị hợp đồng
            $this->updateContractTotalValue($contract);

            LogService::saveLog([
                'action' => 'CANCEL_CONTRACT_SERVICE',
                'ip' => $request->getClientIp(),
                'details' => "Đã hủy dịch vụ #{$service->id} trong hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contract_services|id',
                'fk_value' => $service->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Hủy dịch vụ thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi hủy dịch vụ: ' . $e->getMessage(),
            ]);
        }
    }

    // Thêm biên nhận mới
    public function addPayment(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'contract_id' => 'required|integer|exists:tbl_contracts,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|integer|exists:tbl_currencies,id',
            'method_id' => 'required|integer|exists:tbl_payment_methods,id',
            'due_date' => 'required|date_format:d-m-Y H:i:s',
            'payment_stage' => 'required|in:0,1,2,3',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($request->contract_id);
            $status = $request->status ?? 0;

            $payment = ContractPayment::create([
                'contract_id' => $contract->id,
                'name' => $request->name,
                'price' => $request->price,
                'currency_id' => $request->currency_id,
                'method_id' => $request->method_id,
                'due_date' => formatDateTime($request->due_date, 'Y-m-d H:i:s', 'd-m-Y H:i:s'),
                'payment_stage' => $request->payment_stage,
                'status' => $status,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            ]);

            // Nếu đã thanh toán, tạo phiếu thu/chi
            if ($status == 1) {
                $transactionType = $request->payment_stage == 3 ? 1 : 0;
                $categoryName = $request->payment_stage == 0 ? 'Deposit' : ($request->payment_stage == 1 ? 'Bonus' : ($request->payment_stage == 2 ? 'Final Payment' : 'Deduction'));

                $category = TransactionCategory::firstOrCreate(
                    ['type' => $transactionType, 'name' => $categoryName],
                    ['note' => "Hạng mục cho $categoryName", 'is_active' => 1]
                );

                Transaction::create([
                    'type' => $transactionType,
                    'category_id' => $category->id,
                    'target_client_id' => $contract->provider_id,
                    'payment_id' => $payment->id,
                    'amount' => $payment->price,
                    'paid_date' => $payment->due_date,
                    'status' => 1,
                    'note' => "Tự động tạo từ biên nhận #{$payment->id} của hợp đồng #{$contract->contract_number}",
                    'reason' => $payment->name,
                ]);
            }

            LogService::saveLog([
                'action' => 'ADD_PAYMENT',
                'ip' => $request->getClientIp(),
                'details' => "Đã thêm biên nhận #{$payment->id} vào hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contract_payments|id',
                'fk_value' => $payment->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Thêm biên nhận thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi thêm biên nhận: ' . $e->getMessage(),
            ]);
        }
    }

    // Chỉnh sửa biên nhận
    public function updatePayment(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_contract_payments,id',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|integer|exists:tbl_currencies,id',
            'method_id' => 'nullable|integer|exists:tbl_payment_methods,id',
            'due_date' => 'nullable|date_format:d-m-Y H:i:s',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $payment = ContractPayment::findOrFail($request->id);
            if ($payment->status == 1 && $request->status == 0) {
                throw new \Exception('Không thể chuyển trạng thái từ đã thanh toán sang chưa thanh toán.');
            }

            $data = $request->only(['name', 'price', 'currency_id', 'method_id', 'due_date', 'status']);
            $data = array_filter($data, function($value) { return !is_null($value); });
            if (isset($data['due_date'])) {
                $data['due_date'] = formatDateTime($data['due_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            // Nếu status thay đổi từ 0 sang 1, tạo phiếu thu/chi
            if (isset($data['status']) && $data['status'] == 1 && $payment->status == 0) {
                $transactionType = $payment->payment_stage == 3 ? 1 : 0;
                $categoryName = $payment->payment_stage == 0 ? 'Deposit' : ($payment->payment_stage == 1 ? 'Bonus' : ($payment->payment_stage == 2 ? 'Final Payment' : 'Deduction'));

                $category = TransactionCategory::firstOrCreate(
                    ['type' => $transactionType, 'name' => $categoryName],
                    ['note' => "Hạng mục cho $categoryName", 'is_active' => 1]
                );

                Transaction::create([
                    'type' => $transactionType,
                    'category_id' => $category->id,
                    'target_client_id' => $payment->contract->provider_id,
                    'payment_id' => $payment->id,
                    'amount' => $data['price'] ?? $payment->price,
                    'paid_date' => $data['date'] ?? $payment->due_date,
                    'status' => 1,
                    'note' => "Tự động tạo từ biên nhận #{$payment->id} của hợp đồng #{$payment->contract->contract_number}",
                    'reason' => $data['name'] ?? $payment->name,
                ]);
            }

            $payment->update($data);

            LogService::saveLog([
                'action' => 'UPDATE_PAYMENT',
                'ip' => $request->getClientIp(),
                'details' => "Đã chỉnh sửa biên nhận #{$payment->id} trong hợp đồng #{$payment->contract->contract_number}",
                'fk_key' => 'tbl_contract_payments|id',
                'fk_value' => $payment->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật biên nhận thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật biên nhận: ' . $e->getMessage(),
            ]);
        }
    }

    // Hủy biên nhận
    public function cancelPayment(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_contract_payments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $payment = ContractPayment::findOrFail($request->id);
            if ($payment->status == 1) {
                throw new \Exception('Không thể hủy biên nhận đã thanh toán.');
            }

            $payment->update(['is_active' => 0]);

            LogService::saveLog([
                'action' => 'CANCEL_PAYMENT',
                'ip' => $request->getClientIp(),
                'details' => "Đã hủy biên nhận #{$payment->id} trong hợp đồng #{$payment->contract->contract_number}",
                'fk_key' => 'tbl_contract_payments|id',
                'fk_value' => $payment->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Hủy biên nhận thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi hủy biên nhận: ' . $e->getMessage(),
            ]);
        }
    }

    public function exportPdf($id)
    {
        try {
            $contract = Contract::with(['user', 'customer', 'services.service'])
                ->findOrFail($id);

            $data = [
                'contract' => $contract,
                'services' => $contract->services->map(function ($service) {
                    return [
                        'name' => $service->service->name ?? "Dịch vụ #{$service->service_id}",
                        'quantity' => $service->quantity,
                        'price' => $service->price,
                        'total' => $service->quantity * $service->price,
                        'note' => $service->note,
                    ];
                })->toArray(),
                'total_value' => $contract->services->sum(function ($service) {
                    return $service->quantity * $service->price;
                }),
            ];

            $pdf = Pdf::loadView('dashboard.contract.pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            LogService::saveLog([
                'action' => 'EXPORT_CONTRACT_PDF',
                'ip' => request()->getClientIp(),
                'details' => "Đã xuất PDF hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);

            return $pdf->download("HopDong_{$contract->contract_number}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xuất PDF: ' . $e->getMessage(),
            ]);
        }
    }

    // Helper để cập nhật tổng giá trị hợp đồng
    protected function updateContractTotalValue(Contract $contract)
    {
        $totalValue = $contract->services()
            ->where('is_active', 1)
            ->sum(DB::raw('quantity * price'));
        $contract->update(['total_value' => $totalValue]);
    }
}
