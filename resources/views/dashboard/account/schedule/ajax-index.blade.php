@if(count($data) > 0)
    @foreach($data as $item)
    <tr>
        <td class="text-center">{{ $item['index'] }}</td>
        <td>
            <div class="d-flex align-items-center">
                <div class="text-gray-900">{{ $item['user']['name'] }}</div>
            </div>
        </td>
        <td>{{ $item['schedule_date'] }}</td>
        <td>{{ $item['start_time'] }}</td>
        <td>{{ $item['end_time'] }}</td>
        <td>{{ number_format($item['total_hours'], 2) }}</td>
        <td>
            @php
                $statusClass = '';
                switch($item['status']) {
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
                @if($item['status'] == 'cancel_requested')
                    Yêu cầu hủy
                @else
                    {{ $item['status_text'] }}
                @endif
            </span>
        </td>
        <td>{{ $item['note'] ?: '-' }}</td>
        <td>
            <div class="menu" data-menu="true">
                <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                    <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                        <i class="ki-filled ki-dots-vertical"></i>
                    </button>
                    <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                        <div class="menu-item">
                            <button class="menu-link" onclick="showScheduleDetails({{ $item['id'] }})">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-eye text-primary"></i>
                                </span>
                                <span class="menu-title">
                                    Xem chi tiết
                                </span>
                            </button>
                        </div>
                        
                        @if($item['status'] == 'pending')
                        <div class="menu-item">
                            <button class="menu-link" onclick="approveSchedule({{ $item['id'] }})">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-check text-success"></i>
                                </span>
                                <span class="menu-title">
                                    Duyệt
                                </span>
                            </button>
                        </div>
                        <div class="menu-item">
                            <button class="menu-link" onclick="rejectSchedule({{ $item['id'] }})">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-cross text-danger"></i>
                                </span>
                                <span class="menu-title">
                                    Từ chối
                                </span>
                            </button>
                        </div>
                        <div class="menu-item">
                            <button class="menu-link" onclick="editSchedule({{ $item['id'] }})">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-pencil text-warning"></i>
                                </span>
                                <span class="menu-title">
                                    Chỉnh sửa
                                </span>
                            </button>
                        </div>
                        <div class="menu-item">
                            <button class="menu-link" onclick="deleteSchedule({{ $item['id'] }})">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-trash text-danger"></i>
                                </span>
                                <span class="menu-title">
                                    Xóa
                                </span>
                            </button>
                        </div>
                        @endif
                        @if($item['status'] == 'cancel_requested')
                            <div class="menu-item">
                                <button class="menu-link" onclick="approveCancelRequest({{ $item['id'] }})">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-check text-success"></i>
                                    </span>
                                    <span class="menu-title">
                                        Duyệt hủy lịch
                                    </span>
                                </button>
                            </div>
                            <div class="menu-item">
                                <button class="menu-link" onclick="rejectCancelRequest({{ $item['id'] }})">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-cross text-danger"></i>
                                    </span>
                                    <span class="menu-title">
                                        Từ chối hủy lịch
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="9" class="text-center py-10">
            <div class="text-gray-600">Không có dữ liệu</div>
        </td>
    </tr>
@endif