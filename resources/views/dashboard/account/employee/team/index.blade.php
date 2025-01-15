@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Bộ phận/phòng ban
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
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách bộ phận/phòng ban
                </h3>
                <div class="flex flex-wrap gap-2">
                    <label class="switch switch-sm">
                        <input class="order-2" id="filter-active" data-filter='is_active' type="checkbox" value="1">
                        <span class="switch-label order-1">
                            Đang hoạt động
                        </span>
                    </label>
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                        </i>
                        <input class="input input-sm ps-8" data-filter='search' id="search-input" placeholder="Tìm kiếm" type="text">
                    </div>
                    <a href="{{ route('dashboard.account.team.create') }}" class="btn btn-primary btn-sm">
                        Thêm bộ phận
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="departments-table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">
                                        STT
                                    </th>
                                    <th class="w-[350px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Bộ phận/phòng ban
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Lần cập nhật gần nhất
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Thành viên
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Trạng thái
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]">
                                    </th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/team/data'])
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
@push("scripts")
<script type="text/javascript" src="{{ asset('assets\js\dashboard\account\employee\team\index.js')}}"></script>
@endpush