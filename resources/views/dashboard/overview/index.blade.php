@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <!-- Container -->
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Tổng quan
            </h1>
            <button class="btn btn-sm btn-primary">
                <i class="ki-filled ki-time">
                </i>
                Chấm công
            </button>
            <!-- <span>08:01</span>
            <button class="btn btn-sm btn-danger">
                <i class="ki-filled ki-time">
                </i>
                Kết thúc
            </button> -->
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
        </div>
    </div>
    <!-- End of Container -->
</div>
<div class="container-fixed">
    <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5 items-stretch">
        <div class="lg:col-span-2">
            <div class="grid gap-5 lg:gap-7.5">
                <div class="card h-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-filled ki-cheque text-primary text-2xl"></i>&nbsp;Tổng quan đơn hàng
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid lg:grid-cols-3 gap-4 items-stretch">
                            <div class="lg:col-span-1">
                                <div class="grid lg:grid-cols-1 gap-3.5">
                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-blue-100 dark:bg-blue-900 rounded-xl gap-2 p-3.5">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Tổng số tiền của hoá đơn
                                                    </span>
                                                </div>
                                                <span class="text-2sm font-bold text-gray-900">
                                                    32,439,000đ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-pink-100 dark:bg-pink-900 rounded-xl gap-2 p-3.5">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Số tiền KH chưa thanh toán
                                                    </span>
                                                </div>
                                                <span class="text-2sm font-bold text-gray-900">
                                                    0đ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-green-100 dark:bg-green-900 rounded-xl gap-2 p-3.5">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Tổng tiền KH đã thanh toán
                                                    </span>
                                                </div>
                                                <span class="text-2sm font-bold text-gray-900">
                                                    0đ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-purple-100 dark:bg-purple-900 rounded-xl gap-2 p-3.5">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Số hoá đơn đến hạn hôm nay
                                                    </span>
                                                </div>
                                                <span class="text-2sm font-bold text-gray-900">
                                                    0
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="lg:col-span-2 flex items-center justify-center">
                                <div class="flex flex-col gap-4 border border-gray-200 rounded-xl p-5 lg:p-7.5 lg:pt-4 w-full">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-sm font-normal text-gray-700">
                                            Tổng doanh thu
                                        </span>
                                        <div class="flex items-center gap-2.5">
                                            <span class="text-3xl font-semibold text-gray-900">
                                                $295.7k
                                            </span>
                                            <span class="badge badge-outline badge-success badge-sm">
                                                +2.7%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 mb-1.5">
                                        <div class="bg-success h-2 w-full max-w-[60%] rounded-sm">
                                        </div>
                                        <div class="bg-brand h-2 w-full max-w-[25%] rounded-sm">
                                        </div>
                                        <div class="bg-info h-2 w-full max-w-[15%] rounded-sm">
                                        </div>
                                    </div>
                                    <div class="flex items-center flex-wrap gap-4 mb-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="badge badge-dot size-2 badge-success">
                                            </span>
                                            <span class="text-sm font-normal text-gray-800">
                                                Metronic
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="badge badge-dot size-2 badge-danger">
                                            </span>
                                            <span class="text-sm font-normal text-gray-800">
                                                Bundle
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="badge badge-dot size-2 badge-info">
                                            </span>
                                            <span class="text-sm font-normal text-gray-800">
                                                MetronicNest
                                            </span>
                                        </div>
                                    </div>
                                    <div class="border-b border-gray-300">
                                    </div>
                                    <div class="grid gap-3">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-1.5">
                                                <i class="ki-filled ki-shop text-base text-gray-500">
                                                </i>
                                                <span class="text-sm font-normal text-gray-900">
                                                    Online Store
                                                </span>
                                            </div>
                                            <div class="flex items-center text-sm font-medium text-gray-800 gap-6">
                                                <span class="lg:text-right">
                                                    $172k
                                                </span>
                                                <span class="lg:text-right">
                                                    <i class="ki-filled ki-arrow-up text-success">
                                                    </i>
                                                    3.9%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-1.5">
                                                <i class="ki-filled ki-facebook text-base text-gray-500">
                                                </i>
                                                <span class="text-sm font-normal text-gray-900">
                                                    Facebook
                                                </span>
                                            </div>
                                            <div class="flex items-center text-sm font-medium text-gray-800 gap-6">
                                                <span class="lg:text-right">
                                                    $85k
                                                </span>
                                                <span class="lg:text-right">
                                                    <i class="ki-filled ki-arrow-down text-danger">
                                                    </i>
                                                    0.7%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-1.5">
                                                <i class="ki-filled ki-instagram text-base text-gray-500">
                                                </i>
                                                <span class="text-sm font-normal text-gray-900">
                                                    Instagram
                                                </span>
                                            </div>
                                            <div class="flex items-center text-sm font-medium text-gray-800 gap-6">
                                                <span class="lg:text-right">
                                                    $36k
                                                </span>
                                                <span class="lg:text-right">
                                                    <i class="ki-filled ki-arrow-up text-success">
                                                    </i>
                                                    8.2%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card h-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-filled ki-calendar-edit text-green-900 text-2xl"></i>&nbsp;Tổng quan công việc
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid lg:grid-cols-4 gap-4 items-stretch">
                            <div class="lg:col-span-1">
                                <div class="grid lg:grid-cols-1 gap-3.5">
                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 p-3.5">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <img alt="" class="size-6 shrink-0" src="{{asset('assets/images/icons/calendar.png')}}">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Ngày hôm nay
                                                    </span>
                                                </div>
                                                <span class="text-2sm font-bold text-gray-900">
                                                    05/11/2024
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 p-3.5">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <img alt="" class="size-6 shrink-0" src="{{asset('assets/images/icons/check.webp')}}">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Task đã làm
                                                    </span>
                                                </div>
                                                <span class="text-2sm font-bold text-gray-900">
                                                    0/40
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 p-3.5">
                                        <div class="flex items-center flex-wrap gap-3.5">
                                            <img alt="" class="size-6 shrink-0" src="{{asset('assets/images/icons/clock.png')}}">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-2sm font-medium text-gray-600">
                                                        Task hến hạn
                                                    </span>
                                                </div>
                                                <span class="text-2sm font-bold text-gray-900">
                                                    0/40
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="lg:col-span-3 flex items-center justify-center">
                                <img src="{{asset('assets/images/icons/comingsoon.png')}}" class="h-60" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1">
            <div class="grid gap-5 lg:gap-7.5">
                <div class="card h-full">
                    <div class="card-header">
                        <div class="flex items-center">
                            <h3 class="card-title">
                                <i class="ki-filled ki-people text-blue-800 text-2xl"></i>&nbsp;Nhân sự đang làm việc
                            </h3>
                            &nbsp;
                            <span class="badge badge-xs badge-primary badge-outline">3</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-5">
                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="">
                                        <img class="h-9 rounded-full border border-gray-500" src="{{asset('assets/images/logo/favicon.png')}}">
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <a class="flex items-center gap-1.5 leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/metronic/tailwind/demo8/public-profile/teams">
                                            Nguyễn Trung Hiếu
                                        </a>
                                        <span class="text-2sm text-gray-700">
                                            Vào lúc 07:55:53
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="">
                                        <img class="h-9 rounded-full border border-gray-500" src="{{asset('assets/images/logo/favicon.png')}}">
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <a class="flex items-center gap-1.5 leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/metronic/tailwind/demo8/public-profile/teams">
                                            Nguyễn Trung Hiếu
                                        </a>
                                        <span class="text-2sm text-gray-700">
                                            Vào lúc 07:55:53
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="">
                                        <img class="h-9 rounded-full border border-gray-500" src="{{asset('assets/images/logo/favicon.png')}}">
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <a class="flex items-center gap-1.5 leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/metronic/tailwind/demo8/public-profile/teams">
                                            Nguyễn Trung Hiếu
                                        </a>
                                        <span class="text-2sm text-gray-700">
                                            Vào lúc 07:55:53
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card h-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-filled ki-emoji-happy text-pink-600 text-2xl"></i>&nbsp;Sinh nhật
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-5">
                            <div class="flex flex-col lg:flex-row items-start lg:items-center lg:justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="">
                                        <img class="h-9 rounded-full border border-gray-500" src="{{asset('assets/images/logo/favicon.png')}}">
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <a class="flex items-center gap-1.5 leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/metronic/tailwind/demo8/public-profile/teams">
                                            Nguyễn Trung Hiếu
                                        </a>
                                        <span class="text-2sm text-gray-700">
                                            5 tháng 11
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs flex justify-between w-full lg:flex-col lg:items-end flex-1">
                                    <span>Còn 1 tháng</span>
                                    <a href="/" class="text-purple-800 flex items-center cursor-pointer"><span class="ki-filled ki-barcode"></span>Tặng thiệp</a>
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