@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Chăm sóc khách hàng
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
    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách khách hàng
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex">
                        <label class="switch switch-sm">
                            <span class="switch-label">
                                Khách hàng của tôi
                            </span>
                            <input name="check" type="checkbox" value="1">
                        </label>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        <select class="select select-sm w-40">
                            <option selected="">
                                Theo trạng thái
                            </option>
                            <option>
                                Khách hàng mới
                            </option>
                        </select>
                        <select class="select select-sm w-40">
                            <option selected="">
                                Đang hoạt động
                            </option>
                            <option>
                                Danh sách đen
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="true" data-datatable-page-size="10" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true" id="clients_table">
                            <thead>
                                <tr>
                                    <th class="text-gray-700 font-normal w-[100px]">
                                        #
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[250px]">
                                        Họ tên
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[200px]">
                                        Liên hệ
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[220px]">
                                        Thông tin CTY
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[220px]">
                                        Khách Quan Tâm
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[200px]">
                                        Nhân viên CSKH
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[100px]">
                                        Lần tương tác gần nhất
                                    </th>
                                    <th class="w-[60px]">
                                    </th>
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
                                        <a class="leading-none hover:text-primary" href="/customer/123">
                                            1901
                                        </a>
                                    </td>
                                    <td>
                                        <div class="flex flex-col gap-1.5">
                                            <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/customer/123">
                                                <span>Yenny Dao</span>
                                                <span class="badge badge-sm badge-outline badge-success">
                                                    Khách hàng mới
                                                </span>
                                            </a>
                                            <span class="text-2sm text-gray-700 font-normal">
                                                2 hợp đồng
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="grid gap-3">
                                            <div class="flex items-center justify-between flex-wrap gap-2">
                                                <div class="flex items-center gap-1.5">
                                                    <i class="ki-filled ki-sms text-base text-gray-500"></i>
                                                    <span class="text-sm font-normal text-gray-900">
                                                        abc@gmail.com
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <i class="ki-filled ki-phone text-base text-gray-500"></i>
                                                    <span class="text-sm font-normal text-gray-900">
                                                        0394062122
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm text-gray-800 font-normal">Local brand</td>
                                    <td>
                                        <div class="flex flex-wrap gap-2.5 mb-2">
                                            <span class="badge badge-sm badge-light badge-outline">
                                                Flatrlay trắng Ng Lớn
                                            </span>
                                            <span class="badge badge-sm badge-light badge-outline">
                                                Flaylay Concept Ng Lớn
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex flex-col gap-0.5">
                                                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/123">
                                                    Tyler Hero
                                                </a>
                                                <span class="text-xs text-gray-700 font-normal">
                                                    #1901
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <th class="text-gray-700 font-normal min-w-[220px]">
                                        08:10 12/12/2024
                                    </th>
                                    <td class="w-[60px]">
                                        <div class="menu" data-menu="true">
                                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical">
                                                    </i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="/customer/123">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-search-list">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Xem chi tiết
                                                            </span>
                                                        </a>
                                                        <a class="menu-link" href="/customer/123">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-pencil">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Chỉnh sửa
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="menu-separator">
                                                    </div>
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="/member/123">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-questionnaire-tablet">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Xem hợp đồng
                                                            </span>
                                                        </a>
                                                        <a class="menu-link" href="/member/123">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-questionnaire-tablet">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Lập hợp đồng
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="menu-separator">
                                                    </div>
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="/customer-consultation/123">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-chart">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Quy trình tư vấn
                                                            </span>
                                                        </a>
                                                        <a class="menu-link" href="/member/123">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-calendar">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Lịch hẹn
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="menu-separator">
                                                    </div>
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="#">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-shield-cross !text-red-600">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title !text-red-600">
                                                                Cho vào danh sách đen
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
                            <span data-datatable-info="true">1-10 trong 33</span>
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