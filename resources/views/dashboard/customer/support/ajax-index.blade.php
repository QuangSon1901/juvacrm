@foreach ($data as $item)
<tr>
    <td class="text-gray-800 font-normal">
        <a class="leading-none hover:text-primary" href="/customer/{{$item['id']}}">
        {{$item['id']}}
        </a>
    </td>
    <td>
        <div class="flex flex-col gap-1.5">
            <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/customer/{{$item['id']}}">
                <span>{{$item['name']}}</span>
                <span class="badge badge-sm badge-outline badge-success">
                    {{$item['status']['name']}}
                </span>
            </a>
            <span class="text-2sm text-gray-700 font-normal">
                ###
            </span>
        </div>
    </td>
    <td>
        <div class="grid gap-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-1.5">
                    <i class="ki-filled ki-sms text-base text-gray-500"></i>
                    <span class="text-sm font-normal text-gray-900">
                    {{$item['email']}}
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="ki-filled ki-phone text-base text-gray-500"></i>
                    <span class="text-sm font-normal text-gray-900">
                    {{$item['phone']}}
                    </span>
                </div>
            </div>
        </div>
    </td>
    <td class="text-sm text-gray-800 font-normal">{{$item['company']}}</td>
    <td>
        <div class="flex flex-wrap gap-2.5 mb-2">
        @foreach ($item['services'] as $service)
        <span class="badge badge-sm badge-light badge-outline">
                {{$service['name']}}
            </span>
        @endforeach
        </div>
    </td>
    <td>
        <div class="flex items-center gap-2.5">
            @if ($item['staff'])
            <div class="flex flex-col gap-0.5">
                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/123">
                    {{$item['staff']['name']}}
                </a>
                <span class="text-xs text-gray-700 font-normal">
                    #{{$item['staff']['id']}}
                </span>
            </div>
            @endif
            
        </div>
    </td>
    <th class="text-gray-700 font-normal min-w-[220px]">
    {{$item['updated_at']}}
    </th>
    <td class="w-[60px]">
        <div class="menu" data-menu="true">
            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical">
                    </i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <a class="menu-link" href="/customer/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-search-list">
                                </i>
                            </span>
                            <span class="menu-title">
                                Xem chi tiết
                            </span>
                        </a>
                        <a class="menu-link" href="/customer/{{$item['id']}}">
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
                        <a class="menu-link" href="/customer/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-questionnaire-tablet">
                                </i>
                            </span>
                            <span class="menu-title">
                                Xem hợp đồng
                            </span>
                        </a>
                        <a class="menu-link" href="/contract/create-view?customer_id={{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-questionnaire-tablet">
                                </i>
                            </span>
                            <span class="menu-title">
                                Lập hợp đồng
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
                            <span class="menu-title">
                                Quy trình tư vấn
                            </span>
                        </a>
                        <a class="menu-link" href="/customer/{{$item['id']}}">
                            <span class="menu-icon">
                                <i class="ki-filled ki-calendar">
                                </i>
                            </span>
                            <span class="menu-title">
                                Lịch hẹn
                            </span>
                        </a>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-icon">
                                <i class="ki-filled ki-shield-cross !text-red-600">
                                </i>
                            </span>
                            <span class="menu-title !text-red-600">
                                Cho vào danh sách đen
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach