@foreach ($data as $item)
<tr>
    <td class="text-center">
        <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/team/{{$item['id']}}">
        {{ $item['index'] }}
        </a>
    </td>
    <td>
        <div class="flex flex-col gap-1.5">
            <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/team/{{$item['id']}}">
            <span class="text-primary text-xs">#{{$item['id']}}:</span> {{$item['name']}}
            </a>
            <span class="text-2sm text-gray-700 font-normal">
            {{$item['description']}}
            </span>
        </div>
    </td>
    <td class="text-sm text-gray-800 font-normal"><span class="text-gray-700 font-medium">{{explode(' ', $item['updated_at'])[0]}}</span> <br> <span class="text-gray-500">{{explode(' ', $item['updated_at'])[1] ?? ''}}</span></td>
    <td class="text-sm text-gray-800 font-normal">
    @if ($item['member_count'] == 0)
        Chưa có thành viên
    @else
    {{$item['member_count']}} thành viên
    @endif
</td>
    <td>
        <span class="badge badge-pill badge-outline badge-{{$item['status'] ? 'success' : 'danger'}} gap-1 items-center">
            <span class="badge badge-dot size-1.5 badge-{{$item['status'] ? 'success' : 'danger'}}">
            </span>
            {{$item['status'] ? 'Đang hoạt động' : 'Đã ẩn'}}
        </span>
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
                        <a class="menu-link" href="/team/{{$item['id']}}">
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
                        <a class="menu-link" href="/team/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-pencil">
                                </i>
                            </span>
                            <span class="menu-title">
                                Chỉnh sửa
                            </span>
                        </a>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" onclick="changeStatusDepartment({{$item['id']}})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-shield-cross">
                                </i>
                            </span>
                            <span class="menu-title">
                                {{$item['status'] ? 'Ẩn' : 'Mở'}}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach