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
use App\Models\Product;
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
    public function index()
    {
        return view("dashboard.contract.index");
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        // Xây dựng query cơ bản
        $query = Contract::query()
            ->with(['user', 'provider']) // Load relationships
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
                    'id' => $item->provider->id ?? 0,
                    'name' => $item->provider->name ?? 'N/A',
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

    public function createView(Request $request)
    {
        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $customers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();
        $categories = ServiceCategory::where('is_active', 1)->get()->toArray();
        $services = Service::where('is_active', 1)->get()->toArray();
        $products = Product::where('is_active', 1)->get()->toArray();
        $payments = PaymentMethod::where('is_active', 1)->get()->toArray();
        $currencies = Currency::where('is_active', 1)->get()->toArray();

        $details = [
            'users' => $users,
            'customers' => $customers,
            'categories' => $categories,
            'services' => $services,
            'products' => $products,
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
        dd($request);
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
                'estimate_date' => 'nullable|date',
                'total_value' => 'nullable|numeric',
                'note' => 'nullable|string|max:500',
                'terms_and_conditions' => 'nullable|string|max:5000',
                'status' => 'required|in:0,1',
                'services_data' => 'required|json',
            ]);

            if ($contractValidator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $contractValidator->errors()->first()
                ]);
            }

            $contractData = $request->only([
                'name',
                'user_id',
                'provider_id',
                'category_id',
                'company_name',
                'tax_code',
                'company_address',
                'customer_representative',
                'customer_tax_code',
                'address',
                'phone',
                'sign_date',
                'effective_date',
                'expiry_date',
                'estimate_date',
                'total_value',
                'note',
                'terms_and_conditions',
                'status'
            ]);

            // Tạo mã hợp đồng tự động
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

            // 2. Lưu thông tin dịch vụ
            $servicesData = json_decode($request->input('services_data'), true);
            if (empty($servicesData)) {
                throw new \Exception('Vui lòng thêm ít nhất một dịch vụ cho hợp đồng.');
            }

            // Duyệt qua từng dịch vụ trong services_data
            foreach ($servicesData as $serviceItem) {
                $serviceType = $serviceItem['type'];
                $serviceId = isset($serviceItem['id']) ? $serviceItem['id'] : null;
                $quantity = $serviceItem['quantity'];
                $price = $serviceItem['price'];
                $note = $serviceItem['note'] ?? null;
                $customName = $serviceItem['custom_name'] ?? null;
                $subServices = $serviceItem['sub_services'] ?? [];

                // Xác định loại dịch vụ
                $itemType = 'service'; // Mặc định là dịch vụ thông thường
                if ($serviceType == 'other') {
                    $itemType = ($price < 0) ? 'discount' : 'custom';
                }

                // Lấy tên dịch vụ
                $serviceName = $customName;
                if ($serviceType == 'service' && $serviceId) {
                    $serviceObj = Service::find($serviceId);
                    $serviceName = $serviceObj ? $serviceObj->name : "Dịch vụ #$serviceId";
                }

                // Tạo dịch vụ trong bảng contract_services gộp
                $contractService = ContractService::create([
                    'contract_id' => $contract->id,
                    'service_id' => ($serviceType == 'service') ? $serviceId : null,
                    'name' => $serviceName,
                    'type' => $itemType,
                    'quantity' => $quantity,
                    'price' => $price,
                    'note' => $note,
                    'parent_id' => null, // Đây là dịch vụ cấp cha
                ]);

                // Xử lý dịch vụ con
                if (!empty($subServices)) {
                    foreach ($subServices as $subService) {
                        $subServiceName = $subService['name'] ?? null;
                        $subServiceQuantity = $subService['quantity'] ?? 1;
                        $subServiceNote = $subService['note'] ?? null;
                        $subServicePrice = $subService['price'] ?? 0;

                        if (empty($subServiceName)) {
                            continue;
                        }

                        // Lưu thông tin dịch vụ con vào cùng bảng contract_services
                        ContractService::create([
                            'contract_id' => $contract->id,
                            'service_id' => null,
                            'name' => $subServiceName,
                            'type' => 'sub_service',
                            'quantity' => $subServiceQuantity,
                            'price' => $subServicePrice,
                            'note' => $subServiceNote,
                            'parent_id' => $contractService->id, // Liên kết với dịch vụ cha
                        ]);
                    }
                }
            }

            // 3. Xử lý thông tin biên nhận (thanh toán)
            $paymentNames = $request->input('payment_name', []);
            $paymentPercentages = $request->input('payment_percentage', []);
            $prices = $request->input('payment_price', []);
            $currencies = $request->input('payment_currencies', []);
            $methods = $request->input('payment_methods', []);
            $dates = $request->input('payment_due_dates', []);
            $stages = $request->input('payment_stage', []);
            $statuses = $request->input('payment_status', []);

            if (!empty($paymentNames)) {
                foreach ($paymentNames as $index => $paymentName) {
                    $paymentValidator = Validator::make([
                        'name' => $paymentName,
                        'percentage' => $paymentPercentages[$index] ?? null,
                        'price' => $prices[$index] ?? null,
                        'currency_id' => $currencies[$index] ?? null,
                        'method_id' => $methods[$index] ?? null,
                        'due_date' => $dates[$index] ?? '',
                        'stage' => $stages[$index] ?? null,
                        'status' => $statuses[$index] ?? 0,
                    ], [
                        'name' => 'required|string|max:255',
                        'percentage' => 'nullable|numeric|min:0|max:100',
                        'price' => 'required|numeric|min:0',
                        'currency_id' => 'required|integer|exists:tbl_currencies,id',
                        'method_id' => 'required|integer|exists:tbl_payment_methods,id',
                        'due_date' => 'nullable|date_format:d-m-Y H:i:s',
                        'stage' => 'required|in:0,1,2,3',
                        'status' => 'nullable|in:0,1',
                    ]);

                    if ($paymentValidator->fails()) {
                        throw new \Exception('Dữ liệu biên nhận không hợp lệ: ' . $paymentValidator->errors()->first());
                    }

                    $pushData = [
                        'contract_id' => $contract->id,
                        'name' => $paymentName,
                        'percentage' => $paymentPercentages[$index] ?? null,
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

                    // Tạo transaction khi trạng thái thanh toán là đã thanh toán (status = 1)
                    if (isset($statuses[$index]) && $statuses[$index] == 1) {
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
                            'paid_date' => $payment->due_date ?? date('Y-m-d H:i:s'),
                            'status' => 1, // Hoàn tất vì đã thanh toán
                            'note' => "Tự động tạo từ biên nhận #{$payment->id} của hợp đồng #{$contract->contract_number}",
                            'reason' => $payment->name,
                        ]);
                    }
                }
            }

            // 4. Tạo task nếu status = 1 (Đang triển khai)
            if ($contract->status == 1) {
                $this->createContractTasks($contract);
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

    /**
     * Tạo các task cho hợp đồng
     *
     * @param Contract $contract Đối tượng hợp đồng
     * @return void
     */
    private function createContractTasks(Contract $contract)
    {
        // Tạo task chính cho hợp đồng
        $mainTaskData = [
            'name' => "Hợp đồng #$contract->contract_number - $contract->name",
            'type' => 'CONTRACT',
            'status_id' => 1, // Trạng thái mặc định
            'priority_id' => 1, // Độ ưu tiên mặc định
            'assign_id' => $contract->user_id, // Gán cho nhân viên phụ trách
            'start_date' => $contract->effective_date,
            'due_date' => $contract->estimate_date ?? $contract->expiry_date,
            'estimate_time' => (!empty($contract->estimate_date) && !empty($contract->effective_date))
                ? (strtotime($contract->estimate_date) - strtotime($contract->effective_date)) / 3600
                : 24, // Quy đổi thành giờ
            'description' => "Công việc tổng thể cho hợp đồng #$contract->contract_number",
            'qty_request' => 1,
            'contract_id' => $contract->id,
            'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
        ];

        $mainTask = Task::create($mainTaskData);

        LogService::saveLog([
            'action' => 'TASK_ENUM_LOG',
            'ip' => request()->getClientIp(),
            'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task chính cho hợp đồng #' . $contract->contract_number . ' - ' . $contract->name,
            'fk_key' => 'tbl_tasks|id',
            'fk_value' => $mainTask->id,
        ]);

        // Lấy tất cả dịch vụ cấp cao nhất (parent_id = null và không phải giảm giá)
        $contractServices = ContractService::where('contract_id', $contract->id)
            ->where('parent_id', null)
            ->where('is_active', 1)
            ->where('type', '!=', 'discount')
            ->get();

        // Duyệt qua từng dịch vụ chính
        foreach ($contractServices as $service) {
            // Tạo task con cho dịch vụ chính
            $serviceTaskData = [
                'name' => $service->name,
                'type' => 'SERVICE',
                'status_id' => 1,
                'priority_id' => 1,
                'assign_id' => $contract->user_id,
                'start_date' => $contract->effective_date,
                'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                'estimate_time' => (!empty($contract->estimate_date) && !empty($contract->effective_date))
                    ? (strtotime($contract->estimate_date) - strtotime($contract->effective_date)) / 3600
                    : 24,
                'description' => "Công việc thực hiện {$service->name} cho hợp đồng #$contract->contract_number",
                'qty_request' => $service->quantity,
                'contract_id' => $contract->id,
                'service_id' => $service->service_id,
                'parent_id' => $mainTask->id,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            ];

            $serviceTask = Task::create($serviceTaskData);

            LogService::saveLog([
                'action' => 'TASK_ENUM_LOG',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task con cho dịch vụ ' . $service->name,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $serviceTask->id,
            ]);

            // Tìm các dịch vụ con thuộc dịch vụ chính này
            $subServices = ContractService::where('parent_id', $service->id)->where('is_active', 1)->get();

            // Tạo task cho các dịch vụ con
            foreach ($subServices as $subService) {
                $subTaskData = [
                    'name' => $subService->name,
                    'type' => 'SUB',
                    'status_id' => 1,
                    'priority_id' => 1,
                    'assign_id' => $contract->user_id,
                    'start_date' => $contract->effective_date,
                    'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                    'estimate_time' => (!empty($contract->estimate_date) && !empty($contract->effective_date))
                        ? (strtotime($contract->estimate_date) - strtotime($contract->effective_date)) / 3600 / 2
                        : 12, // Chia đôi thời gian so với task cha
                    'description' => "Công việc con {$subService->name} cho dịch vụ {$service->name}",
                    'qty_request' => $subService->quantity,
                    'contract_id' => $contract->id,
                    'parent_id' => $serviceTask->id,
                    'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                ];

                $subTask = Task::create($subTaskData);

                LogService::saveLog([
                    'action' => 'TASK_ENUM_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task con cho dịch vụ phụ ' . $subService->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $subTask->id,
                ]);
            }
        }
    }

    public function detail($id)
    {
        // Lấy hợp đồng với tất cả quan hệ cần thiết
        $contract = Contract::with([
            'user',
            'provider', // Đổi customer thành provider cho đúng với cấu trúc trong create
            'services' => function ($query) {
                // Sắp xếp để lấy dịch vụ cha trước
                $query->orderBy('parent_id', 'asc');
            },
            'services.subServices', // Lấy các dịch vụ con
            'payments',
            'payments.currency',
            'payments.method',
            'tasks' => function ($query) {
                // Sắp xếp theo cấu trúc cây
                $query->orderBy('parent_id', 'asc');
            },
            'tasks.status',
            'tasks.assign',
            'tasks.subTasks' // Lấy các task con
        ])->findOrFail($id);

        // Tổ chức dịch vụ thành cấu trúc cha-con
        $mainServices = $contract->services
            ->where('parent_id', null)
            ->map(function ($service) {
                $subServices = $service->subServices->map(function ($subService) {
                    return [
                        'id' => $subService->id,
                        'name' => $subService->name,
                        'type' => $subService->type,
                        'quantity' => $subService->quantity,
                        'price' => $subService->price,
                        'note' => $subService->note,
                    ];
                })->toArray();

                return [
                    'id' => $service->id,
                    'service_id' => $service->service_id,
                    'name' => $service->name,
                    'type' => $service->type,
                    'quantity' => $service->quantity,
                    'price' => $service->price,
                    'note' => $service->note,
                    'sub_services' => $subServices
                ];
            })->toArray();

        $totalContractValue = $contract->total_value;
        
        $totalPaid = $contract->payments
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('payment_stage', '!=', 3)
            ->sum('price');
        
        $totalDeduction = $contract->payments
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('payment_stage', 3)
            ->sum('price');

        $totalDeduction = abs($totalDeduction);
        
        $totalRemaining = $totalContractValue - $totalPaid + $totalDeduction;
        $totalExcess = $totalRemaining < 0 ? abs($totalRemaining) : 0;
        $totalRemaining = $totalRemaining < 0 ? 0 : $totalRemaining;
        $paymentPercentage = $totalContractValue > 0 
            ? round(($totalPaid - $totalDeduction) / $totalContractValue * 100, 2) 
            : 0;

        // Biến đổi dữ liệu hợp đồng
        $details = [
            'id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'name' => $contract->name,
            'user' => [
                'id' => $contract->user->id ?? 0,
                'name' => $contract->user->name ?? 'N/A',
            ],
            'creator' => [
                'id' => $contract->creator->id ?? 0,
                'name' => $contract->creator->name ?? 'N/A',
            ],
            'provider' => [
                'id' => $contract->provider->id ?? 0,
                'name' => $contract->provider->name ?? 'N/A',
                'phone' => $contract->provider->phone ?? 'N/A',
                'email' => $contract->provider->email ?? 'N/A',
                'address' => $contract->provider->address ?? 'N/A'
            ],
            'company_name' => $contract->company_name,
            'tax_code' => $contract->tax_code,
            'company_address' => $contract->company_address,
            'customer_representative' => $contract->customer_representative,
            'customer_tax_code' => $contract->customer_tax_code,
            'address' => $contract->address,
            'phone' => $contract->phone,
            'sign_date' => $contract->sign_date,
            'effective_date' => $contract->effective_date,
            'expiry_date' => $contract->expiry_date,
            'estimate_date' => $contract->estimate_date,
            'total_value' => $contract->total_value,
            'status' => $contract->status,
            'status_text' => $contract->status == 1 ? 'Đang triển khai' : 'Chờ duyệt',
            'note' => $contract->note,
            'terms_and_conditions' => $contract->terms_and_conditions,
            'created_at' => $contract->created_at,
            'updated_at' => $contract->updated_at,
            'payment_summary' => [
                'total_value' => $totalContractValue,
                'total_paid' => $totalPaid,
                'total_deduction' => abs($totalDeduction),
                'total_remaining' => $totalRemaining,
                'total_excess' => $totalExcess,
                'payment_percentage' => $paymentPercentage,
            ],
            'services' => $mainServices,
            'payments' => $contract->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'name' => $payment->name,
                    'percentage' => $payment->percentage,
                    'price' => $payment->price,
                    'currency' => [
                        'id' => $payment->currency_id,
                        'name' => $payment->currency->name ?? 'N/A',
                        'code' => $payment->currency->currency_code ?? 'N/A'
                    ],
                    'method' => [
                        'id' => $payment->method_id,
                        'name' => $payment->method->name ?? 'N/A'
                    ],
                    'due_date' => $payment->due_date,
                    'due_date_formatted' => $payment->due_date ? formatDateTime($payment->due_date, 'd-m-Y H:i:s') : '',
                    'payment_stage' => $payment->payment_stage,
                    'payment_stage_text' => $this->getPaymentStageText($payment->payment_stage),
                    'status' => $payment->status,
                    'status_text' => $payment->status == 1 ? 'Đã thanh toán' : 'Chưa thanh toán'
                ];
            })->toArray(),
        ];

        // Tổ chức task thành cấu trúc cha-con
        $mainTasks = $contract->tasks
            ->where('parent_id', null)
            ->map(function ($task) {
                // Lọc task con trực tiếp của task hiện tại
                $subTasks = $task->subTasks->map(function ($subTask) {
                    // Lấy các task con của sub task (cấp 3)
                    $childTasks = $subTask->subTasks->map(function ($childTask) {
                        return [
                            'id' => $childTask->id,
                            'name' => $childTask->name,
                            'status' => [
                                'id' => $childTask->status->id ?? 0,
                                'name' => $childTask->status->name ?? 'N/A',
                                'color' => $childTask->status->color ?? 'gray',
                            ],
                            'assign' => [
                                'id' => $childTask->assign->id ?? 0,
                                'name' => $childTask->assign->name ?? 'N/A',
                            ],
                            'start_date' => $childTask->start_date,
                            'due_date' => $childTask->due_date,
                            'estimate_time' => $childTask->estimate_time,
                            'qty_request' => $childTask->qty_request,
                            'qty_completed' => $childTask->qty_completed,
                            'description' => $childTask->description,
                            'created_at' => $childTask->created_at
                        ];
                    })->toArray();

                    return [
                        'id' => $subTask->id,
                        'name' => $subTask->name,
                        'status' => [
                            'id' => $subTask->status->id ?? 0,
                            'name' => $subTask->status->name ?? 'N/A',
                            'color' => $subTask->status->color ?? 'gray',
                        ],
                        'assign' => [
                            'id' => $subTask->assign->id ?? 0,
                            'name' => $subTask->assign->name ?? 'N/A',
                        ],
                        'start_date' => $subTask->start_date,
                        'due_date' => $subTask->due_date,
                        'estimate_time' => $subTask->estimate_time,
                        'qty_request' => $subTask->qty_request,
                        'qty_completed' => $subTask->qty_completed,
                        'description' => $subTask->description,
                        'service_id' => $subTask->service_id,
                        'created_at' => $subTask->created_at,
                        'sub_tasks' => $childTasks
                    ];
                })->toArray();

                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'status' => [
                        'id' => $task->status->id ?? 0,
                        'name' => $task->status->name ?? 'N/A',
                        'color' => $task->status->color ?? 'gray',
                    ],
                    'assign' => [
                        'id' => $task->assign->id ?? 0,
                        'name' => $task->assign->name ?? 'N/A',
                    ],
                    'start_date' => $task->start_date,
                    'due_date' => $task->due_date,
                    'estimate_time' => $task->estimate_time,
                    'qty_request' => $task->qty_request,
                    'qty_completed' => $task->qty_completed,
                    'description' => $task->description,
                    'created_at' => $task->created_at,
                    'sub_tasks' => $subTasks
                ];
            })->toArray();

        $details['tasks'] = $mainTasks;

        // Lấy các dữ liệu cần thiết cho view
        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $providers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();
        $customers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();
        $categories = ServiceCategory::where('is_active', 1)->get()->toArray();
        $services = Service::where('is_active', 1)->get()->toArray();
        $payment_methods = PaymentMethod::where('is_active', 1)->get()->toArray();
        $currencies = Currency::where('is_active', 1)->get()->toArray();
        // $task_statuses = TaskStatus::where('is_active', 1)->get()->toArray();

        return view('dashboard.contract.detail_origin', compact(
            'details',
            'users',
            'providers',
            'customers',
            'categories',
            'services',
            'payment_methods',
            'currencies'
        ));
    }

    private function getPaymentStageText($stage)
    {
        switch ($stage) {
            case 0:
                return 'Đặt cọc';
            case 1:
                return 'Tiền thưởng';
            case 2:
                return 'Thanh toán cuối cùng';
            case 3:
                return 'Trừ tiền';
            default:
                return 'Không xác định';
        }
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

        DB::beginTransaction();

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
                $this->createContractTasks($contract);
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

            DB::commit();

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

    // Thêm dịch vụ mới
    public function addService(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'contract_id' => 'required|integer|exists:tbl_contracts,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'note' => 'nullable|string|max:500',
            'type' => 'required|in:service,other',
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

            // Xác định tên và loại dịch vụ
            $serviceName = '';
            $serviceId = null;
            $serviceType = ($request->type == 'service') ? 'service' : 'custom';

            if ($request->type == 'service') {
                $serviceObj = Service::find($request->service_id);
                $serviceName = $serviceObj->name;
                $serviceId = $request->service_id;
            } else {
                $serviceName = $request->name;
                // Nếu giá âm, đây là giảm giá
                if ($request->price < 0) {
                    $serviceType = 'discount';
                }
            }

            // Tạo dịch vụ chính
            $mainService = ContractService::create([
                'contract_id' => $request->contract_id,
                'service_id' => $serviceId,
                'name' => $serviceName,
                'type' => $serviceType,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'note' => $request->note,
                'parent_id' => null,
            ]);

            // Xử lý dịch vụ con
            if (isset($request->sub_services) && is_array($request->sub_services)) {
                foreach ($request->sub_services as $subService) {
                    if (empty($subService['name'])) continue;

                    ContractService::create([
                        'contract_id' => $request->contract_id,
                        'service_id' => null,
                        'name' => $subService['name'],
                        'type' => 'sub_service',
                        'quantity' => $subService['quantity'] ?? 1,
                        'price' => $subService['price'] ?? 0,
                        'note' => $subService['note'] ?? null,
                        'parent_id' => $mainService->id,
                    ]);
                }
            }

            // Tạo task nếu hợp đồng đang triển khai và là dịch vụ thực
            if ($contract->status == 1 && $serviceType == 'service') {
                // Tìm task chính
                $mainTask = $contract->tasks()->whereNull('parent_id')->first();

                if ($mainTask) {
                    // Tạo task cho dịch vụ chính
                    $serviceTask = Task::create([
                        'name' => $serviceName,
                        'status_id' => 1,
                        'priority_id' => 1,
                        'assign_id' => $contract->user_id,
                        'start_date' => $contract->effective_date,
                        'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                        'estimate_time' => (!empty($contract->estimate_date) && !empty($contract->effective_date))
                            ? (strtotime($contract->estimate_date) - strtotime($contract->effective_date)) / 3600
                            : 24,
                        'description' => "Công việc thực hiện {$serviceName} cho hợp đồng #{$contract->contract_number}",
                        'qty_request' => $request->quantity,
                        'contract_id' => $contract->id,
                        'service_id' => $serviceId,
                        'parent_id' => $mainTask->id,
                        'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                    ]);

                    // Tạo subtask cho các dịch vụ con
                    if (isset($request->sub_services) && is_array($request->sub_services)) {
                        foreach ($request->sub_services as $subService) {
                            if (empty($subService['name'])) continue;

                            Task::create([
                                'name' => $subService['name'],
                                'status_id' => 1,
                                'priority_id' => 1,
                                'assign_id' => $contract->user_id,
                                'start_date' => $contract->effective_date,
                                'due_date' => $contract->estimate_date ?? $contract->expiry_date,
                                'estimate_time' => (!empty($contract->estimate_date) && !empty($contract->effective_date))
                                    ? (strtotime($contract->estimate_date) - strtotime($contract->effective_date)) / 3600 / 2 // Một nửa thời gian so với task cha
                                    : 12,
                                'description' => "Công việc con {$subService['name']} cho dịch vụ {$serviceName}",
                                'qty_request' => $subService['quantity'] ?? 1,
                                'contract_id' => $contract->id,
                                'parent_id' => $serviceTask->id,
                                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                            ]);
                        }
                    }
                }
            }

            // Cập nhật tổng giá trị hợp đồng
            $this->updateContractTotalValue($contract);

            LogService::saveLog([
                'action' => 'ADD_CONTRACT_SERVICE',
                'ip' => $request->getClientIp(),
                'details' => "Đã thêm dịch vụ #{$mainService->id} vào hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contract_services|id',
                'fk_value' => $mainService->id,
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

    // Cập nhật dịch vụ
    public function updateService(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_contract_services,id',
            'quantity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric',
            'note' => 'nullable|string|max:500',
            'type' => 'nullable|in:service,other',
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

            // Xác định loại dịch vụ
            $serviceType = $request->type ?? $service->type;

            // Chuẩn bị dữ liệu cập nhật
            $updateData = array_filter($request->only(['quantity', 'price', 'note']), fn($value) => !is_null($value));

            // Nếu cập nhật tên hoặc loại dịch vụ
            if ($request->filled('type')) {
                if ($request->type == 'service' && $request->filled('service_id')) {
                    $serviceObj = Service::find($request->service_id);
                    $updateData['name'] = $serviceObj->name;
                    $updateData['service_id'] = $request->service_id;
                    $updateData['type'] = 'service';
                } elseif ($request->type == 'other' && $request->filled('name')) {
                    $updateData['name'] = $request->name;
                    $updateData['service_id'] = null;
                    $updateData['type'] = ($request->price < 0) ? 'discount' : 'custom';
                }
            }

            // Cập nhật dịch vụ chính
            $service->update($updateData);

            // Xử lý dịch vụ con
            if (isset($request->sub_services) && is_array($request->sub_services)) {
                // Lấy danh sách ID của các dịch vụ con hiện tại
                $existingSubServices = $service->subServices()->pluck('id')->toArray();
                $updatedSubServiceIds = [];

                foreach ($request->sub_services as $subService) {
                    if (empty($subService['name'])) continue;

                    if (isset($subService['id']) && $subService['id']) {
                        // Cập nhật dịch vụ con hiện có
                        $subServiceModel = ContractService::find($subService['id']);
                        if ($subServiceModel) {
                            $subServiceModel->update([
                                'name' => $subService['name'],
                                'quantity' => $subService['quantity'] ?? 1,
                                'price' => $subService['price'] ?? 0,
                                'note' => $subService['note'] ?? null,
                            ]);
                            $updatedSubServiceIds[] = $subServiceModel->id;
                        }
                    } else {
                        // Tạo dịch vụ con mới
                        $newSubService = ContractService::create([
                            'contract_id' => $contract->id,
                            'service_id' => null,
                            'name' => $subService['name'],
                            'type' => 'sub_service',
                            'quantity' => $subService['quantity'] ?? 1,
                            'price' => $subService['price'] ?? 0,
                            'note' => $subService['note'] ?? null,
                            'parent_id' => $service->id,
                        ]);
                        $updatedSubServiceIds[] = $newSubService->id;
                    }
                }

                // Xóa các dịch vụ con không còn trong danh sách cập nhật
                foreach ($existingSubServices as $existingId) {
                    if (!in_array($existingId, $updatedSubServiceIds)) {
                        ContractService::where('id', $existingId)->delete();
                    }
                }
            } else {
                // Nếu không có dịch vụ con nào được gửi lên, xóa tất cả dịch vụ con hiện có
                $service->subServices()->delete();
            }

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

    /**
     * Hủy bỏ dịch vụ và các dịch vụ con, đồng thời cập nhật các task liên quan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            // Lấy thông tin dịch vụ
            $service = ContractService::findOrFail($request->id);
            $contract = $service->contract;

            // Kiểm tra xem đây có phải là dịch vụ cha không (không có parent_id)
            if ($service->parent_id !== null) {
                throw new \Exception('Chỉ có thể hủy dịch vụ chính, không thể hủy dịch vụ con trực tiếp.');
            }

            // Đặt trạng thái is_active = 0 cho dịch vụ chính
            $service->update(['is_active' => 0]);

            // Đặt trạng thái is_active = 0 cho tất cả dịch vụ con
            ContractService::where('parent_id', $service->id)
                ->update(['is_active' => 0]);

            // Xử lý các task liên quan
            if ($contract->status == 1) {
                // Tìm task chính liên quan đến dịch vụ này
                $mainTask = Task::where('contract_id', $contract->id)
                    ->where(function ($query) use ($service) {
                        $query->where('service_id', $service->service_id)
                            ->orWhere(function ($q) use ($service) {
                                $q->whereNull('service_id')
                                    ->where('name', 'like', '%' . $service->name . '%');
                            });
                    })
                    ->whereNotNull('parent_id') // Không phải task gốc của hợp đồng
                    ->first();

                if ($mainTask) {
                    // Cập nhật trạng thái is_active cho task chính
                    $mainTask->update(['is_active' => 0]);

                    // Cập nhật trạng thái is_active cho tất cả task con
                    Task::where('parent_id', $mainTask->id)
                        ->update(['is_active' => 0]);
                }
            }

            // Cập nhật tổng giá trị hợp đồng
            $this->updateContractTotalValue($contract);

            // Lưu nhật ký
            LogService::saveLog([
                'action' => 'CANCEL_CONTRACT_SERVICE',
                'ip' => $request->getClientIp(),
                'details' => "Đã hủy dịch vụ #{$service->id} và các dịch vụ con trong hợp đồng #{$contract->contract_number}",
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
            'price' => 'required|numeric',
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

            // If payment status is completed, create transaction
            if ($status == 1) {
                $transactionType = $request->payment_stage == 3 ? 1 : 0; // Deduction is expense (1), others are income (0)
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
            'price' => 'nullable|numeric',
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
            $data = array_filter($data, function ($value) {
                return !is_null($value);
            });
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
            // Lấy thông tin hợp đồng với các mối quan hệ cần thiết
            $contract = Contract::with([
                'user',
                'provider',
                'services' => function ($query) {
                    $query->whereNull('parent_id'); // Chỉ lấy dịch vụ cấp cha
                },
                'services.subServices' // Lấy các dịch vụ con
            ])->findOrFail($id);

            // Tính tổng giá trị hợp đồng
            $totalValue = $contract->services->sum(function ($service) {
                return $service->quantity * $service->price;
            });

            // Chuẩn bị dữ liệu cho PDF
            $data = [
                'contract' => $contract,
                'services' => $contract->services->map(function ($service) {
                    // Xác định tên dịch vụ
                    $serviceName = $service->name;

                    // Xác định loại dịch vụ (để định dạng hiển thị)
                    $serviceType = $service->type;

                    // Lấy các dịch vụ con
                    $subServices = $service->subServices;

                    return [
                        'id' => $service->id,
                        'name' => $serviceName,
                        'type' => $serviceType,
                        'quantity' => $service->quantity,
                        'price' => $service->price,
                        'total' => $service->quantity * $service->price,
                        'note' => $service->note,
                        'has_sub_services' => count($subServices) > 0,
                        'sub_services' => $subServices->map(function ($subService) {
                            return [
                                'id' => $subService->id,
                                'name' => $subService->name,
                                'quantity' => $subService->quantity,
                                'price' => $subService->price,
                                'total' => $subService->quantity * $subService->price,
                                'note' => $subService->note
                            ];
                        })->toArray()
                    ];
                })->toArray(),
                'total_value' => $totalValue,
                'date_now' => date('d/m/Y')
            ];

            // Tạo PDF với mẫu cải tiến
            $pdf = Pdf::loadView('dashboard.contract.pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            // Lưu log
            LogService::saveLog([
                'action' => 'EXPORT_CONTRACT_PDF',
                'ip' => request()->getClientIp(),
                'details' => "Đã xuất PDF hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);

            // Tải xuống PDF
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
