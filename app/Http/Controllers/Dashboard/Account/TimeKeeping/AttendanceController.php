<?php

namespace App\Http\Controllers\Dashboard\Account\Timekeeping;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\PartTimeSchedule;
use App\Models\SalaryConfiguration;
use App\Models\User;
use App\Models\UserDepartment;
use App\Services\LogService;
use App\Services\PaginationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AttendanceController extends Controller
{
    public function timekeeping()
    {
        $users = User::where('is_active', 1)->get();
        
        // Thống kê cơ bản
        $stats = [
            'totalEmployees' => User::where('is_active', 1)->count(),
            'checkedInToday' => AttendanceRecord::whereDate('work_date', Carbon::today())->count(),
            'lateToday' => AttendanceRecord::whereDate('work_date', Carbon::today())
                ->whereIn('status', ['late', 'late_and_early_leave'])->count(),
            'absentToday' => 0, // Tính sau khi ngày kết thúc
        ];
        
        return view('dashboard.account.timekeeping.index', compact('users', 'stats'));
    }
    
    public function checkInOut()
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $today = Carbon::today()->toDateString();
        
        // Lấy thông tin người dùng
        $user = User::find($userId);
        
        // Lấy thông tin phòng ban của người dùng
        $departments = UserDepartment::join('tbl_departments', 'tbl_user_departments.department_id', '=', 'tbl_departments.id')
                                    ->join('tbl_levels', 'tbl_user_departments.level_id', '=', 'tbl_levels.id')
                                    ->where('tbl_user_departments.user_id', $userId)
                                    ->select('tbl_departments.name', 'tbl_levels.name as level_name')
                                    ->get();
        
        // Lấy cấu hình lương
        $salaryConfig = SalaryConfiguration::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
        ->orderBy('user_id', 'desc') // Ưu tiên cấu hình riêng
        ->first();
        
        // Lấy lịch làm việc đã được duyệt cho hôm nay
        $schedule = PartTimeSchedule::where('user_id', $userId)
                                ->where('schedule_date', $today)
                                ->where('status', 'approved')
                                ->first();
        
        // Lấy bản ghi chấm công hôm nay nếu có
        $attendanceRecord = AttendanceRecord::where('user_id', $userId)
                                        ->where('work_date', $today)
                                        ->first();
        
        // Tính toán thống kê chấm công
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $firstDayOfMonth = Carbon::createFromDate($currentYear, $currentMonth, 1);
        $lastDayOfMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->endOfMonth();
        
        // Lấy tất cả bản ghi chấm công trong tháng
        $monthAttendances = AttendanceRecord::where('user_id', $userId)
                                        ->whereBetween('work_date', [$firstDayOfMonth, $lastDayOfMonth])
                                        ->get();
        
        // Số ngày làm việc trong tháng (không tính T7, CN)
        $workingDaysInMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $lastDayOfMonth) + 1;
        
        // Tính toán các thống kê
        $stats = [
            'presentDays' => $monthAttendances->whereIn('status', ['present', 'late', 'early_leave', 'late_and_early_leave'])->count(),
            'workingDaysInMonth' => $workingDaysInMonth,
            'totalHours' => $monthAttendances->sum('total_hours'),
        ];
        
        // Lấy lịch sử chấm công gần đây (10 bản ghi gần nhất)
        $recentAttendance = AttendanceRecord::where('user_id', $userId)
                                        ->orderBy('work_date', 'desc')
                                        ->limit(10)
                                        ->get();
                                        
        return view('dashboard.account.timekeeping.check-in-out', compact(
            'user', 
            'departments', 
            'salaryConfig', 
            'attendanceRecord',
            'schedule',
            'stats', 
            'recentAttendance'
        ));
    }
    
    public function doCheckIn(Request $request)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $now = Carbon::now();
        $today = $now->toDateString();
        
        // Kiểm tra đã check-in chưa
        $existingRecord = AttendanceRecord::where('user_id', $userId)
                                        ->where('work_date', $today)
                                        ->first();
        
        if ($existingRecord && $existingRecord->check_in_time) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn đã check-in hôm nay vào lúc ' . $existingRecord->check_in_time->format('H:i:s'),
            ]);
        }
        
        // Lấy lịch làm việc đã được duyệt cho hôm nay
        $schedule = PartTimeSchedule::where('user_id', $userId)
                                ->where('schedule_date', $today)
                                ->where('status', 'approved')
                                ->first();
        
        if (!$schedule) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn không có lịch làm việc được duyệt cho hôm nay',
            ]);
        }
        
        // Chuyển đổi thời gian lịch sang Carbon objects để dễ so sánh
        $scheduleStart = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . date('H:i:s', strtotime($schedule->start_time)));
        $scheduleEnd = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . date('H:i:s', strtotime($schedule->end_time)));
        
        // Cho phép check-in sớm 60 phút so với lịch
        $allowedCheckInTime = $scheduleStart->copy()->subMinutes(60);
        
        if ($now->lt($allowedCheckInTime)) {
            return response()->json([
                'status' => 400,
                'message' => 'Chưa đến thời gian check-in. Bạn có thể check-in từ ' . $allowedCheckInTime->format('H:i:s'),
            ]);
        }
        
        if ($now->gt($scheduleEnd)) {
            return response()->json([
                'status' => 400,
                'message' => 'Đã quá thời gian làm việc của bạn cho hôm nay',
            ]);
        }
        
        // Tạo hoặc cập nhật bản ghi chấm công
        $attendanceRecord = $existingRecord ?: new AttendanceRecord();
        $attendanceRecord->user_id = $userId;
        $attendanceRecord->work_date = $today;
        $attendanceRecord->check_in_time = $now;
        $attendanceRecord->schedule_id = $schedule->id;
        
        // Xác định trạng thái (trễ hoặc đúng giờ)
        $isLate = $now->gt($scheduleStart);
        $attendanceRecord->status = $isLate ? 'late' : 'present';
        $attendanceRecord->save();
        
        LogService::saveLog([
            'action' => 'CHECK_IN',
            'ip' => $request->getClientIp(),
            'details' => "Người dùng check-in lúc " . $now->format('Y-m-d H:i:s') . ($isLate ? ' (đi trễ)' : ''),
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $attendanceRecord->id,
        ]);
        
        // Chuẩn bị thông báo cho người dùng
        $message = 'Check-in thành công lúc ' . $now->format('H:i:s');
        if ($isLate) {
            $message .= '. Bạn đã đi trễ ' . $now->diffInMinutes($scheduleStart) . ' phút';
        }
        
        return response()->json([
            'status' => 200,
            'message' => $message,
            'attendance' => $attendanceRecord,
        ]);
    }
    
    public function doCheckOut(Request $request)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $now = Carbon::now();
        $today = $now->toDateString();
        
        // Kiểm tra bản ghi chấm công
        $attendanceRecord = AttendanceRecord::where('user_id', $userId)
                                        ->where('work_date', $today)
                                        ->first();
        
        if (!$attendanceRecord || !$attendanceRecord->check_in_time) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn cần check-in trước khi check-out.',
            ]);
        }
        
        if ($attendanceRecord->check_out_time) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn đã check-out hôm nay lúc ' . $attendanceRecord->check_out_time->format('H:i:s'),
            ]);
        }
        
        // Lấy lịch làm việc liên quan
        $schedule = PartTimeSchedule::find($attendanceRecord->schedule_id);
        
        // Kiểm tra xem có về sớm không
        $isEarlyLeave = false;
        if ($schedule) {
            $scheduleEnd = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . date('H:i:s', strtotime($schedule->end_time)));
            $isEarlyLeave = $now->lt($scheduleEnd);
        }
        
        // Cập nhật bản ghi
        $attendanceRecord->check_out_time = $now;
        
        // Tính giờ làm việc
        $attendanceRecord->total_hours = AttendanceRecord::calculateTotalHours(
            $attendanceRecord->check_in_time,
            $attendanceRecord->check_out_time
        );
        
        // Cập nhật trạng thái nếu về sớm
        if ($isEarlyLeave && $attendanceRecord->status == 'present') {
            $attendanceRecord->status = 'early_leave';
        } else if ($isEarlyLeave && $attendanceRecord->status == 'late') {
            $attendanceRecord->status = 'late_and_early_leave';
        }
        
        $attendanceRecord->save();
        
        LogService::saveLog([
            'action' => 'CHECK_OUT',
            'ip' => $request->getClientIp(),
            'details' => "Người dùng check-out lúc " . $now->format('Y-m-d H:i:s') . ($isEarlyLeave ? ' (về sớm)' : ''),
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $attendanceRecord->id,
        ]);
        
        // Chuẩn bị thông báo cho người dùng
        $message = 'Check-out thành công lúc ' . $now->format('H:i:s');
        if ($isEarlyLeave && $schedule) {
            $message .= '. Bạn đã về sớm ' . $now->diffInMinutes($scheduleEnd) . ' phút';
        }
        
        return response()->json([
            'status' => 200,
            'message' => $message,
            'attendance' => $attendanceRecord,
        ]);
    }
    
    public function attendanceData(Request $request)
    {
        $currentPage = $request->input('page', 1);
        
        $query = AttendanceRecord::with('user')
            ->when($request->input('filter.user_id'), function($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->input('filter.date_from'), function($query, $dateFrom) {
                $query->where('work_date', '>=', $dateFrom);
            })
            ->when($request->input('filter.date_to'), function($query, $dateTo) {
                $query->where('work_date', '<=', $dateTo);
            })
            ->when($request->input('filter.status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('work_date', 'desc');
        
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];
        
        $attendances = $paginationResult['data'];
        
        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.timekeeping.ajax-index', ['data' => $attendances, 'offset' => $offset])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }
    
    public function myTimesheet()
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $attendanceRecords = AttendanceRecord::where('user_id', $userId)
                                            ->orderBy('work_date', 'desc')
                                            ->paginate(31);
        
        return view('dashboard.profile.my-timesheet', compact('attendanceRecords'));
    }
    
    public function updateAttendance(Request $request)
    {
        $validator = \App\Services\ValidatorService::make($request, [
            'id' => 'required|exists:tbl_attendance_records,id',
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'status' => 'nullable|in:present,absent,late,early_leave,late_and_early_leave',
            'note' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $attendanceRecord = AttendanceRecord::findOrFail($request->id);
        
        // Cập nhật các trường nếu có
        if ($request->filled('check_in_time')) {
            $attendanceRecord->check_in_time = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $attendanceRecord->work_date->format('Y-m-d') . ' ' . $request->check_in_time
            );
        }
        
        if ($request->filled('check_out_time')) {
            $attendanceRecord->check_out_time = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $attendanceRecord->work_date->format('Y-m-d') . ' ' . $request->check_out_time
            );
            
            // Tính lại tổng giờ làm việc
            if ($attendanceRecord->check_in_time && $attendanceRecord->check_out_time) {
                $attendanceRecord->total_hours = AttendanceRecord::calculateTotalHours(
                    $attendanceRecord->check_in_time,
                    $attendanceRecord->check_out_time
                );
            }
        }
        
        if ($request->filled('status')) {
            $attendanceRecord->status = $request->status;
        }
        
        if ($request->filled('note')) {
            $attendanceRecord->note = $request->note;
        }
        
        $attendanceRecord->save();
        
        LogService::saveLog([
            'action' => 'UPDATE_ATTENDANCE',
            'ip' => $request->getClientIp(),
            'details' => "Cập nhật bản ghi chấm công #" . $attendanceRecord->id,
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $attendanceRecord->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật bản ghi chấm công thành công',
        ]);
    }
}