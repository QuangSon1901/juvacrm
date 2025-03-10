@foreach ($data as $item)
<tr>
    <td class="text-gray-800 font-normal text-center">
        <span class="leading-none">
            {{$item['index']}}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        @if ($item['status'] == 0)
            <span class="badge badge-sm badge-outline badge-warning">Chờ duyệt</span>
        @elseif ($item['status'] == 1)
            <span class="badge badge-sm badge-outline badge-primary">Đang triển khai</span>
        @elseif ($item['status'] == 2)
            <span class="badge badge-sm badge-outline badge-success">Đã hoàn tất</span>
        @elseif ($item['status'] == 3)
            <span class="badge badge-sm badge-outline badge-danger">Đã hủy</span>
        @endif
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-col gap-2">
            <a class="leading-none hover:text-primary text-gray-900 font-medium" href="/contract/{{$item['id']}}">
                {{$item['name']}}
            </a>
            <div class="flex flex-wrap gap-2">
                <span class="badge badge-sm badge-outline badge-neutral">
                    {{$item['contract_number']}}
                </span>
            </div>
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        @if ($item['user']['id'] == 0)
        ---
        @else
        <div class="flex items-center gap-2.5">
            <div class="flex flex-col gap-0.5">
                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/{{$item['user']['id']}}">
                    {{$item['user']['name']}}
                </a>
            </div>
        </div>
        @endif
    </td>
    <td class="text-gray-800 font-normal">
        @if ($item['customer']['id'] == 0)
        ---
        @else
        <div class="flex flex-col gap-0.5">
            <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/customer/{{$item['customer']['id']}}">
                {{$item['customer']['name']}}
            </a>
        </div>
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        <div class="flex flex-col gap-0.5">
            <div class="text-base font-medium">{{number_format($item['total_value'], 0, ',', '.')}} ₫</div>
            <div class="flex items-center gap-1 text-xs">
                <span class="{{$item['payment_percentage'] >= 100 ? 'text-green-600' : 'text-blue-600'}}">{{$item['payment_percentage']}}%</span>
                <div class="w-20 h-1.5 bg-gray-200 rounded-full">
                    <div class="h-1.5 rounded-full {{$item['payment_percentage'] >= 100 ? 'bg-green-600' : 'bg-blue-600'}}" style="width: {{min($item['payment_percentage'], 100)}}%"></div>
                </div>
            </div>
        </div>
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['task_stats']['total'] > 0)
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-1">
                    <span class="text-xs">{{$item['task_progress']}}%</span>
                    <div class="w-20 h-1.5 bg-gray-200 rounded-full">
                        <div class="h-1.5 rounded-full bg-indigo-600" style="width: {{$item['task_progress']}}%"></div>
                    </div>
                </div>
                <div class="text-xs text-gray-600">{{$item['task_stats']['completed']}}/{{$item['task_stats']['total']}} công việc</div>
            </div>
        @else
            <span class="text-xs text-gray-500">Chưa có công việc</span>
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        <div class="flex flex-col gap-1">
            @if ($item['sign_date'])
                <div class="flex items-center gap-1.5">
                    <i class="ki-duotone ki-calendar-tick text-gray-500 text-xs"></i>
                    <span>Ký: {{formatDateTime($item['sign_date'], 'd-m-Y')}}</span>
                </div>
            @endif
            @if ($item['effective_date'])
                <div class="flex items-center gap-1.5">
                    <i class="ki-duotone ki-calendar-edit text-blue-500 text-xs"></i>
                    <span>Hiệu lực: {{formatDateTime($item['effective_date'], 'd-m-Y')}}</span>
                </div>
            @endif
            @if ($item['expiry_date'])
                <div class="flex items-center gap-1.5">
                    <i class="ki-duotone ki-calendar-close text-red-500 text-xs"></i>
                    <span>Hết hạn: {{formatDateTime($item['expiry_date'], 'd-m-Y')}}</span>
                </div>
            @endif
        </div>
    </td>
    <td class="w-[60px]">
        <div class="menu" data-menu="true">
            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical"></i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <a class="menu-link" href="/contract/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-search-list"></i>
                            </span>
                            <span class="menu-title">
                                Xem chi tiết
                            </span>
                        </a>
                    </div>
                    <div class="menu-separator"></div>
                    <div class="menu-item">
                        <a class="menu-link" href="/contract/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-pencil"></i>
                            </span>
                            <span class="menu-title">
                                Chỉnh sửa
                            </span>
                        </a>
                    </div>
                    @if ($item['status'] == 0)
                    <div class="menu-item">
                        <button class="menu-link" onclick="saveCreateTaskContract({{$item['id']}})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-plus"></i>
                            </span>
                            <span class="menu-title">
                                Tạo công việc
                            </span>
                        </button>
                    </div>
                    @endif
                    @if ($item['status'] != 3)
                    <div class="menu-item">
                        <button class="menu-link" onclick="saveCancelContract({{$item['id']}})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-trash !text-red-500"></i>
                            </span>
                            <span class="menu-title !text-red-500">
                                Hủy hợp đồng
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