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
                    <div class="flex flex-wrap gap-2.5">
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-wrap lg:justify-end gap-2">
                                <select data-filter="level_task" class="select select-sm w-40">
                                    <option value="max" selected>
                                        Cấp cao nhất
                                    </option>
                                    <option value="min">
                                        Cấp nhỏ nhất
                                    </option>
                                </select>
                                <select data-filter="priority_task" class="select select-sm w-40">
                                    <option value="" selected>
                                        Tất cả mức độ
                                    </option>
                                    @foreach ($priorities as $priority)
                                    <option value="{{$priority['id']}}">
                                        {{$priority['name']}}
                                    </option>
                                    @endforeach
                                </select>
                                <select data-filter="status_task" class="select select-sm w-40">
                                    <option value="" selected>
                                        Tất cả trạng thái
                                    </option>
                                    @foreach ($statuses as $status)
                                    <option value="{{$status['id']}}">
                                        {{$status['name']}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-wrap lg:justify-end gap-2">
                                <div class="flex">
                                    <label class="switch switch-sm">
                                        <span class="switch-label">
                                            Công việc của tôi
                                        </span>
                                        <input name="check" data-filter="my_task" type="checkbox" value="1">
                                    </label>
                                </div>
                                <div class="relative">
                                    <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                                    </i>
                                    <input class="input input-sm pl-8" id="search-input" data-filter="search" placeholder="Tìm kiếm" type="text">
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/task/create" class="btn btn-primary btn-sm">
                                Thêm công việc
                            </a>
                        </div>
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
                                        <span class="sort">
                                            <span class="sort-label">
                                                STT
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Trạng thái
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[300px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Tên công việc
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Người thực hiện
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Ngày bắt đầu
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Ngày kết thúc
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                % hoàn thành
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Thời gian dự kiến
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Thời gian thực hiện
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/task-data'])
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