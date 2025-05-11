<?php

namespace App\Http\Controllers\Dashboard\Overview;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Consultation;
use App\Models\AttendanceRecord;
use App\Models\ContractPayment;
use App\Models\PartTimeSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    public function index() 
    {
        // 1. Contract Overview
        $contractStats = [
            'total' => Contract::count(),
            'active' => Contract::where('status', 1)->count(),
            'completed' => Contract::where('status', 2)->count(),
            'canceled' => Contract::where('status', 3)->count(),
            'pending_approval' => Contract::where('status', 0)->count(),
            'total_value' => Contract::sum('total_value'),
            'paid_value' => DB::table('tbl_contract_payments')
                            ->where('status', 1)
                            ->where('is_active', 1)
                            ->where('payment_stage', '!=', 3)
                            ->sum('price'),
            'recent' => Contract::with(['user', 'provider'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get()
        ];
        
        $activeContracts = Contract::with(['user', 'provider', 'tasks'])
            ->where('status', 1)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($contract) {
                $tasks = $contract->tasks->where('is_active', 1);
                $totalTasks = $tasks->count();
                $completedTasks = $tasks->where('status_id', '>=', 4)->count();
                $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                
                return (object)[
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'provider' => $contract->provider,
                    'task_progress' => $taskProgress,
                    'created_at' => $contract->created_at
                ];
            });

        // Lấy hợp đồng do người dùng hiện tại quản lý
        $myContracts = Contract::with(['provider'])
            ->where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Lấy hợp đồng chờ duyệt
        $pendingContracts = Contract::with(['provider'])
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Reemplazar esta consulta:
        $paymentDueContracts = DB::table('tbl_contract_payments')
            ->join('tbl_contracts', 'tbl_contract_payments.contract_id', '=', 'tbl_contracts.id')
            ->where('tbl_contract_payments.status', 0)
            ->where('tbl_contract_payments.is_active', 1)
            ->where('tbl_contract_payments.due_date', '<=', now()->addDays(7))
            ->select('tbl_contract_payments.*', 'tbl_contracts.contract_number')
            ->orderBy('tbl_contract_payments.due_date')
            ->limit(10)
            ->get();

        // Con esta consulta usando Eloquent:
        $paymentDueContracts = ContractPayment::with(['contract', 'contract.provider'])
            ->where('status', 0)
            ->where('is_active', 1)
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        // Lấy hợp đồng đã hoàn thành task, đang chờ xử lý
        $completedTaskContracts = Contract::with(['provider'])
            ->where('status', 1)
            ->whereHas('tasks', function ($query) {
                $query->where('is_active', 1)
                    ->where('status_id', 4)
                    ->whereNull('parent_id');
            })
            ->withCount(['tasks as completed_tasks_count' => function ($query) {
                $query->where('is_active', 1)
                    ->where('status_id', 4);
            }])
            ->orderBy('completed_tasks_count', 'desc')
            ->limit(10)
            ->get();

        // Lấy hợp đồng sắp hết hạn trong 14 ngày tới
        $expiringContracts = Contract::with(['provider'])
            ->where('status', 1)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(14))
            ->where('expiry_date', '>=', now())
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        // 2. Customer Overview
        $customerStats = [
            'total' => Customer::where('is_active', 1)->count(),
            'leads' => Customer::where('type', Customer::TYPE_LEAD)->count(),
            'prospects' => Customer::where('type', Customer::TYPE_PROSPECT)->count(),
            'customers' => Customer::where('type', Customer::TYPE_CUSTOMER)->count(),
            'new_today' => Customer::whereDate('created_at', today())->count(),
            'upcoming_appointments' => Appointment::where('start_time', '>=', now())
                                     ->where('start_time', '<=', now()->addDays(3))
                                     ->with(['customer', 'user'])
                                     ->limit(10)
                                     ->get(),
            'active_consultations' => Consultation::whereHas('logs', function($query) {
                                        $query->where('action', '<', 2);
                                     })->count(),
            'need_follow_up' => Customer::where('last_interaction_date', '<', now()->subDays(14))
                              ->where('type', '>', Customer::TYPE_LEAD)
                              ->count()
        ];

        // 3. Task Overview
        $taskStats = [
            'total' => Task::where('is_active', 1)->where('type', 'SERVICE')->count(),
            'completed' => Task::where('is_active', 1)->where('type', 'SERVICE')->whereIn('status_id', [4,8])->count(),
            'in_progress' => Task::where('is_active', 1)->where('type', 'SERVICE')->whereIn('status_id', [2,3])->count(),
            'overdue' => Task::where('is_active', 1)
                            ->where('type', 'SERVICE')
                          ->where('due_date', '<', now())
                          ->whereIn('status_id', [0,1,2,3,4,6,7])
                          ->count(),
            'due_today' => Task::where('is_active', 1)
                            ->where('type', 'SERVICE')
                          ->whereDate('due_date', today())
                          ->whereIn('status_id', [0,1,2,3,4,6,7])
                          ->count(),
            'due_this_week' => Task::where('is_active', 1)
                            ->where('type', 'SERVICE')
                            ->where('due_date', '>=', now())
                            ->where('due_date', '<=', now()->endOfWeek())
                            ->whereIn('status_id', [0,1,2,3,4,6,7])
                            ->count(),
            'need_revision' => Task::where('is_active', 1)->where('status_id', 7)->count(),
            'recent_tasks' => Task::with(['assign', 'status'])
                            ->where('type', 'SERVICE')
                            ->where('is_active', 1)
                            ->orderBy('updated_at', 'desc')
                            ->limit(10)
                            ->get()
        ];

        // 4. Financial Overview
        $financialStats = [
            'total_income' => Transaction::where('type', 0)->where('status', 1)->sum('amount'),
            'total_expense' => Transaction::where('type', 1)->where('status', 1)->sum('amount'),
            'balance' => Transaction::where('type', 0)->where('status', 1)->sum('amount') - 
                      Transaction::where('type', 1)->where('status', 1)->sum('amount'),
            'this_month_income' => Transaction::where('type', 0)
                                 ->where('status', 1)
                                 ->whereMonth('paid_date', now()->month)
                                 ->whereYear('paid_date', now()->year)
                                 ->sum('amount'),
            'this_month_expense' => Transaction::where('type', 1)
                                  ->where('status', 1)
                                  ->whereMonth('paid_date', now()->month)
                                  ->whereYear('paid_date', now()->year)
                                  ->sum('amount'),
            'recent_transactions' => Transaction::with(['category', 'targetClient', 'targetEmployee'])
                                    ->where('status', 1)
                                    ->orderBy('paid_date', 'desc')
                                    ->limit(10)
                                    ->get(),
            'pending_payments' => DB::table('tbl_contract_payments')
                               ->where('status', 0)
                               ->where('is_active', 1)
                               ->where('due_date', '<=', now()->addDays(7))
                               ->count()
        ];

        // 5. Employee Overview
        $employeeStats = [
            'total' => User::where('is_active', 1)->count(),
            'active_today' => AttendanceRecord::whereDate('work_date', today())
                           ->whereIn('status', ['present', 'late', 'early_leave', 'late_and_early_leave'])
                           ->count(),
            'working_now' => AttendanceRecord::whereDate('work_date', today())
                          ->whereNotNull('check_in_time')
                          ->whereNull('check_out_time')
                          ->count(),
            'on_leave' => AttendanceRecord::whereDate('work_date', today())
                        ->where('status', 'absent')
                        ->count(),
            'top_performers' => User::withCount(['task_mission_reports'])
                               ->orderBy('task_mission_reports_count', 'desc')
                               ->limit(10)
                               ->get()
        ];

        // Get weekly task completion trends
        $taskTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $taskTrends[] = [
                'date' => now()->subDays($i)->format('d/m'),
                'completed' => Task::where('is_active', 1)
                            ->whereDate('updated_at', $date)
                            ->where('status_id', 4)
                            ->count()
            ];
        }

        // Get monthly financial trends
        $financialTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $financialTrends[] = [
                'month' => $month->format('m/Y'),
                'income' => Transaction::where('type', 0)
                         ->where('status', 1)
                         ->whereMonth('paid_date', $month->month)
                         ->whereYear('paid_date', $month->year)
                         ->sum('amount'),
                'expense' => Transaction::where('type', 1)
                          ->where('status', 1)
                          ->whereMonth('paid_date', $month->month)
                          ->whereYear('paid_date', $month->year)
                          ->sum('amount')
            ];
        }

        // Lấy thông tin người dùng đã đăng nhập
        $userId = auth()->user()->id;
        $today = date('Y-m-d');
        
        // Lấy lịch làm việc đã được duyệt của ngày hôm nay
        $approvedSchedule = PartTimeSchedule::where('user_id', $userId)
            ->where('schedule_date', $today)
            ->where('status', 'approved')
            ->first();
        
        // Lấy bản ghi chấm công nếu có
        $attendanceRecord = AttendanceRecord::where('user_id', $userId)
            ->where('work_date', $today)
            ->first();
        
        // Kiểm tra thời gian hiện tại có nằm trong khoảng thời gian làm việc không
        $now = Carbon::now();
        $canCheckIn = false;
        
        if ($approvedSchedule) {
            // Sửa lại cách tạo đối tượng thời gian
            $startTimeString = $approvedSchedule->start_time;
            $endTimeString = $approvedSchedule->end_time;
            
            // Chuyển đổi định dạng giờ nếu cần
            if ($startTimeString instanceof \DateTime) {
                $startTimeString = $startTimeString->format('H:i:s');
            }
            
            if ($endTimeString instanceof \DateTime) {
                $endTimeString = $endTimeString->format('H:i:s');
            }
            
            $scheduleDate = $approvedSchedule->schedule_date;
            if ($scheduleDate instanceof \DateTime) {
                $scheduleDate = $scheduleDate->format('Y-m-d');
            }
            
            // Tạo các đối tượng Carbon riêng biệt cho thời gian bắt đầu và kết thúc
            $scheduleStart = Carbon::createFromFormat('Y-m-d H:i:s', $scheduleDate . ' ' . $startTimeString);
            $scheduleEnd = Carbon::createFromFormat('Y-m-d H:i:s', $scheduleDate . ' ' . $endTimeString);
            
            // Cho phép check-in sớm 30 phút so với lịch
            $allowedCheckInTime = $scheduleStart->copy()->subMinutes(30);
            
            // Kiểm tra xem thời gian hiện tại có nằm trong khoảng thời gian cho phép check-in không
            $canCheckIn = $now->between($allowedCheckInTime, $scheduleEnd);
        }

        $pendingSchedules = PartTimeSchedule::with('user')
                    ->pending()
                    ->orderBy('schedule_date', 'asc')
                    ->limit(10)
                    ->get();

        $pendingSchedulesCount = PartTimeSchedule::pending()->count();
        $cancelRequestsCount = PartTimeSchedule::cancelRequested()->count();
        
        return view("dashboard.overview.index", compact(
            'contractStats',
            'customerStats',
            'taskStats',
            'financialStats',
            'employeeStats',
            'taskTrends',
            'financialTrends',
            'attendanceRecord',
            'approvedSchedule',
            'canCheckIn',
            'pendingSchedules',
            'pendingSchedulesCount',
            'cancelRequestsCount',
            'activeContracts',
            'myContracts',
            'pendingContracts',
            'paymentDueContracts',
            'completedTaskContracts',
            'expiringContracts'
        ));
    }
}