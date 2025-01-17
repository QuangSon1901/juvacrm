@extends('dashboard.layouts.layout')
@section('dashboard_content')
@php
    $departmentID = $details['department']->id;
@endphp
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Phòng kinh doanh
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
        <div class="col-span-2">
            <div class="flex flex-col gap-5 lg:gap-7.5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin phòng ban <span>#<span id="department_id">{{ $details['department']->id }}</span></span>
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm">
                            <tbody>
                                <!-- <tr>
                                    <td class="py-2 min-w-32 text-gray-600 font-normal">
                                        Hình ảnh
                                    </td>
                                    <td class="py-2 text-gray-700 font-normal min-w-32 text-2sm">
                                        150x150px JPEG, PNG Image
                                    </td>
                                    <td class="py-2 text-center min-w-16">
                                        <div class="image-input size-16" data-image-input="true">
                                            <input accept=".png, .jpg, .jpeg" name="avatar" type="file">
                                            <input name="avatar_remove" type="hidden">
                                            <div class="btn btn-icon btn-icon-xs btn-light shadow-default absolute z-1 size-5 -top-0.5 -end-0.5 rounded-full" data-image-input-remove="" data-tooltip="#image_input_tooltip" data-tooltip-trigger="hover">
                                                <i class="ki-filled ki-cross">
                                                </i>
                                            </div>
                                            <span class="tooltip" id="image_input_tooltip">
                                                Nhấn để xoá hoặc quay lại
                                            </span>
                                            <div class="image-input-placeholder rounded-full border-2 border-success image-input-empty:border-gray-300" style="background-image:url({{asset('assets/images/icons/blank.png')}})">
                                                <div class="image-input-preview rounded-full" style="background-image:url()">
                                                </div>
                                                <div class="flex items-center justify-center cursor-pointer h-5 left-0 right-0 bottom-0 bg-dark-clarity absolute">
                                                    <svg class="fill-light opacity-80" height="12" viewBox="0 0 14 12" width="14" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M11.6665 2.64585H11.2232C11.0873 2.64749 10.9538 2.61053 10.8382 2.53928C10.7225 2.46803 10.6295 2.36541 10.5698 2.24335L10.0448 1.19918C9.91266 0.931853 9.70808 0.707007 9.45438 0.550249C9.20068 0.393491 8.90806 0.311121 8.60984 0.312517H5.38984C5.09162 0.311121 4.799 0.393491 4.5453 0.550249C4.2916 0.707007 4.08701 0.931853 3.95484 1.19918L3.42984 2.24335C3.37021 2.36541 3.27716 2.46803 3.1615 2.53928C3.04584 2.61053 2.91234 2.64749 2.7765 2.64585H2.33317C1.90772 2.64585 1.49969 2.81486 1.19885 3.1157C0.898014 3.41654 0.729004 3.82457 0.729004 4.25002V10.0834C0.729004 10.5088 0.898014 10.9168 1.19885 11.2177C1.49969 11.5185 1.90772 11.6875 2.33317 11.6875H11.6665C12.092 11.6875 12.5 11.5185 12.8008 11.2177C13.1017 10.9168 13.2707 10.5088 13.2707 10.0834V4.25002C13.2707 3.82457 13.1017 3.41654 12.8008 3.1157C12.5 2.81486 12.092 2.64585 11.6665 2.64585ZM6.99984 9.64585C6.39413 9.64585 5.80203 9.46624 5.2984 9.12973C4.79478 8.79321 4.40225 8.31492 4.17046 7.75532C3.93866 7.19572 3.87802 6.57995 3.99618 5.98589C4.11435 5.39182 4.40602 4.84613 4.83432 4.41784C5.26262 3.98954 5.80831 3.69786 6.40237 3.5797C6.99644 3.46153 7.61221 3.52218 8.1718 3.75397C8.7314 3.98576 9.2097 4.37829 9.54621 4.88192C9.88272 5.38554 10.0623 5.97765 10.0623 6.58335C10.0608 7.3951 9.73765 8.17317 9.16365 8.74716C8.58965 9.32116 7.81159 9.64431 6.99984 9.64585Z" fill="">
                                                        </path>
                                                        <path d="M7 8.77087C8.20812 8.77087 9.1875 7.7915 9.1875 6.58337C9.1875 5.37525 8.20812 4.39587 7 4.39587C5.79188 4.39587 4.8125 5.37525 4.8125 6.58337C4.8125 7.7915 5.79188 8.77087 7 8.77087Z" fill="">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Tên phòng ban
                                    </td>
                                    <td class="py-2 text-gray-700 font-normal">
                                        {{ $details['department']->name }}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-xs btn-icon btn-clear btn-primary info-department-action" data-name="name" data-modal-toggle="#update-info-department-modal">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Ghi chú
                                    </td>
                                    <td class="py-2 text-gray-700 font-normal">
                                        {{ $details['department']->note ?? '---' }}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-xs btn-icon btn-clear btn-primary info-department-action" data-name="note" data-modal-toggle="#update-info-department-modal">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600 font-normal">
                                        Trạng thái
                                    </td>
                                    <td class="py-3 text-gray-700">
                                        <span class="badge badge-sm badge-outline badge-{{ $details['department']->is_active ? 'success' : 'danger' }}">
                                            {{ $details['department']->is_active ? 'Đang hoạt động' : 'Ngưng hoạt động' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <a class="text-center btn btn-sm btn-icon btn-clear btn-{{ $details['department']->is_active ? 'danger' : 'primary' }}" onclick="changeStatusDepartment({{$details['department']->id}})">
                                            <span>
                                                {{$details['department']->is_active ? "Ẩn" : "Mở"}}
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card card-grid min-w-full">
                    <div class="card-header py-5 flex-wrap gap-2">
                        <h3 class="card-title">
                            Danh sách thành viên
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="/team/add-member/{{$details['department']->id}}" class="btn btn-primary btn-sm">
                                Thêm nhân viên
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="datatable-initialized">
                            <div class="scrollable-x-auto">
                                <table class="table table-auto table-border">
                                    <thead>
                                        <tr>
                                            <th class="w-[60px] text-center">
                                                STT
                                            </th>
                                            <th class="min-w-[250px]">
                                                <span class="sort">
                                                    <span class="sort-label text-gray-700 font-normal">
                                                        Thành viên
                                                    </span>
                                                </span>
                                            </th>
                                            <th class="min-w-[250px]">
                                                <span class="sort">
                                                    <span class="sort-label text-gray-700 font-normal">
                                                        Chức vụ
                                                    </span>
                                                </span>
                                            </th>
                                            <th class="w-[60px]">
                                            </th>
                                        </tr>
                                    </thead>
                                    @include('dashboard.layouts.tableloader', ['currentlist' => "/team/employee-by-department/$departmentID"])
                                </table>
                            </div>
                            <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                                <div class="flex items-center gap-2 order-2 md:order-1">
                                    Hiển thị {{TABLE_PERPAGE_NUM}} mỗi trang
                                </div>
                                <div class="flex items-center gap-4 order-1 md:order-2">
                                    <p><span class="sorterlow"></span> - <span class="sorterhigh"></span> trong <span class="sorterrecords"></span></p>
                                    <div class="pagination"></div>
                                </div>
                            </div>
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
                            Danh sách chức vụ
                        </h3>
                        <!-- <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <a class="menu-link" href="#">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm chức vụ
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach($details['levels'] as $level)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="/role/{{ $level['id'] }}/{{ $level['department_id'] }}">
                                            {{ $level['name'] }}
                                        </a>
                                        <span class="text-xs font-semibold text-gray-600">
                                            {{ $level['total'] }} thành viên
                                        </span>
                                    </div>
                                </div>
                                <div class="menu" data-menu="true">
                                    <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-dots-vertical">
                                            </i>
                                        </button>
                                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                            <div class="menu-item">
                                                <a class="menu-link" href="/role/{{ $level['id'] }}/{{ $level['department_id'] }}">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-setting-2">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Phân quyền
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
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
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-info-department-modal" style="z-index: 90;">
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
                    <!-- <div class="flex flex-center gap-1">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Tên phòng ban
                        </label>
                    </div> -->
                    <input class="input" name="info-value" type="text" placeholder="Vui lòng nhập">
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
@push("scripts")
<script>
    $(function() {
        let infoChange = "";
        $('.info-department-action').on('click', function() {
            infoChange = $(this).attr('data-name');
        })

        $('#update-info-department-modal form').on('submit', async function(e) {
            e.preventDefault();

            let infoValue = $(this).find('input[name=info-value]').val().trim();

            if (!infoValue) {
                showAlert('warning', 'Vui lòng nhập thay đổi!');
                return;
            }

            let method = "post",
                url = "/team/update",
                params = null,
                data = {
                    id: $('#department_id').text(),
                    [infoChange]: infoValue
                }
            let res = await axiosTemplate(method, url, params, data);
            switch (res.data.status) {
                case 200:
                    showAlert('success', res.data.message)
                    window.location.reload();
                    break;
                default:
                    showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                    break;
            }
        })
    })
</script>
<script type="text/javascript" src="{{ asset('assets\js\dashboard\account\employee\team\detail.js')}}"></script>
@endpush