@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý công việc
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
    <div class="grid gap-5 lg:gap-7.5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách công việc
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex">
                        <label class="switch switch-sm">
                            <span class="switch-label">
                                Công việc của tôi
                            </span>
                            <input name="check" type="checkbox" value="1">
                        </label>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        <select class="select select-sm w-40">
                            <option selected>
                                Theo hợp đồng
                            </option>
                            <option>
                                Tất cả công việc
                            </option>
                        </select>
                        <select class="select select-sm w-40">
                            <option>
                                Tất cả trạng thái
                            </option>
                            <option>
                                Đang làm
                            </option>
                            <option selected>
                                Đã hoàn thành
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="current_sessions_table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[100px]">
                                        <span class="sort asc">
                                            <span class="sort-label">
                                                #
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Công việc
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Mức độ ưu tiên
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Người thực hiện
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Trạng thái
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Ngày bắt đầu
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Ngày kết thúc
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                % hoàn thành
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Thời gian dự kiến
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Thời gian thực hiện
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" data-datatable-spinner="true" style="display: none;">
                                <div class="flex items-center gap-2 px-4 py-2 font-medium leading-none text-2sm border border-gray-200 shadow-default rounded-md text-gray-500 bg-light">
                                    <svg class="animate-spin -ml-1 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </div>
                            </div>
                            <tbody>
                                <tr>
                                    <td class="text-gray-800 font-normal">
                                        <a class="leading-none hover:text-primary" href="/task/123">
                                            1901
                                        </a>
                                    </td>
                                    <td class="text-gray-800 font-normal">
                                        <a class="leading-none hover:text-primary" href="/task/123">
                                            1/10 - Juva - Anh Ngọc Khanh
                                        </a>
                                    </td>
                                    <td class="text-gray-800 font-normal">
                                        <span class="badge badge-sm badge-outline badge-success">
                                            Tiêu chuẩn
                                        </span>
                                    </td>
                                    <td class="text-gray-800 font-normal">
                                        <a class="leading-none hover:text-primary" href="/task/123">
                                            Quang Sơn
                                        </a>
                                    </td>
                                    <td class="text-gray-800 font-normal">
                                        <span class="badge badge-sm badge-outline badge-success">
                                            Open
                                        </span>
                                    </td>
                                    <td class="text-gray-800 font-normal"></td>
                                    <td class="text-gray-800 font-normal"></td>
                                    <td class="text-gray-800 font-normal"></td>
                                    <td class="text-gray-800 font-normal"></td>
                                    <td class="text-gray-800 font-normal"></td>
                                    <td class="w-[60px]">
                                        <div class="menu" data-menu="true">
                                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical">
                                                    </i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true" style="">
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="/member/123">
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
                                                        <a class="menu-link" href="/member/123">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-pencil">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Chỉnh sửa
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
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
                            <span data-datatable-info="true">1-10 trong 50</span>
                            <div class="pagination" data-datatable-pagination="true">
                                <div class="pagination"><button class="btn disabled" disabled=""><i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i></button><button class="btn active disabled" disabled="">1</button><button class="btn">2</button><button class="btn">3</button><button class="btn">...</button><button class="btn"><i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i></button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection