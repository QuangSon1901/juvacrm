@foreach ($data as $item)
<tr>
    <td class="text-gray-800 font-normal text-center">
        <span class="leading-none">
            {{$item['index']}}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        <span class="badge badge-sm badge-outline {{ $item['type'] == 0 ? 'badge-success' : 'badge-danger' }}">
            {{ $item['type_text'] }}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-col">
            <span class="font-medium">{{ $item['category']['name'] }}</span>
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        {{ $item['target']['display'] }}
    </td>
    <td class="text-gray-800 font-normal">
        @if($item['type'] == 0)
        <span class="text-success font-semibold">+{{ number_format($item['amount'], 0, ',', '.') }}₫</span>
        @else
        <span class="text-danger font-semibold">-{{ number_format($item['amount'], 0, ',', '.') }}₫</span>
        @endif
    </td>
    <td class="text-gray-800 font-normal">
        {{ $item['paid_date_formatted'] }}
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-col">
            <span>{{ $item['reason'] }}</span>
            @if($item['note'])
            <span class="text-xs text-gray-500">{{ $item['note'] }}</span>
            @endif
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        @php
            $badgeClass = $item['status'] == 1 ? 'badge-success' : 
                         ($item['status'] == 0 ? 'badge-warning' : 'badge-danger');
        @endphp
        <span class="badge badge-sm badge-outline {{ $badgeClass }}">
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
                        <a class="menu-link" href="/transaction/{{ $item['id'] }}/export-pdf" target="_blank">
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
                        <button class="menu-link" onclick="confirmTransaction({{ $item['id'] }})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-shield-tick text-success"></i>
                            </span>
                            <span class="menu-title">
                                Xác nhận hoàn tất
                            </span>
                        </button>
                    </div>
                    @endif
                    <div class="menu-separator"></div>
                    <div class="menu-item">
                        <button class="menu-link" onclick="openEditTransactionModal(
                            '{{ $item['id'] }}', 
                            '{{ $item['type'] }}', 
                            '{{ $item['category']['id'] }}', 
                            '{{ $item['target']['type'] }}', 
                            '{{ isset($item['target']['id']) ? $item['target']['id'] : '' }}',
                            '{{ isset($item['target']['name']) ? $item['target']['name'] : '' }}',
                            '{{ $item['amount'] }}',
                            '{{ $item['paid_date_formatted'] }}',
                            '{{ $item['reason'] }}',
                            '{{ $item['note'] }}',
                            '{{ $item['status'] }}'
                            )">
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
                        <button class="menu-link" onclick="cancelTransaction({{ $item['id'] }})">
                            <span class="menu-icon">
                                <i class="ki-filled ki-cross text-danger"></i>
                            </span>
                            <span class="menu-title text-danger">
                                Hủy phiếu
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