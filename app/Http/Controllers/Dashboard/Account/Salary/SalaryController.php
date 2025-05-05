<?php

namespace App\Http\Controllers\Dashboard\Account\Salary;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ContractCommission;
use App\Models\PartTimeSchedule;
use App\Models\SalaryAdvance;
use App\Models\SalaryConfiguration;
use App\Models\SalaryRecord;
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
    public function payroll()
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
            'paidCount' => SalaryRecord::where('status', 'paid')
                                    ->where('period_month', $previousMonth)
                                    ->where('period_year', $previousYear)
                                    ->count(),
            'employeeCount' => User::where('is_active', 1)->count(),
            'totalSalaryAmount' => SalaryRecord::where('period_month', $previousMonth)
                                            ->where('period_year', $previousYear)
                                            ->sum('final_amount'),
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
    
    public function mySalary()
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $salaryRecords = SalaryRecord::where('user_id', $userId)
                                     ->orderBy('period_year', 'desc')
                                     ->orderBy('period_month', 'desc')
                                     ->paginate(12);
        
        return view('dashboard.profile.my-salary', compact('salaryRecords'));
    }
    
    public function payrollData(Request $request)
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
                'overtime_amount' => number_format($item->overtime_amount, 0, ',', '.'),
                'commission_amount' => number_format($item->commission_amount, 0, ',', '.'),
                'task_mission_amount' => number_format($item->task_mission_amount, 0, ',', '.'), // Thêm dòng này
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

        // Kiểm tra chỉ cho phép tính lương tháng trước
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Tính toán tháng trước
        $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        
        if ($month != $previousMonth || $year != $previousYear) {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ được phép tính lương của tháng trước (' . $previousMonth . '/' . $previousYear . ')',
            ]);
        }

        // Lấy danh sách tất cả nhân viên đang hoạt động
        $activeUsers = User::where('is_active', 1)->get();
        
        // Mảng kết quả
        $results = [];
        $totalSalary = 0;
        $successCount = 0;
        $errorCount = 0;
        
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

                // Lấy cấu hình lương
                $fullTimeConfig = SalaryConfiguration::getConfiguration($user->id, 'fulltime');
                $partTimeConfig = SalaryConfiguration::getConfiguration($user->id, 'part-time');

                // Thông tin chấm công
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
                                                    ->whereBetween('work_date', [$startDate, $endDate])
                                                    ->get();

                // Tính ngày công fulltime
                $workingDays = $attendanceRecords->filter(function($record) {
                    return $record->status === 'present' && $record->total_hours >= 8;
                })->count();

                // Tính giờ làm thêm
                $overtimeHours = $attendanceRecords->sum(function($record) {
                    return $record->total_hours > 8 ? $record->total_hours - 8 : 0;
                });

                // Tính giờ làm part-time
                $partTimeSchedules = PartTimeSchedule::where('user_id', $user->id)
                                                ->where('status', 'approved')
                                                ->whereBetween('schedule_date', [$startDate, $endDate])
                                                ->get();

                $partTimeHours = $partTimeSchedules->sum('total_hours');

                // Tính lương cơ bản
                $baseSalary = 0;
                if ($fullTimeConfig && $fullTimeConfig->monthly_salary > 0) {
                    // Lương fulltime theo tháng
                    $workDaysInMonth = $endDate->diffInDaysFiltered(function(Carbon $date) {
                        return !$date->isSaturday() && !$date->isSunday();
                    }, $startDate) + 1;
                    
                    $baseSalary = $workDaysInMonth > 0 ? 
                        $fullTimeConfig->monthly_salary * ($workingDays / $workDaysInMonth) : 0;
                }

                // Tính lương part-time
                $partTimeSalary = 0;
                if ($partTimeConfig && $partTimeConfig->hourly_rate > 0) {
                    $partTimeSalary = $partTimeHours * $partTimeConfig->hourly_rate;
                }

                // Tính lương làm thêm giờ
                $overtimeAmount = 0;
                if ($fullTimeConfig && $overtimeHours > 0) {
                    $hourlyRate = $fullTimeConfig->monthly_salary / (8 * 22); // Giả định 22 ngày làm việc/tháng
                    $overtimeAmount = $overtimeHours * $hourlyRate * $fullTimeConfig->overtime_rate;
                }

                // Tính hoa hồng
                $commissions = ContractCommission::where('user_id', $user->id)
                                                ->where('is_paid', 0)
                                                ->whereNull('transaction_id')
                                                ->get();

                $commissionAmount = $commissions->sum('commission_amount');
                $commissionIds = $commissions->pluck('id')->toArray();

                // Tính tạm ứng
                $advances = SalaryAdvance::where('user_id', $user->id)
                                        ->where('status', 'paid')
                                        ->whereMonth('request_date', $month)
                                        ->whereYear('request_date', $year)
                                        ->get();

                $advanceAmount = $advances->sum('amount');

                // Tính tiền công từ báo cáo nhiệm vụ
                $taskMissionReports = TaskMissionReport::whereHas('assignment', function($query) use ($user) {
                                            $query->where('user_id', $user->id);
                                        })
                                        ->whereMonth('date_completed', $month)
                                        ->whereYear('date_completed', $year)
                                        ->get();

                $taskMissionAmount = $taskMissionReports->sum('price');

                // Tính thuế, bảo hiểm
                $taxRate = $fullTimeConfig ? $fullTimeConfig->tax_rate : 0;
                $insuranceRate = $fullTimeConfig ? $fullTimeConfig->insurance_rate : 0;

                $totalEarnings = $baseSalary + $partTimeSalary + $overtimeAmount + $commissionAmount + $taskMissionAmount;
                $taxAmount = $totalEarnings * ($taxRate / 100);
                $insuranceAmount = $baseSalary * ($insuranceRate / 100);

                // Tính lương cuối cùng
                $finalAmount = $totalEarnings - $taxAmount - $insuranceAmount - $advanceAmount;
                
                // Lưu bảng lương
                $salaryRecord = SalaryRecord::create([
                    'user_id' => $user->id,
                    'period_month' => $month,
                    'period_year' => $year,
                    'base_salary' => $baseSalary + $partTimeSalary,
                    'attendance_bonus' => 0,
                    'overtime_hours' => $overtimeHours,
                    'overtime_amount' => $overtimeAmount,
                    'commission_amount' => $commissionAmount,
                    'task_mission_amount' => $taskMissionAmount,
                    'deductions' => 0,
                    'tax_amount' => $taxAmount,
                    'insurance_amount' => $insuranceAmount,
                    'advance_payments' => $advanceAmount,
                    'final_amount' => $finalAmount,
                    'status' => 'pending',
                    'created_by' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                ]);
                
                // Đánh dấu các hoa hồng đã tính
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
    
    public function saveSalary(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'user_id' => 'required|exists:tbl_users,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'base_salary' => 'required|numeric|min:0',
            'attendance_bonus' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'commission_amount' => 'nullable|numeric|min:0',
            'task_mission_amount' => 'nullable|numeric|min:0', // Thêm validation cho trường mới
            'deductions' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'insurance_amount' => 'nullable|numeric|min:0',
            'advance_payments' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric',
            'commission_ids' => 'nullable|array',
            'commission_ids.*' => 'exists:tbl_contract_commissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            // Tạo bản ghi lương
            $salaryRecord = SalaryRecord::create([
                'user_id' => $request->user_id,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'base_salary' => $request->base_salary,
                'attendance_bonus' => $request->attendance_bonus ?? 0,
                'overtime_hours' => $request->overtime_hours ?? 0,
                'overtime_amount' => $request->overtime_amount ?? 0,
                'commission_amount' => $request->commission_amount ?? 0,
                'task_mission_amount' => $request->task_mission_amount ?? 0, // Thêm trường mới
                'deductions' => $request->deductions ?? 0,
                'tax_amount' => $request->tax_amount ?? 0,
                'insurance_amount' => $request->insurance_amount ?? 0,
                'advance_payments' => $request->advance_payments ?? 0,
                'final_amount' => $request->final_amount,
                'status' => 'pending',
                'created_by' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            ]);
            
            // Đánh dấu các hoa hồng đã tính
            if ($request->filled('commission_ids')) {
                ContractCommission::whereIn('id', $request->commission_ids)
                                ->update(['processed_at' => Carbon::now()]);
            }
            
            LogService::saveLog([
                'action' => 'CREATE_SALARY_RECORD',
                'ip' => $request->getClientIp(),
                'details' => "Tạo bảng lương tháng " . $request->period_month . "/" . $request->period_year . " cho nhân viên #" . $request->user_id,
                'fk_key' => 'tbl_salary_records|id',
                'fk_value' => $salaryRecord->id,
            ]);
            
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Lưu bảng lương thành công',
                'data' => $salaryRecord,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
    
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
                    'message' => 'Bảng lương này đã được chi trả',
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
                
                // Đánh dấu các hoa hồng đã chi
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
                // Cập nhật trạng thái
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
                'message' => 'Cập nhật trạng thái bảng lương thành công',
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
     * Xử lý hàng loạt bảng lương (duyệt/chi trả)
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
                    
                    // Đánh dấu các hoa hồng đã chi
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
                    // Cập nhật trạng thái
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
                'message' => 'Đã ' . ($action === 'process' ? 'duyệt' : 'chi trả') . ' ' . $successCount . ' bảng lương, ' . $errorCount . ' lỗi',
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
     * Lấy danh sách ID bảng lương đang chờ duyệt
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
    
    private function getStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'Chờ xử lý';
            case 'processed':
                return 'Đã duyệt';
            case 'paid':
                return 'Đã chi';
            default:
                return ucfirst($status);
        }
    }
}