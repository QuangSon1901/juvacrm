@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quy trình tư vấn
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
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-body">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base">
                                    </i>
                                </div>
                                <div class="flex flex-col flex-1 gap-0.5">
                                    <a class="leading-none font-semibold text-2xl text-gray-900 hover:text-primary" href="#">
                                        Jacob Smith
                                    </a>
                                    <span class="text-2sm text-gray-700 font-normal">
                                        2 hợp đồng
                                    </span>
                                    <div>
                                        <span class="badge badge-sm badge-success badge-outline">
                                            Khách hàng mới
                                        </span>
                                    </div>
                                </div>
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
                                                        <i class="ki-filled ki-sms">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        abc@gmail.com
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-phone">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        0123654789
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
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Nhật ký tư vấn
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
                                                Thêm lần tư vấn
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-2.5">
                            <div class="flex items-center gap-3 cursor-pointer py-2 px-4 rounded-lg hover:bg-gray-100 bg-gray-100 border border-blue-500">
                                <div class="flex items-center grow gap-2.5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 mb-px">
                                            Tư vấn lần 1
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            01/12/2024 12:00
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
                                                        <i class="ki-filled ki-trash !text-red-600">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title !text-red-600">
                                                        Xoá
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 cursor-pointer py-2 px-4 rounded-lg hover:bg-gray-100">
                                <div class="flex items-center grow gap-2.5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 mb-px">
                                            Tư vấn lần 1
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            01/12/2024 12:00
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
                                                        <i class="ki-filled ki-trash !text-red-600">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title !text-red-600">
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
        <div class="col-span-2">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Quá trình tư vấn
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col">
                            <div class="flex items-start relative">
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-people text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-800">
                                            Jenny sent an
                                            <a class="text-sm link" href="#">
                                                inquiry
                                            </a>
                                            about a
                                            <a class="text-sm link" href="#">
                                                new product
                                            </a>
                                            .
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            Today, 9:00 AM
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start relative">
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-calendar-tick text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col pb-2.5">
                                        <span class="text-sm text-gray-800">
                                            Jenny attended a webinar on new product features.
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            3 days ago, 11:45 AM
                                        </span>
                                    </div>
                                    <div class="card shadow-none p-4">
                                        <div class="flex flex-wrap gap-2.5">
                                            <i class="ki-filled ki-code text-lg text-info">
                                            </i>
                                            <div class="flex flex-col gap-5 grow">
                                                <div class="flex flex-wrap items-center justify-between">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-md font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                                            Leadership Development Series: Part 1
                                                        </span>
                                                        <span class="text-xs text-gray-600">
                                                            The first installment of a leadership development series.
                                                        </span>
                                                    </div>
                                                    <a class="btn btn-link" href="/metronic/tailwind/demo8/account/members/teams">
                                                        View
                                                    </a>
                                                </div>
                                                <div class="flex flex-wrap gap-7.5">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-2sm font-medium text-gray-600">
                                                            Code:
                                                        </span>
                                                        <a class="text-2sm text-primary" href="#">
                                                            #leaderdev-1
                                                        </a>
                                                    </div>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-2sm text-gray-600">
                                                            Progress:
                                                        </span>
                                                        <div class="progress progress-success min-w-[120px]">
                                                            <div class="progress-bar" style="width: 80%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 lg:min-w-24 shrink-0 max-w-auto">
                                                        <span class="text-2sm text-gray-600">
                                                            Guests:
                                                        </span>
                                                        <div class="flex -space-x-2">
                                                            <div class="flex">
                                                                <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-4.png">
                                                            </div>
                                                            <div class="flex">
                                                                <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-1.png">
                                                            </div>
                                                            <div class="flex">
                                                                <img class="hover:z-5 relative shrink-0 rounded-full ring-1 ring-light-light size-7" src="/static/metronic/tailwind/dist/assets/media/avatars/300-2.png">
                                                            </div>
                                                            <div class="flex">
                                                                <span class="hover:z-5 relative inline-flex items-center justify-center shrink-0 rounded-full ring-1 font-semibold leading-none text-3xs size-7 text-primary-inverse ring-primary-light bg-primary">
                                                                    +24
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start relative">
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-entrance-left text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-800">
                                            Jenny's last login to the
                                            <a class="text-sm link" href="#">
                                                Customer Portal
                                            </a>
                                            .
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            5 days ago, 4:07 PM
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start relative">
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-directbox-default text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col pb-2.5">
                                        <span class="text-sm text-gray-800">
                                            Email campaign sent to Jenny for a special promotion.
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            1 week ago, 11:45 AM
                                        </span>
                                    </div>
                                    <div class="card shadow-none">
                                        <div class="card-body lg:py-4">
                                            <div class="flex justify-center">
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <div class="text-md font-medium text-gray-900 text-center">
                                                    First Campaign Created
                                                </div>
                                                <div class="flex items-center justify-center gap-1">
                                                    <a class="text-2sm font-semibold link" href="/metronic/tailwind/demo8/public-profile/profiles/company">
                                                        Axio new release
                                                    </a>
                                                    <span class="text-2sm text-gray-700 me-2">
                                                        email campaign
                                                    </span>
                                                    <span class="badge badge-sm badge-success badge-outline">
                                                        Public
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start relative">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-rocket text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-800">
                                            Explored niche demo ideas for product-specific solutions.
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            3 weeks ago, 4:07 PM
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="flex flex-col w-full">
                            <div class="relative grow">
                                <input class="input h-auto py-4 ps-4 bg-transparent rounded-lg" placeholder="Bổ sung quá trình tư vấn..." type="text" value="">
                                <div class="flex items-center gap-2.5 absolute end-3 top-1/2 -translate-y-1/2">
                                    <select class="text-sm text-gray-900 outline-none" name="" id="">
                                        <option value="">Hỏi nhu cầu</option>
                                        <option value="">Tư vấn gói</option>
                                        <option value="">Lập hợp đồng</option>
                                        <option value="">Gửi bảng giá</option>
                                        <option value="">Khách từ chối</option>
                                        <option value="">Đặt lịch tư vấn lại</option>
                                    </select>
                                    <button class="btn btn-sm btn-icon btn-light btn-clear">
                                        <i class="ki-filled ki-exit-up">
                                        </i>
                                    </button>
                                    <a class="btn btn-dark btn-sm" href="#">
                                        Đăng
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
@endsection