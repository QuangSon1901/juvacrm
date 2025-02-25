@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lead khách hàng
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
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
        <div class="col-span-1">
            <div class="grid grid-cols-1 gap-5">
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Danh sách phương thức liên hệ
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <span class="menu-link" data-modal-toggle="#update-lead-customer-modal" data-id="0" data-type="0">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm mới
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach ($contact_methods as $method)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px">
                                            {{$method['name']}}
                                        </span>
                                        <div class="text-xs font-semibold text-gray-600">
                                            <span class="badge badge-sm badge-outline badge-{{$method['color']}}">Thứ tự: {{$method['sort']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5">
                                    <label class="switch switch-sm">
                                        <input class="config-status" data-id="{{$method['id']}}" {{$method['is_active'] ? 'checked' : ''}} type="checkbox" value="1">
                                    </label>
                                    <div class="btn btn-sm btn-icon btn-clear btn-primary" data-modal-toggle="#update-lead-customer-modal" data-id="{{$method['id']}}" data-type="0">
                                        <i class="ki-filled ki-notepad-edit">
                                        </i>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="grid grid-cols-1 gap-5">
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Danh sách trạng thái
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <span class="menu-link" data-modal-toggle="#update-lead-customer-modal" data-id="0" data-type="2">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm mới
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach ($statuses as $status)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px">
                                            {{$status['name']}}
                                        </span>
                                        <div class="text-xs font-semibold text-gray-600">
                                            <span class="badge badge-sm badge-outline badge-{{$status['color']}}">Thứ tự: {{$status['sort']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5">
                                    <label class="switch switch-sm">
                                        <input class="config-status" data-id="{{$status['id']}}" {{$status['is_active'] ? 'checked' : ''}} type="checkbox" value="1">
                                    </label>
                                    <div class="btn btn-sm btn-icon btn-clear btn-primary" data-modal-toggle="#update-lead-customer-modal" data-id="{{$status['id']}}" data-type="2">
                                        <i class="ki-filled ki-notepad-edit">
                                        </i>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="grid grid-cols-1 gap-5">
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Danh sách nguồn khách hàng
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <span class="menu-link" data-modal-toggle="#update-lead-customer-modal" data-id="0" data-type="1">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm mới
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach ($sources as $source)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px">
                                            {{$source['name']}}
                                        </span>
                                        <div class="text-xs font-semibold text-gray-600">
                                            <span class="badge badge-sm badge-outline badge-{{$source['color']}}">Thứ tự: {{$source['sort']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5">
                                    <label class="switch switch-sm">
                                        <input class="config-status" data-id="{{$source['id']}}" {{$source['is_active'] ? 'checked' : ''}} type="checkbox" value="1">
                                    </label>
                                    <div class="btn btn-sm btn-icon btn-clear btn-primary" data-modal-toggle="#update-lead-customer-modal" data-id="{{$source['id']}}" data-type="1">
                                        <i class="ki-filled ki-notepad-edit">
                                        </i>
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
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-lead-customer-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Dữ liệu mới
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <input name="id" class="input hidden" type="text" hidden>
                <input name="type" class="input hidden" type="text" hidden>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên dữ liệu
                        </span>
                    </div>
                    <input name="name" class="input" type="text" placeholder="Vui lòng nhập tên dữ liệu">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Vị trí sắp xếp
                        </span>
                    </div>
                    <input name="sort" class="input" type="text" placeholder="Vui lòng vị trí danh sách">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Màu sắc
                        </span>
                    </div>
                    <select name="color" class="select p-2.5">
                        <option value="success" selected>Xanh lục (success)</option>
                        <option value="warning" selected>Vàng cam (warning)</option>
                        <option value="primary" selected>Xanh dương (primary)</option>
                        <option value="gray" selected>Xám nhạt (gray)</option>
                        <option value="danger" selected>Đỏ rực (danger)</option>
                        <option value="neutral" selected>Đen nhám (neutral)</option>
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
        $('[data-modal-toggle="#update-lead-customer-modal"][data-id]').on('click', function() {
            $('#update-lead-customer-modal form input[name=id]').val($(this).attr('data-id'));
            $('#update-lead-customer-modal form input[name=type]').val($(this).attr('data-type'));
        })

        $('#update-lead-customer-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateLead($(this));
        })

        $('input.config-status').on('change', function() {
            postChangeStatusLead($(this));
        })
    })

    async function postUpdateLead(_this) {
        let method = "post",
            url = "/leads/post",
            params = null,
            data = _this.serialize();
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

    async function postChangeStatusLead(_this) {
        let method = "post",
            url = "/leads/change-status",
            params = null,
            data = {
                id: _this.attr('data-id'),
                is_active: _this.is(':checked') ? 1 : 0
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }
</script>
@endpush