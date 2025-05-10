<?php

namespace App\Http\Controllers\Dashboard\Account\Schedule;

use App\Http\Controllers\Controller;
use App\Models\PartTimeSchedule;
use App\Models\SystemConfig;
use App\Models\User;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ScheduleController extends Controller
{
    /**
     * Hiển thị trang quản lý lịch làm việc
     */
    public function schedule()
    {
        // Lấy danh sách nhân viên
        $users = User::where('is_active', 1)->get();
        
        // Tính toán thống kê lịch làm việc
        $stats = [
            'totalSchedules' => PartTimeSchedule::count(),
            'approvedSchedules' => PartTimeSchedule::approved()->count(),
            'pendingSchedules' => PartTimeSchedule::pending()->count(),
            'rejectedSchedules' => PartTimeSchedule::rejected()->count(),
        ];
        
        // Chuẩn bị dữ liệu cho fullcalendar
        $calendarEvents = [];
        $allSchedules = PartTimeSchedule::with('user')->get();

        foreach ($allSchedules as $schedule) {
            $eventColor = '#3788d8'; // Màu mặc định - pending
            
            if ($schedule->status == 'approved') {
                $eventColor = '#198754'; // success
            } elseif ($schedule->status == 'rejected') {
                $eventColor = '#dc3545'; // danger
            } elseif ($schedule->status == 'cancel_requested') {
                $eventColor = '#0dcaf0'; // info
            }
            
            // Lấy tên nhân viên
            $userName = $schedule->user ? $schedule->user->name : 'Nhân viên';
            
            // Định dạng đúng cho ISO 8601
            $scheduleDate = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
            $startTime = Carbon::parse($schedule->start_time)->format('H:i:s');
            $endTime = Carbon::parse($schedule->end_time)->format('H:i:s');
            
            $calendarEvents[] = [
                'id' => $schedule->id,
                'title' => $userName . ': ' . substr($startTime, 0, 5) . ' - ' . substr($endTime, 0, 5),
                'start' => $scheduleDate . 'T' . $startTime,
                'end' => $scheduleDate . 'T' . $endTime,
                'color' => $eventColor,
                'extendedProps' => [
                    'status' => $schedule->status,
                    'user_id' => $schedule->user_id,
                    'user_name' => $userName
                ]
            ];
        }
        
        return view('dashboard.account.schedule.index', compact('users', 'stats', 'calendarEvents'));
    }

    /**
     * Lấy dữ liệu thống kê
     */
    public function getStatistics()
    {
        $totalUsers = User::join('tbl_part_time_schedules', 'tbl_users.id', '=', 'tbl_part_time_schedules.user_id')
                        ->distinct('tbl_users.id')
                        ->count('tbl_users.id');
        
        $totalSchedules = PartTimeSchedule::count();
        $pendingSchedules = PartTimeSchedule::pending()->count();
        $totalHours = PartTimeSchedule::approved()->sum('total_hours');
        
        return response()->json([
            'status' => 200,
            'statistics' => [
                'totalUsers' => $totalUsers,
                'totalSchedules' => $totalSchedules,
                'pendingSchedules' => $pendingSchedules,
                'totalHours' => number_format($totalHours, 2)
            ]
        ]);
    }

    /**
     * Lấy danh sách nhân viên có lịch part-time
     */
    public function getUsersList()
    {
        $users = User::join('tbl_part_time_schedules', 'tbl_users.id', '=', 'tbl_part_time_schedules.user_id')
                    ->distinct('tbl_users.id')
                    ->select('tbl_users.id', 'tbl_users.name')
                    ->get();
        
        return response()->json([
            'status' => 200,
            'users' => $users
        ]);
    }

    /**
     * Lấy dữ liệu lịch cho FullCalendar
     */
    public function getCalendarData(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        list($year, $month) = explode('-', $month);
        
        // Lấy ngày đầu và cuối của tháng
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
        
        // Lấy lịch trong khoảng thời gian
        $schedules = PartTimeSchedule::with('user')
                                ->whereBetween('schedule_date', [$startDate, $endDate])
                                ->get();
        
        $events = [];
        
        foreach ($schedules as $schedule) {
            $eventColor = '#3788d8'; // Màu mặc định - pending
            
            if ($schedule->status == 'approved') {
                $eventColor = '#198754'; // success
            } elseif ($schedule->status == 'rejected') {
                $eventColor = '#dc3545'; // danger
            } elseif ($schedule->status == 'cancel_requested') {
                $eventColor = '#0dcaf0'; // info
            }
            
            // Lấy tên nhân viên
            $userName = $schedule->user ? $schedule->user->name : 'Nhân viên';
            
            // Định dạng đúng cho ISO 8601
            $scheduleDate = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
            $startTime = Carbon::parse($schedule->start_time)->format('H:i:s');
            $endTime = Carbon::parse($schedule->end_time)->format('H:i:s');
            
            $events[] = [
                'id' => $schedule->id,
                'title' => $userName . ': ' . substr($startTime, 0, 5) . ' - ' . substr($endTime, 0, 5),
                'start' => $scheduleDate . 'T' . $startTime,
                'end' => $scheduleDate . 'T' . $endTime,
                'color' => $eventColor,
                'extendedProps' => [
                    'status' => $schedule->status,
                    'user_id' => $schedule->user_id,
                    'user_name' => $userName
                ]
            ];
        }
        
        return response()->json([
            'status' => 200,
            'events' => $events
        ]);
    }

    /**
     * Lấy dữ liệu danh sách lịch làm việc
     */
    public function scheduleData(Request $request)
    {
        $currentPage = $request->input('page', 1);

        $query = PartTimeSchedule::with(['user', 'approver'])
            ->when($request->input('filter.user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->input('filter.date_from'), function ($query, $dateFrom) {
                $query->where('schedule_date', '>=', $dateFrom);
            })
            ->when($request->input('filter.date_to'), function ($query, $dateTo) {
                $query->where('schedule_date', '<=', $dateTo);
            })
            ->when($request->input('filter.status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('schedule_date', 'desc');

        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'user' => [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                ],
                'schedule_date' => formatDateTime($item->schedule_date, 'd/m/Y'),
                'start_time' => formatDateTime($item->start_time, 'H:i'),
                'end_time' => formatDateTime($item->end_time, 'H:i'),
                'total_hours' => $item->total_hours,
                'status' => $item->status,
                'status_text' => $item->getStatusText(),
                'note' => $item->note,
                'approver' => $item->approver ? [
                    'id' => $item->approver->id,
                    'name' => $item->approver->name,
                ] : null,
                'approval_time' => $item->approval_time ? formatDateTime($item->approval_time, 'd/m/Y H:i:s') : '-',
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.schedule.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    /**
     * Tạo lịch làm việc mới (dành cho quản lý)
     */
    public function createSchedule(Request $request)
    {
        // Validate đầu vào
        $validator = ValidatorService::make($request, [
            'schedule_date' => 'required|date_format:d-m-Y',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'user_id' => 'required|exists:tbl_users,id',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Chuyển đổi định dạng ngày
        $scheduleDate = Carbon::createFromFormat('d-m-Y', $request->schedule_date)->format('Y-m-d');
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // Tính tổng giờ làm việc
        $totalHours = PartTimeSchedule::calculateTotalHours(
            $scheduleDate . ' ' . $startTime,
            $scheduleDate . ' ' . $endTime
        );
        
        // Lấy cấu hình số giờ tối thiểu
        $minWorkHours = SystemConfig::getValue('min_work_hours', 4);
        
        // Kiểm tra số giờ đăng ký có đủ tối thiểu không
        if ($totalHours < $minWorkHours) {
            return response()->json([
                'status' => 422,
                'message' => "Số giờ làm việc tối thiểu phải là {$minWorkHours} giờ",
            ]);
        }

        $userId = $request->user_id;

        // Kiểm tra trùng lịch
        $existingSchedule = PartTimeSchedule::where('user_id', $userId)
            ->where('schedule_date', $scheduleDate)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                        ->where('end_time', '>=', $endTime);
                    });
            })
            ->first();

        if ($existingSchedule) {
            return response()->json([
                'status' => 422,
                'message' => 'Đã có lịch làm việc trong khoảng thời gian này',
            ]);
        }

        // Quản lý tạo lịch thì luôn tự động duyệt
        $schedule = PartTimeSchedule::create([
            'user_id' => $userId,
            'schedule_date' => $scheduleDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_hours' => $totalHours,
            'note' => $request->note,
            'status' => 'approved',
            'approver_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            'approval_time' => Carbon::now()
        ]);

        LogService::saveLog([
            'action' => 'CREATE_WORK_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Tạo lịch làm việc ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Tạo lịch làm việc thành công',
            'schedule' => [
                'id' => $schedule->id,
                'user_id' => $schedule->user_id,
                'schedule_date' => $scheduleDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'approved'
            ]
        ]);
    }

    /**
     * Phê duyệt/từ chối lịch làm việc
     */
    public function updateScheduleStatus(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_part_time_schedules,id',
            'status' => 'required|in:approved,rejected',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $schedule = PartTimeSchedule::findOrFail($request->id);

        // Chỉ cập nhật nếu đang ở trạng thái pending
        if ($schedule->status !== 'pending') {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ có thể cập nhật trạng thái cho lịch đang chờ duyệt',
            ]);
        }

        $schedule->update([
            'status' => $request->status,
            'note' => $request->filled('note') ? $request->note : $schedule->note,
            'approver_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            'approval_time' => Carbon::now(),
        ]);

        LogService::saveLog([
            'action' => 'UPDATE_PART_TIME_SCHEDULE_STATUS',
            'ip' => $request->getClientIp(),
            'details' => "Cập nhật trạng thái lịch làm việc part-time #" . $schedule->id . " thành " . $request->status,
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật trạng thái lịch làm việc thành công',
        ]);
    }

    /**
     * Phê duyệt yêu cầu hủy lịch
     */
    public function approveCancelRequest(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_part_time_schedules,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $schedule = PartTimeSchedule::findOrFail($request->id);
        
        // Chỉ phê duyệt nếu đang ở trạng thái yêu cầu hủy
        if ($schedule->status !== 'cancel_requested') {
            return response()->json([
                'status' => 422,
                'message' => 'Lịch không ở trạng thái yêu cầu hủy',
            ]);
        }
        
        // Lưu ID trước khi xóa để ghi log
        $scheduleId = $schedule->id;
        $scheduleDate = $schedule->schedule_date;
        
        // Xóa lịch
        $schedule->delete();
        
        LogService::saveLog([
            'action' => 'APPROVE_CANCEL_WORK_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Phê duyệt hủy lịch làm việc ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $scheduleId,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã phê duyệt yêu cầu hủy lịch làm việc',
        ]);
    }

    /**
     * Từ chối yêu cầu hủy lịch
     */
    public function rejectCancelRequest(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_part_time_schedules,id',
            'reason' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $schedule = PartTimeSchedule::findOrFail($request->id);
        
        // Chỉ từ chối nếu đang ở trạng thái yêu cầu hủy
        if ($schedule->status !== 'cancel_requested') {
            return response()->json([
                'status' => 422,
                'message' => 'Lịch không ở trạng thái yêu cầu hủy',
            ]);
        }
        
        // Khôi phục trạng thái và cập nhật ghi chú
        $schedule->update([
            'status' => 'approved',
            'note' => $schedule->note . "\nTừ chối hủy: " . $request->reason
        ]);
        
        LogService::saveLog([
            'action' => 'REJECT_CANCEL_WORK_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Từ chối hủy lịch làm việc ngày " . formatDateTime($schedule->schedule_date, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã từ chối yêu cầu hủy lịch làm việc',
        ]);
    }

    /**
     * Lấy chi tiết lịch làm việc
     */
    public function getScheduleDetail($id)
    {
        $schedule = PartTimeSchedule::with(['user', 'approver'])->findOrFail($id);
        
        $statusMap = [
            'pending' => ['text' => 'Chờ duyệt', 'class' => 'warning'],
            'approved' => ['text' => 'Đã duyệt', 'class' => 'success'],
            'rejected' => ['text' => 'Từ chối', 'class' => 'danger'],
            'cancel_requested' => ['text' => 'Yêu cầu hủy', 'class' => 'info'],
        ];
        
        $status = $statusMap[$schedule->status] ?? ['text' => ucfirst($schedule->status), 'class' => 'gray'];
        
        $html = view('dashboard.account.schedule.detail-modal', compact('schedule', 'status'))->render();
        
        return response()->json([
            'status' => 200,
            'content' => $html,
        ]);
    }

    /**
     * Lấy dữ liệu để chỉnh sửa lịch làm việc
     */
    public function getScheduleEdit($id)
    {
        $schedule = PartTimeSchedule::findOrFail($id);
        
        return response()->json([
            'status' => 200,
            'schedule' => [
                'id' => $schedule->id,
                'user_id' => $schedule->user_id,
                'schedule_date' => formatDateTime($schedule->schedule_date, 'd-m-Y'),
                'start_time' => formatDateTime($schedule->start_time, 'H:i'),
                'end_time' => formatDateTime($schedule->end_time, 'H:i'),
                'note' => $schedule->note,
            ],
        ]);
    }

    /**
     * Xóa lịch làm việc (chỉ dành cho quản lý)
     */
    public function deleteSchedule(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_part_time_schedules,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $schedule = PartTimeSchedule::findOrFail($request->id);
        
        // Lưu thông tin trước khi xóa để ghi log
        $scheduleDate = $schedule->schedule_date;
        $scheduleId = $schedule->id;
        
        $schedule->delete();
        
        LogService::saveLog([
            'action' => 'DELETE_PART_TIME_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Xóa lịch làm việc part-time #" . $scheduleId . " ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $scheduleId,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Xóa lịch làm việc thành công',
        ]);
    }
}