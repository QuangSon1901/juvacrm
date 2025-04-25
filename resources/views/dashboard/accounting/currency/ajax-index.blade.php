@foreach ($data as $item)
<tr>
    <td class="text-gray-800 font-normal text-center">
        <span class="leading-none">
            {{$item['index']}}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        <span class="font-medium">{{ $item['currency_code'] }}</span>
    </td>
    <td class="text-gray-800 font-normal">
        {{ $item['currency_name'] }}
    </td>
    <td class="text-gray-800 font-normal">
        {{ $item['symbol'] ?: 'N/A' }}
    </td>
    <td class="text-gray-800 font-normal">
        @php
            $badgeClass = $item['is_active'] == 1 ? 'badge-success' : 'badge-danger';
        @endphp
        <span class="badge badge-sm badge-outline {{ $badgeClass }}">
            {{ $item['status_text'] }}
        </span>
    </td>
    <td class="text-gray-800 font-normal">
        {{ $item['created_at_formatted'] }}
    </td>
    <td class="w-[60px]">
        <div class="menu" data-menu="true">
            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical"></i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    <div class="menu-item">
                        <button class="menu-link" onclick="openEditCurrencyModal(
                            '{{ $item['id'] }}', 
                            '{{ $item['currency_code'] }}', 
                            '{{ $item['currency_name'] }}', 
                            '{{ $item['symbol'] }}', 
                            '{{ $item['is_active'] }}'
                            )">
                            <span class="menu-icon">
                                <i class="ki-filled ki-pencil"></i>
                            </span>
                            <span class="menu-title">
                                Chỉnh sửa
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach