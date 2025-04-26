    @if(count($data) > 0)
        @foreach($data as $item)
        <tr>
            <td class="text-center">{{ $item['index'] }}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="text-gray-900">{{ $item['user']['name'] }}</div>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="text-gray-900">
                        <span class="text-sm font-medium">#{{ $item['contract']['number'] }}</span>
                        <div class="text-2sm text-gray-600">{{ $item['contract']['name'] }}</div>
                    </div>
                </div>
            </td>
            <td>{{ $item['commission_percentage'] }}</td>
            <td>{{ $item['contract_value'] }} VNĐ</td>
            <td class="text-primary font-medium">{{ $item['commission_amount'] }} VNĐ</td>
            <td>
                @if($item['is_paid'])
                    <span class="badge badge-sm badge-outline badge-success">{{ $item['status_text'] }}</span>
                @else
                    <span class="badge badge-sm badge-outline badge-warning">{{ $item['status_text'] }}</span>
                @endif
            </td>
            <td>{{ $item['processed_at'] }}</td>
            <td>
                <div class="menu" data-menu="true">
                    <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                            <i class="ki-filled ki-dots-vertical"></i>
                        </button>
                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                            @if(!$item['is_paid'])
                            <div class="menu-item">
                                <button class="menu-link" onclick="payCommission({{ $item['id'] }})">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-dollar text-success"></i>
                                    </span>
                                    <span class="menu-title">
                                        Thanh toán
                                    </span>
                                </button>
                            </div>
                            @endif
                            
                            @if($item['transaction'])
                            <div class="menu-item">
                                <button class="menu-link" onclick="viewTransaction({{ $item['transaction']['id'] }})">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-eye text-primary"></i>
                                    </span>
                                    <span class="menu-title">
                                        Xem giao dịch
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
    @else
        <tr>
            <td colspan="9" class="text-center py-10">
                <div class="text-gray-600">Không có dữ liệu</div>
            </td>
        </tr>
    @endif