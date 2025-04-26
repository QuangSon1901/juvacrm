@foreach ($data as $item)
<tr>
    <td class="text-center">
        {{ $item['index'] }}
    </td>
    <td>
        <div class="flex items-center gap-3">
            <div class="flex flex-col">
                <span class="text-gray-800 text-sm font-medium">{{ $item['user']['name'] }}</span>
            </div>
        </div>
    </td>
    <td>{{ $item['work_date'] }}</td>
    <td>{{ $item['check_in_time'] }}</td>
    <td>{{ $item['check_out_time'] }}</td>
    <td>{{ $item['total_hours'] }}</td>
    <td>
        @php
            $statusClass = '';
            
            switch($item['status']) {
                case 'present':
                    $statusClass = 'success';
                    break;
                case 'absent':
                    $statusClass = 'danger';
                    break;
                case 'late':
                    $statusClass = 'warning';
                    break;
                case 'early_leave':
                    $statusClass = 'info';
                    break;
                default:
                    $statusClass = 'gray';
            }
        @endphp
        
        <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
            {{ $item['status_text'] }}
        </span>
    </td>
    <td>{{ $item['note'] }}</td>
    <td>
    @if(hasPermission('edit-timekeeping'))
        <div class="menu" data-menu="true">
            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical"></i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <button class="menu-link" onclick="openEditAttendanceModal('{{ $item['id'] }}', '{{ $item['check_in_time'] }}', '{{ $item['check_out_time'] }}', '{{ $item['status'] }}', '{{ $item['note'] }}')">
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