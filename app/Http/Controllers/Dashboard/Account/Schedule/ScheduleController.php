<?php

namespace App\Http\Controllers\Dashboard\Account\Schedule;

use App\Http\Controllers\Controller;
use App\Models\PartTimeSchedule;
use App\Models\SystemConfig;
use App\Models\User;
use App\Services\LogService;
use App\Services\NotificationService;
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
        
        return view('dashboard.account.schedule.index', compact('users', 'stats'));
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

        $schedules = $paginationResult['data'];

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.schedule.ajax-index', ['data' => $schedules, 'offset' => $offset])->render(),
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
            'start_time' => 'required:s',
            'end_time' => 'required:s|after:start_time',
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

        // Lấy thống kê mới
        $stats = [
            'totalSchedules' => PartTimeSchedule::count(),
            'approvedSchedules' => PartTimeSchedule::approved()->count(),
            'pendingSchedules' => PartTimeSchedule::pending()->count(),
            'rejectedSchedules' => PartTimeSchedule::rejected()->count(),
        ];

        $formattedDate = formatDateTime($scheduleDate, 'd/m/Y');
        $formattedTime = formatDateTime($startTime, 'H:i') . ' - ' . formatDateTime($endTime, 'H:i');
        
        NotificationService::send(
            $userId,
            'Lịch làm việc mới',
            'Quản lý đã tạo lịch làm việc ngày ' . $formattedDate . ' (' . $formattedTime . ') cho bạn.',
            'schedule',
            route('dashboard.profile.my-schedule'),
            'ki-calendar-tick',
            'success',
            5,
            null
        );

        return response()->json([
            'status' => 200,
            'message' => 'Tạo lịch làm việc thành công',
            'stats' => $stats,
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

        $schedule = PartTimeSchedule::with('user')->findOrFail($request->id);

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

         $formattedDate = formatDateTime($schedule->schedule_date, 'd/m/Y');
        $formattedTime = formatDateTime($schedule->start_time, 'H:i') . ' - ' . formatDateTime($schedule->end_time, 'H:i');
        
        if ($request->status === 'approved') {
            NotificationService::send(
                $schedule->user_id,
                'Lịch làm việc đã được duyệt',
                'Lịch làm việc ngày ' . $formattedDate . ' (' . $formattedTime . ') của bạn đã được duyệt.',
                'schedule',
                route('dashboard.profile.my-schedule'),
                'ki-check',
                'success',
                5,
                null
            );
        } else {
            $reason = $request->filled('note') ? "Lý do: " . $request->note : '';
            NotificationService::send(
                $schedule->user_id,
                'Lịch làm việc bị từ chối',
                'Lịch làm việc ngày ' . $formattedDate . ' (' . $formattedTime . ') của bạn đã bị từ chối. ' . $reason,
                'schedule',
                route('dashboard.profile.my-schedule'),
                'ki-cross',
                'danger',
                5,
                null
            );
        }

        // Lấy HTML cho dòng đã cập nhật
        $rowHtml = view('dashboard.account.schedule.schedule-row', ['schedule' => $schedule, 'index' => 0])->render();
        
        // Lấy thống kê mới
        $stats = [
            'totalSchedules' => PartTimeSchedule::count(),
            'approvedSchedules' => PartTimeSchedule::approved()->count(),
            'pendingSchedules' => PartTimeSchedule::pending()->count(),
            'rejectedSchedules' => PartTimeSchedule::rejected()->count(),
        ];

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật trạng thái lịch làm việc thành công',
            'row_html' => $rowHtml,
            'stats' => $stats
        ]);
    }

    /**
     * Phê duyệt hàng loạt
     */
    public function batchApprove(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_part_time_schedules,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $ids = $request->ids;
        $approvedCount = 0;
        $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $now = Carbon::now();

        foreach ($ids as $id) {
            $schedule = PartTimeSchedule::find($id);
            
            if ($schedule && $schedule->status === 'pending') {
                $schedule->update([
                    'status' => 'approved',
                    'approver_id' => $currentUserId,
                    'approval_time' => $now
                ]);
                
                $approvedCount++;
                
                LogService::saveLog([
                    'action' => 'BATCH_APPROVE_WORK_SCHEDULE',
                    'ip' => $request->getClientIp(),
                    'details' => "Phê duyệt hàng loạt lịch làm việc #" . $schedule->id,
                    'fk_key' => 'tbl_part_time_schedules|id',
                    'fk_value' => $schedule->id,
                ]);

                $formattedDate = formatDateTime($schedule->schedule_date, 'd/m/Y');
                $formattedTime = formatDateTime($schedule->start_time, 'H:i') . ' - ' . formatDateTime($schedule->end_time, 'H:i');
                
                NotificationService::send(
                    $schedule->user_id,
                    'Lịch làm việc đã được duyệt',
                    'Lịch làm việc ngày ' . $formattedDate . ' (' . $formattedTime . ') của bạn đã được duyệt.',
                    'schedule',
                    route('dashboard.profile.my-schedule'),
                    'ki-check',
                    'success',
                    5,
                    null
                );
            }
        }

        // Tính toán lại thống kê
        $stats = [
            'totalSchedules' => PartTimeSchedule::count(),
            'approvedSchedules' => PartTimeSchedule::approved()->count(),
            'pendingSchedules' => PartTimeSchedule::pending()->count(),
            'rejectedSchedules' => PartTimeSchedule::rejected()->count(),
        ];

        return response()->json([
            'status' => 200,
            'message' => "Đã phê duyệt thành công $approvedCount lịch làm việc",
            'stats' => $stats
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

        $formattedDate = formatDateTime($scheduleDate, 'd/m/Y');
        $formattedTime = formatDateTime($schedule->start_time, 'H:i') . ' - ' . formatDateTime($schedule->end_time, 'H:i');
        
        NotificationService::send(
            $schedule->user_id,
            'Yêu cầu hủy lịch đã được duyệt',
            'Yêu cầu hủy lịch làm việc ngày ' . $formattedDate . ' (' . $formattedTime . ') của bạn đã được duyệt.',
            'schedule',
            route('dashboard.profile.my-schedule'),
            'ki-check',
            'success',
            5,
            null
        );
        
        // Xóa lịch
        $schedule->delete();
        
        LogService::saveLog([
            'action' => 'APPROVE_CANCEL_WORK_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Phê duyệt hủy lịch làm việc ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $scheduleId,
        ]);
        
        // Tính toán lại thống kê
        $stats = [
            'totalSchedules' => PartTimeSchedule::count(),
            'approvedSchedules' => PartTimeSchedule::approved()->count(),
            'pendingSchedules' => PartTimeSchedule::pending()->count(),
            'rejectedSchedules' => PartTimeSchedule::rejected()->count(),
        ];
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã phê duyệt yêu cầu hủy lịch làm việc',
            'stats' => $stats
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

        $schedule = PartTimeSchedule::with('user')->findOrFail($request->id);
        
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

        $formattedDate = formatDateTime($schedule->schedule_date, 'd/m/Y');
        $formattedTime = formatDateTime($schedule->start_time, 'H:i') . ' - ' . formatDateTime($schedule->end_time, 'H:i');
        
        NotificationService::send(
            $schedule->user_id,
            'Yêu cầu hủy lịch bị từ chối',
            'Yêu cầu hủy lịch làm việc ngày ' . $formattedDate . ' (' . $formattedTime . ') của bạn đã bị từ chối. Lý do: ' . $request->reason,
            'schedule',
            route('dashboard.profile.my-schedule'),
            'ki-cross',
            'danger',
            5,
            null
        );
        
        // Lấy HTML cho dòng đã cập nhật
        $rowHtml = view('dashboard.account.schedule.schedule-row', ['schedule' => $schedule, 'index' => 0])->render();
        
        // Tính toán lại thống kê
        $stats = [
            'totalSchedules' => PartTimeSchedule::count(),
            'approvedSchedules' => PartTimeSchedule::approved()->count(),
            'pendingSchedules' => PartTimeSchedule::pending()->count(),
            'rejectedSchedules' => PartTimeSchedule::rejected()->count(),
        ];
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã từ chối yêu cầu hủy lịch làm việc',
            'row_html' => $rowHtml,
            'stats' => $stats
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

        $formattedDate = formatDateTime($scheduleDate, 'd/m/Y');
        $formattedTime = formatDateTime($schedule->start_time, 'H:i') . ' - ' . formatDateTime($schedule->end_time, 'H:i');
        
        NotificationService::send(
            $schedule->user_id,
            'Lịch làm việc đã bị xóa',
            'Lịch làm việc ngày ' . $formattedDate . ' (' . $formattedTime . ') của bạn đã bị xóa bởi quản lý.',
            'schedule',
            route('dashboard.profile.my-schedule'),
            'ki-trash',
            'danger',
            5,
            null
        );
        
        $schedule->delete();
        
        LogService::saveLog([
            'action' => 'DELETE_PART_TIME_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Xóa lịch làm việc part-time #" . $scheduleId . " ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $scheduleId,
        ]);
        
        // Tính toán lại thống kê
        $stats = [
            'totalSchedules' => PartTimeSchedule::count(),
            'approvedSchedules' => PartTimeSchedule::approved()->count(),
            'pendingSchedules' => PartTimeSchedule::pending()->count(),
            'rejectedSchedules' => PartTimeSchedule::rejected()->count(),
        ];
        
        return response()->json([
            'status' => 200,
            'message' => 'Xóa lịch làm việc thành công',
            'stats' => $stats
        ]);
    }
}