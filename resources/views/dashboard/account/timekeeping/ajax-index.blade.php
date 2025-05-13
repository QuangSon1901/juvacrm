@foreach ($data as $index => $item)
<tr>
    <td class="text-center">
        {{ $offset + $index + 1 }}
    </td>
    <td>
        <div class="flex items-center gap-3">
            <div class="flex flex-col">
                <span class="text-gray-800 text-sm font-medium">{{ $item->user->name }}</span>
            </div>
        </div>
    </td>
    <td>{{ formatDateTime($item->work_date, 'd/m/Y') }}</td>
    <td>
        {{ $item->check_in_time ? formatDateTime($item->check_in_time, 'H:i:s') : '-' }}
        @if($item->late_minutes > 0)
        <span class="badge badge-sm badge-warning">Trễ {{ $item->late_minutes }} phút</span>
        @endif
    </td>
    <td>
        {{ $item->check_out_time ? formatDateTime($item->check_out_time, 'H:i:s') : '-' }}
        @if($item->early_leave_minutes > 0)
        <span class="badge badge-sm badge-info">Sớm {{ $item->early_leave_minutes }} phút</span>
        @endif
    </td>
    <td>{{ number_format($item->total_hours, 2) }}</td>
    <td>{{ number_format($item->valid_hours, 2) }}</td>
    <td>
        <span class="badge badge-sm badge-{{ $item->getStatusClass() }}">
            {{ $item->getStatusText() }}
        </span>
        @if($item->forgot_checkout)
        <span class="badge badge-sm badge-danger ms-1">Quên checkout</span>
        @endif
    </td>
    <td>
        @if($item->overtime_hours > 0)
        <span class="badge badge-sm badge-primary">Tăng ca {{ number_format($item->overtime_hours, 2) }}h</span>
        @endif
    </td>
    <td>
    @if(hasPermission('edit-timekeeping'))
        <div class="menu" data-menu="true">
            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical"></i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <button class="menu-link" onclick="viewAttendanceDetail({{ $item->id }})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-eye"></i>
                            </span>
                            <span class="menu-title">
                                Xem chi tiết
                            </span>
                        </button>
                    </div>
                    <div class="menu-item">
                        <button class="menu-link" onclick="openEditAttendanceModal({{ $item->id }})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-pencil"></i>
                            </span>
                            <span class="menu-title">
                                Chỉnh sửa
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </td>
</tr>
@endforeach

@if(count($data) === 0)
<tr>
    <td colspan="10" class="text-center py-4">
        <div class="text-gray-500">Không có dữ liệu</div>
    </td>
</tr>
@endif