@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Phòng kinh doanh - Trưởng phòng
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
    <div class="flex flex-col gap-5 lg:gap-7.5">
        <div class="card card-grid min-w-full">
            <div class="card-header py-5 flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách thành viên
                </h3>
                <div class="flex gap-6">
                    <!-- <label class="switch switch-sm">
                        <input class="order-2" name="check" type="checkbox" value="1">
                        <span class="switch-label order-1">
                            Đang hoạt động
                        </span>
                    </label>
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                        </i>
                        <input class="input input-sm ps-8" placeholder="Tìm kiếm" type="text">
                    </div> -->
                    <a href="/team/add-member/{{$details['department']->id}}" class="btn btn-light btn-sm">
                        Thêm nhân viên
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="true" data-datatable-page-size="5" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-auto table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="min-w-[250px]">
                                        <span class="sort asc">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Thành viên
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[250px]">
                                        <span class="sort asc">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Chức vụ
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <!-- <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Trạng thái
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th> -->
                                    <th class="w-[60px]">
                                    </th>
                                </tr>
                            </thead>

                            <!-- <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" data-datatable-spinner="true" style="display: none;">
                                <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-2sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-light">
                                    <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </div>
                            </div> -->
                            <tbody>
                                @foreach($details['users'] as $user)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex flex-col gap-0.5">
                                                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="#">
                                                    {{ $user['name'] }}
                                                </a>
                                                <span class="text-2sm text-gray-700 font-normal">
                                                    ###
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1.5">
                                            <span class="leading-none text-gray-800 font-normal">
                                                {{ $user['level']['name'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="menu" data-menu="true">
                                            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical">
                                                    </i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="#">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-search-list">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Xem chi tiết
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="menu-separator">
                                                    </div>
                                                    <div class="menu-item">
                                                        <button class="menu-link" onclick="saveRemoveMemberTeam({{ $user['id'] }}, {{ $details['department']->id }})">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-trash">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Gỡ
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                        <div class="flex items-center gap-2 order-2 md:order-1">
                            Hiển thị
                            <select class="select select-sm w-16" data-datatable-size="true" name="perpage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                            </select>
                            mỗi trang
                        </div>
                        <div class="flex items-center gap-4 order-1 md:order-2">
                            <span data-datatable-info="true">1-5 trong 33</span>
                            <div class="pagination" data-datatable-pagination="true">
                                <div class="pagination"><button class="btn disabled" disabled=""><i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i></button><button class="btn active disabled" disabled="">1</button><button class="btn">2</button><button class="btn">3</button><button class="btn">...</button><button class="btn"><i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i></button></div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Phân quyền
                </h3>
            </div>
            <div class="card-table scrollable-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left text-gray-300 font-normal min-w-[300px]">
                                Mô-đun
                            </th>
                            <th class="min-w-24 text-gray-700 font-normal text-center">
                                Xem
                            </th>
                            <th class="min-w-24 text-gray-700 font-normal text-center">
                                Chỉnh sửa
                            </th>
                            <th class="min-w-24 text-gray-700 font-normal text-center">
                                Xoá
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-900 font-medium">
                        <tr>
                            <td class="!py-5.5">
                                Danh sách nhân viên
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                        </tr>
                        <tr>
                            <td class="!py-5.5">
                                Danh sách công việc
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer justify-end py-7.5 gap-2.5">
                <a class="btn btn-light btn-outline" href="#">
                    Mặc định
                </a>
                <a class="btn btn-primary" href="#">
                    Lưu thay đổi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@push("scripts")
<script>
    async function saveRemoveMemberTeam(user_id, department_id) {
        let method = "post",
            url = "/team/remove-member",
            params = null,
            data = {
                user_id,
                department_id
            }
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                alert(res.data.message)
                window.location.reload();
                break;
            default:
                alert(res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                break;
        }
    }
</script>
@endpush