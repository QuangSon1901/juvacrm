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
            ->with(['user', 'provider', 'tasks', 'payments']) // Thêm relationships cần thiết
            ->when($request->input('filter.search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('contract_number', 'like', "%{$search}%");
            })
            ->when($request->input('filter.my_contract'), function ($query) use ($request) {
                $query->where('user_id', auth()->id());
            })
            ->when($request->has('filter.status') && $request->input('filter.status') !== null, function ($query) use ($request) {
                $query->where('status', $request->input('filter.status'));
            });

        // Phân trang
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        // Format dữ liệu trả về với thông tin tổng quát hơn
        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            // Tính toán tổng giá trị và số tiền đã thanh toán
            $totalValue = $item->total_value;
            $totalPaid = $item->payments->where('status', 1)->where('is_active', 1)->where('payment_stage', '!=', 3)->sum('price');
            $totalDeduction = $item->payments->where('status', 1)->where('is_active', 1)->where('payment_stage', 3)->sum('price');
            $totalDeduction = abs($totalDeduction);
            $totalRemaining = $totalValue - $totalPaid + $totalDeduction;
            $paymentPercentage = $totalValue > 0 ? round(($totalPaid - $totalDeduction) / $totalValue * 100, 2) : 0;

            // Tính tiến độ công việc
            $tasks = $item->tasks->where('is_active', 1);
            $totalTasks = $tasks->count();
            $completedTasks = $tasks->where('status_id', '>=', 4)->count();
            $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

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
                'total_value' => $totalValue,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
                'payment_percentage' => $paymentPercentage,
                'task_progress' => $taskProgress,
                'task_stats' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                ],
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

    /**
     * Xử lý tạo hợp đồng mới
     */
    public function create(Request $request)
    {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            // 1. Validate thông tin cơ bản của hợp đồng
            $contractValidator = ValidatorService::make($request, [
                'name' => 'required|string|max:255',
                'user_id' => 'required|integer|exists:tbl_users,id',
                'provider_id' => 'required|integer|exists:tbl_customers,id',
                'category_id' => 'required|integer|exists:tbl_service_categories,id',
                'customer_representative' => 'nullable|string|max:255',
                'customer_tax_code' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'sign_date' => 'nullable|date_format:d-m-Y H:i:s',
                'effective_date' => 'nullable|date_format:d-m-Y H:i:s',
                'expiry_date' => 'nullable|date_format:d-m-Y H:i:s',
                'estimate_date' => 'nullable|date_format:d-m-Y H:i:s',
                'total_value' => 'nullable|numeric',
                'note' => 'nullable|string',
                'terms_and_conditions' => 'nullable|string',
            ]);

            if ($contractValidator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $contractValidator->errors()->first()
                ]);
            }

            // 2. Thu thập dữ liệu hợp đồng
            $contractData = [
                'name' => $request->input('name'),
                'user_id' => $request->input('user_id'),
                'provider_id' => $request->input('provider_id'),
                'category_id' => $request->input('category_id'),
                'company_name' => NAME_COMPANY,
                'tax_code' => $request->input('tax_code'),
                'company_address' => $request->input('company_address'),
                'customer_representative' => $request->input('customer_representative'),
                'customer_tax_code' => $request->input('customer_tax_code'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'note' => $request->input('note'),
                'terms_and_conditions' => $request->input('terms_and_conditions'),
                'status' => 0,
                'total_value' => $request->input('total_value'),
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            ];

            // Xử lý các trường ngày tháng
            if ($request->filled('sign_date')) {
                $contractData['sign_date'] = formatDateTime($request->input('sign_date'), 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }
            if ($request->filled('effective_date')) {
                $contractData['effective_date'] = formatDateTime($request->input('effective_date'), 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }
            if ($request->filled('expiry_date')) {
                $contractData['expiry_date'] = formatDateTime($request->input('expiry_date'), 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }
            if ($request->filled('estimate_date')) {
                $contractData['estimate_date'] = formatDateTime($request->input('estimate_date'), 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            // Tạo mã hợp đồng tự động
            $lastContract = Contract::orderBy('id', 'desc')->first();
            $nextNumber = $lastContract ? (int)substr($lastContract->contract_number, -3) + 1 : 1;
            $contractData['contract_number'] = 'HD-2025-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // 3. Tạo hợp đồng
            $contract = Contract::create($contractData);

            // Logging
            LogService::saveLog([
                'action' => 'CREATE_CONTRACT',
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo hợp đồng #' . $contract->contract_number,
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);

            // 4. Xử lý dữ liệu các mục (sản phẩm, dịch vụ, dịch vụ con)
            $contractItemsData = json_decode($request->input('contract_items_data'), true);

            if (empty($contractItemsData)) {
                throw new \Exception('Vui lòng thêm ít nhất một sản phẩm hoặc dịch vụ cho hợp đồng.');
            }

            $totalServiceValue = 0;
            $totalDiscountValue = 0;

            // Duyệt qua từng item
            foreach ($contractItemsData as $item) {
                $itemType = $item['type'];

                if ($itemType === 'product') {
                    // Xử lý các sản phẩm
                    $productId = $item['id'];

                    // Duyệt qua các dịch vụ của sản phẩm
                    foreach ($item['services'] as $service) {
                        $serviceData = [
                            'contract_id' => $contract->id,
                            'product_id' => $productId,
                            'price' => (float) $service['price'],
                            'quantity' => 1, // Mặc định là 1 nếu không có quantity
                            'note' => $service['note'] ?? null,
                        ];

                        if ($service['id'] === 'custom') {
                            // Dịch vụ tùy chỉnh
                            $serviceData['name'] = $service['custom_name'];
                            $serviceData['type'] = 'custom';
                            $serviceData['service_id'] = null;
                        } else {
                            // Dịch vụ có sẵn
                            $serviceData['service_id'] = $service['id'];
                            $serviceData['type'] = 'service';
                            $serviceObj = Service::find($service['id']);
                            $serviceData['name'] = $serviceObj ? $serviceObj->name : "Dịch vụ #" . $service['id'];
                        }

                        // Lưu dịch vụ
                        $contractService = ContractService::create($serviceData);
                        $totalServiceValue += $serviceData['price'];

                        // Xử lý các dịch vụ con
                        if (!empty($service['sub_services'])) {
                            foreach ($service['sub_services'] as $subService) {
                                $subServicePrice = (float) $subService['total'];
                                $subServiceQuantity = (float) $subService['quantity'];

                                $subServiceData = [
                                    'contract_id' => $contract->id,
                                    'product_id' => $productId,
                                    'name' => $subService['name'],
                                    'type' => 'sub_service',
                                    'quantity' => $subServiceQuantity,
                                    'price' => $subServicePrice,
                                    'note' => $subService['content'] ?? null,
                                    'parent_id' => $contractService->id,
                                ];

                                ContractService::create($subServiceData);
                            }
                        }
                    }
                } else if ($itemType === 'custom') {
                    // Xử lý mục khác (không phải sản phẩm)
                    $itemPrice = (float) $item['price'];
                    $itemData = [
                        'contract_id' => $contract->id,
                        'name' => $item['name'],
                        'type' => ($itemPrice < 0) ? 'discount' : 'custom',
                        'quantity' => 1,
                        'price' => $itemPrice,
                        'note' => $item['note'] ?? null,
                    ];

                    ContractService::create($itemData);

                    // Cập nhật tổng giá trị dịch vụ và giảm giá
                    if ($itemPrice < 0) {
                        $totalDiscountValue += abs($itemPrice);
                    } else {
                        $totalServiceValue += $itemPrice;
                    }
                }
            }

            // 5. Xử lý thông tin thanh toán (biên nhận)
            $paymentNames = $request->input('payment_name', []);
            $paymentPercentages = $request->input('payment_percentage', []);
            $paymentPrices = $request->input('payment_price', []);
            $paymentCurrencies = $request->input('payment_currencies', []);
            $paymentMethods = $request->input('payment_methods', []);
            $paymentDueDates = $request->input('payment_due_dates', []);
            $paymentStages = $request->input('payment_stage', []);
            $paymentStatuses = $request->input('payment_status', []);

            if (!empty($paymentNames)) {
                foreach ($paymentNames as $index => $paymentName) {
                    if (empty($paymentName)) {
                        continue;
                    }

                    $paymentPrice = (float) $paymentPrices[$index] ?? 0;
                    $paymentData = [
                        'contract_id' => $contract->id,
                        'name' => $paymentName,
                        'percentage' => $paymentPercentages[$index] ?? null,
                        'price' => $paymentPrice,
                        'currency_id' => $paymentCurrencies[$index] ?? null,
                        'method_id' => $paymentMethods[$index] ?? null,
                        'payment_stage' => $paymentStages[$index] ?? 0,
                        'status' => isset($paymentStatuses[$index]) ? 1 : 0,
                        'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                    ];

                    // Xử lý ngày đến hạn
                    if (!empty($paymentDueDates[$index])) {
                        try {
                            $paymentData['due_date'] = formatDateTime($paymentDueDates[$index], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
                        } catch (\Exception $e) {
                            // Nếu không đúng định dạng, thử định dạng khác
                            try {
                                $paymentData['due_date'] = formatDateTime($paymentDueDates[$index], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
                            } catch (\Exception $e) {
                                // Nếu vẫn không đúng, bỏ qua
                            }
                        }
                    }

                    $payment = ContractPayment::create($paymentData);

                    // Tạo transaction nếu đã thanh toán
                    if (isset($paymentStatuses[$index])) {
                        $transactionType = $paymentStages[$index] == 3 ? 1 : 0; // Deduction là chi (1), còn lại là thu (0)
                        $categoryName = $paymentStages[$index] == 0 ? 'Deposit' : ($paymentStages[$index] == 1 ? 'Bonus' : ($paymentStages[$index] == 2 ? 'Final Payment' : 'Deduction'));

                        // Tìm hoặc tạo hạng mục thu/chi
                        $category = TransactionCategory::firstOrCreate(
                            ['type' => $transactionType, 'name' => $categoryName],
                            ['note' => "Hạng mục cho $categoryName", 'is_active' => 1]
                        );

                        Transaction::create([
                            'type' => $transactionType,
                            'category_id' => $category->id,
                            'target_client_id' => $contract->provider_id,
                            'payment_id' => $payment->id,
                            'amount' => $paymentPrice,
                            'paid_date' => $payment->due_date ?? date('Y-m-d H:i:s'),
                            'status' => 1,
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
                'data' => [
                    'id' => $contract->id
                ]
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
    public function createContractTasks(Request $request)
    {
        DB::beginTransaction();
        try {
            $contract = Contract::with([
                'user',
                'provider',
                'creator',
                'services' => function ($query) {
                    // Sắp xếp để lấy dịch vụ cha trước
                    $query->orderBy('parent_id', 'asc')->where('is_active', 1);
                },
                'payments',
                'payments.currency',
                'payments.method'
            ])->findOrFail($request['id']);

            $contract->update(['status' => 1, 'effective_date' => date('Y-m-d H:i:s')]);

            // Tạo task chính cho hợp đồng
            $mainTaskData = [
                'name' => "Hợp đồng #$contract->contract_number - $contract->name",
                'type' => 'CONTRACT',
                'status_id' => 1, // Trạng thái mặc định
                'priority_id' => 1, // Độ ưu tiên mặc định
                'assign_id' => $contract->user_id, // Gán cho nhân viên phụ trách
                'start_date' => $contract->effective_date,
                'due_date' => $contract->expiry_date,
                'estimate_time' => (!empty($contract->expiry_date) && !empty($contract->effective_date))
                    ? (strtotime($contract->expiry_date) - strtotime($contract->effective_date)) / 3600
                    : 24, // Quy đổi thành giờ
                'description' => "Công việc tổng thể cho hợp đồng #$contract->contract_number",
                'qty_request' => 1,
                'contract_id' => $contract->id,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                'is_updated' => 0, // Đánh dấu là task mới tạo
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
            $contractServices = $contract->services->where('parent_id', null)
                ->where('type', '!=', 'discount')
                ->values();

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
                    'due_date' => $contract->expiry_date,
                    'estimate_time' => (!empty($contract->expiry_date) && !empty($contract->effective_date))
                        ? (strtotime($contract->expiry_date) - strtotime($contract->effective_date)) / 3600
                        : 24,
                    'description' => "Công việc thực hiện {$service->name} cho hợp đồng #$contract->contract_number",
                    'qty_request' => $service->quantity,
                    'contract_id' => $contract->id,
                    'service_id' => $service->service_id,
                    'contract_service_id' => $service->id,
                    'parent_id' => $mainTask->id,
                    'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                    'is_updated' => 0, // Đánh dấu là task mới tạo
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
                $subServices = $contract->services->where('parent_id', $service->id)->where('is_active', 1);

                // Tạo task cho các dịch vụ con
                foreach ($subServices as $subService) {
                    $subTaskData = [
                        'name' => $subService->name,
                        'type' => 'SUB',
                        'status_id' => 1,
                        'priority_id' => 1,
                        'assign_id' => $contract->user_id,
                        'start_date' => $contract->effective_date,
                        'due_date' => $contract->expiry_date,
                        'estimate_time' => (!empty($contract->expiry_date) && !empty($contract->effective_date))
                            ? (strtotime($contract->expiry_date) - strtotime($contract->effective_date)) / 3600 / 2
                            : 12, // Chia đôi thời gian so với task cha
                        'description' => "Công việc con {$subService->name} cho dịch vụ {$service->name}",
                        'qty_request' => $subService->quantity,
                        'contract_id' => $contract->id,
                        'contract_service_id' => $subService->id,
                        'parent_id' => $serviceTask->id,
                        'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                        'is_updated' => 0, // Đánh dấu là task mới tạo
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

            // Commit transaction nếu mọi thứ thành công
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Công việc đã được tạo thành công.',
            ]);
        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage() ?? 'Đã xảy ra lỗi khi tạo công việc.',
            ]);
        }
    }

    /**
     * Lấy thông tin chi tiết hợp đồng
     * 
     * @param int $id ID của hợp đồng
     * @return \Illuminate\View\View
     */
    public function detail($id)
    {
        // Lấy hợp đồng với tất cả quan hệ cần thiết
        $contract = Contract::with([
            'user',
            'provider',
            'creator',
            'services' => function ($query) {
                // Sắp xếp để lấy dịch vụ cha trước
                $query->orderBy('parent_id', 'asc');
            },
            'payments',
            'payments.currency',
            'payments.method'
        ])->findOrFail($id);

        // Tổ chức dịch vụ thành cấu trúc cha-con
        $services = $contract->services->where('parent_id', null)->values();

        // Phân loại services theo product_id và type
        $productServices = [];
        $customItems = [];

        foreach ($services as $service) {
            if ($service->type === 'service' || $service->type === 'custom') {
                // Nếu có product_id thì đây là dịch vụ của sản phẩm
                if ($service->product_id) {
                    if (!isset($productServices[$service->product_id])) {
                        $productServices[$service->product_id] = [
                            'id' => $service->id,
                            'product_id' => $service->product_id,
                            'type' => 'product',
                            'services' => []
                        ];
                    }

                    // Lấy các dịch vụ con
                    $subServices = $contract->services
                        ->where('parent_id', $service->id)
                        ->map(function ($subService) use ($service) {
                            return [
                                'id' => $subService->id,
                                'name' => $subService->name,
                                'quantity' => $subService->quantity,
                                'price' => $subService->price,
                                'total' => $subService->price * $service->quantity,
                                'content' => $subService->note,
                            ];
                        })->toArray();

                    $serviceItem = [
                        'id' => $service->id ? $service->id : 'custom',
                        'service_id' => $service->service_id ? $service->service_id : 'custom',
                        'custom_name' => $service->type === 'custom' ? $service->name : null,
                        'price' => $service->price,
                        'note' => $service->note,
                        'sub_services' => $subServices
                    ];

                    $productServices[$service->product_id]['services'][] = $serviceItem;
                } else {
                    // Nếu không có product_id thì đây là mục tùy chỉnh
                    $customItems[] = [
                        'type' => 'custom',
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'note' => $service->note
                    ];
                }
            } elseif ($service->type === 'discount') {
                // Mục giảm giá
                $customItems[] = [
                    'type' => 'custom',
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'note' => $service->note
                ];
            }
        }

        // Biến đổi mảng productServices từ associative sang indexed array
        $productServicesArray = array_values($productServices);

        // Gộp lại tất cả các items
        $contractItems = array_merge($productServicesArray, $customItems);

        // Tính toán thông tin thanh toán
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

        // Lấy tasks của hợp đồng (thêm vào phần này)
        $tasks = Task::with(['assign', 'status'])
            ->where('contract_id', $id)
            ->where('is_active', 1)
            ->orderBy('parent_id', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();


        // Tổ chức tasks thành cấu trúc cha-con
        $mainTasks = $tasks->whereNull('parent_id')->values();
        $taskTree = [];

        foreach ($mainTasks as $mainTask) {
            $taskData = [
                'id' => $mainTask->id,
                'name' => $mainTask->name,
                'type' => $mainTask->type,
                'status' => [
                    'id' => $mainTask->status->id ?? 0,
                    'name' => $mainTask->status->name ?? 'N/A',
                    'color' => $mainTask->status->color ?? 'gray'
                ],
                'assigned_user' => [
                    'id' => $mainTask->assign->id ?? 0,
                    'name' => $mainTask->assign->name ?? 'N/A'
                ],
                'start_date' => $mainTask->start_date,
                'due_date' => $mainTask->due_date,
                'qty_request' => $mainTask->qty_request,
                'qty_completed' => $mainTask->qty_completed ?? 0,
                'progress' => $mainTask->progress,
                'created_at' => $mainTask->created_at,
                'updated_at' => $mainTask->updated_at,
                'children' => []
            ];

            // Tìm các task con cấp 1
            $childTasks = $tasks->where('parent_id', $mainTask->id)->values();

            foreach ($childTasks as $childTask) {
                $childData = [
                    'id' => $childTask->id,
                    'name' => $childTask->name,
                    'type' => $childTask->type,
                    'status' => [
                        'id' => $childTask->status->id ?? 0,
                        'name' => $childTask->status->name ?? 'N/A',
                        'color' => $childTask->status->color ?? 'gray'
                    ],
                    'assigned_user' => [
                        'id' => $childTask->assign->id ?? 0,
                        'name' => $childTask->assign->name ?? 'N/A'
                    ],
                    'start_date' => $childTask->start_date,
                    'due_date' => $childTask->due_date,
                    'qty_request' => $childTask->qty_request,
                    'qty_completed' => $childTask->qty_completed ?? 0,
                    'progress' => $childTask->progress,
                    'created_at' => $childTask->created_at,
                    'updated_at' => $childTask->updated_at,
                    'children' => []
                ];

                // Tìm các task con cấp 2
                $subChildTasks = $tasks->where('parent_id', $childTask->id)->values();

                foreach ($subChildTasks as $subChildTask) {
                    $subChildData = [
                        'id' => $subChildTask->id,
                        'name' => $subChildTask->name,
                        'type' => $subChildTask->type,
                        'status' => [
                            'id' => $subChildTask->status->id ?? 0,
                            'name' => $subChildTask->status->name ?? 'N/A',
                            'color' => $subChildTask->status->color ?? 'gray'
                        ],
                        'assigned_user' => [
                            'id' => $subChildTask->assign->id ?? 0,
                            'name' => $subChildTask->assign->name ?? 'N/A'
                        ],
                        'start_date' => $subChildTask->start_date,
                        'due_date' => $subChildTask->due_date,
                        'qty_request' => $subChildTask->qty_request,
                        'qty_completed' => $subChildTask->qty_completed ?? 0,
                        'progress' => $subChildTask->progress,
                        'created_at' => $subChildTask->created_at,
                        'updated_at' => $subChildTask->updated_at
                    ];

                    $childData['children'][] = $subChildData;
                }

                $taskData['children'][] = $childData;
            }

            $taskTree[] = $taskData;
        }

        // Biến đổi dữ liệu hợp đồng
        $details = [
            'id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'name' => $contract->name,
            'category_id' => 1,
            'category' => [
                'id' => $contract->category->id ?? 0,
                'name' => $contract->category->name ?? 'N/A',
            ],
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
                'total_deduction' => $totalDeduction,
                'total_remaining' => $totalRemaining,
                'total_excess' => $totalExcess,
                'payment_percentage' => $paymentPercentage,
            ],
            'contract_items' => $contractItems,
            'tasks' => $taskTree,
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

        // Lấy các dữ liệu cần thiết cho view
        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $providers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();
        $categories = ServiceCategory::where('is_active', 1)->get()->toArray();
        $services = Service::where('is_active', 1)->get()->toArray();
        $products = Product::where('is_active', 1)->get()->toArray();
        $payment_methods = PaymentMethod::where('is_active', 1)->get()->toArray();
        $currencies = Currency::where('is_active', 1)->get()->toArray();
        $customers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();

        $data_init = [
            'users' => $users,
            'providers' => $providers,
            'categories' => $categories,
            'services' => $services,
            'products' => $products,
            'payment_methods' => $payment_methods,
            'currencies' => $currencies,
            'customers' => $customers,
        ];
        return view('dashboard.contract.show.detail', compact(
            'details',
            'data_init'
        ));
    }

    /**
     * Lấy text hiển thị cho giai đoạn thanh toán
     * 
     * @param int $stage Mã giai đoạn thanh toán
     * @return string Text hiển thị
     */
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
            'status' => 'nullable|integer|in:0,1', // Giả định các trạng thái hợp lệ
            'user_id' => 'nullable|integer|exists:tbl_users,id',
            'provider_id' => 'nullable|integer|exists:tbl_customers,id',
            'category_id' => 'nullable|integer|exists:tbl_categories,id',
            'sign_date' => 'nullable|date_format:d-m-Y H:i:s',
            'effective_date' => 'nullable|date_format:d-m-Y H:i:s',
            'expiry_date' => 'nullable|date_format:d-m-Y H:i:s|after:effective_date',
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
            $contract = Contract::findOrFail($request['id']);
            
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
                $data['sign_date'] = formatDateTime($data['sign_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }
            if (!empty($data['effective_date'])) {
                $data['effective_date'] = formatDateTime($data['effective_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }
            if (!empty($data['expiry_date'])) {
                $data['expiry_date'] = formatDateTime($data['expiry_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            // Loại bỏ các trường null để không ghi đè dữ liệu cũ bằng null
            $data = array_filter($data, function ($value) {
                return !is_null($value);
            });

            if (isset($data['status']) && $data['status'] == 1 && $contract->status == 0) {
                $this->createContractTasks($contract);
            }

            $contract->update($data);

            if ($contract->status == 1) {
                $this->syncContractTasksInternal($contract->id);
            }

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
                        'due_date' => $contract->expiry_date,
                        'estimate_time' => (!empty($contract->expiry_date) && !empty($contract->effective_date))
                            ? (strtotime($contract->expiry_date) - strtotime($contract->effective_date)) / 3600
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
                                'due_date' => $contract->expiry_date,
                                'estimate_time' => (!empty($contract->expiry_date) && !empty($contract->effective_date))
                                    ? (strtotime($contract->expiry_date) - strtotime($contract->effective_date)) / 3600 / 2 // Một nửa thời gian so với task cha
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

            // Đồng bộ lại task sau khi thêm dịch vụ - thêm dòng này
            if ($contract->status == 1) {
                $this->syncContractTasksInternal($contract->id);
            }

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

            // Đồng bộ lại task sau khi cập nhật dịch vụ - thêm dòng này
            if ($contract->status == 1) {
                $this->syncContractTasksInternal($contract->id);
            }

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

            if ($contract->status == 1) {
                $this->syncContractTasksInternal($contract->id);
            }

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

    /**
     * Xuất hợp đồng dưới dạng PDF để làm báo giá
     * 
     * @param int $id ID của hợp đồng
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id)
    {
        try {
            // Lấy hợp đồng với tất cả quan hệ cần thiết
            $contract = Contract::with([
                'user',
                'provider',
                'creator',
                'services' => function ($query) {
                    // Sắp xếp để lấy dịch vụ cha trước
                    $query->orderBy('parent_id', 'asc');
                },
                'payments',
                'payments.currency',
                'payments.method'
            ])->findOrFail($id);

            // Tổ chức dịch vụ thành cấu trúc cha-con
            $services = $contract->services->where('parent_id', null)->values();

            // Lấy thông tin sản phẩm
            $productIds = $services->pluck('product_id')->filter()->unique()->toArray();
            $products = Product::whereIn('id', $productIds)->get();
            $productNames = [];
            foreach ($products as $product) {
                $productNames[$product->id] = $product->name;
            }

            // Phân nhóm dịch vụ theo sản phẩm
            $servicesByProduct = [];
            foreach ($services as $service) {
                if ($service->product_id) {
                    if (!isset($servicesByProduct[$service->product_id])) {
                        $servicesByProduct[$service->product_id] = [];
                    }
                    $servicesByProduct[$service->product_id][] = $service;
                } else {
                    // Dịch vụ không thuộc sản phẩm nào
                    if (!isset($servicesByProduct['no_product'])) {
                        $servicesByProduct['no_product'] = [];
                    }
                    $servicesByProduct['no_product'][] = $service;
                }
            }

            // Chuẩn bị dữ liệu dịch vụ cho PDF
            $formattedServicesByProduct = [];

            // Duyệt qua từng sản phẩm
            foreach ($servicesByProduct as $productId => $productServices) {
                $productFormattedServices = [];

                // Duyệt qua các dịch vụ của sản phẩm
                foreach ($productServices as $service) {
                    // Xác định tên và loại dịch vụ
                    $serviceType = $service->type;

                    // Lấy các dịch vụ con
                    $subServices = $contract->services->where('parent_id', $service->id);

                    $formattedService = [
                        'id' => $service->id,
                        'name' => $service->name,
                        'type' => $serviceType,
                        'quantity' => $service->quantity,
                        'price' => $service->price,
                        'total' => $service->quantity * $service->price,
                        'note' => $service->note,
                        'has_sub_services' => $subServices->count() > 0,
                        'product_id' => $service->product_id,
                        'sub_services' => []
                    ];

                    // Thêm thông tin dịch vụ con nếu có
                    if ($subServices->count() > 0) {
                        foreach ($subServices as $subService) {
                            $formattedService['sub_services'][] = [
                                'id' => $subService->id,
                                'name' => $subService->name,
                                'quantity' => $subService->quantity,
                                'price' => $subService->price,
                                'total' => $subService->quantity * $subService->price,
                                'note' => $subService->note
                            ];
                        }
                    }

                    $productFormattedServices[] = $formattedService;
                }

                $formattedServicesByProduct[$productId] = $productFormattedServices;
            }

            // Tính tổng giá trị hợp đồng
            $totalValue = $contract->total_value;

            // Xử lý thông tin thanh toán
            $paymentInfo = [];
            if ($contract->payments->count() > 0) {
                foreach ($contract->payments as $payment) {
                    $paymentInfo[] = [
                        'name' => $payment->name,
                        'percentage' => $payment->percentage,
                        'price' => $payment->price,
                        'currency' => $payment->currency->currency_code ?? 'VND',
                        'due_date' => $payment->due_date ? formatDateTime($payment->due_date, 'd/m/Y') : '',
                        'payment_stage' => $this->getPaymentStageText($payment->payment_stage),
                    ];
                }
            }

            // Chuẩn bị dữ liệu cho PDF
            $data = [
                'contract' => $contract,
                'services_by_product' => $formattedServicesByProduct,
                'total_value' => $totalValue,
                'payment_info' => $paymentInfo,
                'date_now' => date('d/m/Y'),
                'quote_expiry' => $contract->expiry_date ?
                    \Carbon\Carbon::parse($contract->expiry_date)->format('d/m/Y') :
                    \Carbon\Carbon::now()->addDays(30)->format('d/m/Y'),
                'product_names' => $productNames
            ];


            // Tạo PDF với mẫu báo giá cải tiến
            $pdf = PDF::loadView('dashboard.contract.pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            // Lưu log
            LogService::saveLog([
                'action' => 'EXPORT_QUOTE_PDF',
                'ip' => request()->getClientIp(),
                'details' => "Đã xuất PDF báo giá #{$contract->contract_number}",
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);

            // Tải xuống PDF
            return $pdf->download("BaoGia_{$contract->contract_number}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xuất PDF báo giá: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Helper để cập nhật tổng giá trị hợp đồng
     * 
     * Quy tắc tính:
     * - Nếu service có subservice (theo parent_id) thì tính theo các subservice
     * - Nếu service không có subservice thì tính theo service
     * - Các type khác (discount, custom) tính bình thường
     * 
     * @param Contract $contract
     * @return void
     */
    protected function updateContractTotalValue(Contract $contract)
    {
        // Lấy tất cả dịch vụ active của hợp đồng
        $services = $contract->services()->get();

        $totalValue = 0;


        foreach ($services as $service) {
            if ($service->type == 'service' && $service->subServices()->count() > 0) {
                continue;
            }

            $totalValue += $service->price;
        }

        $contract->update(['total_value' => $totalValue]);

        return $totalValue;
    }


    /**
     * Cập nhật các dịch vụ của hợp đồng
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateContractServices(Request $request)
    {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            // Kiểm tra và lấy thông tin hợp đồng
            $contractId = $request->input('contract_id');
            $contract = Contract::findOrFail($contractId);

            // Kiểm tra điều kiện status hợp đồng
            if (!in_array($contract->status, [0, 1])) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Chỉ được phép cập nhật dịch vụ cho hợp đồng ở trạng thái Chờ duyệt hoặc Đang triển khai.'
                ]);
            }

            // Lấy danh sách dịch vụ hiện tại của hợp đồng
            $currentServices = ContractService::where('contract_id', $contractId)
                ->where('is_active', 1)
                ->get();

            // Lấy dữ liệu cập nhật từ request
            $contractItemsData = json_decode($request->input('contract_items_data'), true);

            if (empty($contractItemsData)) {
                throw new \Exception('Vui lòng thêm ít nhất một sản phẩm hoặc dịch vụ cho hợp đồng.');
            }

            // Mảng lưu ID các dịch vụ đã được xử lý trong request
            $processedServiceIds = [];
            $totalServiceValue = 0;
            $totalDiscountValue = 0;

            // Mảng lưu các task cần tạo mới sau khi cập nhật
            $newTasksToCreate = [];

            // Xử lý từng item trong request
            foreach ($contractItemsData as $item) {
                $itemType = $item['type'];

                if ($itemType === 'product') {
                    // Xử lý các sản phẩm
                    $productId = $item['product_id'];

                    // Bỏ qua nếu product_id không hợp lệ
                    if ($productId === 'Chọn sản phẩm' || empty($productId)) {
                        continue;
                    }

                    // Duyệt qua các dịch vụ của sản phẩm
                    foreach ($item['services'] as $service) {
                        $serviceId = null;
                        $serviceType = 'service';
                        $serviceName = null;

                        // Xác định loại dịch vụ và lấy thông tin
                        if ($service['service_id'] === 'custom') {
                            $serviceType = 'custom';
                            $serviceName = $service['custom_name'];
                        } else {
                            $serviceId = $service['service_id'];
                            $serviceObj = Service::find($serviceId);
                            $serviceName = $serviceObj ? $serviceObj->name : "Dịch vụ #" . $serviceId;
                        }

                        $servicePrice = (float) $service['price'];
                        $serviceQuantity = 1; // Mặc định là 1

                        // Cập nhật tổng giá trị dịch vụ
                        $totalServiceValue += $servicePrice;

                        // Tìm dịch vụ hiện tại nếu có ID trong request
                        $currentService = null;
                        $serviceDbId = isset($service['id']) ? $service['id'] : null;

                        if ($serviceDbId) {
                            $currentService = $currentServices->where('id', $serviceDbId)->first();
                        }

                        // Nếu dịch vụ đã tồn tại thì cập nhật, nếu không thì tạo mới
                        if ($currentService) {
                            // Cập nhật thông tin dịch vụ
                            $currentService->update([
                                'service_id' => $serviceId,
                                'name' => $serviceName,
                                'type' => $serviceType,
                                'price' => $servicePrice,
                                'note' => $service['note'] ?? null,
                            ]);

                            // Lưu ID vào danh sách đã xử lý
                            $processedServiceIds[] = $currentService->id;

                            // Kiểm tra task liên quan nếu hợp đồng đang triển khai
                            if ($contract->status == 1) {
                                $this->updateRelatedServiceTask($currentService, $servicePrice, $serviceQuantity);
                            }
                        } else {
                            // Tạo mới dịch vụ
                            $newService = ContractService::create([
                                'contract_id' => $contractId,
                                'service_id' => $serviceId,
                                'product_id' => $productId,
                                'name' => $serviceName,
                                'type' => $serviceType,
                                'quantity' => $serviceQuantity,
                                'price' => $servicePrice,
                                'note' => $service['note'] ?? null,
                                'is_active' => 1,
                            ]);

                            // Lưu ID vào danh sách đã xử lý
                            $processedServiceIds[] = $newService->id;

                            // Nếu hợp đồng đang triển khai, tạo task cho dịch vụ mới
                            if ($contract->status == 1) {
                                $newTasksToCreate[] = [
                                    'serviceId' => $newService->id,
                                    'serviceName' => $serviceName,
                                    'contractId' => $contractId,
                                    'serviceDbId' => $serviceId,
                                    'serviceType' => 'SERVICE',
                                    'price' => $servicePrice,
                                    'quantity' => $serviceQuantity,
                                ];
                            }
                        }

                        // Xử lý các dịch vụ con
                        if (!empty($service['sub_services'])) {
                            $processedSubServiceIds = [];

                            foreach ($service['sub_services'] as $subService) {
                                $subServiceName = $subService['name'];
                                $subServiceQuantity = (float) $subService['quantity'];
                                $subServicePrice = (float) $subService['total'];
                                $subServiceNote = $subService['content'] ?? null;

                                // Tìm dịch vụ con hiện tại nếu có ID trong request
                                $currentSubService = null;
                                $subServiceDbId = isset($subService['id']) ? $subService['id'] : null;

                                if ($subServiceDbId) {
                                    $currentSubService = $currentServices->where('id', $subServiceDbId)->first();
                                }

                                // Lấy ID dịch vụ cha (dịch vụ đã xử lý ở trên)
                                $parentServiceId = $currentService ? $currentService->id : $newService->id;

                                // Nếu dịch vụ con đã tồn tại thì cập nhật, nếu không thì tạo mới
                                if ($currentSubService) {
                                    // Cập nhật thông tin dịch vụ con
                                    $currentSubService->update([
                                        'name' => $subServiceName,
                                        'quantity' => $subServiceQuantity,
                                        'price' => $subServicePrice,
                                        'note' => $subServiceNote,
                                        'parent_id' => $parentServiceId,
                                    ]);

                                    // Lưu ID vào danh sách đã xử lý
                                    $processedServiceIds[] = $currentSubService->id;
                                    $processedSubServiceIds[] = $currentSubService->id;

                                    // Kiểm tra task liên quan nếu hợp đồng đang triển khai
                                    if ($contract->status == 1) {
                                        $this->updateRelatedSubServiceTask($currentSubService, $subServicePrice, $subServiceQuantity);
                                    }
                                } else {
                                    // Tạo mới dịch vụ con
                                    $newSubService = ContractService::create([
                                        'contract_id' => $contractId,
                                        'product_id' => $productId,
                                        'name' => $subServiceName,
                                        'type' => 'sub_service',
                                        'quantity' => $subServiceQuantity,
                                        'price' => $subServicePrice,
                                        'note' => $subServiceNote,
                                        'parent_id' => $parentServiceId,
                                        'is_active' => 1,
                                    ]);

                                    // Lưu ID vào danh sách đã xử lý
                                    $processedServiceIds[] = $newSubService->id;
                                    $processedSubServiceIds[] = $newSubService->id;

                                    // Nếu hợp đồng đang triển khai, tạo task cho dịch vụ con mới
                                    if ($contract->status == 1) {
                                        $newTasksToCreate[] = [
                                            'serviceId' => $newSubService->id,
                                            'serviceName' => $subServiceName,
                                            'contractId' => $contractId,
                                            'parentServiceId' => $parentServiceId,
                                            'serviceType' => 'SUB',
                                            'price' => $subServicePrice,
                                            'quantity' => $subServiceQuantity,
                                        ];
                                    }
                                }
                            }

                            // Vô hiệu hóa các dịch vụ con không có trong request mới
                            $currentSubServices = $currentServices->where('parent_id', $parentServiceId);
                            foreach ($currentSubServices as $existingSubService) {
                                if (!in_array($existingSubService->id, $processedSubServiceIds)) {
                                    // Kiểm tra và xử lý task liên quan
                                    if ($contract->status == 1) {
                                        $this->deactivateServiceTask($existingSubService->id);
                                    }

                                    // Vô hiệu hóa dịch vụ con
                                    $existingSubService->update(['is_active' => 0]);
                                }
                            }
                        }
                    }
                } else if ($itemType === 'custom') {
                    // Xử lý mục tùy chỉnh (có thể là custom hoặc discount)
                    $itemName = $item['name'];
                    $itemPrice = (float) $item['price'];
                    $itemNote = $item['note'] ?? null;
                    $customItemType = ($itemPrice < 0) ? 'discount' : 'custom';

                    // Cập nhật tổng giá trị dịch vụ hoặc giảm giá
                    if ($customItemType === 'discount') {
                        $totalDiscountValue += abs($itemPrice);
                    } else {
                        $totalServiceValue += $itemPrice;
                    }

                    // Tìm mục tùy chỉnh hiện tại nếu có ID trong request
                    $currentCustomItem = null;
                    $customItemDbId = isset($item['id']) ? $item['id'] : null;

                    if ($customItemDbId) {
                        $currentCustomItem = $currentServices->where('id', $customItemDbId)->first();
                    }

                    // Nếu mục tùy chỉnh đã tồn tại thì cập nhật, nếu không thì tạo mới
                    if ($currentCustomItem) {
                        // Cập nhật thông tin mục tùy chỉnh
                        $currentCustomItem->update([
                            'name' => $itemName,
                            'type' => $customItemType,
                            'price' => $itemPrice,
                            'note' => $itemNote,
                        ]);

                        // Lưu ID vào danh sách đã xử lý
                        $processedServiceIds[] = $currentCustomItem->id;

                        // Kiểm tra task liên quan nếu hợp đồng đang triển khai
                        if ($contract->status == 1 && $customItemType !== 'discount') {
                            $this->updateRelatedServiceTask($currentCustomItem, $itemPrice, 1);
                        }
                    } else {
                        // Tạo mới mục tùy chỉnh
                        $newCustomItem = ContractService::create([
                            'contract_id' => $contractId,
                            'name' => $itemName,
                            'type' => $customItemType,
                            'quantity' => 1,
                            'price' => $itemPrice,
                            'note' => $itemNote,
                            'is_active' => 1,
                        ]);

                        // Lưu ID vào danh sách đã xử lý
                        $processedServiceIds[] = $newCustomItem->id;

                        // Nếu hợp đồng đang triển khai và không phải discount, tạo task cho mục tùy chỉnh mới
                        if ($contract->status == 1 && $customItemType !== 'discount') {
                            $newTasksToCreate[] = [
                                'serviceId' => $newCustomItem->id,
                                'serviceName' => $itemName,
                                'contractId' => $contractId,
                                'serviceType' => 'SERVICE',
                                'price' => $itemPrice,
                                'quantity' => 1,
                            ];
                        }
                    }
                }
            }

            // Vô hiệu hóa các dịch vụ không có trong request mới
            foreach ($currentServices as $existingService) {
                if (!in_array($existingService->id, $processedServiceIds)) {
                    // Kiểm tra và xử lý task liên quan
                    if ($contract->status == 1) {
                        $this->deactivateServiceTask($existingService->id);
                    }

                    // Vô hiệu hóa dịch vụ
                    $existingService->update(['is_active' => 0]);
                }
            }

            // Cập nhật tổng giá trị hợp đồng
            $this->updateContractTotalValue($contract);
            // Tạo các task mới nếu cần
            if (!empty($newTasksToCreate)) {
                $this->createNewTasksForServices($newTasksToCreate, $contract);
            }

            // Đồng bộ lại toàn bộ task sau các thay đổi - thêm dòng này
            if ($contract->status == 1) {
                $this->syncContractTasksInternal($contract->id);
            }

            // Logging
            LogService::saveLog([
                'action' => 'UPDATE_CONTRACT_SERVICES',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật dịch vụ cho hợp đồng #' . $contract->contract_number,
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);

            // Commit transaction nếu mọi thứ thành công
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Dịch vụ hợp đồng đã được cập nhật thành công.',
                'data' => [
                    'contract_id' => $contract->id,
                    'total_value' => $contract->total_value,
                ]
            ]);
        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage() ?? 'Đã xảy ra lỗi khi cập nhật dịch vụ hợp đồng.',
            ]);
        }
    }

    /**
     * Cập nhật task liên quan đến dịch vụ
     *
     * @param ContractService $service Đối tượng dịch vụ
     * @param float $newPrice Giá mới của dịch vụ
     * @param int $newQuantity Số lượng mới của dịch vụ
     * @return void
     */
    private function updateRelatedServiceTask($service, $newPrice, $newQuantity)
    {
        // Tìm tất cả task liên quan đến dịch vụ (bao gồm cả task gốc và task bổ sung)
        $allTasks = Task::where('contract_service_id', $service->id)
            ->where('is_active', 1)
            ->orderBy('created_at', 'asc') // Sắp xếp theo thời gian tạo để xử lý đúng thứ tự
            ->get();

        if ($allTasks->isEmpty()) {
            return; // Không có task liên quan
        }

        // Phân loại các task theo trạng thái
        $uncompletedTasks = $allTasks->filter(function ($task) {
            return $task->status_id < 4;
        });

        $completedTasks = $allTasks->filter(function ($task) {
            return $task->status_id >= 4;
        });

        // Cập nhật thông tin các task chưa hoàn thành
        foreach ($uncompletedTasks as $task) {
            // Chỉ cập nhật tên và mô tả, không cập nhật số lượng ngay
            $task->update([
                'name' => strpos($task->name, ' (Bổ sung') !== false ? $task->name : $service->name,
                'description' => strpos($task->name, ' (Bổ sung') !== false
                    ? $task->description
                    : "Công việc thực hiện {$service->name} cho hợp đồng #{$service->contract_id}",
                'is_updated' => 1, // Đánh dấu là task đã cập nhật
            ]);

            // Log sự thay đổi
            LogService::saveLog([
                'action' => 'TASK_UPDATE_LOG',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật thông tin task #' . $task->id . ' theo dịch vụ ' . $service->name,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $task->id,
            ]);
        }

        // Tính tổng số lượng đã hoàn thành từ các task
        $totalCompletedQuantity = $completedTasks->sum('qty_request');

        // Tính tổng số lượng đang thực hiện (chưa hoàn thành)
        $totalUncompletedQuantity = $uncompletedTasks->sum('qty_request');

        // Tính số lượng còn thiếu so với yêu cầu mới
        $quantityNeeded = $newQuantity - $totalCompletedQuantity;

        // Không cần xử lý nếu số lượng đã hoàn thành vượt quá số lượng yêu cầu mới
        if ($quantityNeeded <= 0) {
            return;
        }

        // Nếu còn task chưa hoàn thành, phân bổ số lượng mới cho các task này
        if (!$uncompletedTasks->isEmpty()) {
            // Nếu tổng số lượng chưa hoàn thành bằng số lượng cần, không cần cập nhật
            if ($totalUncompletedQuantity == $quantityNeeded) {
                return;
            }

            // Nếu chỉ có 1 task chưa hoàn thành, cập nhật số lượng cho task đó
            if ($uncompletedTasks->count() == 1) {
                $uncompletedTask = $uncompletedTasks->first();
                $uncompletedTask->update([
                    'qty_request' => $quantityNeeded,
                    'description' => strpos($uncompletedTask->name, ' (Bổ sung') !== false
                        ? "Công việc bổ sung cho {$service->name} (phần chênh lệch sau cập nhật - đã điều chỉnh)"
                        : "Công việc thực hiện {$service->name} cho hợp đồng #{$service->contract_id}",
                ]);

                // Log việc cập nhật task
                LogService::saveLog([
                    'action' => 'TASK_UPDATE_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật số lượng task #' . $uncompletedTask->id . ' từ ' . $uncompletedTask->qty_request . ' thành ' . $quantityNeeded . ' cho dịch vụ ' . $service->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $uncompletedTask->id,
                ]);

                return;
            }

            // Nếu có nhiều task chưa hoàn thành, ưu tiên cập nhật task gốc trước, sau đó đến task bổ sung
            $originalTask = $uncompletedTasks->first(function ($task) {
                return strpos($task->name, ' (Bổ sung') === false;
            });

            $supplementTasks = $uncompletedTasks->filter(function ($task) {
                return strpos($task->name, ' (Bổ sung') !== false;
            })->sortBy('created_at');

            // Cập nhật task gốc nếu có
            if ($originalTask) {
                $originalTask->update(['qty_request' => $quantityNeeded]);
                $quantityNeeded = 0; // Đã phân bổ hết

                // Log việc cập nhật task
                LogService::saveLog([
                    'action' => 'TASK_UPDATE_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật số lượng task gốc #' . $originalTask->id . ' cho dịch vụ ' . $service->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $originalTask->id,
                ]);

                // Vô hiệu hóa các task bổ sung không cần thiết
                foreach ($supplementTasks as $supplementTask) {
                    $supplementTask->update([
                        'is_active' => 0,
                        'description' => $supplementTask->description . " (Đã gỡ do đã cập nhật số lượng ở task gốc)",
                    ]);

                    // Log việc gỡ task bổ sung
                    LogService::saveLog([
                        'action' => 'TASK_REMOVE_LOG',
                        'ip' => request()->getClientIp(),
                        'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã gỡ task bổ sung #' . $supplementTask->id . ' do đã cập nhật số lượng ở task gốc',
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $supplementTask->id,
                    ]);
                }
            } else if (!$supplementTasks->isEmpty()) {
                // Nếu không có task gốc, cập nhật task bổ sung đầu tiên
                $firstSupplementTask = $supplementTasks->first();
                $firstSupplementTask->update(['qty_request' => $quantityNeeded]);
                $quantityNeeded = 0; // Đã phân bổ hết

                // Log việc cập nhật task
                LogService::saveLog([
                    'action' => 'TASK_UPDATE_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật số lượng task bổ sung #' . $firstSupplementTask->id . ' cho dịch vụ ' . $service->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $firstSupplementTask->id,
                ]);

                // Vô hiệu hóa các task bổ sung khác không cần thiết
                $otherSupplementTasks = $supplementTasks->filter(function ($task) use ($firstSupplementTask) {
                    return $task->id !== $firstSupplementTask->id;
                });

                foreach ($otherSupplementTasks as $otherTask) {
                    $otherTask->update([
                        'is_active' => 0,
                        'description' => $otherTask->description . " (Đã gỡ do đã cập nhật số lượng ở task bổ sung khác)",
                    ]);

                    // Log việc gỡ task bổ sung
                    LogService::saveLog([
                        'action' => 'TASK_REMOVE_LOG',
                        'ip' => request()->getClientIp(),
                        'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã gỡ task bổ sung #' . $otherTask->id . ' do đã cập nhật số lượng ở task bổ sung khác',
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $otherTask->id,
                    ]);
                }
            }
        } else {
            // Nếu không có task chưa hoàn thành, tạo task mới

            // Tìm task đã hoàn thành gần nhất để lấy thông tin
            $lastCompletedTask = $completedTasks->sortByDesc('created_at')->first();

            // Tạo task mới cho phần chênh lệch
            $supplementNumber = Task::where('contract_service_id', $service->id)
                ->where('name', 'like', $service->name . " (Bổ sung%")
                ->count() + 1;

            $supplementName = $supplementNumber > 1
                ? $service->name . " (Bổ sung " . $supplementNumber . ")"
                : $service->name . " (Bổ sung)";

            $newTask = Task::create([
                'name' => $supplementName,
                'type' => $lastCompletedTask->type,
                'status_id' => 1, // Trạng thái mới
                'priority_id' => $lastCompletedTask->priority_id,
                'assign_id' => $lastCompletedTask->assign_id,
                'start_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+7 days')),
                'estimate_time' => $lastCompletedTask->estimate_time,
                'description' => "Công việc bổ sung cho {$service->name} (phần chênh lệch sau cập nhật)",
                'qty_request' => $quantityNeeded,
                'contract_id' => $service->contract_id,
                'service_id' => $service->service_id,
                'contract_service_id' => $service->id,
                'parent_id' => $lastCompletedTask->parent_id,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                'is_updated' => 0,
                'is_active' => 1,
            ]);

            // Log việc tạo task mới
            LogService::saveLog([
                'action' => 'TASK_CREATE_LOG',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task bổ sung #' . $newTask->id . ' cho dịch vụ ' . $service->name . ' với số lượng ' . $quantityNeeded,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $newTask->id,
            ]);
        }
    }


    /**
     * Cập nhật task liên quan đến dịch vụ con
     *
     * @param ContractService $subService Đối tượng dịch vụ con
     * @param float $newPrice Giá mới của dịch vụ con
     * @param int $newQuantity Số lượng mới của dịch vụ con
     * @return void
     */
    private function updateRelatedSubServiceTask($subService, $newPrice, $newQuantity)
    {
        // Tìm tất cả task liên quan đến dịch vụ con (bao gồm cả task gốc và task bổ sung)
        $allTasks = Task::where('contract_service_id', $subService->id)
            ->where('is_active', 1)
            ->orderBy('created_at', 'asc') // Sắp xếp theo thời gian tạo để xử lý đúng thứ tự
            ->get();

        if ($allTasks->isEmpty()) {
            return; // Không có task liên quan
        }

        // Tìm task cha (task của dịch vụ cha)
        $parentTask = Task::where('contract_service_id', $subService->parent_id)
            ->where('is_active', 1)
            ->first();

        // Phân loại các task theo trạng thái
        $uncompletedTasks = $allTasks->filter(function ($task) {
            return $task->status_id < 4;
        });

        $completedTasks = $allTasks->filter(function ($task) {
            return $task->status_id >= 4;
        });

        // Cập nhật thông tin các task chưa hoàn thành
        foreach ($uncompletedTasks as $task) {
            // Chỉ cập nhật tên và mô tả, không cập nhật số lượng ngay
            $task->update([
                'name' => strpos($task->name, ' (Bổ sung') !== false ? $task->name : $subService->name,
                'description' => strpos($task->name, ' (Bổ sung') !== false
                    ? $task->description
                    : "Công việc con {$subService->name} cho dịch vụ chính",
                'is_updated' => 1, // Đánh dấu là task đã cập nhật
            ]);

            // Log sự thay đổi
            LogService::saveLog([
                'action' => 'TASK_UPDATE_LOG',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật thông tin task con #' . $task->id . ' theo dịch vụ con ' . $subService->name,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $task->id,
            ]);
        }

        // Tính tổng số lượng đã hoàn thành từ các task
        $totalCompletedQuantity = $completedTasks->sum('qty_request');

        // Tính số lượng còn thiếu so với yêu cầu mới
        $quantityNeeded = $newQuantity - $totalCompletedQuantity;

        // Không cần xử lý nếu số lượng đã hoàn thành vượt quá số lượng yêu cầu mới
        if ($quantityNeeded <= 0) {
            return;
        }

        // Nếu còn task chưa hoàn thành, phân bổ số lượng mới cho các task này
        if (!$uncompletedTasks->isEmpty()) {
            // Nếu tổng số lượng chưa hoàn thành bằng số lượng cần, không cần cập nhật
            $totalUncompletedQuantity = $uncompletedTasks->sum('qty_request');
            if ($totalUncompletedQuantity == $quantityNeeded) {
                return;
            }

            // Nếu chỉ có 1 task chưa hoàn thành, cập nhật số lượng cho task đó
            if ($uncompletedTasks->count() == 1) {
                $uncompletedTask = $uncompletedTasks->first();
                $uncompletedTask->update([
                    'qty_request' => $quantityNeeded,
                    'description' => strpos($uncompletedTask->name, ' (Bổ sung') !== false
                        ? "Công việc con bổ sung cho {$subService->name} (phần chênh lệch sau cập nhật - đã điều chỉnh)"
                        : "Công việc con {$subService->name} cho dịch vụ chính",
                ]);

                // Log việc cập nhật task
                LogService::saveLog([
                    'action' => 'TASK_UPDATE_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật số lượng task con #' . $uncompletedTask->id . ' từ ' . $uncompletedTask->qty_request . ' thành ' . $quantityNeeded . ' cho dịch vụ con ' . $subService->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $uncompletedTask->id,
                ]);

                return;
            }

            // Nếu có nhiều task chưa hoàn thành, ưu tiên cập nhật task gốc trước, sau đó đến task bổ sung
            $originalTask = $uncompletedTasks->first(function ($task) {
                return strpos($task->name, ' (Bổ sung') === false;
            });

            $supplementTasks = $uncompletedTasks->filter(function ($task) {
                return strpos($task->name, ' (Bổ sung') !== false;
            })->sortBy('created_at');

            // Cập nhật task gốc nếu có
            if ($originalTask) {
                $originalTask->update(['qty_request' => $quantityNeeded]);
                $quantityNeeded = 0; // Đã phân bổ hết

                // Log việc cập nhật task
                LogService::saveLog([
                    'action' => 'TASK_UPDATE_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật số lượng task con gốc #' . $originalTask->id . ' cho dịch vụ con ' . $subService->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $originalTask->id,
                ]);

                // Vô hiệu hóa các task bổ sung không cần thiết
                foreach ($supplementTasks as $supplementTask) {
                    $supplementTask->update([
                        'is_active' => 0,
                        'description' => $supplementTask->description . " (Đã gỡ do đã cập nhật số lượng ở task gốc)",
                    ]);

                    // Log việc gỡ task bổ sung
                    LogService::saveLog([
                        'action' => 'TASK_REMOVE_LOG',
                        'ip' => request()->getClientIp(),
                        'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã gỡ task con bổ sung #' . $supplementTask->id . ' do đã cập nhật số lượng ở task gốc',
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $supplementTask->id,
                    ]);
                }
            } else if (!$supplementTasks->isEmpty()) {
                // Nếu không có task gốc, cập nhật task bổ sung đầu tiên
                $firstSupplementTask = $supplementTasks->first();
                $firstSupplementTask->update(['qty_request' => $quantityNeeded]);
                $quantityNeeded = 0; // Đã phân bổ hết

                // Log việc cập nhật task
                LogService::saveLog([
                    'action' => 'TASK_UPDATE_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã cập nhật số lượng task con bổ sung #' . $firstSupplementTask->id . ' cho dịch vụ con ' . $subService->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $firstSupplementTask->id,
                ]);

                // Vô hiệu hóa các task bổ sung khác không cần thiết
                $otherSupplementTasks = $supplementTasks->filter(function ($task) use ($firstSupplementTask) {
                    return $task->id !== $firstSupplementTask->id;
                });

                foreach ($otherSupplementTasks as $otherTask) {
                    $otherTask->update([
                        'is_active' => 0,
                        'description' => $otherTask->description . " (Đã gỡ do đã cập nhật số lượng ở task bổ sung khác)",
                    ]);

                    // Log việc gỡ task bổ sung
                    LogService::saveLog([
                        'action' => 'TASK_REMOVE_LOG',
                        'ip' => request()->getClientIp(),
                        'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã gỡ task con bổ sung #' . $otherTask->id . ' do đã cập nhật số lượng ở task bổ sung khác',
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $otherTask->id,
                    ]);
                }
            }
        } else {
            // Nếu không có task chưa hoàn thành, tạo task mới

            // Tìm task đã hoàn thành gần nhất để lấy thông tin
            $lastCompletedTask = $completedTasks->sortByDesc('created_at')->first();

            // Tạo task mới cho phần chênh lệch
            $supplementNumber = Task::where('contract_service_id', $subService->id)
                ->where('name', 'like', $subService->name . " (Bổ sung%")
                ->count() + 1;

            $supplementName = $supplementNumber > 1
                ? $subService->name . " (Bổ sung " . $supplementNumber . ")"
                : $subService->name . " (Bổ sung)";

            $newTask = Task::create([
                'name' => $supplementName,
                'type' => 'SUB',
                'status_id' => 1, // Trạng thái mới
                'priority_id' => $lastCompletedTask->priority_id,
                'assign_id' => $lastCompletedTask->assign_id,
                'start_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+7 days')),
                'estimate_time' => $lastCompletedTask->estimate_time,
                'description' => "Công việc con bổ sung cho {$subService->name} (phần chênh lệch sau cập nhật)",
                'qty_request' => $quantityNeeded,
                'contract_id' => $subService->contract_id,
                'contract_service_id' => $subService->id,
                'parent_id' => $parentTask ? $parentTask->id : $lastCompletedTask->parent_id,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                'is_updated' => 0,
                'is_active' => 1,
            ]);

            // Log việc tạo task mới
            LogService::saveLog([
                'action' => 'TASK_CREATE_LOG',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task con bổ sung #' . $newTask->id . ' cho dịch vụ con ' . $subService->name . ' với số lượng ' . $quantityNeeded,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $newTask->id,
            ]);
        }
    }

    /**
     * Vô hiệu hóa task liên quan đến dịch vụ bị xóa
     *
     * @param int $serviceId ID của dịch vụ
     * @return void
     */
    private function deactivateServiceTask($serviceId)
    {
        // Tìm dịch vụ để xác định loại
        $service = ContractService::find($serviceId);
        if (!$service) {
            return; // Dịch vụ không tồn tại
        }

        // Vô hiệu hóa task của dịch vụ hiện tại
        $tasks = Task::where('contract_service_id', $serviceId)
            ->where('is_active', 1)
            ->get();

        foreach ($tasks as $task) {
            // Chỉ vô hiệu hóa task chưa hoàn thành
            if ($task->status_id < 4) {
                $task->update([
                    'is_active' => 0,
                    'description' => $task->description . " (Đã gỡ do dịch vụ đã bị xóa)",
                ]);

                // Log việc gỡ task
                LogService::saveLog([
                    'action' => 'TASK_REMOVE_LOG',
                    'ip' => request()->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã gỡ task #' . $task->id . ' do dịch vụ liên quan đã bị xóa',
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $task->id,
                ]);
            }
        }

        // Nếu là dịch vụ cha, vô hiệu hóa tất cả dịch vụ con và task con liên quan
        if ($service->parent_id === null) {
            // Tìm tất cả dịch vụ con
            $childServices = ContractService::where('parent_id', $serviceId)
                ->where('is_active', 1)
                ->get();

            foreach ($childServices as $childService) {
                // Vô hiệu hóa dịch vụ con
                $childService->update(['is_active' => 0]);

                // Vô hiệu hóa task của dịch vụ con
                $childTasks = Task::where('contract_service_id', $childService->id)
                    ->where('is_active', 1)
                    ->get();

                foreach ($childTasks as $childTask) {
                    // Chỉ vô hiệu hóa task chưa hoàn thành
                    if ($childTask->status_id < 4) {
                        $childTask->update([
                            'is_active' => 0,
                            'description' => $childTask->description . " (Đã gỡ do dịch vụ cha đã bị xóa)",
                        ]);

                        // Log việc gỡ task con
                        LogService::saveLog([
                            'action' => 'TASK_REMOVE_LOG',
                            'ip' => request()->getClientIp(),
                            'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã gỡ task con #' . $childTask->id . ' do dịch vụ cha đã bị xóa',
                            'fk_key' => 'tbl_tasks|id',
                            'fk_value' => $childTask->id,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Tạo các task mới cho dịch vụ mới thêm vào
     *
     * @param array $newTasksInfo Mảng thông tin các task cần tạo
     * @param Contract $contract Đối tượng hợp đồng
     * @return void
     */
    private function createNewTasksForServices($newTasksInfo, $contract)
    {
        // Tìm task chính của hợp đồng
        $mainTask = Task::where('contract_id', $contract->id)
            ->where('type', 'CONTRACT')
            ->where('is_active', 1)
            ->first();

        if (!$mainTask) {
            // Nếu không tìm thấy task chính, tạo task chính
            $mainTask = Task::create([
                'name' => "Hợp đồng #$contract->contract_number - $contract->name",
                'type' => 'CONTRACT',
                'status_id' => 1, // Trạng thái mặc định
                'priority_id' => 1, // Độ ưu tiên mặc định
                'assign_id' => $contract->user_id, // Gán cho nhân viên phụ trách
                'start_date' => $contract->effective_date,
                'due_date' => $contract->expiry_date,
                'estimate_time' => (!empty($contract->effective_date) && !empty($contract->effective_date))
                    ? (strtotime($contract->effective_date) - strtotime($contract->effective_date)) / 3600
                    : 24, // Quy đổi thành giờ
                'description' => "Công việc tổng thể cho hợp đồng #$contract->contract_number",
                'qty_request' => 1,
                'contract_id' => $contract->id,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                'is_updated' => 0, // Đánh dấu là task mới tạo
                'is_active' => 1,
            ]);

            // Log việc tạo task chính
            LogService::saveLog([
                'action' => 'TASK_CREATE_LOG',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task chính #' . $mainTask->id . ' cho hợp đồng ' . $contract->contract_number,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $mainTask->id,
            ]);
        }

        // Duyệt qua các task cần tạo
        foreach ($newTasksInfo as $taskInfo) {
            // Xác định loại task và task cha
            $serviceType = $taskInfo['serviceType'];
            $parentId = null;

            if ($serviceType === 'SERVICE') {
                // Nếu là dịch vụ chính, parent là task chính
                $parentId = $mainTask->id;
            } else if ($serviceType === 'SUB') {
                // Nếu là dịch vụ con, tìm task của dịch vụ cha
                $parentServiceTask = Task::where('contract_service_id', $taskInfo['parentServiceId'])
                    ->where('is_active', 1)
                    ->first();

                $parentId = $parentServiceTask ? $parentServiceTask->id : $mainTask->id;
            }

            // Tạo task mới
            $newTask = Task::create([
                'name' => $taskInfo['serviceName'],
                'type' => $serviceType,
                'status_id' => 1, // Trạng thái mặc định
                'priority_id' => 1, // Độ ưu tiên mặc định
                'assign_id' => $contract->user_id, // Gán cho nhân viên phụ trách
                'start_date' => $contract->effective_date ?? date('Y-m-d'),
                'due_date' => $contract->expiry_date ?? date('Y-m-d', strtotime('+30 days')),
                'estimate_time' => (!empty($contract->effective_date) && !empty($contract->effective_date))
                    ? (strtotime($contract->effective_date) - strtotime($contract->effective_date)) / 3600
                    : ($serviceType === 'SUB' ? 12 : 24), // Nếu là task con thì thời gian ít hơn
                'description' => $serviceType === 'SERVICE'
                    ? "Công việc thực hiện {$taskInfo['serviceName']} cho hợp đồng #{$contract->contract_number}"
                    : "Công việc con {$taskInfo['serviceName']} cho dịch vụ chính",
                'qty_request' => $taskInfo['quantity'],
                'contract_id' => $contract->id,
                'service_id' => isset($taskInfo['serviceDbId']) ? $taskInfo['serviceDbId'] : null,
                'contract_service_id' => $taskInfo['serviceId'],
                'parent_id' => $parentId,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                'is_updated' => 0, // Đánh dấu là task mới tạo
                'is_active' => 1,
            ]);

            // Log việc tạo task
            LogService::saveLog([
                'action' => 'TASK_CREATE_LOG',
                'ip' => request()->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã tạo task ' .
                    ($serviceType === 'SUB' ? 'con ' : '') . '#' . $newTask->id . ' cho ' .
                    ($serviceType === 'SUB' ? 'dịch vụ con ' : 'dịch vụ ') . $taskInfo['serviceName'],
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $newTask->id,
            ]);
        }
    }

    /**
     * Hủy hợp đồng và cập nhật các đối tượng liên quan
     * 
     * @param int $id ID của hợp đồng cần hủy
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelContract(Request $request)
    {
        try {
            DB::beginTransaction();

            // Tìm hợp đồng
            $contract = Contract::findOrFail($request['id']);

            // Cập nhật trạng thái hợp đồng thành 3 (đã hủy)
            $contract->update(['status' => 3]);

            // Tìm và cập nhật tất cả các task liên quan sang trạng thái 5
            Task::where('contract_id', $request['id'])->update(['status_id' => 5]);

            // Ghi log
            LogService::saveLog([
                'action' => 'CANCEL_CONTRACT',
                'ip' => request()->getClientIp(),
                'details' => "Đã hủy hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Hợp đồng đã được hủy thành công.',
                'data' => [
                    'contract_id' => $contract->id,
                    'contract_number' => $contract->contract_number
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi hủy hợp đồng: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Đồng bộ lại trạng thái và số lượng của các task theo hợp đồng
     * 
     * @param int $contractId ID của hợp đồng cần đồng bộ
     * @return bool Trạng thái thành công
     */
    private function syncContractTasksInternal($contractId)
    {
        try {
            // Tạo instance của TaskController
            $taskController = new \App\Http\Controllers\Dashboard\Account\Task\TaskController();

            // Tạo request mới với contract_id
            $request = new \Illuminate\Http\Request();
            $request->merge(['contract_id' => $contractId]);

            // Gọi phương thức synchronizeContractTasks
            $response = $taskController->synchronizeContractTasks($request);

            // Kiểm tra kết quả
            $responseData = json_decode($response->getContent(), true);
            return $responseData['status'] == 200;
        } catch (\Exception $e) {
            // Log lỗi nếu cần
            \Illuminate\Support\Facades\Log::error('Không thể đồng bộ công việc cho hợp đồng ' . $contractId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gọi đồng bộ task từ controller hợp đồng
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncTasks(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'contract_id' => 'required|integer|exists:tbl_contracts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $contractId = $request->contract_id;
            $contract = Contract::findOrFail($contractId);

            // Đảm bảo hợp đồng đang triển khai
            if ($contract->status != 1) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Chỉ có thể đồng bộ task cho hợp đồng đang trong trạng thái triển khai.'
                ]);
            }

            // Tạo instance của TaskController và gọi phương thức đồng bộ
            $taskController = new \App\Http\Controllers\Dashboard\Account\Task\TaskController();
            $response = $taskController->synchronizeContractTasks($request);

            // Lấy kết quả từ response
            $responseData = json_decode($response->getContent(), true);

            // Lưu log
            LogService::saveLog([
                'action' => 'SYNC_CONTRACT_TASKS',
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') đã đồng bộ task cho hợp đồng #' . $contract->contract_number,
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contractId,
            ]);

            return response()->json([
                'status' => $responseData['status'],
                'message' => $responseData['message'],
                'data' => $responseData['data'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi đồng bộ công việc: ' . $e->getMessage()
            ]);
        }
    }
}
