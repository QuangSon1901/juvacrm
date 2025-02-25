@foreach ($data as $item)
<tr>
    <td class="text-gray-800 font-normal text-center">
        <span class="leading-none">
            {{$item['index']}}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        @if ($item['status']['id'] != 0)
        <span class="badge badge-sm badge-outline badge-{{$item['status']['color']}}">
            {{$item['status']['name']}}
        </span>
        @else
        ---
        @endif
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-wrap gap-2">
            <span class="badge badge-sm badge-outline badge-neutral">
                ID: {{$item['id']}}
            </span>
            @if ($item['parent_id'])
            <a class="badge badge-sm badge-outline badge-neutral hover:text-primary" href="/task/{{$item['parent_id']}}">
                Parent: {{$item['parent_id']}}
            </a>
            @endif
            @if ($item['priority']['id'] != 0)
            <span class="badge badge-sm badge-outline badge-{{$item['priority']['color']}}">
                {{$item['priority']['name']}}
            </span>
            @endif
            <span class="badge badge-sm badge-outline badge-neutral">
                SL: {{$item['qty_request']}}
            </span>
        </div>
        <a class="leading-none hover:text-primary text-gray-900 font-medium" href="/task/{{$item['id']}}">
            {{$item['name']}}
        </a>
    </td>
    <td class="text-gray-800 font-normal">
        @if ($item['assign']['id'] == 0)
        ---
        @else
        <div class="flex items-center gap-2.5">
            <div class="flex flex-col gap-0.5">
                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/{{$item['assign']['id']}}">
                    {{$item['assign']['name']}}
                </a>
                <span class="text-xs text-gray-700 font-normal">
                    ID: {{$item['assign']['id']}}
                </span>
            </div>
        </div>
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['start_date'])
        <p class="leading-none text-gray-900 font-medium">{{formatDateTime($item['start_date'], 'd-m-Y')}}</p>
        <span class="text-gray-700 text-xs">{{formatDateTime($item['start_date'], 'H:i:s')}}</span>
        @else
        ---
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['due_date'])
        <p class="leading-none text-gray-900 font-medium">{{formatDateTime($item['due_date'], 'd-m-Y')}}</p>
        <span class="text-gray-700 text-xs">{{formatDateTime($item['due_date'], 'H:i:s')}}</span>
        @else
        ---
        @endif
    </td>
    <td class="text-gray-800 font-normal">
        <div class="max-w-32 bg-gray-300 rounded-sm h-4">
            <div class="bg-blue-800 h-4 rounded-sm flex items-center {{$item['progress'] == 0 ? 'justify-start' : 'justify-center'}}" style="width: {{$item['progress'] ?? 0}}%">
                <span class="text-xs checkbox-label !text-white">
                    &nbsp;{{$item['progress']}}%&nbsp;
                </span>
            </div>
        </div>
    </td>
    <td class="text-sm text-gray-800 font-normal">
        <span class="text-gray-900 font-medium">{{$item['estimate_time'] ?? 0}}h</span>
    </td>
    <td class="text-sm text-gray-800 font-normal">
        <span class="text-gray-900 font-medium">{{$item['spend_time'] ?? 0}}h</span>
    </td>
    <td class="w-[60px]">
        <div class="menu" data-menu="true">
            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical">
                    </i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <a class="menu-link" href="/task/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-search-list">
                                </i>
                            </span>
                            <span class="menu-title">
                                Xem chi tiết
                            </span>
                        </a>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/task/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-pencil">
                                </i>
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