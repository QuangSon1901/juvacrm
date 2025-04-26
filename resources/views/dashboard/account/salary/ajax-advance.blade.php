@foreach ($data as $item)
<tr>
    <td class="text-center">
        {{ $item['index'] }}
    </td>
    <td>
        <div class="flex items-center gap-3">
            <div class="flex flex-col">
                <span class="text-gray-800 text-sm font-medium">{{ $item['user']['name'] }}</span>
            </div>
        </div>
    </td>
    <td>{{ $item['request_date'] }}</td>
    <td class="font-medium">{{ $item['amount'] }}</td>
    <td>{{ $item['reason'] }}</td>
    <td>
        @php
            $statusClass = '';
            
            switch($item['status']) {
                case 'pending':
                    $statusClass = 'warning';
                    break;
                case 'approved':
                    $statusClass = 'primary';
                    break;
                case 'rejected':
                    $statusClass = 'danger';
                    break;
                case 'paid':
                    $statusClass = 'success';
                    break;
                default:
                    $statusClass = 'gray';
            }
        @endphp
        
        <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
            {{ $item['status_text'] }}
        </span>
    </td>
    <td>
        @if($item['approver'])
        <div class="flex items-center gap-3">
            <div class="symbol symbol-circle symbol-25px overflow-hidden">
                <img src="{{asset('assets/images/logo/favicon.png')}}" alt="">
            </div>
            <div class="flex flex-col">
                <span class="text-gray-800 text-sm font-medium">{{ $item['approver']['name'] }}</span>
            </div>
        </div>
        @else
        -
        @endif
    </td>
    <td>{{ $item['approval_date'] }}</td>
    <td>
        <div class="menu" data-menu="true">
            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                    <i class="ki-filled ki-dots-vertical"></i>
                </button>
                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                    @if($item['status'] === 'pending')
                    <div class="menu-item">
                        <button class="menu-link" onclick="openProcessModal({{ $item['id'] }}, 'approved')">
                            <span class="menu-icon">
                                <i class="ki-filled ki-check text-success"></i>
                            </span>
                            <span class="menu-title">
                                Duyệt yêu cầu
                            </span>
                        </button>
                    </div>
                    <div class="menu-item">
                        <button class="menu-link" onclick="openProcessModal({{ $item['id'] }}, 'rejected')">
                            <span class="menu-icon">
                                <i class="ki-filled ki-cross text-danger"></i>
                            </span>
                            <span class="menu-title">
                                Từ chối yêu cầu
                            </span>
                        </button>
                    </div>
                    @endif
                    
                    @if($item['status'] === 'approved')
                    <div class="menu-item">
                        <button class="menu-link" onclick="openProcessModal({{ $item['id'] }}, 'paid')">
                            <span class="menu-icon">
                                <i class="ki-filled ki-dollar text-primary"></i>
                            </span>
                            <span class="menu-title">
                                Chi trả tạm ứng
                            </span>
                        </button>
                    </div>
                    @endif
                    
                    @if($item['transaction'])
                    <div class="menu-item">
                        <a class="menu-link" href="/accounting/transaction/{{ $item['transaction']['id'] }}/export-pdf" target="_blank">
                            <span class="menu-icon">
                                <i class="ki-filled ki-document text-primary"></i>
                            </span>
                            <span class="menu-title">
                                Phiếu chi
                            </span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </td>
</tr>
@endforeach