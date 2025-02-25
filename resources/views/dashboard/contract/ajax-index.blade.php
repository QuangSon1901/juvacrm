@foreach ($data as $item)
<tr>
    <td class="text-gray-800 font-normal text-center">
        <span class="leading-none">
            {{$item['index']}}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        <span class="badge badge-sm badge-outline badge-neutral">
            @if ($item['status'] == 0)
                Chờ duyệt
            @elseif ($item['status'] == 1)
                Đang triển khai
            @elseif ($item['status'] == 2)
                Đã hoàn tất
            @endif
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-wrap gap-2">
            <span class="badge badge-sm badge-outline badge-neutral">
                Số HD: {{$item['contract_number']}}
            </span>
            <span class="badge badge-sm badge-outline badge-neutral">
                Tổng: {{number_format($item['total_value'], 0, ',', '.')}}
            </span>
        </div>
        <a class="leading-none hover:text-primary text-gray-900 font-medium" href="/contract/{{$item['id']}}">
            {{$item['name']}}
        </a>
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
                <span class="text-xs text-gray-700 font-normal">
                    ID: {{$item['user']['id']}}
                </span>
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
            <span class="text-xs text-gray-700 font-normal">
                ID: {{$item['customer']['id']}}
            </span>
        </div>
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['sign_date'])
        <p class="leading-none text-gray-900 font-medium">{{formatDateTime($item['sign_date'], 'd-m-Y')}}</p>
        @else
        ---
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['effective_date'])
        <p class="leading-none text-gray-900 font-medium">{{formatDateTime($item['effective_date'], 'd-m-Y')}}</p>
        @else
        ---
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['expiry_date'])
        <p class="leading-none text-gray-900 font-medium">{{formatDateTime($item['expiry_date'], 'd-m-Y')}}</p>
        @else
        ---
        @endif
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
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach