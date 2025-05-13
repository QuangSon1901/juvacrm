<div class="grid gap-5">
    <div class="flex flex-col">
        <div class="grid grid-cols-2 gap-x-8 gap-y-3">
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Nhân viên:</label>
                <span class="text-sm font-medium">{{ $attendance->user->name }}</span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Ngày làm việc:</label>
                <span class="text-sm font-medium">{{ formatDateTime($attendance->work_date, 'd/m/Y') }}</span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Giờ check-in:</label>
                <span class="text-sm font-medium" data-check-in-time="{{ $attendance->check_in_time ? formatDateTime($attendance->check_in_time, 'H:i:s') : '' }}">
                    {{ $attendance->check_in_time ? formatDateTime($attendance->check_in_time, 'H:i:s') : '—' }}
                </span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Giờ check-out:</label>
                <span class="text-sm font-medium" data-check-out-time="{{ $attendance->check_out_time ? formatDateTime($attendance->check_out_time, 'H:i:s') : '' }}">
                    {{ $attendance->check_out_time ? formatDateTime($attendance->check_out_time, 'H:i:s') : '—' }}
                </span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Ca làm việc:</label>
                <span class="text-sm font-medium">
                    @if($attendance->schedule)
                        {{ formatDateTime($attendance->schedule->start_time, 'H:i') }} - {{ formatDateTime($attendance->schedule->end_time, 'H:i') }}
                    @else
                        —
                    @endif
                </span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Trạng thái:</label>
                <span class="flex items-center gap-2" data-status="{{ $attendance->status }}">
                    <span class="badge badge-sm badge-{{ $attendance->getStatusClass() }}">{{ $attendance->getStatusText() }}</span>
                    @if($attendance->forgot_checkout)
                    <span class="badge badge-sm badge-danger">Quên checkout</span>
                    @endif
                </span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Tổng giờ làm việc:</label>
                <span class="text-sm font-medium">{{ number_format($attendance->total_hours, 2) }} giờ</span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Giờ làm việc hợp lệ:</label>
                <span class="text-sm font-medium" data-valid-hours="{{ $attendance->valid_hours }}">{{ number_format($attendance->valid_hours, 2) }} giờ</span>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col">
        <div class="grid grid-cols-2 gap-x-8 gap-y-3">
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Thời gian đi trễ:</label>
                <span class="text-sm font-medium" data-late-minutes="{{ $attendance->late_minutes }}">
                    @if($attendance->late_minutes > 0)
                    <span class="text-warning">{{ $attendance->late_minutes }} phút</span>
                    @else
                    <span class="text-success">Không trễ</span>
                    @endif
                </span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Thời gian về sớm:</label>
                <span class="text-sm font-medium" data-early-leave-minutes="{{ $attendance->early_leave_minutes }}">
                    @if($attendance->early_leave_minutes > 0)
                    <span class="text-info">{{ $attendance->early_leave_minutes }} phút</span>
                    @else
                    <span class="text-success">Không về sớm</span>
                    @endif
                </span>
            </div>
            
            <div class="flex flex-col gap-1">
                <label class="text-xs text-gray-500">Giờ tăng ca:</label>
                <span class="text-sm font-medium" data-overtime-hours="{{ $attendance->overtime_hours }}">
                    @if($attendance->overtime_hours > 0)
                    <span class="text-primary">{{ number_format($attendance->overtime_hours, 2) }} giờ</span>
                    @else
                    <span class="text-muted">Không có tăng ca</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
    
    @if($attendance->late_reason)
    <div class="flex flex-col gap-1">
        <label class="text-xs text-gray-500">Lý do đi trễ:</label>
        <div class="p-3 bg-light-warning rounded-lg" data-late-reason>
            {{ $attendance->late_reason }}
        </div>
    </div>
    @endif
    
    @if($attendance->early_leave_reason)
    <div class="flex flex-col gap-1">
        <label class="text-xs text-gray-500">Lý do về sớm:</label>
        <div class="p-3 bg-light-info rounded-lg" data-early-leave-reason>
            {{ $attendance->early_leave_reason }}
        </div>
    </div>
    @endif
    
    @if($attendance->forgot_checkout_reason)
    <div class="flex flex-col gap-1">
        <label class="text-xs text-gray-500">Lý do quên checkout:</label>
        <div class="p-3 bg-light-danger rounded-lg">
            {{ $attendance->forgot_checkout_reason }}
        </div>
    </div>
    @endif
    
    @if($attendance->note)
    <div class="flex flex-col gap-1">
        <label class="text-xs text-gray-500">Ghi chú:</label>
        <div class="p-3 bg-light-secondary rounded-lg" data-note>
            {{ $attendance->note }}
        </div>
    </div>
    @endif
    
    @if(isset($logs) && count($logs) > 0)
    <div class="flex flex-col gap-2 mt-3">
        <h4 class="text-sm font-semibold">Lịch sử chỉnh sửa</h4>
        <div class="rounded-lg border border-gray-200 overflow-hidden">
            <div class="max-h-40 overflow-y-auto">
                <table class="table table-sm table-hover m-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="w-40">Thời gian</th>
                            <th>Người chỉnh sửa</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ formatDateTime($log->created_at, 'd/m/Y H:i:s') }}</td>
                            <td>{{ $log->user ? $log->user->name : 'Hệ thống' }}</td>
                            <td>{{ $log->details }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <div class="flex justify-end gap-2 pt-3">
        @if(hasPermission('edit-timekeeping'))
        <button class="btn btn-primary btn-sm" onclick="openEditAttendanceModal({{ $attendance->id }})">
            <i class="ki-outline ki-pencil me-1"></i>Chỉnh sửa
        </button>
        @endif
        
        <button class="btn btn-secondary btn-sm" data-modal-dismiss="true">
            <i class="ki-outline ki-cross-square me-1"></i>Đóng
        </button>
    </div>
</div>