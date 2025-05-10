<div class="grid gap-4">
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-sm font-medium">Nhân viên:</label>
        <div class="text-gray-900">{{ $schedule->user->name }}</div>
    </div>
    
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-sm font-medium">Ngày làm việc:</label>
        <div class="text-gray-900">{{ formatDateTime($schedule->schedule_date, 'd/m/Y') }}</div>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-2">
            <label class="text-gray-600 text-sm font-medium">Giờ bắt đầu:</label>
            <div class="text-gray-900">{{ formatDateTime($schedule->start_time, 'H:i') }}</div>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-gray-600 text-sm font-medium">Giờ kết thúc:</label>
            <div class="text-gray-900">{{ formatDateTime($schedule->end_time, 'H:i') }}</div>
        </div>
    </div>
    
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-sm font-medium">Tổng giờ làm việc:</label>
        <div class="text-gray-900">{{ number_format($schedule->total_hours, 2) }} giờ</div>
    </div>
    
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-sm font-medium">Trạng thái:</label>
        <div>
            <span class="badge badge-sm badge-outline badge-{{ $status['class'] }}">
                {{ $status['text'] }}
            </span>
        </div>
    </div>
    
    @if($schedule->note)
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-sm font-medium">Ghi chú:</label>
        <div class="text-gray-900 whitespace-pre-line">{{ $schedule->note }}</div>
    </div>
    @endif
    
    @if($schedule->approval_time)
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-sm font-medium">Thời gian duyệt:</label>
        <div class="text-gray-900">{{ formatDateTime($schedule->approval_time, 'd/m/Y H:i:s') }}</div>
    </div>
    @endif
    
    @if($schedule->approver)
    <div class="flex flex-col gap-2">
        <label class="text-gray-600 text-sm font-medium">Người duyệt:</label>
        <div class="text-gray-900">{{ $schedule->approver->name }}</div>
    </div>
    @endif
    
    <div class="border-t border-gray-200 my-2 pt-4 grid grid-cols-1 gap-3">
        @if($schedule->status == 'pending')
        <div class="grid grid-cols-2 gap-3">
            <button class="btn btn-success w-full" onclick="quickApprove({{ $schedule->id }})">
                <i class="ki-outline ki-check me-1"></i>Duyệt lịch
            </button>
            <button class="btn btn-danger w-full" onclick="rejectSchedule({{ $schedule->id }})">
                <i class="ki-outline ki-cross me-1"></i>Từ chối
            </button>
        </div>
        @elseif($schedule->status == 'cancel_requested')
        <div class="grid grid-cols-2 gap-3">
            <button class="btn btn-success w-full" onclick="approveCancelRequest({{ $schedule->id }})">
                <i class="ki-outline ki-check me-1"></i>Duyệt hủy lịch
            </button>
            <button class="btn btn-danger w-full" onclick="rejectCancelRequest({{ $schedule->id }})">
                <i class="ki-outline ki-cross me-1"></i>Từ chối hủy
            </button>
        </div>
        @endif
    </div>
</div>