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
        $users = User::where('is_active', 1)->get();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        return view('dashboard.account.salary.payroll', compact('users', 'currentMonth', 'currentYear'));
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
            'user_id' => 'required|exists:tbl_users,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        $userId = $request->user_id;
        $month = $request->period_month;
        $year = $request->period_year;

        // Kiểm tra nếu đã có bảng lương cho tháng này
        $existingSalary = SalaryRecord::where('user_id', $userId)
                                    ->where('period_month', $month)
                                    ->where('period_year', $year)
                                    ->first();

        if ($existingSalary) {
            return response()->json([
                'status' => 422,
                'message' => 'Đã tồn tại bảng lương cho nhân viên này trong tháng ' . $month . '/' . $year,
            ]);
        }

        $user = User::find($userId);

        // Lấy cấu hình lương
        $fullTimeConfig = SalaryConfiguration::getConfiguration($userId, 'fulltime');
        $partTimeConfig = SalaryConfiguration::getConfiguration($userId, 'part-time');

        // Thông tin chấm công
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $attendanceRecords = AttendanceRecord::where('user_id', $userId)
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
        $partTimeSchedules = PartTimeSchedule::where('user_id', $userId)
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
            
            $baseSalary = $fullTimeConfig->monthly_salary * ($workingDays / $workDaysInMonth);
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
        $commissions = ContractCommission::where('user_id', $userId)
                                        ->where('is_paid', 0)
                                        ->whereNull('transaction_id')
                                        ->get();

        $commissionAmount = $commissions->sum('commission_amount');

        // Tính tạm ứng
        $advances = SalaryAdvance::where('user_id', $userId)
                                ->where('status', 'paid')
                                ->whereMonth('request_date', $month)
                                ->whereYear('request_date', $year)
                                ->get();

        $advanceAmount = $advances->sum('amount');

        // Tính tiền công từ báo cáo nhiệm vụ
        $taskMissionReports = TaskMissionReport::whereHas('assignment', function($query) use ($userId) {
                                    $query->where('user_id', $userId);
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

        return response()->json([
            'status' => 200,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'period' => [
                    'month' => $month,
                    'year' => $year,
                ],
                'workingDays' => $workingDays,
                'overtimeHours' => $overtimeHours,
                'partTimeHours' => $partTimeHours,
                'baseSalary' => $baseSalary,
                'partTimeSalary' => $partTimeSalary,
                'overtimeAmount' => $overtimeAmount,
                'commissionAmount' => $commissionAmount,
                'taskMissionAmount' => $taskMissionAmount, // Thêm khoản tiền từ báo cáo nhiệm vụ
                'advanceAmount' => $advanceAmount,
                'taxAmount' => $taxAmount,
                'insuranceAmount' => $insuranceAmount,
                'finalAmount' => $finalAmount,
                'commissionIds' => $commissions->pluck('id')->toArray(),
            ],
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