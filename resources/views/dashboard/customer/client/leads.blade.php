@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Khách hàng tiềm năng
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
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap lg:justify-end gap-2">
                            <div class="hidden">
                                <label class="switch switch-sm">
                                    <span class="switch-label">
                                        Khách hàng của tôi
                                    </span>
                                    <input data-filter="my_customer" type="checkbox" value="1">
                                </label>
                            </div>
                            <div class="hidden">
                                <label class="switch switch-sm">
                                    <span class="switch-label">
                                        Danh sách đen
                                    </span>
                                    <input data-filter="black_list" type="checkbox" value="1">
                                </label>
                            </div>
                            <input data-filter="lead" type="checkbox" value="0" class="hidden">
                            <div class="relative">
                                <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                                </i>
                                <input class="input input-sm pl-8" id="search-input" data-filter="search" placeholder="Tìm kiếm" type="text">
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="/customer/create-view" class="btn btn-primary btn-sm">
                            Thêm khách hàng
                        </a>
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
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/customer-leads-data'])
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
@endsection