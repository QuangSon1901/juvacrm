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
                        <div class="flex items-center flex-wrap justify-between gap-2.5">
                            <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-gray-200 pl-4 hover:bg-gray-50 hover:after:bg-blue-800">
                                <div class="flex flex-col">
                                    <div>
                                        <a href="/task/6">
                                            <span class="checkbox-label font-normal text-primary">#6:</span>
                                            <span class="checkbox-label font-semibold hover:text-primary-active">Chụp sản phẩm thuốc trắng da</span>
                                        </a>
                                    </div>
                                    <div>
                                        <span class="checkbox-label font-normal text-success">Chưa bắt đầu</span>
                                        <span>-</span>
                                        <span class="checkbox-label font-normal text-danger">Chưa Chụp</span>
                                        <span>-</span>
                                        <span class="checkbox-label font-medium"><a class="hover:text-primary-active" href="/member/1">Vũ Quang Sơn</a></span>
                                        <span>-</span>
                                        <span class="checkbox-label font-normal">Từ <span class="font-medium">16-01-2025</span></span>
                                        <span class="checkbox-label font-normal">đến <span class="font-medium">31-01-2025</span></span>
                                    </div>
                                </div>
                                <div class="menu" data-menu="true">
                                    <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-dots-vertical">
                                            </i>
                                        </button>
                                        <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true" style="">
                                            <div class="menu-item">
                                                <a class="menu-link" href="/task/6">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-search-list">
                                                        </i>
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
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Lịch hẹn
                        </h3>
                    </div>
                    <div class="card-body"></div>
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
                                0 hợp đồng
                            </span>
                        </label>
                        <div class="flex items-center flex-wrap gap-2">
                            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-green-400 bg-green-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                                <span class="text-green-900 text-md leading-none font-medium">
                                    0
                                </span>
                                <span class="text-green-700 text-2sm">
                                    Hoàn thành
                                </span>
                            </div>
                            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-blue-400 bg-blue-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                                <span class="text-blue-900 text-md leading-none font-medium">
                                    0
                                </span>
                                <span class="text-blue-700 text-2sm">
                                    Đang thực hiện
                                </span>
                            </div>
                            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-red-400 bg-red-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                                <span class="text-red-900 text-md leading-none font-medium">
                                    0
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
                                0 đ
                            </span>
                        </label>
                        <ul class="list-disc ml-4">
                            <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Số tiền đã thu:
                                </span>
                                <span class="text-green-700 text-2sm font-medium">
                                    0 đ
                                </span>
                            </li>
                            <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Số tiền còn nợ:
                                </span>
                                <span class="text-red-700 text-2sm font-medium">
                                    0 đ
                                </span>
                            </li>
                        </ul>
                        <label class="checkbox-group mb-2 mt-4">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Hình thức thanh toán
                            </span>
                        </label>
                        <ul class="list-disc ml-4">
                            <!-- <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Chuyển khoản ngân hàng
                                </span>
                            </li>
                            <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Tiền mặt
                                </span>
                            </li> -->
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
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Lịch sử
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col scrollable-y-auto max-h-[500px]">
                            @foreach ($activity_logs as $log)
                            <div class="flex items-start relative">
                                @if ($log['index'] != count($activity_logs) - 1)
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                @endif
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-xs text-gray-800">
                                            Cập nhật bởi <b>{{$log['user']['name'] ?? '---'}}</b>
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            Khoảng {{timeAgo(strtotime($log['created_at']))}} trước
                                        </span>
                                        <ul class="ml-4 list-disc">
                                            <li class="text-xs">{{$log['details']}}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
@endpush