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
                        ->limit(5)
                        ->get()
        ];

        // 2. Customer Overview
        $customerStats = [
            'total' => Customer::where('is_active', 1)->count(),
            'leads' => Customer::where('type', Customer::TYPE_LEAD)->count(),
            'prospects' => Customer::where('type', Customer::TYPE_PROSPECT)->count(),
            'customers' => Customer::where('type', Customer::TYPE_CUSTOMER)->count(),
            'new_today' => Customer::whereDate('created_at', today())->count(),
            'upcoming_appointments' => Appointment::where('start_time', '>=', now())
                                     ->where('start_time', '<=', now()->addDays(7))
                                     ->with(['customer', 'user'])
                                     ->limit(5)
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
            'total' => Task::where('is_active', 1)->count(),
            'completed' => Task::where('is_active', 1)->where('status_id', 4)->count(),
            'in_progress' => Task::where('is_active', 1)->where('status_id', 3)->count(),
            'overdue' => Task::where('is_active', 1)
                          ->where('due_date', '<', now())
                          ->where('status_id', '<', 4)
                          ->count(),
            'due_today' => Task::where('is_active', 1)
                          ->whereDate('due_date', today())
                          ->where('status_id', '<', 4)
                          ->count(),
            'due_this_week' => Task::where('is_active', 1)
                            ->where('due_date', '>=', now())
                            ->where('due_date', '<=', now()->endOfWeek())
                            ->where('status_id', '<', 4)
                            ->count(),
            'need_revision' => Task::where('is_active', 1)->where('status_id', 7)->count(),
            'recent_tasks' => Task::with(['assign', 'status'])
                              ->where('is_active', 1)
                              ->orderBy('updated_at', 'desc')
                              ->limit(5)
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
                                    ->limit(5)
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
                           ->where('status', 'present')
                           ->count(),
            'working_now' => AttendanceRecord::whereDate('work_date', today())
                          ->whereNotNull('check_in_time')
                          ->whereNull('check_out_time')
                          ->count(),
            'on_leave' => AttendanceRecord::whereDate('work_date', today())
                        ->where('status', 'absent')
                        ->count(),
            'top_performers' => User::withCount(['tasks' => function($query) {
                                    $query->where('status_id', 4);
                                }])
                               ->orderBy('tasks_count', 'desc')
                               ->limit(5)
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

        return view("dashboard.overview.index", compact(
            'contractStats',
            'customerStats',
            'taskStats',
            'financialStats',
            'employeeStats',
            'taskTrends',
            'financialTrends'
        ));
    }
}