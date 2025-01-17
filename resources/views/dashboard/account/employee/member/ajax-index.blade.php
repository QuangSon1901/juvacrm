@foreach ($data as $item)
<tr>
    <td class="text-center">{{$item['index']}}</td>
    <td>
        <div class="flex items-center gap-2.5">
            <div class="flex flex-col gap-0.5">
                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/{{$item['id']}}">
                    {{$item['name']}}
                </a>
                <span class="text-xs text-gray-700 font-normal">
                    ID: {{$item['id']}}
                </span>
            </div>
        </div>
    </td>
    <td>
        <div class="flex flex-wrap gap-2.5 mb-2">
            @foreach ($item['departments'] as $dep)
            <span class="badge badge-sm badge-light badge-outline">
                {{$dep['name']}}
            </span>
            @endforeach
        </div>
    </td>
    <td>
        <div class="grid gap-3">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-1.5">
                    <i class="ki-filled ki-sms text-base text-gray-500"></i>
                    <span class="text-sm font-normal text-gray-900">
                        {{$item['email'] ?? '---'}}
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="ki-filled ki-phone text-base text-gray-500"></i>
                    <span class="text-sm font-normal text-gray-900">
                        {{$item['phone'] ?? '---'}}
                    </span>
                </div>
            </div>
        </div>
    </td>
    <td><span class="badge badge-sm badge-outline badge-{{$item['is_active'] ? 'success' : 'danger'}}">
            {{$item['is_active'] ? 'Đang hoạt động' : 'Đã khoá'}}
        </span></td>
    <td class="text-sm text-gray-800 font-normal"><span class="text-gray-700 font-medium">{{explode(' ', $item['created_at'])[0]}}</span> <br> <span class="text-gray-500">{{explode(' ', $item['created_at'])[1] ?? ''}}</span></td>
    <td class="w-[60px]">
        <div class="menu" data-menu="true">
            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
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
                        <a class="menu-link" href="/member/{{$item['id']}}">
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
                        <button class="reset-password-btn menu-link" data-id="{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-key">
                                </i>
                            </span>
                            <span class="menu-title">
                                Đặt lại mật khẩu
                            </span>
                        </button>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="menu-item">
                        <button class="lock-account-btn menu-link" data-id="{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-lock !text-red-600">
                                </i>
                            </span>
                            <span class="menu-title !text-red-600">
                                {{$item['is_active'] ? 'Khoá tài khoản' : 'Mở khoá'}}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach