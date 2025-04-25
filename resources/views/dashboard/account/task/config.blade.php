@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thiết lập công việc
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
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-5 lg:gap-7.5">
        <!-- Cột 1: Danh sách mức độ ưu tiên -->
        <div class="col-span-1">
            <div class="grid grid-cols-1 gap-5">
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Danh sách mức độ ưu tiên
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <span class="menu-link" data-modal-toggle="#update-config-task-modal" data-id="0" data-type="0">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm mức độ ưu tiên
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach ($priorities as $priority)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px">
                                            {{$priority['name']}}
                                        </span>
                                        <div class="text-xs font-semibold text-gray-600">
                                            <span class="badge badge-sm badge-outline badge-{{$priority['color']}}">Thứ tự: {{$priority['sort']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5">
                                    <label class="switch switch-sm">
                                        <input class="config-status" data-id="{{$priority['id']}}" {{$priority['is_active'] ? 'checked' : ''}} type="checkbox" value="1">
                                    </label>
                                    <div class="btn btn-sm btn-icon btn-clear btn-primary" data-modal-toggle="#update-config-task-modal" data-id="{{$priority['id']}}" data-type="0">
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
        
        <!-- Cột 2: Danh sách trạng thái công việc -->
        <div class="col-span-1">
            <div class="grid grid-cols-1 gap-5">
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Danh sách trạng thái công việc
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <span class="menu-link" data-modal-toggle="#update-config-task-modal" data-id="0" data-type="1">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm trạng thái
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
                                    <div class="btn btn-sm btn-icon btn-clear btn-primary" data-modal-toggle="#update-config-task-modal" data-id="{{$status['id']}}" data-type="1">
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
        
        <!-- Cột 3: Danh sách vấn đề xử lý -->
        <div class="col-span-1">
            <div class="grid grid-cols-1 gap-5">
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Danh sách vấn đề xử lý
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <span class="menu-link" data-modal-toggle="#update-config-task-modal" data-id="0" data-type="2">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm vấn đề
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach ($issues as $issue)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px">
                                            {{$issue['name']}}
                                        </span>
                                        <div class="text-xs font-semibold text-gray-600">
                                            <span class="badge badge-sm badge-outline badge-{{$issue['color']}}">Thứ tự: {{$issue['sort']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5">
                                    <label class="switch switch-sm">
                                        <input class="config-status" data-id="{{$issue['id']}}" {{$issue['is_active'] ? 'checked' : ''}} type="checkbox" value="1">
                                    </label>
                                    <div class="btn btn-sm btn-icon btn-clear btn-primary" data-modal-toggle="#update-config-task-modal" data-id="{{$issue['id']}}" data-type="2">
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
        
        <!-- Cột 4: Danh sách nhiệm vụ và mức lương (Phần mới thêm vào) -->
        <div class="col-span-1">
            <div class="grid grid-cols-1 gap-5">
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Danh sách nhiệm vụ
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <span class="menu-link" data-modal-toggle="#update-mission-modal" data-id="0">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm nhiệm vụ
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach ($missions as $mission)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px">
                                            {{$mission['name']}}
                                        </span>
                                        <div class="text-xs font-semibold text-gray-600">
                                            <span class="badge badge-sm badge-outline badge-success">{{number_format($mission['salary'])}}đ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5">
                                    <label class="switch switch-sm">
                                        <input class="mission-status" data-id="{{$mission['id']}}" {{$mission['is_active'] ? 'checked' : ''}} type="checkbox" value="1">
                                    </label>
                                    <div class="btn btn-sm btn-icon btn-clear btn-primary" data-modal-toggle="#update-mission-modal" data-id="{{$mission['id']}}">
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

<!-- Modal cập nhật cấu hình task -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-config-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cấu hình
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
                            Tên cấu hình
                        </span>
                    </div>
                    <input name="name" class="input" type="text" placeholder="Vui lòng nhập tên cấu hình">
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
                        <option value="success">Xanh lục (success)</option>
                        <option value="warning">Vàng cam (warning)</option>
                        <option value="primary">Xanh dương (primary)</option>
                        <option value="gray">Xám nhạt (gray)</option>
                        <option value="danger">Đỏ rực (danger)</option>
                        <option value="neutral">Đen nhám (neutral)</option>
                        <option value="info">Xanh nước (info)</option>
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

<!-- Modal mới cho nhiệm vụ -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-mission-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thiết lập nhiệm vụ
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <input name="id" class="input hidden" type="text" hidden>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên nhiệm vụ
                        </span>
                    </div>
                    <input name="name" class="input" type="text" placeholder="Vui lòng nhập tên nhiệm vụ">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Mức thù lao (đơn vị: VNĐ)
                        </span>
                    </div>
                    <input name="salary" class="input" type="number" min="0" step="1000" placeholder="Vui lòng nhập mức thù lao cho nhiệm vụ">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Mô tả nhiệm vụ
                        </span>
                    </div>
                    <textarea name="description" class="textarea" rows="3" placeholder="Vui lòng nhập mô tả chi tiết nhiệm vụ"></textarea>
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
        // Xử lý khi click vào nút chỉnh sửa/thêm mới nhiệm vụ
        $('[data-modal-toggle="#update-mission-modal"][data-id]').on('click', function() {
            const id = $(this).attr('data-id');
            $('#update-mission-modal form input[name=id]').val(id);
            
            // Nếu là chỉnh sửa, lấy dữ liệu hiện tại để điền vào form
            if (id != "0") {
                // Gọi AJAX để lấy dữ liệu của nhiệm vụ
                fetchMissionData(id);
            } else {
                // Nếu là thêm mới, reset form
                $('#update-mission-modal form input[name=name]').val('');
                $('#update-mission-modal form input[name=salary]').val('');
                $('#update-mission-modal form textarea[name=description]').val('');
            }
        });

        // Các xử lý cho modal chỉnh sửa cấu hình task hiện tại
        $('[data-modal-toggle="#update-config-task-modal"][data-id]').on('click', function() {
            $('#update-config-task-modal form input[name=id]').val($(this).attr('data-id'));
            $('#update-config-task-modal form input[name=type]').val($(this).attr('data-type'));
        });

        $('#update-config-task-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateConfigTask($(this));
        });

        // Xử lý form cập nhật nhiệm vụ
        $('#update-mission-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateMission($(this));
        });

        // Xử lý sự kiện thay đổi trạng thái
        $('input.config-status').on('change', function() {
            postChangeStatusConfigTask($(this));
        });

        // Xử lý thay đổi trạng thái nhiệm vụ
        $('input.mission-status').on('change', function() {
            postChangeStatusMission($(this));
        });
    });

    // Hàm lấy dữ liệu nhiệm vụ khi chỉnh sửa
    async function fetchMissionData(id) {
        let method = "get",
            url = "/task-mission/" + id,
            params = null,
            data = null;
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                const mission = res.data.data;
                $('#update-mission-modal form input[name=name]').val(mission.name);
                $('#update-mission-modal form input[name=salary]').val(mission.salary);
                $('#update-mission-modal form textarea[name=description]').val(mission.description);
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra khi lấy dữ liệu!");
                break;
        }
    }

    // Hàm cập nhật nhiệm vụ
    async function postUpdateMission(_this) {
        let method = "post",
            url = "/task-mission/update",
            params = null,
            data = _this.serialize();
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

    // Hàm thay đổi trạng thái nhiệm vụ
    async function postChangeStatusMission(_this) {
        let method = "post",
            url = "/task-mission/change-status",
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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    // Hàm cập nhật cấu hình task
    async function postUpdateConfigTask(_this) {
        let method = "post",
            url = "/task-config/update",
            params = null,
            data = _this.serialize();
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

    // Hàm thay đổi trạng thái cấu hình task
    async function postChangeStatusConfigTask(_this) {
        let method = "post",
            url = "/task-config/change-status",
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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }
</script>
@endpush