<?php

namespace App\Http\Controllers\Dashboard\Account\Salary;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ContractCommission;
use App\Models\SalaryRecord;
use App\Models\SystemConfig;
use App\Models\TaskMissionReport;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalaryController extends Controller
{
    /**
     * Hiển thị trang quản lý bảng lương
     */
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Tính toán tháng trước
        $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        
        // Thống kê tổng quan
        $stats = [
            'totalPaid' => SalaryRecord::where('status', 'paid')
                            ->where('period_month', $previousMonth)
                            ->where('period_year', $previousYear)
                            ->sum('final_amount'),
            'pendingCount' => SalaryRecord::where('status', 'pending')
                            ->where('period_month', $previousMonth)
                            ->where('period_year', $previousYear)
                            ->count(),
            'processedCount' => SalaryRecord::where('status', 'processed')
                            ->where('period_month', $previousMonth)
                            ->where('period_year', $previousYear)
                            ->count(),
            'employeeCount' => User::where('is_active', 1)->count(),
            'totalBaseSalary' => SalaryRecord::where('period_month', $previousMonth)
                            ->where('period_year', $previousYear)
                            ->sum('base_salary'),
            'totalCommission' => SalaryRecord::where('period_month', $previousMonth)
                            ->where('period_year', $previousYear)
                            ->sum('commission_amount'),
            'totalMission' => SalaryRecord::where('period_month', $previousMonth)
                            ->where('period_year', $previousYear)
                            ->sum('task_mission_amount'),
        ];
        
        return view('dashboard.account.salary.payroll', compact('previousMonth', 'previousYear', 'stats'));
    }
    
    /**
     * Lấy dữ liệu bảng lương cho AJAX
     */
    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);
        
        $query = SalaryRecord::with(['user', 'creator', 'transaction'])
            ->when($request->input('filter.user_id'), function($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->has('filter.period_month') && $request->input('filter.period_month') !== null, function($query) use ($request) {
                $query->where('period_month', $request->input('filter.period_month'));
            })
            ->when($request->has('filter.period_year') && $request->input('filter.period_year') !== null, function($query) use ($request) {
                $query->where('period_year', $request->input('filter.period_year'));
            })
            ->when($request->has('filter.status') && $request->input('filter.status') !== null, function($query) use ($request) {
                $query->where('status', $request->input('filter.status'));
            })
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc');
        
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];
        
        $result = $paginationResult['data']->map(function($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'user' => [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                ],
                'period' => $item->period_month . '/' . $item->period_year,
                'base_salary' => number_format($item->base_salary, 0, ',', '.'),
                'commission_amount' => number_format($item->commission_amount, 0, ',', '.'),
                'task_mission_amount' => number_format($item->task_mission_amount, 0, ',', '.'),
                'deductions' => number_format($item->deductions, 0, ',', '.'),
                'final_amount' => number_format($item->final_amount, 0, ',', '.'),
                'status' => $item->status,
                'status_text' => $this->getStatusText($item->status),
                'creator' => [
                    'id' => $item->creator->id,
                    'name' => $item->creator->name,
                ],
                'created_at' => formatDateTime($item->created_at, 'd/m/Y H:i'),
                'transaction' => $item->transaction ? [
                    'id' => $item->transaction->id,
                    'paid_date' => formatDateTime($item->transaction->paid_date, 'd/m/Y'),
                ] : null,
            ];
        });
        
        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.salary.ajax-payroll', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }
    
    /**
     * Hiển thị trang lương cá nhân
     */
    public function mySalary()
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $salaryRecords = SalaryRecord::where('user_id', $userId)
                                   ->orderBy('period_year', 'desc')
                                   ->orderBy('period_month', 'desc')
                                   ->paginate(12);
        
        // Lấy lương cơ bản từ dữ liệu người dùng
        $user = User::find($userId);
        $baseSalary = $user->salary ?? 0;
        $taxRate = SystemConfig::getValue('tax_rate', 0);
        $insuranceRate = SystemConfig::getValue('insurance_rate', 0);
        
        return view('dashboard.profile.my-salary', compact('salaryRecords', 'baseSalary', 'taxRate', 'insuranceRate'));
    }
    
    /**
     * Lấy chi tiết bảng lương (dành cho quản trị viên)
     */
    public function getSalaryDetail(Request $request, $id)
    {
        try {
            $salaryRecord = SalaryRecord::with(['user', 'transaction'])->findOrFail($id);
            
            $html = view('dashboard.account.salary.detail', compact('salaryRecord'))->render();
            
            return response()->json([
                'status' => 200,
                'content' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi tải thông tin: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Lấy chi tiết bảng lương cá nhân
     */
    public function getMySalaryDetail(Request $request, $id)
    {
        try {
            $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $salaryRecord = SalaryRecord::with(['transaction'])
                ->where('user_id', $userId)
                ->findOrFail($id);
            
            $html = view('dashboard.profile.my-salary-detail', compact('salaryRecord'))->render();
            
            return response()->json([
                'status' => 200,
                'content' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi tải thông tin: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tính lương cho tháng trước
     */
    public function calculateSalary(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        $month = $request->period_month;
        $year = $request->period_year;

        // Chỉ cho phép tính lương tháng trước
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Tính toán tháng trước
        $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        
        if ($month != $previousMonth || $year != $previousYear) {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ được phép tính lương cho tháng trước (' . $previousMonth . '/' . $previousYear . ')',
            ]);
        }

        // Lấy danh sách tất cả nhân viên đang hoạt động
        $activeUsers = User::where('is_active', 1)->get();
        
        // Kết quả
        $results = [];
        $totalSalary = 0;
        $successCount = 0;
        $errorCount = 0;
        
        // Lấy cấu hình từ hệ thống
        $taxRate = SystemConfig::getValue('tax_rate', 0);
        $insuranceRate = SystemConfig::getValue('insurance_rate', 0);
        $hourlyRate = SystemConfig::getValue('hourly_rate', 0);
        
        // Tính lương cho từng nhân viên
        foreach ($activeUsers as $user) {
            try {
                // Kiểm tra nếu đã có bảng lương cho tháng này
                $existingSalary = SalaryRecord::where('user_id', $user->id)
                                            ->where('period_month', $month)
                                            ->where('period_year', $year)
                                            ->first();

                if ($existingSalary) {
                    $results[] = [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'status' => 'error',
                        'message' => 'Đã tồn tại bảng lương cho tháng ' . $month . '/' . $year
                    ];
                    $errorCount++;
                    continue;
                }

                // Thời gian
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                // 1. Lương cơ bản (từ bản ghi người dùng)
                $baseSalary = $user->salary ?? 0;
                
                // 2. Tính lương theo giờ từ chấm công
                $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
                                                    ->whereBetween('work_date', [$startDate, $endDate])
                                                    ->get();
                
                $totalHours = $attendanceRecords->sum('total_hours');
                $hourlyPay = $totalHours * $hourlyRate;

                // 3. Tính hoa hồng
                $commissions = ContractCommission::where('user_id', $user->id)
                                              ->where('is_paid', 0)
                                              ->whereNull('transaction_id')
                                              ->get();
                
                $commissionAmount = $commissions->sum('commission_amount');
                $commissionIds = $commissions->pluck('id')->toArray();

                // 4. Tính tiền công nhiệm vụ
                $taskMissionReports = TaskMissionReport::whereHas('assignment', function($query) use ($user) {
                                            $query->where('user_id', $user->id);
                                        })
                                        ->whereMonth('date_completed', $month)
                                        ->whereYear('date_completed', $year)
                                        ->get();

                $taskMissionAmount = $taskMissionReports->sum('price');

                // 5. Tính các khoản khấu trừ
                $totalEarnings = $baseSalary + $hourlyPay + $commissionAmount + $taskMissionAmount;
                $taxAmount = $totalEarnings * ($taxRate / 100);
                $insuranceAmount = $baseSalary * ($insuranceRate / 100);
                $totalDeductions = $taxAmount + $insuranceAmount;

                // 6. Tính lương cuối cùng
                $finalAmount = $totalEarnings - $totalDeductions;
                
                // Tạo bảng lương
                $salaryRecord = SalaryRecord::create([
                    'user_id' => $user->id,
                    'period_month' => $month,
                    'period_year' => $year,
                    'base_salary' => $baseSalary + $hourlyPay,
                    'commission_amount' => $commissionAmount,
                    'task_mission_amount' => $taskMissionAmount,
                    'tax_amount' => $taxAmount,
                    'insurance_amount' => $insuranceAmount,
                    'deductions' => $totalDeductions,
                    'final_amount' => $finalAmount,
                    'status' => 'pending',
                    'created_by' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                ]);
                
                // Đánh dấu hoa hồng đã tính
                if (count($commissionIds) > 0) {
                    ContractCommission::whereIn('id', $commissionIds)
                                     ->update(['processed_at' => Carbon::now()]);
                }
                
                $results[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'salary' => number_format($finalAmount, 0, ',', '.'),
                    'status' => 'success',
                    'salary_record_id' => $salaryRecord->id
                ];
                
                $totalSalary += $finalAmount;
                $successCount++;
                
                LogService::saveLog([
                    'action' => 'CREATE_SALARY_RECORD',
                    'ip' => $request->getClientIp(),
                    'details' => "Tạo bảng lương tháng " . $month . "/" . $year . " cho nhân viên #" . $user->id,
                    'fk_key' => 'tbl_salary_records|id',
                    'fk_value' => $salaryRecord->id,
                ]);
                
            } catch (\Exception $e) {
                $results[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'status' => 'error',
                    'message' => 'Lỗi: ' . $e->getMessage()
                ];
                $errorCount++;
            }
        }
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã tính lương cho ' . $successCount . ' nhân viên, ' . $errorCount . ' lỗi',
            'data' => [
                'results' => $results,
                'total_salary' => number_format($totalSalary, 0, ',', '.'),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'period' => $month . '/' . $year
            ]
        ]);
    }
    
    /**
     * Cập nhật trạng thái bảng lương (duyệt hoặc chi trả)
     */
    public function processSalary(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_salary_records,id',
            'status' => 'required|in:processed,paid',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        DB::beginTransaction();
        try {
            $salaryRecord = SalaryRecord::findOrFail($request->id);
            
            // Kiểm tra trạng thái hiện tại
            if ($salaryRecord->status === 'paid' && $request->status === 'paid') {
                return response()->json([
                    'status' => 422,
                    'message' => 'Bảng lương này đã được thanh toán',
                ]);
            }
            
            // Nếu đánh dấu là đã chi
            if ($request->status === 'paid' && $salaryRecord->status !== 'paid') {
                // Tạo phiếu chi
                $category = TransactionCategory::firstOrCreate(
                    ['type' => 1, 'name' => 'Chi lương nhân viên'],
                    ['is_active' => 1]
                );
                
                $transaction = Transaction::create([
                    'type' => 1, // Chi
                    'category_id' => $category->id,
                    'target_employee_id' => $salaryRecord->user_id,
                    'amount' => $salaryRecord->final_amount,
                    'paid_date' => Carbon::now(),
                    'status' => 1, // Hoàn tất
                    'reason' => "Chi lương tháng " . $salaryRecord->period_month . "/" . $salaryRecord->period_year . " cho " . $salaryRecord->user->name,
                    'note' => "Tự động tạo từ bảng lương #" . $salaryRecord->id,
                ]);
                
                // Cập nhật bảng lương
                $salaryRecord->update([
                    'status' => 'paid',
                    'transaction_id' => $transaction->id,
                ]);
                
                // Đánh dấu hoa hồng đã chi
                if ($salaryRecord->commission_amount > 0) {
                    ContractCommission::where('user_id', $salaryRecord->user_id)
                                     ->whereNotNull('processed_at')
                                     ->whereNull('transaction_id')
                                     ->where('is_paid', 0)
                                     ->update([
                                         'is_paid' => 1,
                                         'transaction_id' => $transaction->id,
                                     ]);
                }
            } else {
                // Chỉ cập nhật trạng thái
                $salaryRecord->update(['status' => $request->status]);
            }
            
            LogService::saveLog([
                'action' => 'PROCESS_SALARY',
                'ip' => $request->getClientIp(),
                'details' => "Cập nhật trạng thái bảng lương #" . $salaryRecord->id . " thành " . $request->status,
                'fk_key' => 'tbl_salary_records|id',
                'fk_value' => $salaryRecord->id,
            ]);
            
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Đã cập nhật trạng thái bảng lương thành công',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Xử lý hàng loạt bảng lương
     */
    public function bulkProcessSalary(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'salary_ids' => 'required|array',
            'salary_ids.*' => 'exists:tbl_salary_records,id',
            'action' => 'required|in:process,pay',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $successCount = 0;
            $errorCount = 0;
            $totalAmount = 0;
            $action = $request->action;
            $newStatus = $action === 'process' ? 'processed' : 'paid';
            
            foreach ($request->salary_ids as $salaryId) {
                $salaryRecord = SalaryRecord::findOrFail($salaryId);
                
                // Kiểm tra trạng thái hiện tại
                if ($action === 'process' && $salaryRecord->status !== 'pending') {
                    $errorCount++;
                    continue;
                }
                
                if ($action === 'pay' && $salaryRecord->status !== 'processed') {
                    $errorCount++;
                    continue;
                }
                
                // Nếu đánh dấu là đã chi
                if ($action === 'pay') {
                    // Tạo phiếu chi
                    $category = TransactionCategory::firstOrCreate(
                        ['type' => 1, 'name' => 'Chi lương nhân viên'],
                        ['is_active' => 1]
                    );
                    
                    $transaction = Transaction::create([
                        'type' => 1, // Chi
                        'category_id' => $category->id,
                        'target_employee_id' => $salaryRecord->user_id,
                        'amount' => $salaryRecord->final_amount,
                        'paid_date' => Carbon::now(),
                        'status' => 1, // Hoàn tất
                        'reason' => "Chi lương tháng " . $salaryRecord->period_month . "/" . $salaryRecord->period_year . " cho " . $salaryRecord->user->name,
                        'note' => "Tự động tạo từ bảng lương #" . $salaryRecord->id,
                    ]);
                    
                    // Cập nhật bảng lương
                    $salaryRecord->update([
                        'status' => 'paid',
                        'transaction_id' => $transaction->id,
                    ]);
                    
                    // Đánh dấu hoa hồng đã chi
                    if ($salaryRecord->commission_amount > 0) {
                        ContractCommission::where('user_id', $salaryRecord->user_id)
                                         ->whereNotNull('processed_at')
                                         ->whereNull('transaction_id')
                                         ->where('is_paid', 0)
                                         ->update([
                                             'is_paid' => 1,
                                             'transaction_id' => $transaction->id,
                                         ]);
                    }
                } else {
                    // Chỉ cập nhật trạng thái
                    $salaryRecord->update(['status' => $newStatus]);
                }
                
                $totalAmount += $salaryRecord->final_amount;
                $successCount++;
                
                LogService::saveLog([
                    'action' => 'BULK_PROCESS_SALARY',
                    'ip' => $request->getClientIp(),
                    'details' => "Cập nhật trạng thái bảng lương #" . $salaryRecord->id . " thành " . $newStatus,
                    'fk_key' => 'tbl_salary_records|id',
                    'fk_value' => $salaryRecord->id,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 200,
                'message' => ($action === 'process' ? 'Đã duyệt ' : 'Đã chi trả ') . $successCount . ' bảng lương, ' . $errorCount . ' lỗi',
                'data' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'total_amount' => number_format($totalAmount, 0, ',', '.'),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Lấy danh sách ID bảng lương đang chờ xử lý
     */
    public function getPendingSalaryIds()
    {
        $pendingIds = SalaryRecord::where('status', 'pending')
                                ->pluck('id')
                                ->toArray();
        
        return response()->json([
            'status' => 200,
            'data' => [
                'ids' => $pendingIds,
                'count' => count($pendingIds)
            ]
        ]);
    }

    /**
     * Lấy danh sách ID bảng lương đã duyệt
     */
    public function getProcessedSalaryIds()
    {
        $processedIds = SalaryRecord::where('status', 'processed')
                                  ->pluck('id')
                                  ->toArray();
        
        return response()->json([
            'status' => 200,
            'data' => [
                'ids' => $processedIds,
                'count' => count($processedIds)
            ]
        ]);
    }
    
    /**
     * Duyệt tất cả bảng lương đang chờ xử lý
     */
    public function bulkProcessAllPending(Request $request)
    {
        $pendingIds = SalaryRecord::where('status', 'pending')
                                ->pluck('id')
                                ->toArray();
        
        if (empty($pendingIds)) {
            return response()->json([
                'status' => 422,
                'message' => 'Không có bảng lương nào đang chờ duyệt'
            ]);
        }
        
        // Sử dụng lại phương thức bulkProcessSalary
        $request->merge([
            'salary_ids' => $pendingIds,
            'action' => 'process'
        ]);
        
        return $this->bulkProcessSalary($request);
    }

    /**
     * Chi trả tất cả bảng lương đã duyệt
     */
    public function bulkPayAllProcessed(Request $request)
    {
        $processedIds = SalaryRecord::where('status', 'processed')
                                  ->pluck('id')
                                  ->toArray();
        
        if (empty($processedIds)) {
            return response()->json([
                'status' => 422,
                'message' => 'Không có bảng lương nào đã duyệt'
            ]);
        }
        
        // Sử dụng lại phương thức bulkProcessSalary
        $request->merge([
            'salary_ids' => $processedIds,
            'action' => 'pay'
        ]);
        
        return $this->bulkProcessSalary($request);
    }
    
    /**
     * Lấy văn bản trạng thái
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'Chờ xử lý';
            case 'processed':
                return 'Đã duyệt';
            case 'paid':
                return 'Đã thanh toán';
            default:
                return ucfirst($status);
        }
    }
}