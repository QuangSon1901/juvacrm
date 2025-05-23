<?php

namespace App\Http\Controllers\Dashboard\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractCommission;
use App\Models\ContractPayment;
use App\Models\ContractService;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\Product;
use App\Models\ServiceCategory;
use App\Models\SystemConfig;
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
    public function index(Request $request)
    {
        $filterStatus = $request->input('filter.status');
        $filterMyContract = $request->input('filter.my_contract', 0);
        $filterExpiring = $request->input('filter.expiring', 0);
        $filterCompletedTasks = $request->input('filter.completed_tasks', 0);
        
        return view("dashboard.contract.index", [
            'filterStatus' => $filterStatus,
            'filterMyContract' => $filterMyContract,
            'filterExpiring' => $filterExpiring,
            'filterCompletedTasks' => $filterCompletedTasks
        ]);
    }

    public function complete(Request $request) {
        try {
            DB::beginTransaction();
            
            $contract = Contract::with('user')->findOrFail($request['id']);
            $contract->update(['status' => 2]);
    
            // Cập nhật tất cả các task liên quan
            Task::where('contract_id', $request['id'])->update(['status_id' => 8]);
            
            Customer::where('id', $contract->provider_id)->where('type', 1)->update(['type' => 2]);
            // Tính và lưu hoa hồng cho nhân viên được phân công
            $this->calculateAndSaveCommission($contract);
    
            DB::commit();
    
            return response()->json([
                'status' => 200,
                'message' => 'Hợp đồng đã kết thúc.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage() ?? 'Đã xảy ra lỗi khi kết thúc hợp đồng.',
            ]);
        }
    }
    
    /**
     * Tính và lưu hoa hồng cho nhân viên được phân công hợp đồng
     * 
     * @param Contract $contract Hợp đồng đã hoàn tất
     * @return void
     */
    private function calculateAndSaveCommission(Contract $contract)
    {
        // Lấy nhân viên được phân công
        $assignedUser = $contract->user;
        if (!$assignedUser) {
            return;
        }
        
        // Lấy tổng giá trị hợp đồng
        $totalValue = $contract->total_value;
        
        // Lấy phần trăm hoa hồng từ cấu hình hệ thống
        $commissionPercentage = SystemConfig::where('config_key', 'contract_commission_percentage')
                                ->where('is_active', 1)
                                ->first();
        
        $percentage = $commissionPercentage ? (float)$commissionPercentage->config_value : 0;
        
        if ($percentage > 0 && $totalValue > 0) {
            // Tính số tiền hoa hồng
            $commissionAmount = ($totalValue * $percentage) / 100;
            
            // Lưu vào bảng hoa hồng
            ContractCommission::create([
                'contract_id' => $contract->id,
                'user_id' => $assignedUser->id,
                'commission_percentage' => $percentage,
                'commission_amount' => $commissionAmount,
                'contract_value' => $totalValue,
                'processed_at' => now(),
                'is_paid' => 0,
            ]);
            
            // Lưu log
            LogService::saveLog([
                'action' => 'COMMISSION_CREATED',
                'ip' => request()->getClientIp(),
                'details' => "Đã tạo hoa hồng {$percentage}% ({$commissionAmount}) cho nhân viên {$assignedUser->name} từ hợp đồng #{$contract->contract_number}",
                'fk_key' => 'tbl_contracts|id',
                'fk_value' => $contract->id,
            ]);
        }
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
        ->when($request->input('filter.my_contract'), function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->when($request->has('filter.status') && $request->input('filter.status') !== null, function ($query) use ($request) {
            $query->where('status', $request->input('filter.status'));
        })
        ->when($request->input('filter.expiring'), function ($query) {
            $query->where('status', 1)
                  ->whereNotNull('expiry_date')
                  ->where('expiry_date', '<=', now()->addDays(3))
                  ->where('expiry_date', '>=', now());
        })
        ->when($request->input('filter.completed_tasks'), function ($query) {
            $query->where('status', 1)
                  ->whereHas('tasks', function ($q) {
                      $q->where('is_active', 1)
                        ->where('status_id', 4)
                        ->whereNull('parent_id');
                  });
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
            'content' => isset($request['json']) && $request['json'] == 1 ? $result : view('dashboard.contract.ajax-index', ['data' => $result])->render(),
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
            'customer' => $request['customer'] ?? 0
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
                            'sample_image_id' => $service['sample_image_id'] ?? null,
                            'result_image_id' => $service['result_image_id'] ?? null,
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
                                    'sample_image_id' => $subService['sample_image_id'] ?? null,
                                    'result_image_id' => $subService['result_image_id'] ?? null,
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

            Customer::where('id', $contract->provider_id)->where('type', 0)->update(['type' => 1]);

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
                    'sample_image_id' => $service->sample_image_id,
                    'result_image_id' => $service->result_image_id,
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
                        'sample_image_id' => $subService->sample_image_id,
                        'result_image_id' => $subService->result_image_id,
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
                                'sample_image_id' => $subService->sample_image_id,
                                'result_image_id' => $subService->result_image_id,
                                'service_type' => $service->service_type
                            ];
                        })->toArray();

                    $serviceItem = [
                        'id' => $service->id ? $service->id : 'custom',
                        'service_id' => $service->service_id ? $service->service_id : 'custom',
                        'custom_name' => $service->type === 'custom' ? $service->name : null,
                        'price' => $service->price,
                        'note' => $service->note,
                        'sub_services' => $subServices,
                        'sample_image_id' => $service->sample_image_id,
                        'result_image_id' => $service->result_image_id,
                        'service_type' => $service->service_type
                    ];

                    $productServices[$service->product_id]['services'][] = $serviceItem;
                } else {
                    // Nếu không có product_id thì đây là mục tùy chỉnh
                    $customItems[] = [
                        'type' => 'custom',
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'note' => $service->note,
                        'service_type' => $service->service_type
                    ];
                }
            } elseif ($service->type === 'discount') {
                $customItems[] = [
                    'type' => 'custom',
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'note' => $service->note,
                    'service_type' => $service->service_type
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
            'status_text' => $contract->status == 1 ? 'Đang triển khai' : 'Đang chờ',
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
            $updateData = array_filter($request->only([
                'quantity', 'price', 'note', 
                'sample_image_id', 'result_image_id' // Thêm các trường hình ảnh
            ]), function($value) {return !is_null($value);});

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
                        'service_type' => $service->service_type,
                        'quantity' => $service->quantity,
                        'price' => $service->price,
                        'total' => $service->quantity * $service->price,
                        'note' => $service->note,
                        'has_sub_services' => $subServices->count() > 0,
                        'product_id' => $service->product_id,
                        'sample_image_id' => $service->sample_image_id,
                        'result_image_id' => $service->result_image_id,
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
                                'note' => $subService->note,
                                'sample_image_id' => $subService->sample_image_id,
                                'result_image_id' => $subService->result_image_id
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
 * Xuất hợp đồng dưới dạng Excel với ảnh nhúng trực tiếp
 * 
 * @param int $id ID của hợp đồng
 * @return \Illuminate\Http\Response
 */
public function exportExcel($id)
{
    try {
        // Kiểm tra nếu thư viện PhpSpreadsheet chưa được cài đặt thì báo lỗi
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return response()->json([
                'status' => 500,
                'message' => 'Thư viện PhpSpreadsheet chưa được cài đặt. Vui lòng chạy: composer require phpoffice/phpspreadsheet',
            ]);
        }

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
        $services = $contract->services->where('parent_id', null)->where('is_active', 1)->values();

        // Lấy thông tin sản phẩm
        $productIds = $services->pluck('product_id')->filter()->unique()->toArray();
        $products = Product::whereIn('id', $productIds)->get();
        $productNames = [];
        foreach ($products as $product) {
            $productNames[$product->id] = $product->name;
        }

        // Khởi tạo Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Thiết lập thông tin cơ bản
        $sheet->setTitle('Bảng báo giá');
        
        // Merge cells và đặt tiêu đề
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'BÁO GIÁ DỊCH VỤ - ' . $contract->contract_number);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Thông tin công ty và khách hàng
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Công ty: ' . $contract->company_name . ' - Khách hàng: ' . $contract->provider->name);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        
        // Thiết lập ngày báo giá
        $sheet->mergeCells('A3:H3');
        $sheet->setCellValue('A3', 'Ngày báo giá: ' . date('d/m/Y'));
        
        // Header của bảng
        $sheet->setCellValue('A5', 'STT');
        $sheet->setCellValue('B5', 'TÊN SẢN PHẨM/DỊCH VỤ');
        $sheet->setCellValue('C5', 'MÔ TẢ');
        $sheet->setCellValue('D5', 'ẢNH MẪU');
        $sheet->setCellValue('E5', 'ẢNH KẾT QUẢ');
        $sheet->setCellValue('F5', 'SỐ LƯỢNG');
        $sheet->setCellValue('G5', 'ĐƠN GIÁ');
        $sheet->setCellValue('H5', 'THÀNH TIỀN');
        
        // Định dạng header
        $headerStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '1A3D66',
                ],
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'bold' => true,
            ],
        ];
        $sheet->getStyle('A5:H5')->applyFromArray($headerStyle);
        
        // Thiết lập độ rộng cột
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        
        // Tạo thư mục tạm để lưu ảnh
        $tempDir = sys_get_temp_dir() . '/excel_images_' . time();
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        // Biến để theo dõi số thứ tự và dòng hiện tại
        $currentRow = 6;
        $index = 1;
        $totalValue = 0;
        $imageIndex = 1;
        
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
        
        // Duyệt qua từng sản phẩm
        foreach ($servicesByProduct as $productId => $productServices) {
            // Thêm tiêu đề cho sản phẩm nếu có
            if ($productId !== 'no_product') {
                $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'Sản phẩm: ' . ($productNames[$productId] ?? 'Sản phẩm #'.$productId));
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A' . $currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle('A' . $currentRow)->getFill()->getStartColor()->setRGB('E8F5E9');
                $currentRow++;
            } else if (count($servicesByProduct) > 1) {
                $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'Dịch vụ bổ sung');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A' . $currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle('A' . $currentRow)->getFill()->getStartColor()->setRGB('E8F5E9');
                $currentRow++;
            }
            
            // Duyệt qua từng dịch vụ của sản phẩm
            foreach ($productServices as $service) {
                // Bỏ qua nếu dịch vụ không active
                if ($service->is_active != 1) {
                    continue;
                }
                
                // Kiểm tra loại dịch vụ
                $serviceClass = '';
                if ($service->type === 'discount') {
                    $serviceClass = 'discount';
                } else if ($service->type === 'custom') {
                    $serviceClass = 'custom';
                }
                
                // Lấy các dịch vụ con
                $subServices = $contract->services
                    ->where('parent_id', $service->id)
                    ->where('is_active', 1);
                
                // Có dịch vụ con hay không
                $hasSubServices = ($subServices->count() > 0);
                
                // Thêm dịch vụ chính
                $sheet->setCellValue('A' . $currentRow, $index);
                $sheet->setCellValue('B' . $currentRow, $service->name);
                $sheet->setCellValue('C' . $currentRow, $service->note ?? '');
                
                $rowHeight = 90; // Chiều cao mặc định khi có ảnh
                // Nếu có dịch vụ con, không hiển thị số lượng, đơn giá, thành tiền
                if (!$hasSubServices) {
                    // Thiết lập chiều cao dòng phù hợp cho ảnh
                    $sheet->getRowDimension($currentRow)->setRowHeight($rowHeight);
                    
                   // Thêm ảnh mẫu nếu có
                    if ($service->sample_image_id) {
                            foreach(explode('|', $service->sample_image_id) as $item) {
                                $sampleImageUrl = "https://res.cloudinary.com/" . env('CLOUDINARY_CLOUD_NAME') . "/image/upload/q_auto,f_auto/uploads/" . $item;
                                $this->addImageToCell($sheet, $sampleImageUrl, 'D', $currentRow, $imageIndex, $tempDir);
                                $imageIndex++;
                            }
                        } else {
                            $sheet->setCellValue('D' . $currentRow, 'Không có ảnh');
                        }
                        
                        // Thêm ảnh kết quả nếu có
                        if ($service->result_image_id) {
                            foreach(explode('|', $service->result_image_id) as $item) {
                                $resultImageUrl = "https://res.cloudinary.com/" . env('CLOUDINARY_CLOUD_NAME') . "/image/upload/q_auto,f_auto/uploads/" . $item;
                                $this->addImageToCell($sheet, $resultImageUrl, 'E', $currentRow, $imageIndex, $tempDir);
                                $imageIndex++;
                            }
                        } else {
                            $sheet->setCellValue('E' . $currentRow, 'Không có ảnh');
                        }

                    $sheet->setCellValue('F' . $currentRow, $service->quantity);
                    $sheet->setCellValue('G' . $currentRow, number_format($service->price, 0, ',', '.'));
                    $sheet->setCellValue('H' . $currentRow, number_format($service->price * $service->quantity, 0, ',', '.'));
                    $totalValue += ($service->price * $service->quantity);
                }
                
                // Định dạng theo loại dịch vụ
                if ($serviceClass === 'discount') {
                    $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFill()->getStartColor()->setRGB('FFEEEE');
                    $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFont()->getColor()->setRGB('D32F2F');
                } else if ($serviceClass === 'custom') {
                    $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFill()->getStartColor()->setRGB('E8F5E9');
                }
                
                $index++;
                $currentRow++;
                
                // Thêm các dịch vụ con nếu có
                if ($hasSubServices) {
                    foreach ($subServices as $subService) {
                        $sheet->setCellValue('A' . $currentRow, '');
                        $sheet->setCellValue('B' . $currentRow, '→ ' . $subService->name);
                        $sheet->setCellValue('C' . $currentRow, $subService->note ?? '');
                        
                        // Thiết lập chiều cao dòng phù hợp cho ảnh
                        $sheet->getRowDimension($currentRow)->setRowHeight($rowHeight);
                        
                        // Thêm ảnh mẫu nếu có
                        if ($subService->sample_image_id) {
                            foreach(explode('|', $subService->sample_image_id) as $item) {
                                $sampleImageUrl = "https://res.cloudinary.com/" . env('CLOUDINARY_CLOUD_NAME') . "/image/upload/q_auto,f_auto/uploads/" . $item;
                                $this->addImageToCell($sheet, $sampleImageUrl, 'D', $currentRow, $imageIndex, $tempDir);
                                $imageIndex++;
                            }
                        } else {
                            $sheet->setCellValue('D' . $currentRow, 'Không có ảnh');
                        }
                        
                        // Thêm ảnh kết quả nếu có
                        if ($subService->result_image_id) {
                            foreach(explode('|', $subService->result_image_id) as $item) {
                                $resultImageUrl = "https://res.cloudinary.com/" . env('CLOUDINARY_CLOUD_NAME') . "/image/upload/q_auto,f_auto/uploads/" . $item;
                                $this->addImageToCell($sheet, $resultImageUrl, 'E', $currentRow, $imageIndex, $tempDir);
                                $imageIndex++;
                            }
                        } else {
                            $sheet->setCellValue('E' . $currentRow, 'Không có ảnh');
                        }
                        
                        $sheet->setCellValue('F' . $currentRow, $subService->quantity);
                        $sheet->setCellValue('G' . $currentRow, number_format($service->price, 0, ',', '.'));
                        $sheet->setCellValue('H' . $currentRow, number_format($service->price * $subService->quantity, 0, ',', '.'));
                        
                        // Định dạng dịch vụ con
                        $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFill()->getStartColor()->setRGB('F5F5F5');
                        $sheet->getStyle('B' . $currentRow)->getAlignment()->setIndent(3);
                        
                        $currentRow++;
                        $totalValue += ($service->price * $subService->quantity);
                    }
                }
            }
        }
        
        // Thiết lập border cho nội dung
        $contentStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A5:H' . ($currentRow - 1))->applyFromArray($contentStyle);
        
        // Định dạng căn phải cho cột số lượng và giá
        $sheet->getStyle('F6:H' . ($currentRow - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        
        // Thêm dòng tổng cộng
        $sheet->mergeCells('A' . $currentRow . ':F' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'TỔNG CỘNG');
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('G' . $currentRow, '');
        $sheet->setCellValue('H' . $currentRow, number_format($contract->total_value, 0, ',', '.'));
        
        // Định dạng dòng tổng cộng
        $totalStyle = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '1A3D66',
                ],
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'bold' => true,
            ],
        ];
        $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->applyFromArray($totalStyle);
        $sheet->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        
        // Thêm thông tin thanh toán
        $currentRow += 2;
        $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'ĐIỀU KHOẢN THANH TOÁN');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        
        $currentRow++;
        $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
        
        // Lấy thông tin thanh toán từ hợp đồng
        $paymentText = '';
        if ($contract->payments->where('is_active', 1)->count() > 0) {
            foreach ($contract->payments->where('is_active', 1) as $key => $payment) {
                $paymentText .= ($key + 1) . ". " . $payment->name . ": " . 
                    number_format($payment->price, 0, ',', '.') . "đ" . 
                    ($payment->due_date ? " (hạn thanh toán: " . formatDateTime($payment->due_date, 'd/m/Y') . ")" : "") . "\n";
            }
        } else {
            // Nếu không có điều khoản thanh toán cụ thể, sử dụng mặc định
            $paymentText = "Thanh toán 100% giá trị khi ký hợp đồng.";
        }
        
        $sheet->setCellValue('A' . $currentRow, $paymentText);
        $sheet->getStyle('A' . $currentRow)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($currentRow)->setRowHeight(60);
        
        // Thêm ghi chú
        if ($contract->note) {
            $currentRow += 2;
            $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'GHI CHÚ');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            
            $currentRow++;
            $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, $contract->note);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($currentRow)->setRowHeight(40);
        }
        
        // Thêm điều khoản và điều kiện
        if ($contract->terms_and_conditions) {
            $currentRow += 2;
            $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'ĐIỀU KHOẢN VÀ ĐIỀU KIỆN');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            
            $currentRow++;
            $sheet->mergeCells('A' . $currentRow . ':H' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, $contract->terms_and_conditions);
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($currentRow)->setRowHeight(100);
        }
        
        // Tạo file Excel và trả về phản hồi
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Lưu log
        LogService::saveLog([
            'action' => 'EXPORT_EXCEL',
            'ip' => request()->getClientIp(),
            'details' => "Đã xuất Excel báo giá #{$contract->contract_number}",
            'fk_key' => 'tbl_contracts|id',
            'fk_value' => $contract->id,
        ]);
        
        // Tạo phản hồi cho tải xuống
        $filename = "BaoGia_{$contract->contract_number}.xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        
        // Xóa thư mục tạm và các file ảnh sau khi hoàn thành
        $this->removeDirectory($tempDir);
        
        exit;
        
    } catch (\Exception $e) {
        // Đảm bảo xóa thư mục tạm nếu có lỗi
        if (isset($tempDir) && is_dir($tempDir)) {
            $this->removeDirectory($tempDir);
        }
        
        return response()->json([
            'status' => 500,
            'message' => 'Đã xảy ra lỗi khi xuất Excel báo giá: ' . $e->getMessage(),
        ]);
    }
}

