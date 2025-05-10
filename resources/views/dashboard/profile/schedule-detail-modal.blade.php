<div class="grid gap-4">
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-2sm">Ngày làm việc:</label>
        <div class="text-gray-900">{{ formatDateTime($schedule->schedule_date, 'd/m/Y') }}</div>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-2">
            <label class="text-gray-600 text-2sm">Giờ bắt đầu:</label>
            <div class="text-gray-900">{{ formatDateTime($schedule->start_time, 'H:i') }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-gray-600 text-2sm">Giờ kết thúc:</label>
            <div class="text-gray-900">{{ formatDateTime($schedule->end_time, 'H:i') }}</div>
        </div>
    </div>
    
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-2sm">Tổng giờ làm việc:</label>
        <div class="text-gray-900">{{ number_format($schedule->total_hours, 2) }} giờ</div>
    </div>
    
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-2sm">Trạng thái:</label>
        <div>
            <span class="badge badge-sm badge-outline badge-{{ $status['class'] }}">
                {{ $status['text'] }}
            </span>
        </div>
    </div>
    
    @if($schedule->note)
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-2sm">Ghi chú:</label>
        <div class="text-gray-900">{{ $schedule->note }}</div>
    </div>
    @endif
    
    @if($schedule->approval_time)
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-2sm">Thời gian duyệt:</label>
        <div class="text-gray-900">{{ formatDateTime($schedule->approval_time, 'd/m/Y H:i:s') }}</div>
    </div>
    @endif
    
    @if($schedule->approver)
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-2sm">Người duyệt:</label>
        <div class="text-gray-900">{{ $schedule->approver->name }}</div>
    </div>
    @endif
    
    @if($schedule->status == 'pending')
    <div class="mt-4">
        <button class="btn btn-danger w-full" onclick="cancelSchedule({{ $schedule->id }})">
            <i class="ki-filled ki-trash me-2"></i>Hủy lịch
        </button>
    </div>
    @elseif($schedule->status == 'approved' && \Carbon\Carbon::parse($schedule->schedule_date)->gt(\Carbon\Carbon::today()))
    <div class="mt-4">
        <button class="btn btn-warning w-full" onclick="requestCancelSchedule({{ $schedule->id }})">
            <i class="ki-filled ki-cross me-2"></i>Yêu cầu hủy
        </button>
    </div>
    @endif
</div>