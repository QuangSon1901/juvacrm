@foreach ($data as $item)
<tr>
    <td class="text-center">{{$item['index']}}</td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-wrap gap-2">
            <span class="badge badge-sm badge-outline badge-neutral">
                ID: {{$item['id']}}
            </span>
            @if ($item['classification']['id'] != 0)
            <span class="badge badge-sm badge-outline badge-{{$item['classification']['color']}}">
                {{$item['classification']['name']}}
            </span>
            @endif
        </div>
        <a class="leading-none hover:text-primary text-gray-900 font-medium" href="/customer/{{$item['id']}}">
            {{$item['name']}}
        </a>
        @if ($item['status']['id'] != 0)
        <div class="mt-1">
            <span class="badge badge-sm badge-outline badge-{{$item['status']['color']}}">
                {{$item['status']['name']}}
            </span>
        </div>
        @endif
    </td>
    <td>
        <div class="grid gap-3">
            <div class="flex items-center justify-between flex-wrap">
                <div class="flex items-center gap-1.5">
                    <i class="ki-filled ki-sms text-base text-gray-500"></i>
                    <span class="text-sm font-normal text-gray-900">
                        {{$item['email'] ?: 'Chưa có'}}
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="ki-filled ki-phone text-base text-gray-500"></i>
                    <span class="text-sm font-normal text-gray-900">
                        {{$item['phone'] ?: 'Chưa có'}}
                    </span>
                </div>
            </div>
            @if ($item['source']['id'] != 0)
            <div class="flex items-center gap-1.5">
                <i class="ki-filled ki-arrow-right-square text-base text-gray-500"></i>
                <span class="text-sm font-normal text-gray-700">
                    Nguồn: {{$item['source']['name']}}
                </span>
            </div>
            @endif
        </div>
    </td>
    <td>
        <div class="flex flex-wrap gap-2.5 mb-2">
            @if (count($item['services']) > 1)
            <span class="badge badge-sm badge-light badge-outline">
                {{$item['services'][0]['name']}}
            </span>
            <span class="badge badge-sm badge-light badge-outline">
                +{{count($item['services'])-1}}
            </span>
            @else
            @foreach ($item['services'] as $service)
            <span class="badge badge-sm badge-light badge-outline">
                {{$service['name']}}
            </span>
            @endforeach
            @endif
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        @if ($item['staff']['id'] == 0)
        <span class="text-sm text-gray-500">Chưa phân công</span>
        @else
        <div class="flex items-center gap-2.5">
            <div class="flex flex-col gap-0.5">
                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/{{$item['staff']['id']}}">
                    {{$item['staff']['name']}}
                </a>
                <span class="text-xs text-gray-700 font-normal">
                    ID: {{$item['staff']['id']}}
                </span>
            </div>
        </div>
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">{{$item['company'] ?: '---'}}</td>
    <!-- Thêm cột điểm tiềm năng -->
    <td class="text-center">
        @if($item['lead_score'] > 60)
        <span class="badge badge-success">{{$item['lead_score']}}</span>
        @elseif($item['lead_score'] > 30)
        <span class="badge badge-warning">{{$item['lead_score']}}</span>
        @else
        <span class="badge badge-gray">{{$item['lead_score']}}</span>
        @endif
    </td>
    <!-- Thêm cột tương tác gần nhất -->
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['last_interaction'])
        <p class="leading-none text-gray-900 font-medium">{{formatDateTime($item['last_interaction'], 'd-m-Y')}}</p>
        <span class="text-gray-700 text-xs">{{formatDateTime($item['last_interaction'], 'H:i:s')}}</span>
        @else
        <span class="text-sm text-gray-500">Chưa tương tác</span>
        @endif
    </td>
    <td class="w-[60px]">
        <div class="menu" data-menu="true">
            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical">
                    </i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <a class="menu-link" href="/customer/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-search-list">
                                </i>
                            </span>
                            <span class="menu-title text-left">
                                Xem chi tiết
                            </span>
                        </a>
                        <a class="menu-link" href="/customer/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-pencil">
                                </i>
                            </span>
                            <span class="menu-title text-left">
                                Chỉnh sửa
                            </span>
                        </a>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/customer-consultation/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-chart">
                                </i>
                            </span>
                            <span class="menu-title text-left">
                                Quy trình tư vấn
                            </span>
                        </a>
                        <a class="menu-link" href="/appointment/detail/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-calendar">
                                </i>
                            </span>
                            <span class="menu-title text-left">
                                Lịch hẹn
                            </span>
                        </a>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="menu-item">
                        <!-- Thêm nút chuyển đổi khách hàng -->
                        <button class="menu-link convert-lead-btn" data-id="{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-external-drive text-success">
                                </i>
                            </span>
                            <span class="menu-title text-success text-left">
                                Chuyển thành KH chính thức
                            </span>
                        </button>
                        <button class="menu-link black-list-customer-btn" data-id="{{$item['id']}}" data-active="{{$item['is_active']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-shield-cross !text-red-600">
                                </i>
                            </span>
                            <span class="menu-title !text-red-600 text-left">
                                {{$item['is_active'] ? 'Cho vào danh sách đen' : 'Gỡ khỏi danh sách đen'}}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach