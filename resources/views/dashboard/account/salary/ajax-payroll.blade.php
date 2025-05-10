@foreach ($data as $item)
<tr>
    <td>
        <div class="checkbox">
            <input class="form-checkbox checkbox-row checkbox" type="checkbox" id="checkbox-{{ $item['id'] }}" value="{{ $item['id'] }}" {{ $item['status'] === 'paid' ? 'disabled' : '' }}>
            <label for="checkbox-{{ $item['id'] }}"></label>
        </div>
    </td>
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
    <td>{{ $item['period'] }}</td>
    <td>{{ $item['base_salary'] }}</td>
    <td>{{ $item['commission_amount'] }}</td>
    <td>{{ $item['task_mission_amount'] }}</td>
    <td>{{ $item['deductions'] }}</td>
    <td class="font-semibold text-success">{{ $item['final_amount'] }}</td>
    <td>
        @php
            $statusClass = '';
            
            switch($item['status']) {
                case 'pending':
                    $statusClass = 'warning';
                    break;
                case 'processed':
                    $statusClass = 'primary';
                    break;
                case 'paid':
                    $statusClass = 'success';
                    break;
                default:
                    $statusClass = 'gray';
            }
        @endphp
        
        <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
            {{ $item['status_text'] }}
        </span>
    </td>
    <td>
        <div class="menu" data-menu="true">
            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical"></i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <button class="menu-link" onclick="viewSalaryDetail({{ $item['id'] }})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-search-list"></i>
                            </span>
                            <span class="menu-title">
                                Xem chi tiết
                            </span>
                        </button>
                    </div>
                    
                    @if($item['status'] === 'pending')
                    <div class="menu-item">
                        <button class="menu-link" onclick="processSalary({{ $item['id'] }}, 'processed')">
                            <span class="menu-icon">
                                <i class="ki-filled ki-check"></i>
                            </span>
                            <span class="menu-title !text-left">
                                Duyệt bảng lương
                            </span>
                        </button>
                    </div>
                    @endif
                    
                    @if($item['status'] === 'processed')
                    <div class="menu-item">
                        <button class="menu-link" onclick="processSalary({{ $item['id'] }}, 'paid')">
                            <span class="menu-icon">
                                <i class="ki-filled ki-dollar"></i>
                            </span>
                            <span class="menu-title !text-left">
                                Chi trả lương
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