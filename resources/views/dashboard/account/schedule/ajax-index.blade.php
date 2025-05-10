@if(count($data) > 0)
    @foreach($data as $key => $item)
    <tr data-id="{{ $item->id }}">
        <td class="text-center">
            @if($item->status == 'pending')
            <input type="checkbox" class="schedule-checkbox checkbox" value="{{ $item->id }}">
            @endif
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="text-gray-900">{{ $item->user->name }}</div>
            </div>
        </td>
        <td>{{ formatDateTime($item->schedule_date, 'd/m/Y') }}</td>
        <td>{{ formatDateTime($item->start_time, 'H:i') }}</td>
        <td>{{ formatDateTime($item->end_time, 'H:i') }}</td>
        <td>{{ number_format($item->total_hours, 2) }}</td>
        <td>
            @php
                $statusClass = '';
                switch($item->status) {
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
                {{ $item->getStatusText() }}
            </span>
        </td>
        <td>
            <div class="d-flex gap-1">
                @if($item->status == 'pending')
                <button class="btn btn-xs btn-icon btn-success" onclick="quickApprove({{ $item->id }})" title="Duyệt">
                    <i class="ki-outline ki-check"></i>
                </button>
                <button class="btn btn-xs btn-icon btn-danger" onclick="rejectSchedule({{ $item->id }})" title="Từ chối">
                    <i class="ki-outline ki-cross"></i>
                </button>
                @elseif($item->status == 'cancel_requested')
                <button class="btn btn-xs btn-icon btn-success" onclick="approveCancelRequest({{ $item->id }})" title="Duyệt hủy">
                    <i class="ki-outline ki-check"></i>
                </button>
                <button class="btn btn-xs btn-icon btn-danger" onclick="rejectCancelRequest({{ $item->id }})" title="Từ chối hủy">
                    <i class="ki-outline ki-cross"></i>
                </button>
                @endif
                
                <button class="btn btn-xs btn-icon btn-light" onclick="showScheduleDetails({{ $item->id }})" title="Chi tiết">
                    <i class="ki-outline ki-eye"></i>
                </button>
                
                @if($item->status == 'pending')
                <button class="btn btn-xs btn-icon btn-danger" onclick="deleteSchedule({{ $item->id }})" title="Xóa">
                    <i class="ki-outline ki-trash"></i>
                </button>
                @endif
            </div>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" class="text-center py-10">
            <div class="text-gray-600">Không có dữ liệu</div>
        </td>
    </tr>
@endif