/**
 * Thêm ảnh vào ô trong bảng tính Excel
 * 
 * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
 * @param string $imageUrl URL của ảnh
 * @param string $column Cột để chèn ảnh
 * @param int $row Dòng để chèn ảnh
 * @param int $imageIndex Chỉ mục của ảnh (để tạo tên duy nhất)
 * @param string $tempDir Thư mục tạm để lưu ảnh
 * @return bool
 */
private function addImageToCell($sheet, $imageUrl, $column, $row, $imageIndex, $tempDir)
{
    try {
        // Tạo tên file tạm thời
        $tempFile = $tempDir . '/image_' . $imageIndex . '.jpg';
        
        // Tải hình ảnh từ URL
        $imageContent = @file_get_contents($imageUrl);
        if ($imageContent === false) {
            // Nếu không tải được ảnh, hiển thị thông báo
            $sheet->setCellValue($column . $row, 'Không thể tải ảnh');
            return false;
        }
        
        // Lưu ảnh vào file tạm
        file_put_contents($tempFile, $imageContent);
        
        // Tạo đối tượng Drawing
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Image ' . $imageIndex);
        $drawing->setDescription('Image ' . $imageIndex);
        $drawing->setPath($tempFile);
        $drawing->setCoordinates($column . $row);
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWidth(80); // Thiết lập chiều rộng cố định
        $drawing->setHeight(80); // Thiết lập chiều cao cố định
        $drawing->setWorksheet($sheet);
        
        return true;
    } catch (\Exception $e) {
        // Nếu có lỗi, hiển thị link thay vì ảnh
        $sheet->setCellValue($column . $row, $imageUrl);
        return false;
    }
}

