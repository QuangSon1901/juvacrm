<tr data-id="{{ $schedule->id }}">
    <td class="text-center">
        @if($schedule->status == 'pending')
        <input type="checkbox" class="schedule-checkbox" value="{{ $schedule->id }}">
        @endif
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="text-gray-900">{{ $schedule->user->name }}</div>
        </div>
    </td>
    <td>{{ formatDateTime($schedule->schedule_date, 'd/m/Y') }}</td>
    <td>{{ formatDateTime($schedule->start_time, 'H:i') }}</td>
    <td>{{ formatDateTime($schedule->end_time, 'H:i') }}</td>
    <td>{{ number_format($schedule->total_hours, 2) }}</td>
    <td>
        @php
            $statusClass = '';
            switch($schedule->status) {
                case 'pending':
                    $statusClass = 'warning';
                    break;
                case 'approved':
                    $statusClass = 'success';
                    break;
                case 'rejected':
                    $statusClass = 'danger';
                    break;
                case 'cancel_requested':
                    $statusClass = 'info';
                    break;
                default:
                    $statusClass = 'gray';
            }
        @endphp
        <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
            {{ $schedule->getStatusText() }}
        </span>
    </td>
    <td>
        <div class="d-flex gap-1">
            @if($schedule->status == 'pending')
            <button class="btn btn-xs btn-icon btn-success" onclick="quickApprove({{ $schedule->id }})" title="Duyệt">
                <i class="ki-outline ki-check"></i>
            </button>
            <button class="btn btn-xs btn-icon btn-danger" onclick="rejectSchedule({{ $schedule->id }})" title="Từ chối">
                <i class="ki-outline ki-cross"></i>
            </button>
            @elseif($schedule->status == 'cancel_requested')
            <button class="btn btn-xs btn-icon btn-success" onclick="approveCancelRequest({{ $schedule->id }})" title="Duyệt hủy">
                <i class="ki-outline ki-check"></i>
            </button>
            <button class="btn btn-xs btn-icon btn-danger" onclick="rejectCancelRequest({{ $schedule->id }})" title="Từ chối hủy">
                <i class="ki-outline ki-cross"></i>
            </button>
            @endif
            
            <button class="btn btn-xs btn-icon btn-light" onclick="showScheduleDetails({{ $schedule->id }})" title="Chi tiết">
                <i class="ki-outline ki-eye"></i>
            </button>
            
            @if($schedule->status == 'pending')
            <button class="btn btn-xs btn-icon btn-danger" onclick="deleteSchedule({{ $schedule->id }})" title="Xóa">
                <i class="ki-outline ki-trash"></i>
            </button>
            @endif
        </div>
    </td>
</tr>