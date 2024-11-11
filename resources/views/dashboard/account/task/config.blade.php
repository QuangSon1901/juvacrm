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
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
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
                                        <a class="menu-link" href="#">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm mức độ ưu tiên
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="/role/123">
                                            Chưa Chụp
                                        </a>
                                        <span class="text-xs font-semibold text-gray-600">
                                            Mô tả
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
                                                <a class="menu-link" href="/role/123">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-trash">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Xoá
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="#">
                                            Chưa Quay
                                        </a>
                                        <span class="text-xs font-semibold text-gray-600">
                                            Mô tả
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
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-trash">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Xoá
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
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
                                        <a class="menu-link" href="#">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm trạng thái
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="/role/123">
                                            Hoàn thành
                                        </a>
                                        <span class="text-xs font-semibold text-gray-600">
                                            Mô tả
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
                                                <a class="menu-link" href="/role/123">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-trash">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Xoá
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="#">
                                            Đang tiến hành
                                        </a>
                                        <span class="text-xs font-semibold text-gray-600">
                                            Mô tả
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
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-trash">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Xoá
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1"></div>
    </div>
</div>
@endsection