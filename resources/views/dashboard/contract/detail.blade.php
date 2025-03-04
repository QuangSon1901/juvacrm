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
                        $canEdit = ($details['status'] == 0);
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
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="name">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="form-info text-gray-800 font-normal">
                                        Được thêm bởi <b>{{$details['creator']['name']}}</b> khoảng <b>{{timeAgo(strtotime($details['created_at']))}}</b> trước.
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
                                        {{$details['status_text']}}
                                    </span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="status">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Nhân viên phụ trách:</span>
                                    <a class="checkbox-label text-gray-800 hover:text-primary" href="/member/{{$details['user']['id']}}">{{$details['user']['name']}}</a>

                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="user_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Khách hàng:</span>
                                    <a class="checkbox-label text-gray-800 hover:text-primary" href="/customer/{{$details['provider']['id']}}">{{$details['provider']['name']}}</a>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="provider_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col gap-5">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Ngày ký:</span>
                                    <span class="checkbox-label text-gray-800">{{formatDateTime($details['sign_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="sign_date">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Ngày hiệu lực:</span>
                                    <span class="checkbox-label text-gray-800">{{formatDateTime($details['effective_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="effective_date">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Ngày hết hạn:</span>
                                    <span class="checkbox-label text-gray-800">{{formatDateTime($details['expiry_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="expiry_date">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <div class="checkbox-label text-gray-800 !font-bold">Tổng giá trị hợp đồng:</div>
                                    <div class="checkbox-label text-gray-800">{{ number_format($details['payment_summary']['total_value'], 0, ',', '.') }} VND</div>
                                </div>
                                <div class="checkbox-group">
                                    <div class="checkbox-label text-gray-800 !font-bold">Đã thanh toán:</div>
                                    <div class="checkbox-label !text-green-600">{{ number_format($details['payment_summary']['total_paid'], 0, ',', '.') }} VND ({{ $details['payment_summary']['payment_percentage'] }}% giá trị hợp đồng)</div>
                                </div>

                                <div class="checkbox-group">
                                    <div class="checkbox-label text-gray-800 !font-bold">Còn phải thanh toán:</div>
                                    <div class="checkbox-label !text-orange-600">{{ number_format($details['payment_summary']['total_remaining'], 0, ',', '.') }} VND</div>
                                </div>

                                <div class="checkbox-group">
                                    <div class="checkbox-label text-gray-800 !font-bold">Đã khấu trừ:</div>
                                    <div class="checkbox-label !text-red-600">{{ number_format($details['payment_summary']['total_deduction'], 0, ',', '.') }} VND</div>
                                </div>

                                <div class="checkbox-group">
                                    <div class="checkbox-label text-gray-800 !font-bold">Thanh toán thừa:</div>
                                    <div class="checkbox-label !text-blue-600">{{ number_format($details['payment_summary']['total_excess'], 0, ',', '.') }} VND</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Ghi chú</span>
                                @if ($canEdit)
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="note">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                                @endif
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
                                @if ($canEdit)
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="terms_and_conditions">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                                @endif
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
                                        @if ($canEdit)
                                        <button class="btn btn-light btn-xs" data-modal-toggle="#add-service-modal">
                                            <i class="ki-filled ki-plus"></i>
                                            Thêm dịch vụ
                                        </button>
                                        @endif
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
                                                </div>
                                                <div>
                                                    <span class="checkbox-label font-normal text-gray-700">Số lượng: {{$service['quantity']}}</span>
                                                    <span>-</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Giá: {{number_format($service['price'], 0, ',', '.')}} VND</span>
                                                    @if (isset($service['note']) && $service['note'])
                                                    <span>-</span>
                                                    <span class="checkbox-label font-normal text-gray-700">{{$service['note']}}</span>
                                                    @endif
                                                </div>

                                                <!-- Hiển thị dịch vụ con nếu có -->
                                                @if (isset($service['sub_services']) && count($service['sub_services']) > 0)
                                                <div class="mt-2 border-gray-200">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-700"><i class="ki-filled ki-double-right-arrow"></i></span>
                                                        <span class="checkbox-label font-semibold">Dịch vụ con:</span>
                                                    </div>
                                                    @foreach ($service['sub_services'] as $subService)
                                                    <div class="pl-6">
                                                        <span class="checkbox-label font-normal text-gray-700">{{$subService['name']}}</span>
                                                        <span>-</span>
                                                        <span class="checkbox-label font-normal text-gray-700">Số lượng: {{$subService['quantity']}}</span>
                                                        @if (isset($subService['note']) && $subService['note'])
                                                        <span>-</span>
                                                        <span class="checkbox-label font-normal text-gray-700">{{$subService['note']}}</span>
                                                        @endif
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                            @if ($canEdit)
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
                                                        <div class="menu-item">
                                                            <button class="menu-link" onclick="cancelService({{$service['id']}})">
                                                                <span class="menu-icon">
                                                                    <i class="ki-filled ki-trash"></i>
                                                                </span>
                                                                <span class="menu-title">Hủy bỏ</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
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
                                                        {{ $payment['status_text'] }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="checkbox-label font-normal text-gray-700">Số tiền: {{number_format($payment['price'], 0, ',', '.')}} {{$payment['currency']['code']}}</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Phương thức: {{$payment['method']['name']}}</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Ngày: {{$payment['due_date_formatted']}}</span>
                                                    <span class="checkbox-label font-normal text-gray-700">Loại phiếu: {{$payment['payment_stage_text']}}</span>
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
                                    <div class="flex flex-col w-full">
                                        <div>
                                            <a href="/task/{{$task['id']}}">
                                                <span class="checkbox-label font-normal text-primary">#{{$task['id']}}:</span>
                                                <span class="checkbox-label font-semibold hover:text-primary-active">{{$task['name']}}</span>
                                            </a>
                                            <span class="badge badge-sm badge-outline" style="color: {{$task['status']['color']}}; border-color: {{$task['status']['color']}};">
                                                {{$task['status']['name']}}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="checkbox-label font-normal text-gray-700">Số lượng: {{$task['qty_completed']}}/{{$task['qty_request']}}</span>
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

                                        <!-- Hiển thị subtask nếu có -->
                                        @if (count($task['sub_tasks']) > 0)
                                        <div class="mt-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-700"><i class="ki-filled ki-double-right-arrow"></i></span>
                                                <span class="checkbox-label font-semibold">Công việc:</span>
                                            </div>
                                            @foreach ($task['sub_tasks'] as $subTask)
                                            <div class="pl-6 text-sm">
                                                <a href="/task/{{$subTask['id']}}" class="checkbox-label font-normal hover:text-primary">
                                                    #{{$subTask['id']}}: {{$subTask['name']}}
                                                </a>
                                                <span class="badge badge-xs" style="color: {{$subTask['status']['color']}}; border-color: {{$subTask['status']['color']}};">
                                                    {{$subTask['status']['name']}}
                                                </span>
                                                <span class="checkbox-label font-normal">- Số lượng: {{$subTask['qty_completed']}}/{{$subTask['qty_request']}}</span>
                                                - <a href="/member/{{$subTask['assign']['id']}}" class="checkbox-label font-normal hover:text-primary">{{$subTask['assign']['name']}}</a>

                                                <!-- Hiển thị sub-sub task nếu có -->
                                                @if (isset($subTask['sub_tasks']) && count($subTask['sub_tasks']) > 0)
                                                <div class="mt-1 pl-3 border-l border-gray-200">
                                                    @foreach ($subTask['sub_tasks'] as $childTask)
                                                    <div class="text-xs mb-1">
                                                        <span class="checkbox-label font-normal">
                                                            - {{$childTask['name']}}
                                                        </span>
                                                        <span class="checkbox-label font-normal">- Số lượng: {{$childTask['qty_completed']}}/{{$childTask['qty_request']}}</span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
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

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-service-modal" style="z-index: 90;">
    <div class="modal-content max-w-[700px] top-5 lg:top-[15%]">
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

                    <div class="flex items-center justify-between">
                        <span class="checkbox-label text-gray-800 !font-bold"></span>
                        <div class="flex gap-2">
                            <label class="checkbox flex items-center gap-2">
                                <input type="radio" name="service_type" value="service" checked onclick="toggleServiceTypeFields('service')">
                                <span class="checkbox-label text-gray-700">Dịch vụ có sẵn</span>
                            </label>
                            <label class="checkbox flex items-center gap-2">
                                <input type="radio" name="service_type" value="other" onclick="toggleServiceTypeFields('other')">
                                <span class="checkbox-label text-gray-700">Mục khác</span>
                            </label>
                        </div>
                    </div>

                    <div id="service-select-container">
                        <span class="checkbox-label text-gray-800 !font-bold">Chọn dịch vụ</span>
                        <div class="checkbox-group">
                            <select name="service_id" class="select">
                                <option value="" selected>Chọn dịch vụ</option>
                                @foreach ($services as $service)
                                <option value="{{$service['id']}}">{{$service['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="custom-service-container" class="hidden">
                        <span class="checkbox-label text-gray-800 !font-bold">Tên dịch vụ</span>
                        <div class="checkbox-group">
                            <input name="custom_name" class="input" type="text" placeholder="Nhập tên dịch vụ tùy chỉnh">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Số lượng</span>
                            <div class="checkbox-group">
                                <input name="quantity" class="input" type="number" min="1" placeholder="Số lượng">
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Giá</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="price" class="input pl-8" type="number" min="0" placeholder="Giá tiền (VND)">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Ghi chú</span>
                    <div class="checkbox-group">
                        <textarea name="note" class="textarea" rows="2" placeholder="Ghi chú"></textarea>
                    </div>

                    <!-- Sub-services section -->
                    <div class="mt-3">
                        <div class="flex items-center justify-between">
                            <span class="checkbox-label text-gray-800 !font-bold">Dịch vụ con</span>
                            <button type="button" class="btn btn-light btn-xs" onclick="addSubServiceField()">
                                <i class="ki-filled ki-plus"></i>
                                Thêm dịch vụ con
                            </button>
                        </div>

                        <div id="sub-services-container" class="mt-3">
                            <!-- Sub-services will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tương tự, cập nhật modal chỉnh sửa dịch vụ -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-service-modal" style="z-index: 90;">
    <div class="modal-content max-w-[700px] top-5 lg:top-[15%]">
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
                    <input type="hidden" name="service_type" id="edit-service-type">

                    <div id="edit-service-select-container">
                        <span class="checkbox-label text-gray-800 !font-bold">Chọn dịch vụ</span>
                        <div class="checkbox-group">
                            <select name="service_id" class="select">
                                <option value="" selected>Chọn dịch vụ</option>
                                @foreach ($services as $service)
                                <option value="{{$service['id']}}">{{$service['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="edit-custom-service-container" class="hidden">
                        <span class="checkbox-label text-gray-800 !font-bold">Tên dịch vụ</span>
                        <div class="checkbox-group">
                            <input name="custom_name" class="input" type="text" placeholder="Nhập tên dịch vụ tùy chỉnh">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Số lượng</span>
                            <div class="checkbox-group">
                                <input name="quantity" class="input" type="number" min="1" placeholder="Số lượng">
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Giá</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="price" class="input pl-8" type="number" min="0" placeholder="Giá tiền (VND)">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Ghi chú</span>
                    <div class="checkbox-group">
                        <textarea name="note" class="textarea" rows="2" placeholder="Ghi chú"></textarea>
                    </div>

                    <!-- Sub-services section -->
                    <div class="mt-3">
                        <div class="flex items-center justify-between">
                            <span class="checkbox-label text-gray-800 !font-bold">Dịch vụ con</span>
                            <button type="button" class="btn btn-light btn-xs" onclick="addEditSubServiceField()">
                                <i class="ki-filled ki-plus"></i>
                                Thêm dịch vụ con
                            </button>
                        </div>

                        <div id="edit-sub-services-container" class="mt-3">
                            <!-- Sub-services will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-payment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[15%]">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Phần trăm tổng giá trị (%)</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="percentage" class="input pl-8" type="number" min="0" max="100" step="0.1" placeholder="% giá trị" oninput="calculatePaymentAmount()">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Số tiền</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="price" class="input pl-8" type="number" placeholder="Giá tiền">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Tiền tệ</span>
                            <div class="checkbox-group">
                                <select name="currency_id" class="select">
                                    <option value="" selected>Chọn tiền tệ</option>
                                    @foreach ($currencies as $currency)
                                    <option value="{{$currency['id']}}">{{$currency['currency_code']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Phương thức</span>
                            <div class="checkbox-group">
                                <select name="method_id" class="select">
                                    <option value="" selected>Chọn phương thức</option>
                                    @foreach ($payment_methods as $method)
                                    <option value="{{$method['id']}}">{{$method['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Ngày</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="due_date" class="input pl-8" type="text" placeholder="DD/MM/YYYY">
                                    <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Loại phiếu</span>
                            <div class="checkbox-group">
                                <select name="payment_stage" class="select">
                                    <option value="0">Biên nhận cọc</option>
                                    <option value="1">Tiền thưởng thêm</option>
                                    <option value="2">Biên nhận cuối</option>
                                    <option value="3">Tiền khấu trừ</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <span class="checkbox-label text-gray-800 !font-bold mt-2">Trạng thái</span>
                    <div class="checkbox-group">
                        <label class="checkbox flex items-center gap-2">
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
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-payment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[15%]">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Phần trăm tổng giá trị (%)</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="percentage" class="input pl-8" type="number" min="0" max="100" step="0.1" placeholder="% giá trị" oninput="calculatePaymentAmount()">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Số tiền</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="price" class="input pl-8" type="number" placeholder="Giá tiền">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Tiền tệ</span>
                            <div class="checkbox-group">
                                <select name="currency_id" class="select">
                                    <option value="" selected>Chọn tiền tệ</option>
                                    @foreach ($currencies as $currency)
                                    <option value="{{$currency['id']}}">{{$currency['currency_code']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Phương thức</span>
                            <div class="checkbox-group">
                                <select name="method_id" class="select">
                                    <option value="" selected>Chọn phương thức</option>
                                    @foreach ($payment_methods as $method)
                                    <option value="{{$method['id']}}">{{$method['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Ngày</span>
                            <div class="checkbox-group">
                                <div class="relative w-full">
                                    <input name="due_date" class="input pl-8" type="text" placeholder="DD/MM/YYYY">
                                    <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="checkbox-label text-gray-800 !font-bold mt-2">Trạng thái</span>
                            <div class="checkbox-group">
                                <label class="checkbox flex items-center gap-2">
                                    <input name="status" type="checkbox" value="1">
                                    <span class="checkbox-label">Đã thanh toán</span>
                                </label>
                            </div>
                        </div>
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

        // Thiết lập flatpickr cho các input ngày
        flatpickrMake($('input[name="due_date"]'), 'datetime');
    });

    // Load dữ liệu biên nhận vào modal chỉnh sửa
    function loadPaymentData(paymentId) {
        const payment = @json($details['payments']).find(p => p.id === paymentId);

        if (payment) {
            $('#edit-payment-id').val(payment.id);
            $('#edit-payment-form [name=name]').val(payment.name);
            $('#edit-payment-form [name=percentage]').val(payment.percentage);
            $('#edit-payment-form [name=price]').val(payment.price);
            $('#edit-payment-form [name=currency_id]').val(payment.currency.id);
            $('#edit-payment-form [name=method_id]').val(payment.method.id);
            $('#edit-payment-form [name=due_date]').val(payment.due_date_formatted);
            $('#edit-payment-form [name=status]').prop('checked', payment.status == 1);
        }
    }

    // Tính toán số tiền dựa trên phần trăm
    function calculatePaymentAmount() {
        const percentage = parseFloat($('#add-payment-form [name=percentage]').val()) || 0;
        const totalValue = parseFloat('{{$details["total_value"]}}') || 0;

        if (percentage > 0 && totalValue > 0) {
            const amount = (percentage / 100) * totalValue;
            $('#add-payment-form [name=price]').val(Math.round(amount));
        }

        // Cũng áp dụng cho form chỉnh sửa
        const editPercentage = parseFloat($('#edit-payment-form [name=percentage]').val()) || 0;
        if (editPercentage > 0 && totalValue > 0) {
            const editAmount = (editPercentage / 100) * totalValue;
            $('#edit-payment-form [name=price]').val(Math.round(editAmount));
        }
    }

    // Thêm biên nhận
    async function postAddPayment() {
        let method = "post",
            url = "/contract/add-payment",
            params = null,
            data = $('#add-payment-form').serialize();

        try {
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
        } catch (error) {
            showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
            console.error(error);
        }
    }

    // Cập nhật biên nhận
    async function postUpdatePayment() {
        let method = "post",
            url = "/contract/update-payment",
            params = null,
            data = $('#edit-payment-form').serialize();

        try {
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
        } catch (error) {
            showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
            console.error(error);
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
                        data = {
                            id: paymentId
                        };
                    try {
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
                    } catch (error) {
                        showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                        console.error(error);
                    }
                },
                () => {}, {}
        );
    }
</script>
@if ($canEdit)
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

    // Chuyển đổi loại dịch vụ trong modal thêm dịch vụ
    function toggleServiceTypeFields(type) {
        if (type === 'service') {
            $('#service-select-container').removeClass('hidden');
            $('#custom-service-container').addClass('hidden');
        } else {
            $('#service-select-container').addClass('hidden');
            $('#custom-service-container').removeClass('hidden');
        }
    }

    // Biến đếm cho các trường dịch vụ con
    let subServiceCounter = 0;
    let editSubServiceCounter = 0;

    // Thêm trường dịch vụ con cho modal thêm mới
    function addSubServiceField() {
        const field = `
        <div class="bg-gray-50 p-3 rounded-lg mb-3 sub-service-field" data-index="${subServiceCounter}">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-700 font-medium"></span>
                <button type="button" class="btn btn-sm btn-icon btn-outline btn-danger" onclick="removeSubServiceField(this)">
                    <i class="ki-filled ki-trash text-red-500"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="col-span-3">
                    <span class="checkbox-label text-gray-700">Tên dịch vụ con</span>
                    <input name="sub_service_name[${subServiceCounter}]" class="input border-gray-200 w-full" type="text" placeholder="Tên dịch vụ con">
                </div>
                <div>
                    <span class="checkbox-label text-gray-700">Số lượng</span>
                    <input name="sub_service_quantity[${subServiceCounter}]" class="input border-gray-200 w-full" type="number" min="1" placeholder="Số lượng">
                </div>
                <div>
                    <span class="checkbox-label text-gray-700">Giá (không bắt buộc)</span>
                    <div class="relative">
                        <input name="sub_service_price[${subServiceCounter}]" class="input border-gray-200 w-full pl-8" type="number" min="0" placeholder="Giá tiền">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                    </div>
                </div>
                <div class="col-span-3">
                    <span class="checkbox-label text-gray-700">Ghi chú</span>
                    <input name="sub_service_note[${subServiceCounter}]" class="input border-gray-200 w-full" type="text" placeholder="Ghi chú">
                </div>
            </div>
        </div>
    `;

        $('#sub-services-container').append(field);
        subServiceCounter++;
    }

    // Xóa trường dịch vụ con
    function removeSubServiceField(button) {
        $(button).closest('.sub-service-field').remove();
    }

    // Thêm trường dịch vụ con cho modal chỉnh sửa
    function addEditSubServiceField() {
        const field = `
        <div class="bg-gray-50 p-3 rounded-lg mb-3 edit-sub-service-field" data-index="${editSubServiceCounter}">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-700 font-medium"></span>
                <button type="button" class="btn btn-sm btn-icon btn-outline btn-danger" onclick="removeEditSubServiceField(this)">
                    <i class="ki-filled ki-trash text-red-500"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="col-span-3">
                    <span class="checkbox-label text-gray-700">Tên dịch vụ con</span>
                    <input name="edit_sub_service_name[${editSubServiceCounter}]" class="input border-gray-200 w-full" type="text" placeholder="Tên dịch vụ con">
                </div>
                <div>
                    <span class="checkbox-label text-gray-700">Số lượng</span>
                    <input name="edit_sub_service_quantity[${editSubServiceCounter}]" class="input border-gray-200 w-full" type="number" min="1" placeholder="Số lượng">
                </div>
                <div>
                    <span class="checkbox-label text-gray-700">Giá (không bắt buộc)</span>
                    <div class="relative">
                        <input name="edit_sub_service_price[${editSubServiceCounter}]" class="input border-gray-200 w-full pl-8" type="number" min="0" placeholder="Giá tiền">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                    </div>
                </div>
                <div class="col-span-3">
                    <span class="checkbox-label text-gray-700">Ghi chú</span>
                    <input name="edit_sub_service_note[${editSubServiceCounter}]" class="input border-gray-200 w-full" type="text" placeholder="Ghi chú">
                </div>
            </div>
        </div>
    `;

        $('#edit-sub-services-container').append(field);
        editSubServiceCounter++;
    }

    // Xóa trường dịch vụ con trong modal chỉnh sửa
    function removeEditSubServiceField(button) {
        $(button).closest('.edit-sub-service-field').remove();
    }
    // Cập nhật hàm load dữ liệu dịch vụ để xử lý dịch vụ con
    function loadServiceData(serviceId) {
        // Tìm dịch vụ từ danh sách dịch vụ
        const services = @json($details['services']);

        // Tìm kiếm trong các dịch vụ chính
        let service = null;
        for (const mainService of services) {
            if (mainService.id === serviceId) {
                service = mainService;
                break;
            }
        }

        if (service) {
            // Reset form trước khi load dữ liệu mới
            $('#edit-service-form')[0].reset();
            $('#edit-sub-services-container').empty();
            editSubServiceCounter = 0;

            $('#edit-service-id').val(service.id);

            // Xác định loại dịch vụ và hiển thị trường phù hợp
            if (service.type === 'service') {
                $('#edit-service-type').val('service');
                $('#edit-service-select-container').removeClass('hidden');
                $('#edit-custom-service-container').addClass('hidden');
                $('#edit-service-form [name=service_id]').val(service.service_id || '');
            } else {
                $('#edit-service-type').val('other');
                $('#edit-service-select-container').addClass('hidden');
                $('#edit-custom-service-container').removeClass('hidden');
                $('#edit-service-form [name=custom_name]').val(service.name || '');
            }

            $('#edit-service-form [name=quantity]').val(service.quantity);
            $('#edit-service-form [name=price]').val(service.price);
            $('#edit-service-form [name=note]').val(service.note || '');

            // Load dịch vụ con
            if (service.sub_services && service.sub_services.length > 0) {
                service.sub_services.forEach(subService => {
                    const field = `
                    <div class="bg-gray-50 p-3 rounded-lg mb-3 edit-sub-service-field" data-index="${editSubServiceCounter}" data-id="${subService.id || ''}">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-700 font-medium"></span>
                            <button type="button" class="btn btn-sm btn-icon btn-outline btn-danger" onclick="removeEditSubServiceField(this)">
                                <i class="ki-filled ki-trash text-red-500"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="col-span-3">
                                <span class="checkbox-label text-gray-700">Tên dịch vụ con</span>
                                <input name="edit_sub_service_name[${editSubServiceCounter}]" class="input border-gray-200 w-full" type="text" placeholder="Tên dịch vụ con" value="${subService.name || ''}">
                                ${subService.id ? `<input type="hidden" name="edit_sub_service_id[${editSubServiceCounter}]" value="${subService.id}">` : ''}
                            </div>
                            <div>
                                <span class="checkbox-label text-gray-700">Số lượng</span>
                                <input name="edit_sub_service_quantity[${editSubServiceCounter}]" class="input border-gray-200 w-full" type="number" min="1" placeholder="Số lượng" value="${subService.quantity || 1}">
                            </div>
                            <div>
                                <span class="checkbox-label text-gray-700">Giá (không bắt buộc)</span>
                                <div class="relative">
                                    <input name="edit_sub_service_price[${editSubServiceCounter}]" class="input border-gray-200 w-full pl-8" type="number" min="0" placeholder="Giá tiền" value="${subService.price || 0}">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                </div>
                            </div>
                            <div class="col-span-3">
                                <span class="checkbox-label text-gray-700">Ghi chú</span>
                                <input name="edit_sub_service_note[${editSubServiceCounter}]" class="input border-gray-200 w-full" type="text" placeholder="Ghi chú" value="${subService.note || ''}">
                            </div>
                        </div>
                    </div>
                `;

                    $('#edit-sub-services-container').append(field);
                    editSubServiceCounter++;
                });
            }
        }
    }

    // Cập nhật hàm gửi dữ liệu thêm dịch vụ
    async function postAddService() {
        // Thu thập thông tin dịch vụ chính
        const serviceType = $('input[name="service_type"]:checked').val();
        let serviceData = {
            contract_id: $('input[name="contract_id"]').val(),
            type: serviceType,
            quantity: $('input[name="quantity"]').val(),
            price: $('input[name="price"]').val(),
            note: $('textarea[name="note"]').val(),
            sub_services: []
        };

        if (serviceType === 'service') {
            serviceData.service_id = $('select[name="service_id"]').val();
        } else {
            serviceData.name = $('input[name="custom_name"]').val();
        }

        // Thu thập thông tin dịch vụ con
        $('.sub-service-field').each(function() {
            const index = $(this).data('index');
            const subService = {
                name: $(`input[name="sub_service_name[${index}]"]`).val(),
                quantity: $(`input[name="sub_service_quantity[${index}]"]`).val(),
                price: $(`input[name="sub_service_price[${index}]"]`).val() || 0,
                note: $(`input[name="sub_service_note[${index}]"]`).val() || ''
            };

            // Chỉ thêm dịch vụ con nếu có tên
            if (subService.name && subService.name.trim() !== '') {
                serviceData.sub_services.push(subService);
            }
        });

        try {
            let method = "post",
                url = "/contract/add-service",
                params = null,
                data = serviceData;

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
        } catch (error) {
            showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
            console.error(error);
        }
    }

    // Cập nhật hàm gửi dữ liệu chỉnh sửa dịch vụ
    async function postUpdateService() {
        // Thu thập thông tin dịch vụ chính
        const serviceType = $('#edit-service-type').val();
        let serviceData = {
            id: $('#edit-service-id').val(),
            type: serviceType,
            quantity: $('input[name="quantity"]').val(),
            price: $('input[name="price"]').val(),
            note: $('textarea[name="note"]').val(),
            sub_services: []
        };

        if (serviceType === 'service') {
            serviceData.service_id = $('#edit-service-form select[name="service_id"]').val();
        } else {
            serviceData.name = $('#edit-service-form input[name="custom_name"]').val();
        }

        // Thu thập thông tin dịch vụ con
        $('.edit-sub-service-field').each(function() {
            const index = $(this).data('index');
            const existingId = $(this).data('id') || null;

            const subService = {
                id: existingId,
                name: $(`input[name="edit_sub_service_name[${index}]"]`).val(),
                quantity: $(`input[name="edit_sub_service_quantity[${index}]"]`).val(),
                price: $(`input[name="edit_sub_service_price[${index}]"]`).val() || 0,
                note: $(`input[name="edit_sub_service_note[${index}]"]`).val() || ''
            };

            // Chỉ thêm dịch vụ con nếu có tên
            if (subService.name && subService.name.trim() !== '') {
                serviceData.sub_services.push(subService);
            }
        });

        try {
            let method = "post",
                url = "/contract/update-service",
                params = null,
                data = serviceData;

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
        } catch (error) {
            showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
            console.error(error);
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
                    try {
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
                    } catch (error) {
                        showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                        console.error(error);
                    }
                },
                () => {}, {}
        );
    }
</script>
@endif

@endpush