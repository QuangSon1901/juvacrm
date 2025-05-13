<?php

namespace App\Http\Controllers\Dashboard\Account\Timekeeping;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\AttendanceRecord;
use App\Models\PartTimeSchedule;
use App\Models\SalaryConfiguration;
use App\Models\SystemConfig;
use App\Models\User;
use App\Models\UserDepartment;
use App\Models\AttendanceLog;
use App\Services\LogService;
use App\Services\PaginationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function timekeeping()
    {
        $users = User::where('is_active', 1)->get();
        $today = Carbon::today()->toDateString();
        
        // Đếm số ca làm việc hôm nay
        $schedulesToday = PartTimeSchedule::where('schedule_date', $today)
                                      ->where('status', 'approved')
                                      ->count();
        
        // Đếm số nhân viên đã check-in
        $checkedInToday = AttendanceRecord::whereDate('work_date', $today)
                                     ->whereNotNull('check_in_time')
                                     ->distinct('user_id')
                                     ->count('user_id');
        
        // Đếm số nhân viên đi trễ
        $lateToday = AttendanceRecord::whereDate('work_date', $today)
                                 ->whereIn('status', ['late', 'late_and_early_leave'])
                                 ->distinct('user_id')
                                 ->count('user_id');
        
        // Đếm số nhân viên vắng mặt
        $absentToday = AttendanceRecord::whereDate('work_date', $today)
                                   ->where('status', 'absent')
                                   ->count();
        
        // Thống kê
        $stats = [
            'totalEmployees' => User::where('is_active', 1)->count(),
            'scheduledToday' => $schedulesToday,
            'checkedInToday' => $checkedInToday,
            'lateToday' => $lateToday,
            'absentToday' => $absentToday,
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
        
        // Kiểm tra xem có record nào chưa checkout không
        $incompleteRecords = AttendanceRecord::where('user_id', $userId)
                                      ->whereNotNull('check_in_time')
                                      ->whereNull('check_out_time')
                                      ->where('work_date', '<', $today)
                                      ->orderBy('work_date', 'desc')
                                      ->get();
        
        // Lấy lịch làm việc đã được duyệt cho hôm nay
        $schedules = PartTimeSchedule::where('user_id', $userId)
                                ->where('schedule_date', $today)
                                ->where('status', 'approved')
                                ->get();
        
        // Lấy bản ghi chấm công hôm nay nếu có
        $attendanceRecords = AttendanceRecord::where('user_id', $userId)
                                        ->where('work_date', $today)
                                        ->get();
        
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
            'totalValidHours' => $monthAttendances->sum('valid_hours'),
            'totalOvertimeHours' => $monthAttendances->sum('overtime_hours'),
            'lateDays' => $monthAttendances->where('late_minutes', '>', 0)->count(),
            'earlyLeaveDays' => $monthAttendances->where('early_leave_minutes', '>', 0)->count(),
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
            'attendanceRecords',
            'schedules',
            'incompleteRecords',
            'stats', 
            'recentAttendance'
        ));
    }
    
    public function fixIncompleteCheckout(Request $request)
    {
        $validator = \App\Services\ValidatorService::make($request, [
            'record_id' => 'required|exists:tbl_attendance_records,id',
            'check_out_time' => 'required|date_format:H:i:s',
            'reason' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $record = AttendanceRecord::where('id', $request->record_id)
                                ->where('user_id', $userId)
                                ->first();
        
        if (!$record) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy bản ghi chấm công',
            ]);
        }
        
        // Parse checkout time
        $checkOutTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $record->work_date->format('Y-m-d') . ' ' . $request->check_out_time
        );
        
        // Cập nhật thời gian checkout
        $record->check_out_time = $checkOutTime;
        $record->forgot_checkout = true;
        $record->forgot_checkout_reason = $request->reason;
        
        // Lấy giờ nghỉ trưa từ cấu hình
        $breakTime = SystemConfig::getValue('break_time', '12:00-13:00');
        
        // Tính toán giờ làm việc
        $record->total_hours = AttendanceRecord::calculateTotalHours(
            $record->check_in_time,
            $record->check_out_time,
            $breakTime
        );
        
        // Cập nhật valid_hours bằng total_hours
        $record->valid_hours = $record->total_hours;
        
        // Xác định nếu về sớm
        if ($record->schedule) {
            $scheduleEnd = Carbon::createFromFormat('Y-m-d H:i:s', $record->work_date->format('Y-m-d') . ' ' . date('H:i:s', strtotime($record->schedule->end_time)));
            
            if ($checkOutTime->lt($scheduleEnd)) {
                $record->early_leave_minutes = $checkOutTime->diffInMinutes($scheduleEnd);
                
                // Cập nhật trạng thái
                if ($record->status == 'present') {
                    $record->status = 'early_leave';
                } else if ($record->status == 'late') {
                    $record->status = 'late_and_early_leave';
                }
            }
        }
        
        $record->save();
        
        LogService::saveLog([
            'action' => 'FIX_CHECKOUT',
            'ip' => $request->getClientIp(),
            'details' => "Đã cập nhật checkout cho ngày " . $record->work_date->format('d/m/Y') . " lúc " . $checkOutTime->format('H:i:s'),
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $record->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã cập nhật thành công checkout cho ngày ' . $record->work_date->format('d/m/Y'),
        ]);
    }
    
    public function doCheckIn(Request $request)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $now = Carbon::now();
        $today = $now->toDateString();
        
        // Kiểm tra có bản ghi nào chưa checkout không
        $incompleteRecord = AttendanceRecord::where('user_id', $userId)
                                      ->whereNotNull('check_in_time')
                                      ->whereNull('check_out_time')
                                      ->where('work_date', '<', $today)
                                      ->first();
        
        if ($incompleteRecord) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn có bản ghi chấm công ngày ' . $incompleteRecord->work_date->format('d/m/Y') . ' chưa checkout. Vui lòng cập nhật checkout trước.',
                'record_id' => $incompleteRecord->id,
                'require_fix' => true
            ]);
        }
        
        // Kiểm tra schedule_id được cung cấp
        if (!$request->has('schedule_id')) {
            return response()->json([
                'status' => 400,
                'message' => 'Vui lòng chọn ca làm việc để check-in',
            ]);
        }
        
        // Kiểm tra đã check-in chưa
        $existingRecord = AttendanceRecord::where('user_id', $userId)
                                        ->where('work_date', $today)
                                        ->where('schedule_id', $request->schedule_id)
                                        ->first();
        
        if ($existingRecord && $existingRecord->check_in_time) {
            return response()->json([
                'status' => 400,
                'message' => 'Bạn đã check-in cho ca làm việc này vào lúc ' . $existingRecord->check_in_time->format('H:i:s'),
            ]);
        }
        
        // Lấy lịch làm việc đã được duyệt
        $schedule = PartTimeSchedule::where('id', $request->schedule_id)
                                ->where('user_id', $userId)
                                ->where('schedule_date', $today)
                                ->where('status', 'approved')
                                ->first();
        
        if (!$schedule) {
            return response()->json([
                'status' => 400,
                'message' => 'Không tìm thấy lịch làm việc hợp lệ',
            ]);
        }
        
        // Chuyển đổi thời gian lịch sang Carbon objects để dễ so sánh
        $scheduleStart = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . date('H:i:s', strtotime($schedule->start_time)));
        $scheduleEnd = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . date('H:i:s', strtotime($schedule->end_time)));
        
        // Lấy giới hạn thời gian check-in sớm từ cấu hình
        $earlyCheckInMinutes = SystemConfig::getValue('early_checkin_minutes', 60);
        
        // Cho phép check-in sớm theo cấu hình
        $allowedCheckInTime = $scheduleStart->copy()->subMinutes($earlyCheckInMinutes);
        
        if ($now->lt($allowedCheckInTime)) {
            return response()->json([
                'status' => 400,
                'message' => 'Chưa đến thời gian check-in. Bạn có thể check-in từ ' . $allowedCheckInTime->format('H:i:s'),
            ]);
        }
        
        if ($now->gt($scheduleEnd)) {
            return response()->json([
                'status' => 400,
                'message' => 'Đã quá thời gian làm việc của bạn cho ca này',
            ]);
        }
        
        // Xác định trạng thái (trễ hoặc đúng giờ)
        $isLate = $now->gt($scheduleStart);
        $lateMinutes = $isLate ? $now->diffInMinutes($scheduleStart) : 0;
        
        // Tạo hoặc cập nhật bản ghi chấm công
        $attendanceRecord = $existingRecord ?: new AttendanceRecord();
        $attendanceRecord->user_id = $userId;
        $attendanceRecord->work_date = $today;
        $attendanceRecord->check_in_time = $now;
        $attendanceRecord->schedule_id = $schedule->id;
        $attendanceRecord->late_minutes = $lateMinutes;
        $attendanceRecord->status = $isLate ? 'late' : 'present';
        
        // Nếu đi trễ, kiểm tra lý do
        if ($isLate && (!$request->filled('late_reason') || trim($request->late_reason) === '')) {
            return response()->json([
                'status' => 422,
                'message' => 'Bạn đi trễ, vui lòng cung cấp lý do',
                'require_reason' => true,
                'is_late' => true,
                'late_minutes' => $lateMinutes
            ]);
        }
        
        // Lưu lý do đi trễ nếu có
        if ($isLate && $request->filled('late_reason')) {
            $attendanceRecord->late_reason = $request->late_reason;
        }
        
        $attendanceRecord->save();
        
        LogService::saveLog([
            'action' => 'CHECK_IN',
            'ip' => $request->getClientIp(),
            'details' => "Người dùng check-in lúc " . $now->format('Y-m-d H:i:s') . ($isLate ? ' (đi trễ ' . $lateMinutes . ' phút)' : ''),
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $attendanceRecord->id,
        ]);
        
        // Chuẩn bị thông báo cho người dùng
        $message = 'Check-in thành công lúc ' . $now->format('H:i:s');
        if ($isLate) {
            $message .= '. Bạn đã đi trễ ' . $lateMinutes . ' phút';
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
        
        // Kiểm tra schedule_id được cung cấp
        if (!$request->has('schedule_id')) {
            return response()->json([
                'status' => 400,
                'message' => 'Vui lòng chọn ca làm việc để check-out',
            ]);
        }
        
        // Kiểm tra bản ghi chấm công
        $attendanceRecord = AttendanceRecord::where('user_id', $userId)
                                        ->where('work_date', $today)
                                        ->where('schedule_id', $request->schedule_id)
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
                'message' => 'Bạn đã check-out ca này lúc ' . $attendanceRecord->check_out_time->format('H:i:s'),
            ]);
        }
        
        // Lấy lịch làm việc liên quan
        $schedule = PartTimeSchedule::find($attendanceRecord->schedule_id);
        
        // Lấy giờ nghỉ trưa từ cấu hình
        $breakTime = SystemConfig::getValue('break_time', '12:00-13:00');
        
        // Kiểm tra xem có về sớm không
        $isEarlyLeave = false;
        $earlyLeaveMinutes = 0;
        $overtimeMinutes = 0;
        
        if ($schedule) {
            $scheduleEnd = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . date('H:i:s', strtotime($schedule->end_time)));
            $isEarlyLeave = $now->lt($scheduleEnd);
            
            if ($isEarlyLeave) {
                $earlyLeaveMinutes = $now->diffInMinutes($scheduleEnd);
            } else {
                // Tính thời gian tăng ca (nếu checkout sau giờ kết thúc)
                $overtimeMinutes = $now->diffInMinutes($scheduleEnd);
            }
        }
        
        // Nếu về sớm, kiểm tra lý do
        if ($isEarlyLeave && (!$request->filled('early_leave_reason') || trim($request->early_leave_reason) === '')) {
            return response()->json([
                'status' => 422,
                'message' => 'Bạn về sớm, vui lòng cung cấp lý do',
                'require_reason' => true,
                'is_early_leave' => true,
                'early_leave_minutes' => $earlyLeaveMinutes
            ]);
        }
        
        // Cập nhật bản ghi
        $attendanceRecord->check_out_time = $now;
        $attendanceRecord->early_leave_minutes = $isEarlyLeave ? $earlyLeaveMinutes : 0;
        
        // Lưu lý do về sớm nếu có
        if ($isEarlyLeave && $request->filled('early_leave_reason')) {
            $attendanceRecord->early_leave_reason = $request->early_leave_reason;
        }
        
        // Tính giờ làm việc
        $attendanceRecord->total_hours = AttendanceRecord::calculateTotalHours(
            $attendanceRecord->check_in_time,
            $attendanceRecord->check_out_time,
            $breakTime
        );
        
        // Tính giờ tăng ca nếu có
        if (!$isEarlyLeave && $overtimeMinutes > 0) {
            $attendanceRecord->overtime_hours = round($overtimeMinutes / 60, 2);
        }
        
        // Tính giờ làm việc hợp lệ (không tính tăng ca)
        if ($isEarlyLeave) {
            // Nếu về sớm, valid_hours = total_hours
            $attendanceRecord->valid_hours = $attendanceRecord->total_hours;
        } else {
            // Nếu không về sớm, valid_hours là thời gian từ check-in đến schedule_end
            $validEndTime = $scheduleEnd->lt($now) ? $scheduleEnd : $now;
            $attendanceRecord->valid_hours = AttendanceRecord::calculateTotalHours(
                $attendanceRecord->check_in_time,
                $validEndTime,
                $breakTime
            );
        }
        
        // Cập nhật trạng thái
        if ($isEarlyLeave && $attendanceRecord->status == 'present') {
            $attendanceRecord->status = 'early_leave';
        } else if ($isEarlyLeave && $attendanceRecord->status == 'late') {
            $attendanceRecord->status = 'late_and_early_leave';
        }
        
        $attendanceRecord->save();
        
        LogService::saveLog([
            'action' => 'CHECK_OUT',
            'ip' => $request->getClientIp(),
            'details' => "Người dùng check-out lúc " . $now->format('Y-m-d H:i:s') . 
                         ($isEarlyLeave ? ' (về sớm ' . $earlyLeaveMinutes . ' phút)' : '') .
                         (!$isEarlyLeave && $overtimeMinutes > 0 ? ' (tăng ca ' . $overtimeMinutes . ' phút)' : ''),
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $attendanceRecord->id,
        ]);
        
        // Chuẩn bị thông báo cho người dùng
        $message = 'Check-out thành công lúc ' . $now->format('H:i:s');
        if ($isEarlyLeave) {
            $message .= '. Bạn đã về sớm ' . $earlyLeaveMinutes . ' phút';
        } else if ($overtimeMinutes > 0) {
            $message .= '. Bạn đã tăng ca ' . $overtimeMinutes . ' phút';
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
        
        $query = AttendanceRecord::with(['user', 'schedule'])
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
            ->orderBy('work_date', 'desc')
            ->orderBy('check_in_time', 'desc');
        
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
        
        // Tổng hợp thống kê
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Tính toán thống kê cho tháng hiện tại
        $monthStats = $this->calculateMonthlyStats($userId, $currentMonth, $currentYear);
        
        return view('dashboard.profile.my-timesheet', compact('attendanceRecords', 'monthStats'));
    }
    
    private function calculateMonthlyStats($userId, $month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // Lấy tất cả bản ghi chấm công trong tháng
        $monthAttendances = AttendanceRecord::where('user_id', $userId)
                                     ->whereBetween('work_date', [$startDate, $endDate])
                                     ->get();
        
        // Tính ngày làm việc trong tháng (không tính T7, CN)
        $workingDaysInMonth = $startDate->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + 1;
        
        return [
            'month' => $month,
            'year' => $year,
            'totalDays' => $monthAttendances->count(),
            'presentDays' => $monthAttendances->whereIn('status', ['present', 'late', 'early_leave', 'late_and_early_leave'])->count(),
            'absentDays' => $monthAttendances->where('status', 'absent')->count(),
            'lateDays' => $monthAttendances->where('late_minutes', '>', 0)->count(),
            'earlyLeaveDays' => $monthAttendances->where('early_leave_minutes', '>', 0)->count(),
            'totalLateMinutes' => $monthAttendances->sum('late_minutes'),
            'totalEarlyLeaveMinutes' => $monthAttendances->sum('early_leave_minutes'),
            'totalWorkHours' => $monthAttendances->sum('total_hours'),
            'totalValidHours' => $monthAttendances->sum('valid_hours'),
            'totalOvertimeHours' => $monthAttendances->sum('overtime_hours'),
            'workingDaysInMonth' => $workingDaysInMonth,
        ];
    }
    
    public function updateAttendance(Request $request)
    {
        $validator = \App\Services\ValidatorService::make($request, [
            'id' => 'required|exists:tbl_attendance_records,id',
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'status' => 'nullable|in:present,absent,late,early_leave,late_and_early_leave',
            'note' => 'nullable|string',
            'valid_hours' => 'nullable|numeric|min:0',
            'late_minutes' => 'nullable|numeric|min:0',
            'early_leave_minutes' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'late_reason' => 'nullable|string',
            'early_leave_reason' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $attendanceRecord = AttendanceRecord::findOrFail($request->id);
        
        // Ghi log thay đổi trước
        $changes = [];
        
        // Cập nhật các trường nếu có
        if ($request->filled('check_in_time')) {
            $oldValue = $attendanceRecord->check_in_time ? $attendanceRecord->check_in_time->format('H:i:s') : null;
            $changes['check_in_time'] = ['old' => $oldValue, 'new' => $request->check_in_time];
            
            $attendanceRecord->check_in_time = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $attendanceRecord->work_date->format('Y-m-d') . ' ' . $request->check_in_time
            );
        }
        
        if ($request->filled('check_out_time')) {
            $oldValue = $attendanceRecord->check_out_time ? $attendanceRecord->check_out_time->format('H:i:s') : null;
            $changes['check_out_time'] = ['old' => $oldValue, 'new' => $request->check_out_time];
            
            $attendanceRecord->check_out_time = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $attendanceRecord->work_date->format('Y-m-d') . ' ' . $request->check_out_time
            );
            
            // Tính lại tổng giờ làm việc
            if ($attendanceRecord->check_in_time && $attendanceRecord->check_out_time) {
                $breakTime = SystemConfig::getValue('break_time', '12:00-13:00');
                $oldValue = $attendanceRecord->total_hours;
                
                $newTotalHours = AttendanceRecord::calculateTotalHours(
                    $attendanceRecord->check_in_time,
                    $attendanceRecord->check_out_time,
                    $breakTime
                );
                
                $attendanceRecord->total_hours = $newTotalHours;
                $changes['total_hours'] = ['old' => $oldValue, 'new' => $newTotalHours];
            }
        }
        
        if ($request->filled('status')) {
            $changes['status'] = ['old' => $attendanceRecord->status, 'new' => $request->status];
            $attendanceRecord->status = $request->status;
        }
        
        if ($request->filled('note')) {
            $changes['note'] = ['old' => $attendanceRecord->note, 'new' => $request->note];
            $attendanceRecord->note = $request->note;
        }
        
        if ($request->filled('valid_hours')) {
            $changes['valid_hours'] = ['old' => $attendanceRecord->valid_hours, 'new' => $request->valid_hours];
            $attendanceRecord->valid_hours = $request->valid_hours;
        }
        
        if ($request->filled('late_minutes')) {
            $changes['late_minutes'] = ['old' => $attendanceRecord->late_minutes, 'new' => $request->late_minutes];
            $attendanceRecord->late_minutes = $request->late_minutes;
        }
        
        if ($request->filled('early_leave_minutes')) {
            $changes['early_leave_minutes'] = ['old' => $attendanceRecord->early_leave_minutes, 'new' => $request->early_leave_minutes];
            $attendanceRecord->early_leave_minutes = $request->early_leave_minutes;
        }
        
        if ($request->filled('overtime_hours')) {
            $changes['overtime_hours'] = ['old' => $attendanceRecord->overtime_hours, 'new' => $request->overtime_hours];
            $attendanceRecord->overtime_hours = $request->overtime_hours;
        }
        
        if ($request->filled('late_reason')) {
            $changes['late_reason'] = ['old' => $attendanceRecord->late_reason, 'new' => $request->late_reason];
            $attendanceRecord->late_reason = $request->late_reason;
        }
        
        if ($request->filled('early_leave_reason')) {
            $changes['early_leave_reason'] = ['old' => $attendanceRecord->early_leave_reason, 'new' => $request->early_leave_reason];
            $attendanceRecord->early_leave_reason = $request->early_leave_reason;
        }
        
        $attendanceRecord->save();
        
        LogService::saveLog([
            'action' => 'UPDATE_ATTENDANCE',
            'ip' => $request->getClientIp(),
            'details' => "Cập nhật bản ghi chấm công #" . $attendanceRecord->id . " - Chi tiết thay đổi: " . json_encode($changes, JSON_UNESCAPED_UNICODE),
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $attendanceRecord->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật bản ghi chấm công thành công',
        ]);
    }

    public function markAbsent(Request $request)
    {
        $validator = \App\Services\ValidatorService::make($request, [
            'user_id' => 'required|exists:tbl_users,id',
            'schedule_id' => 'required|exists:tbl_part_time_schedules,id', // Yêu cầu chỉ định cụ thể ca
            'note' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        // Lấy thông tin lịch làm việc
        $schedule = PartTimeSchedule::find($request->schedule_id);
        
        if (!$schedule || $schedule->user_id != $request->user_id) {
            return response()->json([
                'status' => 422,
                'message' => 'Lịch làm việc không hợp lệ hoặc không thuộc về nhân viên này',
            ]);
        }
        
        // Kiểm tra đã có bản ghi chấm công chưa
        $existingRecord = AttendanceRecord::where('schedule_id', $request->schedule_id)
                                    ->first();
        
        if ($existingRecord) {
            return response()->json([
                'status' => 422,
                'message' => 'Đã có bản ghi chấm công cho ca này',
            ]);
        }
        
        // Tạo bản ghi vắng mặt mới
        $attendanceRecord = AttendanceRecord::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'work_date' => $schedule->schedule_date,
            'status' => 'absent',
            'total_hours' => 0,
            'valid_hours' => 0,
            'note' => $request->note ?: 'Vắng mặt không báo trước - ' . 
                      formatDateTime($schedule->start_time, 'H:i') . ' đến ' . formatDateTime($schedule->end_time, 'H:i')
        ]);
        
        LogService::saveLog([
            'action' => 'MARK_ABSENT',
            'ip' => $request->getClientIp(),
            'details' => "Đánh dấu vắng mặt cho nhân viên ca " . 
                         formatDateTime($schedule->start_time, 'H:i') . "-" . formatDateTime($schedule->end_time, 'H:i') . 
                         " ngày " . formatDateTime($schedule->schedule_date, 'd/m/Y'),
            'fk_key' => 'tbl_attendance_records|id',
            'fk_value' => $attendanceRecord->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã đánh dấu vắng mặt thành công',
        ]);
    }
    
    public function getAttendanceDetail(Request $request, $id)
    {
        $attendance = AttendanceRecord::with(['user', 'schedule'])->findOrFail($id);
        
        $logs = null;
        if (hasPermission('view-logs')) {
            // Lấy lịch sử chỉnh sửa từ activity_logs
            $logs = ActivityLogs::where('fk_key', 'tbl_attendance_records|id')
                                     ->where('fk_value', $id)
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        }
        
        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.timekeeping.detail-modal', compact('attendance', 'logs'))->render(),
        ]);
    }
}