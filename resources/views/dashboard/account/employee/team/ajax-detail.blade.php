@foreach ($data as $item)
<tr>
    <td class="text-center">
        <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/{{$item['id']}}">
            {{ $item['index'] }}
        </a>
    </td>
    <td>
        <div class="flex items-center gap-2.5">
            <div class="flex flex-col gap-0.5">
                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/{{$item['id']}}">
                    {{ $item['name'] }}
                </a>
                <span class="text-2sm text-primary font-normal">
                    ID: {{ $item['id'] }}
                </span>
            </div>
        </div>
    </td>
    <td>
        <div class="flex items-center gap-1.5">
            <span class="leading-none text-gray-800 font-normal">
                {{ $item['level']['name'] }}
            </span>
        </div>
    </td>
    <td>
        <div class="menu" data-menu="true">
            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical">
                    </i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <a class="menu-link" href="/member/{{$item['id']}}">
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
                        <button class="menu-link" onclick="saveRemoveMemberTeam({{ $item['id'] }}, {{ $department_id }}, $(this))">
                            <span class="menu-icon">
                                <i class="ki-filled ki-trash">
                                </i>
                            </span>
                            <span class="menu-title">
                                Gỡ
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach