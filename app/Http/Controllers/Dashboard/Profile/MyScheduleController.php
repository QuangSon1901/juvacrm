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
        
        $month = $request->input('month', date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }
        list($year, $monthsp) = explode('-', $month);
        
        // Lấy ngày đầu và cuối của tháng
        $startDate = Carbon::createFromDate($year, $monthsp, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $monthsp, 1)->endOfMonth()->format('Y-m-d');
        
        // Lấy danh sách lịch làm việc của nhân viên
        $schedules = PartTimeSchedule::byUser($userId)
                        ->whereBetween('schedule_date', [$startDate, $endDate])
                        ->orderBy('schedule_date', 'desc')
                        ->orderBy('start_time', 'asc')
                        ->paginate(10);
        
        // Tính toán thống kê
        $partTimeStats = [
            'total' => PartTimeSchedule::byUser($userId)->count(),
            'approved' => PartTimeSchedule::byUser($userId)->approved()->count(),
            'pending' => PartTimeSchedule::byUser($userId)->pending()->count(),
            'totalHours' => PartTimeSchedule::byUser($userId)->approved()->sum('total_hours'),
        ];
        
        return view('dashboard.profile.my-schedule', compact('schedules', 'partTimeStats', 'month'));
    }
    
    /**
     * Tạo lịch làm việc mới
     */
    public function createSchedule(Request $request)
    {
        // Validate đầu vào
        $validator = ValidatorService::make($request, [
            'schedule_date' => 'required|date_format:d-m-Y',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        
        // Chuyển đổi định dạng ngày
        $scheduleDate = Carbon::createFromFormat('d-m-Y', $request->schedule_date)->format('Y-m-d');
        $startTime = $request->start_time.':00';
        $endTime = $request->end_time.':00';

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
                'message' => 'Bạn đã có lịch làm việc trong khoảng thời gian này',
            ]);
        }
        
        // Không cho đăng ký lịch trong quá khứ
        if (Carbon::parse($scheduleDate)->lt(Carbon::today())) {
            return response()->json([
                'status' => 422,
                'message' => 'Không thể đăng ký lịch làm việc cho ngày trong quá khứ',
            ]);
        }

        // Nhân viên tạo lịch thì sẽ ở trạng thái pending chờ duyệt
        $schedule = PartTimeSchedule::create([
            'user_id' => $userId,
            'schedule_date' => $scheduleDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_hours' => $totalHours,
            'note' => $request->note,
            'status' => 'pending',
        ]);

        LogService::saveLog([
            'action' => 'CREATE_PERSONAL_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Đăng ký lịch làm việc ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Đăng ký lịch làm việc thành công, vui lòng chờ quản lý duyệt',
        ]);
    }
    
    /**
     * Hủy lịch làm việc (cho lịch đang chờ duyệt)
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
        $schedule = PartTimeSchedule::where('id', $request->id)
                        ->where('user_id', $userId)
                        ->first();
        
        if (!$schedule) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy lịch làm việc',
            ]);
        }
        
        // Chỉ được hủy lịch đang chờ duyệt
        if ($schedule->status !== 'pending') {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ có thể hủy lịch đang chờ duyệt',
            ]);
        }
        
        // Lưu thông tin trước khi xóa để ghi log
        $scheduleId = $schedule->id;
        $scheduleDate = $schedule->schedule_date;
        
        // Xóa lịch
        $schedule->delete();
        
        LogService::saveLog([
            'action' => 'CANCEL_PENDING_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Hủy lịch làm việc chờ duyệt ngày " . formatDateTime($scheduleDate, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $scheduleId,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Hủy lịch làm việc thành công',
        ]);
    }
    
    /**
     * Yêu cầu hủy lịch (cho lịch đã được duyệt)
     */
    public function requestCancelSchedule(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_part_time_schedules,id',
            'reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }

        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $schedule = PartTimeSchedule::where('id', $request->id)
                        ->where('user_id', $userId)
                        ->first();
        
        if (!$schedule) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy lịch làm việc',
            ]);
        }
        
        // Chỉ được yêu cầu hủy lịch đã duyệt và trong tương lai
        if ($schedule->status !== 'approved') {
            return response()->json([
                'status' => 422,
                'message' => 'Chỉ có thể yêu cầu hủy lịch đã được duyệt',
            ]);
        }
        
        if (Carbon::parse($schedule->schedule_date)->lt(Carbon::today())) {
            return response()->json([
                'status' => 422,
                'message' => 'Không thể yêu cầu hủy lịch cho ngày đã qua',
            ]);
        }
        
        // Cập nhật trạng thái và ghi chú
        $schedule->update([
            'status' => 'cancel_requested',
            'note' => ($schedule->note ? $schedule->note . "\n\n" : '') . "Yêu cầu hủy: " . $request->reason,
        ]);
        
        LogService::saveLog([
            'action' => 'REQUEST_CANCEL_SCHEDULE',
            'ip' => $request->getClientIp(),
            'details' => "Yêu cầu hủy lịch làm việc ngày " . formatDateTime($schedule->schedule_date, 'd/m/Y'),
            'fk_key' => 'tbl_part_time_schedules|id',
            'fk_value' => $schedule->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Đã gửi yêu cầu hủy lịch làm việc, vui lòng chờ quản lý duyệt',
        ]);
    }
    
    /**
     * Lấy chi tiết lịch làm việc
     */
    public function getScheduleDetail($id)
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $schedule = PartTimeSchedule::with('approver')
                        ->where('id', $id)
                        ->where('user_id', $userId)
                        ->firstOrFail();
        
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