<div class="grid gap-4">
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-2sm">Nhân viên:</label>
        <div class="text-gray-900 font-medium">{{ $schedule->user->name }}</div>
    </div>
    
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
    <div class="grid grid-cols-2 gap-4 mt-4">
        <button class="btn btn-success" onclick="approveSchedule({{ $schedule->id }})">
            <i class="ki-filled ki-check me-2"></i>Duyệt lịch
        </button>
        <button class="btn btn-danger" onclick="rejectSchedule({{ $schedule->id }})">
            <i class="ki-filled ki-cross me-2"></i>Từ chối
        </button>
    </div>
    @endif
</div>