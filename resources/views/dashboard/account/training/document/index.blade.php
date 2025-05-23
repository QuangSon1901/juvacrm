@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                File tài liệu hướng dẫn
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
                    Danh sách file
                </h3>
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
                                                Tài liệu
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Tác giả
                                            </span>
                                            <span class="sort-icon">
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Ngày phát hành
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