@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin khách hàng
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
        </div>
    </div>
</div>
<div class="container-fixed">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="col-span-2">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin liên hệ
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Họ tên
                                    </td>
                                    <td class="py-2 text-gray-800 text-sm">
                                        {{$details['name']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="name">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Số điện thoại
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['phone']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="phone">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Email
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['email']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="email">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Địa chỉ
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['address']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="address">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Ghi chú
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['note']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="note">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Nguồn
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['source']['name']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="source_id">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Hình thức liên hệ
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        @foreach ($details['contacts'] as $contact)
                                        {{$contact['name']}}
                                        <br>
                                        @endforeach
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="contacts[]">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Lần tương tác gần nhất
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$contact['updated_at'] ?? ''}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin công ty
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Tên công ty
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['company']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-customer-modal" data-name="company">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card min-w-full">
    <div class="card-header">
        <h3 class="card-title">
            Danh sách hợp đồng
        </h3>
        <div class="flex gap-2">
            <a href="/contract/create-view?customer={{ $details['id'] }}" class="btn btn-light btn-xs">
                <i class="ki-filled ki-plus"></i>
                Tạo hợp đồng
            </a>
        </div>
    </div>
    <div class="card-body">
        @forelse($recent_contracts as $contract)
        <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-{{ $contract['status_color'] }} pl-4 hover:bg-gray-50 hover:after:bg-blue-800 mb-3">
            <div class="flex flex-col">
                <div>
                    <a href="/contract/{{ $contract['id'] }}">
                        <span class="checkbox-label font-normal text-primary">#{{ $contract['contract_number'] }}:</span>
                        <span class="checkbox-label font-semibold hover:text-primary-active">{{ $contract['name'] }}</span>
                    </a>
                </div>
                <div>
                    <span class="checkbox-label font-normal badge badge-{{ $contract['status_color'] }} badge-sm">{{ $contract['status_text'] }}</span>
                    <span>-</span>
                    <span class="checkbox-label font-medium">
                        <span class="text-gray-600">Giá trị:</span> 
                        <span class="font-bold">{{ number_format($contract['total_value'], 0, ',', '.') }}đ</span>
                    </span>
                    <span>-</span>
                    <span class="checkbox-label font-normal">Từ <span class="font-medium">{{ formatDateTime($contract['effective_date'], 'd-m-Y') }}</span></span>
                    <span class="checkbox-label font-normal">đến <span class="font-medium">{{ formatDateTime($contract['expiry_date'], 'd-m-Y') }}</span></span>
                </div>
            </div>
            <div class="menu" data-menu="true">
                <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                    <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                        <i class="ki-filled ki-dots-vertical"></i>
                    </button>
                    <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                        <div class="menu-item">
                            <a class="menu-link" href="/contract/{{ $contract['id'] }}">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-search-list"></i>
                                </span>
                                <span class="menu-title">
                                    Xem chi tiết
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center p-6 text-gray-500">
            <i class="ki-filled ki-questionnaire-tablet text-3xl mb-2"></i>
            <p>Khách hàng này chưa có hợp đồng nào</p>
            <a href="/contract/create-view?customer={{ $details['id'] }}" class="btn btn-sm btn-primary mt-2">
                <i class="ki-filled ki-plus"></i> Tạo hợp đồng mới
            </a>
        </div>
        @endforelse
    </div>
</div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Lịch hẹn sắp tới
                        </h3>
                        <a href="/appointment/detail/{{$details['id']}}" class="btn btn-xs btn-light">
                            <i class="ki-filled ki-calendar"></i> Xem tất cả
                        </a>
                    </div>
                    <div class="card-body">
                        <div id="upcoming-appointments">
                            <!-- Hiển thị 3 lịch hẹn sắp tới -->
                            @if(isset($upcoming_appointments) && count($upcoming_appointments) > 0)
                                @foreach($upcoming_appointments as $appointment)
                                <div class="flex items-center gap-3 p-3 mb-2 rounded-lg border border-{{$appointment['color']}}-200 bg-{{$appointment['color']}}-50">
                                    <div class="rounded-full bg-{{$appointment['color']}}-100 p-2">
                                        <i class="ki-filled ki-calendar text-{{$appointment['color']}} text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">{{$appointment['name']}}</p>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="ki-filled ki-time mr-1"></i>
                                            <span>{{formatDateTime($appointment['start_time'], 'd/m/Y H:i')}} - {{formatDateTime($appointment['end_time'], 'H:i')}}</span>
                                        </div>
                                    </div>
                                    <div>
                                        @if(strtotime($appointment['start_time']) - time() < 86400)
                                            <span class="badge badge-danger">Hôm nay</span>
                                        @elseif(strtotime($appointment['start_time']) - time() < 172800)
                                            <span class="badge badge-warning">Ngày mai</span>
                                        @else
                                            <span class="badge badge-info">{{round((strtotime($appointment['start_time']) - time())/86400)}} ngày nữa</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center p-6 text-gray-500">
                                    <i class="ki-filled ki-calendar-8 text-3xl mb-2"></i>
                                    <p>Chưa có lịch hẹn</p>
                                    <a href="/appointment/detail/{{$details['id']}}" class="btn btn-sm btn-light mt-2">
                                        <i class="ki-filled ki-plus"></i> Tạo lịch hẹn
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Trạng thái
                        </h3>
                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-status-customer-modal">
                            <i class="ki-filled ki-notepad-edit">
                            </i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="flex justify-between">
                            <div class="flex flex-wrap gap-2">
                                @if ($details['classification']['id'] != 0)
                                <span class="badge badge-sm badge-outline badge-{{$details['classification']['color']}}">
                                    {{$details['classification']['name']}}
                                </span>
                                @endif
                                @if ($details['status']['id'] != 0)
                                <span class="badge badge-sm badge-outline badge-{{$details['status']['color']}}">
                                    {{$details['status']['name']}}
                                </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a class="btn btn-xs btn-link" href="/customer-consultation/{{$details['id']}}">
                                    Xem quy trình tư vấn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
    <div class="card-header">
        <h3 class="card-title">
            Thống kê khách hàng
        </h3>
    </div>
    <div class="card-body">
        <label class="checkbox-group mb-2">
            <span class="checkbox-label text-gray-800 !font-bold">
                Tổng số hợp đồng:
            </span>
            <span class="checkbox-label text-gray-800">
                {{ $contract_stats['total'] }} hợp đồng
            </span>
        </label>
        <div class="flex items-center flex-wrap gap-2">
            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-green-400 bg-green-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                <span class="text-green-900 text-md leading-none font-medium">
                    {{ $contract_stats['completed'] }}
                </span>
                <span class="text-green-700 text-2sm">
                    Hoàn thành
                </span>
            </div>
            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-blue-400 bg-blue-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                <span class="text-blue-900 text-md leading-none font-medium">
                    {{ $contract_stats['active'] }}
                </span>
                <span class="text-blue-700 text-2sm">
                    Đang thực hiện
                </span>
            </div>
            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-red-400 bg-red-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                <span class="text-red-900 text-md leading-none font-medium">
                    {{ $contract_stats['canceled'] }}
                </span>
                <span class="text-red-700 text-2sm">
                    Đã huỷ
                </span>
            </div>
        </div>
        <label class="checkbox-group mb-2 mt-4">
            <span class="checkbox-label text-gray-800 !font-bold">
                Tổng số dư tài chính:
            </span>
            <span class="checkbox-label text-gray-800">
                {{ number_format($financial_stats['total_value'], 0, ',', '.') }} đ
            </span>
        </label>
        <ul class="list-disc ml-4">
            <li>
                <span class="text-gray-800 text-2sm leading-none">
                    Số tiền đã thu:
                </span>
                <span class="text-green-700 text-2sm font-medium">
                    {{ number_format($financial_stats['paid_amount'], 0, ',', '.') }} đ
                </span>
            </li>
            <li>
                <span class="text-gray-800 text-2sm leading-none">
                    Số tiền còn nợ:
                </span>
                <span class="text-red-700 text-2sm font-medium">
                    {{ number_format($financial_stats['due_amount'], 0, ',', '.') }} đ
                </span>
            </li>
        </ul>
        <label class="checkbox-group mb-2 mt-4">
            <span class="checkbox-label text-gray-800 !font-bold">
                Hình thức thanh toán
            </span>
        </label>
        <ul class="list-disc ml-4">
            @forelse($payment_methods as $method => $count)
            <li>
                <span class="text-gray-800 text-2sm leading-none">
                    {{ $method }} ({{ $count }})
                </span>
            </li>
            @empty
            <li>
                <span class="text-gray-500 text-2sm leading-none italic">
                    Chưa có giao dịch
                </span>
            </li>
            @endforelse
        </ul>
    </div>
</div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Dịch vụ khách quan tâm
                        </h3>
                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-services-customer-modal">
                            <i class="ki-filled ki-notepad-edit">
                            </i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-wrap gap-2.5 mb-2">
                            @foreach ($details['services'] as $service)
                            <span class="badge badge-sm badge-light badge-outline">
                                {{$service['name']}}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Lịch sử tương tác gần đây
                        </h3>
                        <a href="/customer-consultation/{{$details['id']}}" class="btn btn-light btn-xs">
                            Xem đầy đủ
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="timeline-vertical py-0">
                            @if(isset($recent_interactions) && count($recent_interactions) > 0)
                                @foreach($recent_interactions as $interaction)
                                <div class="timeline-item">
                                    <div class="timeline-badge">
                                        @if($interaction['type'] == 'consultation')
                                            <i class="ki-filled ki-message-text-2 text-primary"></i>
                                        @elseif($interaction['type'] == 'appointment')
                                            <i class="ki-filled ki-calendar text-success"></i>
                                        @elseif($interaction['type'] == 'contract')
                                            <i class="ki-filled ki-questionnaire-tablet text-warning"></i>
                                        @else
                                            <i class="ki-filled ki-notification text-gray-600"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-content ps-4 pb-2">
                                        <div class="text-sm font-medium text-gray-900 mb-1">{{ $interaction['title'] }}</div>
                                        <div class="text-xs text-gray-600">{{ timeAgo(strtotime($interaction['created_at'])) }} trước</div>
                                        <p class="text-sm text-gray-700 mt-1">{{ $interaction['description'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="p-6 text-center text-gray-500">
                                    <p>Chưa có lịch sử tương tác</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-customer-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật thông tin
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <input class="input hidden" name="name" type="text" placeholder="Vui lòng nhập họ tên">
                    <input class="input hidden" name="phone" type="text" placeholder="Vui lòng nhập số điện thoại">
                    <input class="input hidden" name="email" type="text" placeholder="Vui lòng nhập email">
                    <input class="input hidden" name="address" type="text" placeholder="Vui lòng nhập địa chỉ">
                    <input class="input hidden" name="note" type="text" placeholder="Vui lòng nhập ghi chú">
                    <select name="source_id" class="select hidden">
                        @foreach ($sources as $source)
                        <option value="{{$source['id']}}">
                            {{$source['name']}}
                        </option>
                        @endforeach
                    </select>
                    @foreach ($contacts as $contact)
                    <label class="hidden">
                        <input value="{{$contact['id']}}" type="checkbox" name="contacts[]">
                        <span>{{$contact['name']}}</span>
                    </label>
                    @endforeach
                    <input class="input hidden" name="company" type="text" placeholder="Vui lòng nhập công ty">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-status-customer-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật trạng thái
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nhóm đối tượng
                        </span>
                    </div>
                    <select name="class_id" class="select">
                        @foreach ($classes as $class_item)
                        <option value="{{$class_item['id']}}">
                            {{$class_item['name']}}
                        </option>
                        @endforeach
                    </select>
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Trạng thái
                        </span>
                    </div>
                    <select name="status_id" class="select">
                        @foreach ($statuses as $status)
                        <option value="{{$status['id']}}">
                            {{$status['name']}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-services-customer-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật dịch vụ
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Dịch vụ khách quan tâm
                        </span>
                    </div>
                    <select name="services[]" class="select min-h-96 py-2" multiple>
                        <option value="">Không chọn</option>
                        @foreach ($services as $service)
                        <option value="{{$service['id']}}">
                            {{$service['name']}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(function() {
        $('button[data-modal-toggle="#update-customer-modal"][data-name]').on('click', function() {
            let _this = $(this);
            let _modal = $('#update-customer-modal');
            _modal.find('input[name][type=text], select[name], textarea[name]').addClass('hidden');
            _modal.find('input[name][type=checkbox], input[name][type=radio]').closest('label').addClass('hidden');
            _modal.find('input[name][type=text], textarea[name]').val('');
            _modal.find('input[name][type=radio], input[name][type=checkbox]').prop('checked', false);
            _modal.find('select[name] option:eq(0)').prop('selected', true);

            _modal.find(`input[name='${_this.attr('data-name')}'][type=text], select[name='${_this.attr('data-name')}'], textarea[name='${_this.attr('data-name')}']`).removeClass('hidden');
            _modal.find(`input[name='${_this.attr('data-name')}'][type=checkbox], input[name='${_this.attr('data-name')}'][type=radio]`).closest('label').removeClass('hidden');
        })

        $('#update-customer-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateCustomer(this);
        })

        $('#update-status-customer-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateCustomer(this);
        })

        $('#update-services-customer-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateCustomer(this);
        })
    })

    async function postUpdateCustomer(_this) {
        let field = $(_this)
            .find('input:not(.hidden), select:not(.hidden), textarea:not(.hidden)')
            .filter(function() {
                return $(this).closest('.hidden').length === 0;
            });

        let strserial = '';
        field.each((_, item) => {
            if ($(item).is('[type=radio]') || $(item).is('[type=checkbox]')) {
                if ($(item).is(':checked')) {
                    strserial += `${$(item).attr('name')}=${$(item).val()}&`;
                }
            } else if ($(item).is('select[multiple]')) {
                $.each($(item).val(), (_, value) => {
                    strserial += `${$(item).attr('name')}=${value}&`;
                })
            } else {
                strserial += `${$(item).attr('name')}=${$(item).val()}&`;
            }
        })
        let method = "post",
            url = "/customer/update",
            params = null,
            data = strserial += "id={{$details['id']}}";
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }
</script>
<script>
    $(function() {
    // Hiệu ứng hover cho card lịch hẹn
    $('.appointment-card').hover(
        function() { $(this).addClass('shadow-md'); },
        function() { $(this).removeClass('shadow-md'); }
    );
    
    // Hiệu ứng đếm ngược cho lịch hẹn
    function updateCountdowns() {
        $('.appointment-countdown').each(function() {
            const targetDate = new Date($(this).data('date'));
            const now = new Date();
            const diff = Math.floor((targetDate - now) / 1000); // Seconds
            
            if (diff < 0) {
                $(this).text('Đã diễn ra');
                return;
            }
            
            const days = Math.floor(diff / 86400);
            const hours = Math.floor((diff % 86400) / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            
            if (days > 0) {
                $(this).text(`Còn ${days} ngày ${hours} giờ`);
            } else if (hours > 0) {
                $(this).text(`Còn ${hours} giờ ${minutes} phút`);
            } else {
                $(this).text(`Còn ${minutes} phút`);
            }
        });
    }
    
    // Cập nhật mỗi phút
    updateCountdowns();
    setInterval(updateCountdowns, 60000);
});
</script>
@endpush