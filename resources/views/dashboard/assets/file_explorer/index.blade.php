@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý tập tin
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
        <div class="flex flex-col gap-4 border border-gray-200 rounded-xl p-5 w-full">
            <div class="lg:w-1/2">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-normal text-gray-700">
                        Dung lượng khả dụng
                    </span>
                    <div class="flex items-end">
                        <span class="font-semibold text-gray-900">
                            Đã sử dụng {{formatBytes($storage['usage'] ?? 0)}} / {{formatBytes($storage['limit'] ?? 0)}}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1 my-1.5">
                    <div class="progress progress-primary">
                        <div class="progress-bar" style="width: {{$storage['usage']/$storage['limit']*100}}%">
                        </div>
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-4 mb-1">
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-primary">
                        </span>
                        <span class="text-sm font-normal text-gray-800">
                            {{formatBytes($storage['usage'] ?? 0)}} đã sử dụng
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-gray">
                        </span>
                        <span class="text-sm font-normal text-gray-900">
                            {{formatBytes($storage['remaining'] ?? 0)}} còn trống
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center flex-wrap md:flex-nowrap lg:items-end justify-between border-b border-b-gray-200 dark:border-b-coal-100 gap-3 lg:gap-6 mb-5 lg:mb-10">
            <div class="grid">
                <div class="scrollable-x-auto">
                    <div class="tabs gap-6" data-tabs="true">
                        <div class="tab cursor-pointer active" data-tab-toggle="#tab_4_1">
                            <span class="text-nowrap text-sm">
                                Tất cả tệp
                            </span>
                        </div>
                        <div class="tab cursor-pointer" data-tab-toggle="#tab_4_2">
                            <span class="text-nowrap text-sm">
                                Tệp của tôi
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end grow lg:grow-0 lg:pb-4 gap-2.5 mb-3 lg:mb-0">
                <button class="btn btn-sm btn-light">
                    <i class="ki-outline ki-file-up"></i>
                    Tải lên
                </button>
                <button class="btn btn-sm btn-icon btn-light">
                    <i class="ki-filled ki-messages"></i>
                </button>
            </div>
        </div>
        <div class="transition-opacity duration-700" id="tab_4_1">
            Tab 1 content.
        </div>
        <div class="hidden transition-opacity duration-700" id="tab_4_2">
            Tab 2 content.
        </div>
        <div class="hidden transition-opacity duration-700" id="tab_4_3">
            Tab 3 content.
        </div>
    </div>
</div>
@endsection