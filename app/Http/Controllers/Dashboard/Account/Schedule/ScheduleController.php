<?php

namespace App\Http\Controllers\Dashboard\Account\Schedule;

use App\Http\Controllers\Controller;
use App\Models\PartTimeSchedule;
use App\Models\User;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ScheduleController extends Controller
{
    public function schedule()
    {
        // Lấy danh sách nhân viên
        $users = User::where('is_active', 1)->get();
        
        // Tính toán thống kê lịch làm việc
        $allSchedules = PartTimeSchedule::all();
        
        $stats = [
            'totalSchedules' => $allSchedules->count(),
            'approvedSchedules' => $allSchedules->where('status', 'approved')->count(),
            'pendingSchedules' => $allSchedules->where('status', 'pending')->count(),
            'rejectedSchedules' => $allSchedules->where('status', 'rejected')->count(),
        ];
        
        // Chuẩn bị dữ liệu cho fullcalendar
        $calendarEvents = [];

        foreach ($allSchedules as $schedule) {
            $eventColor = '#3788d8'; // Màu mặc định - pending
            
            if ($schedule->status == 'approved') {
                $eventColor = '#198754'; // success
            } elseif ($schedule->status == 'rejected') {
                $eventColor = '#dc3545'; // danger
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

    public function partTime()
    {
        return view('dashboard.account.schedule.part-time');
    }

    // Lấy dữ liệu thống kê
    public function getStatistics()
    {
        $totalUsers = User::join('tbl_part_time_schedules', 'tbl_users.id', '=', 'tbl_part_time_schedules.user_id')
                        ->distinct('tbl_users.id')
                        ->count('tbl_users.id');
        
        $totalSchedules = PartTimeSchedule::count();
        $pendingSchedules = PartTimeSchedule::where('status', 'pending')->count();
        $totalHours = PartTimeSchedule::where('status', 'approved')->sum('total_hours');
        
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

    // Lấy danh sách nhân viên có lịch part-time
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

    // Lấy dữ liệu lịch cho FullCalendar
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
                'status_text' => $this->getStatusText($item->status),
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

    public function mySchedule(Request $request)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        // Lấy tháng hiện tại hoặc tháng được chọn
        $selectedMonth = $request->input('month', date('Y-m'));
        list($year, $month) = explode('-', $selectedMonth);
        
        // Lấy ngày đầu và cuối của tháng
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // Lấy danh sách lịch part-time trong tháng đã chọn
        $schedules = PartTimeSchedule::where('user_id', $userId)
            ->when($selectedMonth, function($query) use ($startDate, $endDate) {
                $query->whereBetween('schedule_date', [$startDate, $endDate]);
            })
            ->orderBy('schedule_date', 'desc')
            ->paginate(31);
        
        // Tính toán thống kê
        $allSchedules = PartTimeSchedule::where('user_id', $userId)->get();
        
        $partTimeStats = [
            'total' => $allSchedules->count(),
            'approved' => $allSchedules->where('status', 'approved')->count(),
            'pending' => $allSchedules->where('status', 'pending')->count(),
            'totalHours' => $allSchedules->where('status', 'approved')->sum('total_hours')
        ];
        
        // Chuẩn bị dữ liệu cho fullcalendar
        $calendarEvents = [];
        
        // Thêm lịch part-time
        foreach ($allSchedules as $schedule) {
            $eventColor = '#3788d8'; // Màu mặc định - pending
            
            if ($schedule->status == 'approved') {
                $eventColor = '#198754'; // success
            } elseif ($schedule->status == 'rejected') {
                $eventColor = '#dc3545'; // danger
            }
            
            $calendarEvents[] = [
                'id' => $schedule->id,
                'title' => 'Part-time: ' . formatDateTime($schedule->start_time, 'H:i') . ' - ' . formatDateTime($schedule->end_time, 'H:i'),
                'start' => $schedule->schedule_date . 'T' . formatDateTime($schedule->start_time, 'H:i:s'),
                'end' => $schedule->schedule_date . 'T' . formatDateTime($schedule->end_time, 'H:i:s'),
                'color' => $eventColor,
                'extendedProps' => [
                    'type' => 'part-time',
                    'status' => $schedule->status
                ]
            ];
        }
        
        return view('dashboard.profile.my-schedule', compact('schedules', 'partTimeStats', 'calendarEvents'));
    }

    public function cancelSchedule(Request $request)
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

        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $schedule = PartTimeSchedule::findOrFail($request->id);
        
        // Kiểm tra quyền: chỉ chủ sở hữu mới được hủy
        if ($schedule->user_id != $userId) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền hủy lịch này',
            ]);
        }
        
        // Chỉ được hủy khi đang ở trạng thái chờ duyệt
        if ($schedule->status !== 'pending') {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ có thể hủy lịch đang chờ duyệt',
            ]);
        }
        
        // Thực hiện hủy lịch (xóa hoặc đánh dấu là cancelled)
        $schedule->delete(); // hoặc $schedule->update(['status' => 'cancelled']);
        
        LogService::saveLog([
            'action' => 'CANCEL_PART_TIME_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Hủy lịch làm việc part-time ngày " . formatDateTime($schedule->schedule_date, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Hủy lịch làm việc thành công',
        ]);
    }

    public function createSchedule(Request $request)
    {
        $validator = \App\Services\ValidatorService::make($request, [
            'schedule_date' => 'required|date_format:d-m-Y',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $scheduleDate = Carbon::createFromFormat('d-m-Y', $request->schedule_date)->format('Y-m-d');
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        // Tính tổng giờ
        $totalHours = PartTimeSchedule::calculateTotalHours(
            $scheduleDate . ' ' . $startTime,
            $scheduleDate . ' ' . $endTime
        );

        $userId = $request->input('user_id', Session::get(ACCOUNT_CURRENT_SESSION)['id']);

        // Kiểm tra trùng lịch
        $existingSchedule = PartTimeSchedule::where('user_id', $userId)
            ->where('schedule_date', $scheduleDate)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->first();

        if ($existingSchedule) {
            return response()->json([
                'status' => 422,
                'message' => 'Đã có lịch làm việc trong khoảng thời gian này',
            ]);
        }

        $schedule = PartTimeSchedule::create([
            'user_id' => $userId,
            'schedule_date' => $scheduleDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_hours' => $totalHours,
            'note' => $request->note,
            'status' => 'pending'
        ]);

        LogService::saveLog([
            'action' => 'CREATE_PART_TIME_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Tạo lịch làm việc part-time ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Tạo lịch làm việc thành công',
        ]);
    }

    public function updateScheduleStatus(Request $request)
    {
        $validator = \App\Services\ValidatorService::make($request, [
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

    // Thêm endpoint để lấy chi tiết lịch
    public function getScheduleDetail($id)
    {
        $schedule = PartTimeSchedule::with(['user', 'approver'])->findOrFail($id);
        
        $statusMap = [
            'pending' => ['text' => 'Chờ duyệt', 'class' => 'warning'],
            'approved' => ['text' => 'Đã duyệt', 'class' => 'success'],
            'rejected' => ['text' => 'Từ chối', 'class' => 'danger'],
        ];
        
        $status = $statusMap[$schedule->status] ?? ['text' => ucfirst($schedule->status), 'class' => 'gray'];
        
        $html = view('dashboard.account.schedule.detail-modal', compact('schedule', 'status'))->render();
        
        return response()->json([
            'status' => 200,
            'content' => $html,
        ]);
    }

    // Thêm endpoint để lấy dữ liệu chỉnh sửa lịch
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

    // Thêm endpoint để xóa lịch
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

    private function getStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'Chờ duyệt';
            case 'approved':
                return 'Đã duyệt';
            case 'rejected':
                return 'Từ chối';
            default:
                return ucfirst($status);
        }
    }
}
