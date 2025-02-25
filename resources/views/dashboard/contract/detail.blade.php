@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin hợp đồng
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
        </div>
    </div>
</div>
<div class="container-fixed">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="col-span-1 lg:col-span-3">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header flex-wrap gap-2">
                        <h3 class="card-title flex items-center gap-2">
                            <span>Hợp đồng #{{$details['contract_number']}}</span>
                        </h3>
                    </div>
                    <div class="card-body lg:py-7.5 grid gap-5">
                        @php
                        use Carbon\Carbon;
                        $now = Carbon::now();
                        @endphp
                        @if ($details['expiry_date'] && Carbon::parse($details['expiry_date'])->lt($now))
                        <div class="badge badge-outline badge-danger px-3">
                            <div class="relative w-full text-sm flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                Hợp đồng này đã hết hạn. Vui lòng kiểm tra và xử lý ngay!
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center justify-between grow border border-gray-200 rounded-xl gap-2 p-5">
                            <div class="flex flex-col lg:flex-row items-center gap-4">
                                @include("dashboard.layouts.icons.user")
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-gray-900">
                                            {{$details['name']}}
                                        </span>
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="name">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                    </div>
                                    <div class="form-info text-gray-800 font-normal">
                                        Được thêm bởi <b>{{$details['user']['name']}}</b> khoảng <b>{{timeAgo(strtotime($details['created_at']))}}</b> trước.
                                        @if ($details['updated_at'] != $details['created_at'])
                                        Đã cập nhật <b>{{timeAgo(strtotime($details['updated_at']))}}</b> trước.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-5">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Loại hình dịch vụ:</span>
                                    <span class="checkbox-label text-gray-800">Chụp ảnh sản phẩm</span>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Trạng thái:</span>
                                    <span class="badge badge-sm badge-outline badge-neutral">
                                        @if ($details['status'] == 0)
                                        Chờ duyệt
                                        @elseif ($details['status'] == 1)
                                        Đang triển khai
                                        @elseif ($details['status'] == 2)
                                        Đã hoàn tất
                                        @endif
                                    </span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="status">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Nhân viên phụ trách:</span>
                                    <a class="checkbox-label text-gray-800 hover:text-primary" href="/member/{{$details['user']['id']}}">{{$details['user']['name']}}</a>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="user_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Khách hàng:</span>
                                    <a class="checkbox-label text-gray-800 hover:text-primary" href="/customer/{{$details['customer']['id']}}">{{$details['customer']['name']}}</a>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="provider_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col gap-5">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Ngày ký:</span>
                                    <span class="checkbox-label text-gray-800">{{formatDateTime($details['sign_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="sign_date">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Ngày hiệu lực:</span>
                                    <span class="checkbox-label text-gray-800">{{formatDateTime($details['effective_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="effective_date">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Ngày hết hạn:</span>
                                    <span class="checkbox-label text-gray-800">{{formatDateTime($details['expiry_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="expiry_date">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Tổng giá trị:</span>
                                    <span class="checkbox-label text-gray-800">{{number_format($details['total_value'], 0, ',', '.')}} VND</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Ghi chú</span>
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="note">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                            </div>
                            <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                <div class="ql-editor" style="white-space: normal;">
                                    {!! nl2br(e($details['note'] ?? '---')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Điều khoản chung</span>
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="terms_and_conditions">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                            </div>
                            <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                <div class="ql-editor" style="white-space: normal;">
                                    {!! nl2br(e($details['terms_and_conditions'] ?? '---')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="menu-separator simple"></div>
                        <div class="grid grid-cols-1 lg:!grid-cols-2 gap-5">
                            <div class="col-span-1">
                                <div class="flex flex-col gap-2.5">
                                    <div class="flex items-center justify-between">
                                        <div class="checkbox-group">
                                            <span class="checkbox-label text-gray-800 !font-bold">Dịch vụ</span>
                                            <span class="badge badge-xs badge-primary badge-outline">{{count($details['services'])}}</span>
                                        </div>
                                        <button class="btn btn-light btn-xs" data-modal-toggle="#add-service-modal">
                                            <i class="ki-filled ki-plus"></i>
                                            Thêm dịch vụ
                                        </button>
                                    </div>
                                    <div class="flex items-center flex-wrap justify-between gap-2.5">
                                        @if (count($details['services']) == 0)
                                        ---
                                        @else
                                        @foreach ($details['services'] as $service)
                                        <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-gray-200 pl-4 hover:bg-gray-50 hover:after:bg-blue-800">
                                            <div class="flex flex-col">
                                                <div>
                                                    <span class="checkbox-label font-semibold hover:text-primary-active">{{$service['name']}}</span>
                                                    @if (!$service['is_active'])
                                                    <span class="badge badge-sm badge-outline badge-danger">Đã hủy bỏ</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="checkbox-label font-normal text-gray-700">Số lượng: {{$service['quantity']}}</span>
                                                    <span>-</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Giá: {{number_format($service['price'], 0, ',', '.')}} VND</span>
                                                    @if ($service['note'])
                                                    <span>-</span>
                                                    <span class="checkbox-label font-normal text-gray-700">{{$service['note']}}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="menu" data-menu="true">
                                                <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                    <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                        <i class="ki-filled ki-dots-vertical"></i>
                                                    </button>
                                                    <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                        <div class="menu-item">
                                                            <button class="menu-link" data-modal-toggle="#edit-service-modal" onclick="loadServiceData({{$service['id']}})">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-pencil"></i>
                                                                </span>
                                                                <span class="menu-title">Chỉnh sửa</span>
                                                            </button>
                                                        </div>
                                                        @if ($service['is_active'])
                                                        <div class="menu-item">
                                                            <button class="menu-link" onclick="cancelService({{$service['id']}})">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-trash"></i>
                                                                </span>
                                                                <span class="menu-title">Hủy bỏ</span>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-1">
                                <div class="flex flex-col gap-2.5">
                                    <div class="flex items-center justify-between">
                                        <div class="checkbox-group">
                                            <span class="checkbox-label text-gray-800 !font-bold">Biên nhận thanh toán</span>
                                            <span class="badge badge-xs badge-primary badge-outline">{{count($details['payments'])}}</span>
                                        </div>
                                        <button class="btn btn-light btn-xs" data-modal-toggle="#add-payment-modal">
                                            <i class="ki-filled ki-plus"></i>
                                            Thêm biên nhận
                                        </button>
                                    </div>
                                    <div class="flex items-center flex-wrap justify-between gap-2.5">
                                        @if (count($details['payments']) == 0)
                                        ---
                                        @else
                                        @foreach ($details['payments'] as $payment)
                                        <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-gray-200 pl-4 hover:bg-gray-50 hover:after:bg-blue-800">
                                            <div class="flex flex-col">
                                                <div>
                                                    <span class="checkbox-label font-semibold hover:text-primary-active">{{$payment['name']}}</span>
                                                    <span class="badge badge-sm badge-outline badge-{{ $payment['status'] ? 'success' : 'warning' }}">
                                                        {{ $payment['status'] ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="checkbox-label font-normal text-gray-700">Số tiền: {{number_format($payment['price'], 0, ',', '.')}} {{$payment['currency']}}</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Phương thức: {{$payment['method']}}</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Ngày: {{$payment['due_date']}}</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Loại phiếu:
                                                        @switch($payment['payment_stage'])
                                                        @case(0) Biên nhận cọc @break
                                                        @case(1) Tiền thưởng thêm @break
                                                        @case(2) Biên nhận cuối @break
                                                        @case(3) Tiền khấu trừ @break
                                                        @default Unknown @break
                                                        @endswitch
                                                    </span>
                                                    @if ($payment['percentage'])
                                                    <span class="checkbox-label font-normal text-gray-700">Tỷ lệ: {{$payment['percentage']}}%</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="menu" data-menu="true">
                                                @if ($payment['status'] == 0) <!-- Chỉ hiển thị menu cho biên nhận chưa thanh toán -->
                                                <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                    <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                        <i class="ki-filled ki-dots-vertical"></i>
                                                    </button>
                                                    <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                        <div class="menu-item">
                                                            <button class="menu-link" data-modal-toggle="#edit-payment-modal" onclick="loadPaymentData({{$payment['id']}})">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-pencil"></i>
                                                                </span>
                                                                <span class="menu-title">Chỉnh sửa</span>
                                                            </button>
                                                        </div>
                                                        <div class="menu-item">
                                                            <button class="menu-link" onclick="cancelPayment({{$payment['id']}})">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-trash"></i>
                                                                </span>
                                                                <span class="menu-title">Hủy</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Công việc liên quan</span>
                                    <span class="badge badge-xs badge-primary badge-outline">{{count($details['tasks'])}}</span>
                                </div>
                            </div>
                            <div class="flex items-center flex-wrap justify-between gap-2.5">
                                @if (count($details['tasks']) == 0)
                                ---
                                @else
                                @foreach ($details['tasks'] as $task)
                                <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-gray-200 pl-4 hover:bg-gray-50 hover:after:bg-blue-800">
                                    <div class="flex flex-col">
                                        <div>
                                            <a href="/task/{{$task['id']}}">
                                                <span class="checkbox-label font-normal text-primary">#{{$task['id']}}:</span>
                                                <span class="checkbox-label font-semibold hover:text-primary-active">{{$task['name']}}</span>
                                            </a>
                                        </div>
                                        <div>
                                            <span class="checkbox-label font-normal text-gray-700">{{$task['qty_completed']}}/{{$task['qty_request']}}</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$task['status']['color']}}">{{$task['status']['name']}}</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-medium"><a class="hover:text-primary-active" href="/member/{{$task['assign']['id']}}">{{$task['assign']['name']}}</a></span>
                                            @if ($task['start_date'])
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">Từ <span class="font-medium">{{formatDateTime($task['start_date'], 'd-m-Y')}}</span></span>
                                            @endif
                                            @if ($task['due_date'])
                                            <span class="checkbox-label font-normal">đến <span class="font-medium">{{formatDateTime($task['due_date'], 'd-m-Y')}}</span></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal cập nhật thông tin hợp đồng -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-contract-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Cập nhật thông tin</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <input class="input hidden" name="name" type="text" placeholder="Tên hợp đồng mới">
                    <select name="status" class="select hidden">
                        <option value="" selected>Chọn trạng thái</option>
                        <option value="0">Chờ duyệt</option>
                        <option value="1">Đang triển khai</option>
                        <option value="2">Đã hoàn thành</option>
                    </select>
                    <select name="user_id" class="select hidden">
                        <option value="" selected>Chọn nhân viên</option>
                        @foreach ($users as $user)
                        <option value="{{$user['id']}}">{{$user['name']}}</option>
                        @endforeach
                    </select>
                    <select name="provider_id" class="select hidden">
                        <option value="" selected>Chọn khách hàng</option>
                        @foreach ($customers as $customer)
                        <option value="{{$customer['id']}}">{{$customer['name']}}</option>
                        @endforeach
                    </select>
                    <input class="input hidden" name="sign_date" type="text" placeholder="Ngày ký (DD/MM/YYYY)">
                    <input class="input hidden" name="effective_date" type="text" placeholder="Ngày hiệu lực (DD/MM/YYYY)">
                    <input class="input hidden" name="expiry_date" type="text" placeholder="Ngày hết hạn (DD/MM/YYYY)">
                    <textarea class="textarea hidden" name="note" rows="5" placeholder="Ghi chú"></textarea>
                    <textarea class="textarea hidden" name="terms_and_conditions" rows="5" placeholder="Điều khoản chung"></textarea>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Xong</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Thêm dịch vụ -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-service-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm dịch vụ mới</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="add-service-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <input type="hidden" name="contract_id" value="{{$details['id']}}">
                    <span class="checkbox-label text-gray-800 !font-bold">Chọn dịch vụ</span>
                    <div class="checkbox-group">
                        <select name="service_id" class="select">
                            <option value="" selected>Chọn dịch vụ</option>
                            @foreach ($services as $service)
                            <option value="{{$service['id']}}">{{$service['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Số lượng</span>
                    <div class="checkbox-group">
                        <input name="quantity" class="input" type="number" min="1" placeholder="Số lượng">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Giá</span>
                    <div class="checkbox-group">
                        <input name="price" class="input" type="number" min="0" placeholder="Giá tiền (VND)">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Ghi chú</span>
                    <div class="checkbox-group">
                        <textarea name="note" class="textarea" rows="3" placeholder="Ghi chú"></textarea>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chỉnh sửa dịch vụ -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-service-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Chỉnh sửa dịch vụ</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-service-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <input type="hidden" name="id" id="edit-service-id">
                    <span class="checkbox-label text-gray-800 !font-bold">Chọn dịch vụ</span>
                    <div class="checkbox-group">
                        <select name="service_id" class="select">
                            <option value="" selected>Chọn dịch vụ</option>
                            @foreach ($services as $service)
                            <option value="{{$service['id']}}">{{$service['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Số lượng</span>
                    <div class="checkbox-group">
                        <input name="quantity" class="input" type="number" min="1" placeholder="Số lượng">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Giá</span>
                    <div class="checkbox-group">
                        <input name="price" class="input" type="number" min="0" placeholder="Giá tiền (VND)">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Ghi chú</span>
                    <div class="checkbox-group">
                        <textarea name="note" class="textarea" rows="3" placeholder="Ghi chú"></textarea>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Thêm biên nhận -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-payment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm biên nhận mới</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="add-payment-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <input type="hidden" name="contract_id" value="{{$details['id']}}">
                    <span class="checkbox-label text-gray-800 !font-bold">Tên biên nhận</span>
                    <div class="checkbox-group">
                        <input name="name" class="input" type="text" placeholder="Tên biên nhận">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Số tiền</span>
                    <div class="checkbox-group">
                        <input name="price" class="input" type="number" min="0" placeholder="Giá tiền">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Tiền tệ</span>
                    <div class="checkbox-group">
                        <select name="currency_id" class="select">
                            <option value="" selected>Chọn tiền tệ</option>
                            @foreach ($currencies as $currency)
                            <option value="{{$currency['id']}}">{{$currency['currency_code']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Phương thức</span>
                    <div class="checkbox-group">
                        <select name="method_id" class="select">
                            <option value="" selected>Chọn phương thức</option>
                            @foreach ($payment_methods as $method)
                            <option value="{{$method['id']}}">{{$method['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Ngày</span>
                    <div class="checkbox-group">
                        <input name="due_date" class="input" type="text" placeholder="DD/MM/YYYY">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Loại phiếu</span>
                    <div class="checkbox-group">
                        <select name="payment_stage" class="select">
                            <option value="0">Biên nhận cọc</option>
                            <option value="1">Tiền thưởng thêm</option>
                            <option value="2">Biên nhận cuối</option>
                            <option value="3">Tiền khấu trừ</option>
                        </select>
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Trạng thái</span>
                    <div class="checkbox-group">
                        <label class="checkbox">
                            <input name="status" type="checkbox" value="1">
                            <span class="checkbox-label">Đã thanh toán</span>
                        </label>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chỉnh sửa biên nhận -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-payment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Chỉnh sửa biên nhận</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-payment-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <input type="hidden" name="id" id="edit-payment-id">
                    <span class="checkbox-label text-gray-800 !font-bold">Tên biên nhận</span>
                    <div class="checkbox-group">
                        <input name="name" class="input" type="text" placeholder="Tên biên nhận">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Số tiền</span>
                    <div class="checkbox-group">
                        <input name="price" class="input" type="number" min="0" placeholder="Giá tiền">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Tiền tệ</span>
                    <div class="checkbox-group">
                        <select name="currency_id" class="select">
                            <option value="" selected>Chọn tiền tệ</option>
                            @foreach ($currencies as $currency)
                            <option value="{{$currency['id']}}">{{$currency['currency_code']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Phương thức</span>
                    <div class="checkbox-group">
                        <select name="method_id" class="select">
                            <option value="" selected>Chọn phương thức</option>
                            @foreach ($payment_methods as $method)
                            <option value="{{$method['id']}}">{{$method['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Ngày</span>
                    <div class="checkbox-group">
                        <input name="due_date" class="input" type="text" placeholder="DD/MM/YYYY">
                    </div>
                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Trạng thái</span>
                    <div class="checkbox-group">
                        <label class="checkbox">
                            <input name="status" type="checkbox" value="1">
                            <span class="checkbox-label">Đã thanh toán</span>
                        </label>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push("actions")
<a href="{{ route('dashboard.contract.export-pdf', $details['id']) }}" class="btn btn-light">Xuất PDF</a>
@endpush
@push('scripts')
<script>
    $(function() {
        $('button[data-modal-toggle="#update-contract-modal"][data-name]').on('click', function() {
            let _this = $(this);
            let _modal = $('#update-contract-modal');
            _modal.find('input[name], select[name], textarea[name]').val('').addClass('hidden');
            _modal.find(`input[name=${_this.attr('data-name')}], select[name=${_this.attr('data-name')}], textarea[name=${_this.attr('data-name')}]`).removeClass('hidden');
        });

        $('#update-contract-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateContract();
        });

        flatpickrMake($("input[name=sign_date], input[name=effective_date], input[name=expiry_date]"), 'date');
    });

    async function postUpdateContract() {
        let field = $('#update-contract-modal form').find('input:not(.hidden),select:not(.hidden),textarea:not(.hidden)');
        let method = "post",
            url = "/contract/update",
            params = null,
            data = {
                id: "{{$details['id']}}",
                [field.attr('name')]: field.val()
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }
</script>
<script>
    $(function() {
        // Thêm dịch vụ
        $('#add-service-form').on('submit', function(e) {
            e.preventDefault();
            postAddService();
        });

        // Chỉnh sửa dịch vụ
        $('#edit-service-form').on('submit', function(e) {
            e.preventDefault();
            postUpdateService();
        });
    });

    // Load dữ liệu dịch vụ vào modal chỉnh sửa
    function loadServiceData(serviceId) {
        const service = @json($details['services']).find(s => s.id === serviceId);
        if (service) {
            $('#edit-service-id').val(service.id);
            $('#edit-service-form [name=service_id]').val(service.service_id);
            $('#edit-service-form [name=quantity]').val(service.quantity);
            $('#edit-service-form [name=price]').val(service.price);
            $('#edit-service-form [name=note]').val(service.note);
        }
    }

    // Thêm dịch vụ
    async function postAddService() {
        let method = "post",
            url = "/contract/add-service",
            params = null,
            data = $('#add-service-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                break;
        }
    }

    // Cập nhật dịch vụ
    async function postUpdateService() {
        let method = "post",
            url = "/contract/update-service",
            params = null,
            data = $('#edit-service-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                break;
        }
    }

    // Hủy bỏ dịch vụ
    async function cancelService(serviceId) {
        Notiflix.Confirm.show(
            'Hủy dịch vụ',
            'Bạn có chắc chắn muốn hủy dịch vụ này?',
            'Đúng',
            'Hủy',
            async () => {
                    let method = "post",
                        url = "/contract/cancel-service",
                        params = null,
                        data = {
                            id: serviceId
                        };
                    let res = await axiosTemplate(method, url, params, data);
                    switch (res.data.status) {
                        case 200:
                            showAlert('success', res.data.message);
                            window.location.reload();
                            break;
                        default:
                            showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                            break;
                    }
                },
                () => {}, {}
        );
    }
</script>
<script>
    $(function() {
        // Thêm biên nhận
        $('#add-payment-form').on('submit', function(e) {
            e.preventDefault();
            postAddPayment();
        });

        // Chỉnh sửa biên nhận
        $('#edit-payment-form').on('submit', function(e) {
            e.preventDefault();
            postUpdatePayment();
        });

        flatpickrMake($("input[name=due_date]"), 'datetime');
    });

    // Load dữ liệu biên nhận vào modal chỉnh sửa
    function loadPaymentData(paymentId) {
        const payment = @json($details['payments']).find(p => p.id === paymentId);
        console.log(payment);
        
        if (payment) {
            $('#edit-payment-id').val(payment.id);
            $('#edit-payment-form [name=name]').val(payment.name);
            $('#edit-payment-form [name=price]').val(payment.price);
            $('#edit-payment-form [name=currency_id]').val(payment.currency_id);
            $('#edit-payment-form [name=method_id]').val(payment.method_id);
            $('#edit-payment-form [name=due_date]').val(payment.due_date);
            $('#edit-payment-form [name=status]').prop('checked', payment.status == 1);
        }
    }

    // Thêm biên nhận
    async function postAddPayment() {
        let method = "post",
            url = "/contract/add-payment",
            params = null,
            data = $('#add-payment-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                break;
        }
    }

    // Cập nhật biên nhận
    async function postUpdatePayment() {
        let method = "post",
            url = "/contract/update-payment",
            params = null,
            data = $('#edit-payment-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                break;
        }
    }

    // Hủy biên nhận
    async function cancelPayment(paymentId) {
        Notiflix.Confirm.show(
            'Hủy biên nhận',
            'Bạn có chắc chắn muốn hủy biên nhận này?',
            'Đúng',
            'Hủy',
            async () => {
                let method = "post",
                    url = "/contract/cancel-payment",
                    params = null,
                    data = { id: paymentId };
                let res = await axiosTemplate(method, url, params, data);
                switch (res.data.status) {
                    case 200:
                        showAlert('success', res.data.message);
                        window.location.reload();
                        break;
                    default:
                        showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                        break;
                }
            },
            () => {}, {}
        );
    }
</script>
@endpush