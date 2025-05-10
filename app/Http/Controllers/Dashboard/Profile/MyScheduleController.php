<?php

namespace App\Http\Controllers\Dashboard\Profile;

use App\Http\Controllers\Controller;
use App\Models\PartTimeSchedule;
use App\Models\SystemConfig;
use App\Services\LogService;
use App\Services\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MyScheduleController extends Controller
{
    /**
     * Hiển thị trang lịch làm việc cá nhân
     */
    public function index(Request $request)
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
            } elseif ($schedule->status == 'cancel_requested') {
                $eventColor = '#0dcaf0'; // info - yêu cầu hủy
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

    /**
     * Đăng ký lịch làm việc mới (từ người dùng)
     */
    public function createSchedule(Request $request)
    {
        // Validate đầu vào
        $validator = ValidatorService::make($request, [
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

        // Chuyển đổi định dạng ngày
        $scheduleDate = Carbon::createFromFormat('d-m-Y', $request->schedule_date)->format('Y-m-d');
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        // Kiểm tra ngày đăng ký phải là ngày tương lai
        if (Carbon::parse($scheduleDate)->lt(Carbon::tomorrow())) {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ được đăng ký lịch cho ngày mai trở đi',
            ]);
        }

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

        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];

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

        // Kiểm tra cấu hình tự động duyệt
        $autoApprove = SystemConfig::getValue('auto_approve_schedule', 0);
        $status = $autoApprove ? 'approved' : 'pending';

        $schedule = PartTimeSchedule::create([
            'user_id' => $userId,
            'schedule_date' => $scheduleDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_hours' => $totalHours,
            'note' => $request->note,
            'status' => $status,
            'approver_id' => $autoApprove ? Session::get(ACCOUNT_CURRENT_SESSION)['id'] : null,
            'approval_time' => $autoApprove ? Carbon::now() : null
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
            'message' => 'Tạo lịch làm việc thành công' . ($autoApprove ? '' : ', đang chờ duyệt'),
        ]);
    }

    /**
     * Hủy lịch làm việc chưa được duyệt
     */
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
        
        // Thực hiện hủy lịch (xóa)
        $schedule->delete();
        
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

    /**
     * Yêu cầu hủy lịch làm việc đã được duyệt
     */
    public function requestCancelSchedule(Request $request)
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

        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $schedule = PartTimeSchedule::findOrFail($request->id);
        
        // Kiểm tra quyền: chỉ chủ sở hữu mới được yêu cầu hủy
        if ($schedule->user_id != $userId) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền yêu cầu hủy lịch này',
            ]);
        }
        
        // Kiểm tra trạng thái: chỉ lịch đã duyệt mới được yêu cầu hủy
        if ($schedule->status != 'approved') {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ có thể yêu cầu hủy lịch đã được duyệt',
            ]);
        }
        
        // Kiểm tra ngày: chỉ được hủy lịch trong tương lai
        if (Carbon::parse($schedule->schedule_date)->lt(Carbon::today())) {
            return response()->json([
                'status' => 422,
                'message' => 'Không thể hủy lịch làm việc đã qua',
            ]);
        }
        
        // Cập nhật trạng thái và ghi chú
        $schedule->update([
            'status' => 'cancel_requested',
            'note' => $schedule->note . "\nYêu cầu hủy: " . $request->reason
        ]);
        
        LogService::saveLog([
            'action' => 'REQUEST_CANCEL_WORK_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Yêu cầu hủy lịch làm việc ngày " . formatDateTime($schedule->schedule_date, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã gửi yêu cầu hủy lịch làm việc. Vui lòng đợi phê duyệt',
        ]);
    }

    /**
     * Lấy chi tiết lịch làm việc
     */
    public function getScheduleDetail($id)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        // Lấy thông tin lịch làm việc và kiểm tra quyền
        $schedule = PartTimeSchedule::with(['user', 'approver'])->findOrFail($id);
        
        if ($schedule->user_id != $userId) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền xem chi tiết lịch này',
            ]);
        }
        
        $statusMap = [
            'pending' => ['text' => 'Chờ duyệt', 'class' => 'warning'],
            'approved' => ['text' => 'Đã duyệt', 'class' => 'success'],
            'rejected' => ['text' => 'Từ chối', 'class' => 'danger'],
            'cancel_requested' => ['text' => 'Yêu cầu hủy', 'class' => 'info'],
        ];
        
        $status = $statusMap[$schedule->status] ?? ['text' => ucfirst($schedule->status), 'class' => 'gray'];
        
        $html = view('dashboard.profile.schedule-detail-modal', compact('schedule', 'status'))->render();
        
        return response()->json([
            'status' => 200,
            'content' => $html,
        ]);
    }
}