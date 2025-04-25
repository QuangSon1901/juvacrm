@foreach ($data as $item)
<tr>
    <td class="text-gray-800 font-normal text-center">
        <span class="leading-none">
            {{$item['index']}}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-col">
            <span class="font-medium">{{ $item['name'] }}</span>
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-col gap-0.5">
            <a href="/contract/{{ $item['contract']['id'] }}" class="text-primary hover:text-primary-dark">
                {{ $item['contract']['number'] }}
            </a>
            <span class="text-xs text-gray-600">{{ $item['contract']['name'] }}</span>
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        <a href="/customer/{{ $item['customer']['id'] }}" class="hover:text-primary">
            {{ $item['customer']['name'] }}
        </a>
    </td>
    <td class="text-gray-800 font-normal">
        @php
            $badgeClass = $item['payment_stage'] == 0 ? 'badge-warning' : 
                         ($item['payment_stage'] == 1 ? 'badge-success' : 
                         ($item['payment_stage'] == 2 ? 'badge-primary' : 'badge-danger'));
        @endphp
        <span class="badge badge-sm badge-outline {{ $badgeClass }}">
            {{ $item['payment_stage_text'] }}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        <span class="font-semibold">{{ number_format($item['price'], 0, ',', '.') }}</span>
        <span class="text-sm">{{ $item['currency']['code'] }}</span>
    </td>
    <td class="text-gray-800 font-normal">
        {{ $item['method']['name'] }}
    </td>
    <td class="text-gray-800 font-normal">
        {{ $item['due_date_formatted'] }}
    </td>
    <td class="text-gray-800 font-normal">
        <span class="badge badge-sm badge-outline {{ $item['status'] ? 'badge-success' : 'badge-warning' }}">
            {{ $item['status_text'] }}
        </span>
    </td>
    <td class="w-[60px]">
        <div class="menu" data-menu="true">
            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical"></i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <a class="menu-link" href="/deposit-receipt/{{ $item['id'] }}/export-pdf" target="_blank">
                            <span class="menu-icon">
                                <i class="ki-filled ki-file-down"></i>
                            </span>
                            <span class="menu-title">
                                Xuất PDF
                            </span>
                        </a>
                    </div>
                    @if($item['status'] == 0)
                    <div class="menu-separator"></div>
                    <div class="menu-item">
                        <button class="menu-link" onclick="confirmPayment({{ $item['id'] }})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-shield-tick text-success"></i>
                            </span>
                            <span class="menu-title">
                                Xác nhận thanh toán
                            </span>
                        </button>
                    </div>
                    @endif
                    <div class="menu-separator"></div>
                    <div class="menu-item">
                        <button class="menu-link" onclick="openEditReceiptModal('{{ $item['id'] }}', '{{ $item['name'] }}', '{{ $item['price'] }}', '{{ $item['currency']['id'] }}', '{{ $item['method']['id'] }}', '{{ $item['due_date_formatted'] }}', '{{ $item['status'] }}')">
                            <span class="menu-icon">
                                <i class="ki-filled ki-pencil"></i>
                            </span>
                            <span class="menu-title">
                                Chỉnh sửa
                            </span>
                        </button>
                    </div>
                    @if($item['status'] == 0)
                    <div class="menu-separator"></div>
                    <div class="menu-item">
                        <button class="menu-link" onclick="cancelReceipt({{ $item['id'] }})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-cross text-danger"></i>
                            </span>
                            <span class="menu-title text-danger">
                                Hủy biên nhận
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