/**
 * Xóa thư mục và tất cả nội dung bên trong
 * 
 * @param string $dir Đường dẫn thư mục cần xóa
 * @return void
 */
private function removeDirectory($dir)
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                if (is_dir($dir . "/" . $file)) {
                    $this->removeDirectory($dir . "/" . $file);
                } else {
                    @unlink($dir . "/" . $file);
                }
            }
        }
        @rmdir($dir);
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
        $services = $contract->services()->where('is_active', 1)->get();

        $totalValue = 0;
        $totalValue += $contract->services()->where('is_active', 1)->where('service_type', 'combo')->sum('price');
        
        // Tính giá trị từ góc máy (dịch vụ con)
        $subServices = $services->where('type', 'sub_service');
        foreach ($subServices as $subService) {
            if ($subService->parent->service_type == 'individual') {
                $totalValue += $subService->price;
            }
        }

        // Cộng thêm giá trị từ các mục customized
        $customItems = $services->whereIn('type', ['custom', 'discount'])->whereNull('parent_id');
        foreach ($customItems as $item) {
            if ($subService->parent->service_type == 'individual') {
                $totalValue += $item->price;
            }
        }

        // Cập nhật tổng giá trị hợp đồng
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

            // Xử lý từng item trong request
            foreach ($contractItemsData as $item) {
                $itemType = $item['type'];

                if ($itemType === 'product') {
                    // Xử lý các sản phẩm
                    $productId = $item['product_id'];
    
                    // Duyệt qua các dịch vụ của sản phẩm
                    if (!empty($item['services'])) {
                        foreach ($item['services'] as $service) {
                            $serviceId = null;
                            $pricingModel = $service['service_type'] ?? 'individual'; // Model tính giá (individual/combo)
                            $serviceName = null;
                            $contractServiceId = isset($service['id']) ? $service['id'] : null;
                            $serviceType = 'service'; // Type mặc định là service
    
                            // Xác định loại dịch vụ (type) và lấy thông tin
                            if ($service['service_id'] === 'custom') {
                                $serviceType = 'custom'; // Type là custom
                                $serviceName = $service['custom_name'];
                            } else {
                                $serviceId = $service['service_id'];
                                $serviceObj = Service::find($serviceId);
                                $serviceName = $serviceObj ? $serviceObj->name : "Dịch vụ #" . $serviceId;
                            }
    
                            $servicePrice = (float) $service['price'];
    
                            // Tìm dịch vụ hiện tại nếu có ID
                            $currentService = null;
                            if ($contractServiceId) {
                                $currentService = ContractService::find($contractServiceId);
                            }
    
                            // Nếu dịch vụ đã tồn tại thì cập nhật, nếu không thì tạo mới
                            if ($currentService) {
                                // Cập nhật thông tin dịch vụ
                                $currentService->update([
                                    'service_id' => $serviceId,
                                    'name' => $serviceName,
                                    'type' => $serviceType, // Type: service hoặc custom
                                    'service_type' => $pricingModel, // Model tính giá: individual hoặc combo
                                    'price' => $servicePrice,
                                    'note' => $service['note'] ?? null,
                                ]);
    
                                // Lưu ID vào danh sách đã xử lý
                                $processedServiceIds[] = $currentService->id;
                            } else {
                                // Tạo mới dịch vụ
                                $newService = ContractService::create([
                                    'contract_id' => $contractId,
                                    'service_id' => $serviceId,
                                    'product_id' => $productId,
                                    'name' => $serviceName,
                                    'type' => $serviceType, // Type: service hoặc custom
                                    'service_type' => $pricingModel, // Model tính giá: individual hoặc combo
                                    'quantity' => 1,
                                    'price' => $servicePrice,
                                    'note' => $service['note'] ?? null,
                                    'is_active' => 1,
                                ]);
    
                                // Lưu ID vào danh sách đã xử lý
                                $processedServiceIds[] = $newService->id;
                                $currentService = $newService;
                            }
    
                            // Xử lý các dịch vụ con (góc máy)
                            if (!empty($service['sub_services'])) {
                                $processedSubServiceIds = [];
    
                                foreach ($service['sub_services'] as $subService) {
                                    $subServiceName = $subService['name'];
                                    $subServiceQuantity = (float) $subService['quantity'];
                                    $subServiceTotal = (float) preg_replace('/[^\d]/', '', $subService['total']);
                                    $subServiceNote = $subService['content'] ?? null;
                                    $contractSubServiceId = isset($subService['id']) ? $subService['id'] : null;
                                    
                                    // Xử lý danh sách ảnh
                                    $sampleImageId = $subService['sample_image_id'] ?? '';
                                    $resultImageId = $subService['result_image_id'] ?? '';
    
                                    // Tìm dịch vụ con hiện tại nếu có ID
                                    $currentSubService = null;
                                    if ($contractSubServiceId) {
                                        $currentSubService = ContractService::find($contractSubServiceId);
                                    }
    
                                    if ($currentSubService) {
                                        // Cập nhật thông tin dịch vụ con
                                        $currentSubService->update([
                                            'name' => $subServiceName,
                                            'quantity' => $subServiceQuantity,
                                            'price' => $subServiceTotal,
                                            'note' => $subServiceNote,
                                            'sample_image_id' => $sampleImageId,
                                            'result_image_id' => $resultImageId,
                                            // Không thay đổi type và service_type
                                        ]);
    
                                        // Lưu ID vào danh sách đã xử lý
                                        $processedServiceIds[] = $currentSubService->id;
                                        $processedSubServiceIds[] = $currentSubService->id;
                                    } else {
                                        // Tạo mới dịch vụ con
                                        $newSubService = ContractService::create([
                                            'contract_id' => $contractId,
                                            'product_id' => $productId,
                                            'name' => $subServiceName,
                                            'type' => 'sub_service', // Type luôn là sub_service
                                            'service_type' => 'individual', // Góc máy luôn tính theo individual
                                            'quantity' => $subServiceQuantity,
                                            'price' => $subServiceTotal,
                                            'note' => $subServiceNote,
                                            'parent_id' => $currentService->id,
                                            'sample_image_id' => $sampleImageId,
                                            'result_image_id' => $resultImageId,
                                            'is_active' => 1,
                                        ]);
    
                                        // Lưu ID vào danh sách đã xử lý
                                        $processedServiceIds[] = $newSubService->id;
                                        $processedSubServiceIds[] = $newSubService->id;
                                    }
                                }
    
                                // Vô hiệu hóa các dịch vụ con không có trong request mới
                                $existingSubServices = ContractService::where('parent_id', $currentService->id)->get();
                                foreach ($existingSubServices as $existingSubService) {
                                    if (!in_array($existingSubService->id, $processedSubServiceIds)) {
                                        $existingSubService->update(['is_active' => 0]);
                                    }
                                }
                            }
                        }
                    }
                } else if ($itemType === 'custom') {
                    // Xử lý mục tùy chỉnh (có thể là custom hoặc discount)
                    $itemName = $item['name'];
                    $itemPrice = (float) $item['price'];
                    $itemNote = $item['note'] ?? null;
                    $itemType = ($itemPrice < 0) ? 'discount' : 'custom'; // Type dựa vào giá trị
                    $customItemId = isset($item['id']) ? $item['id'] : null;

                    // Tìm mục tùy chỉnh hiện tại nếu có ID trong request
                    $currentCustomItem = null;
                    if ($customItemId) {
                        $currentCustomItem = $currentServices->where('id', $customItemId)->first();
                    }

                    // Nếu mục tùy chỉnh đã tồn tại thì cập nhật, nếu không thì tạo mới
                    if ($currentCustomItem) {
                        // Cập nhật thông tin mục tùy chỉnh
                        $currentCustomItem->update([
                            'name' => $itemName,
                            'type' => $itemType, // Type: custom hoặc discount
                            'price' => $itemPrice,
                            'note' => $itemNote,
                            // Không thay đổi service_type
                        ]);

                        // Lưu ID vào danh sách đã xử lý
                        $processedServiceIds[] = $currentCustomItem->id;
                    } else {
                        // Tạo mới mục tùy chỉnh
                        $newCustomItem = ContractService::create([
                            'contract_id' => $contractId,
                            'name' => $itemName,
                            'type' => $itemType, // Type: custom hoặc discount
                            'service_type' => 'individual', // Mặc định là individual
                            'quantity' => 1,
                            'price' => $itemPrice,
                            'note' => $itemNote,
                            'is_active' => 1,
                        ]);

                        // Lưu ID vào danh sách đã xử lý
                        $processedServiceIds[] = $newCustomItem->id;
                    }
                }
            }

            // Vô hiệu hóa các dịch vụ không có trong request mới
            foreach ($currentServices as $existingService) {
                if (!in_array($existingService->id, $processedServiceIds)) {
                    // Vô hiệu hóa dịch vụ
                    $existingService->update(['is_active' => 0]);
                }
            }

            // Cập nhật tổng giá trị hợp đồng bằng cách tính lại toàn bộ
            $this->updateContractTotalValue($contract);

            // Đồng bộ lại toàn bộ task sau các thay đổi
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
            // Log lỗi
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
