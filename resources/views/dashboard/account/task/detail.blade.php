@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin công việc
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
        <div class="col-span-2">
            <div class="grid gap-5 lg:gap-7.5">
                <div class="card">
                    <div class="card-header flex-wrap gap-2">
                        <h3 class="card-title flex items-center gap-2">
                            <span>Công việc #40319</span>
                            <span class="badge badge-sm badge-outline badge-success">
                                Đang mở
                            </span>
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
                                                <i class="ki-filled ki-messages">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Phản hồi
                                            </span>
                                        </a>
                                    </div>
                                    <div class="menu-separator"></div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="#">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-pencil">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Chỉnh sửa
                                            </span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="#">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-copy">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Tạo bản sao
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body lg:py-7.5 grid gap-5 lg:gap-7.5">
                        <div class="flex items-center justify-between grow border border-gray-200 rounded-xl gap-2 p-5">
                            <div class="flex flex-col lg:flex-row items-center gap-4">
                                @include("dashboard.layouts.icons.user")
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2.5">
                                        <a class="text-base font-medium text-gray-900 hover:text-primary-active" href="#">
                                            Chụp kỉ yếu lớp 12 trung học Krong Bong
                                        </a>
                                    </div>
                                    <div class="form-info text-gray-800 font-normal">
                                        Được thêm bởi Trương Việt Hòa khoảng 1 năm trước. Đã cập nhật 4 ngày trước.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-5">
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Trạng thái:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            Đang mở
                                        </span>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Độ ưu tiên:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            Trung bình
                                        </span>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Người thực hiện:
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex flex-col gap-5">
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Ngày bắt đầu:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            10/19/2023
                                        </span>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Ngày kết thúc:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            11/06/2024 (4 days late)
                                        </span>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            % hoàn thành:
                                        </span>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Thời gian dự kiến:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            (Tổng: 18.00 h)
                                        </span>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <label class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Thời gian thực tế:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            (Tổng: 23.00 h)
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="menu-separator simple"></div>
                        <div class="flex flex-col gap-2.5">
                            <label class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">
                                    Mô tả
                                </span>
                            </label>
                            <p class="form-info leading-5 text-gray-800 font-normal">
                                Select this option to create new user accounts for individuals whose details are included in the import data but who do not have an existing account in the system.
                            </p>
                        </div>
                        <div class="menu-separator simple"></div>
                        <div class="flex flex-col gap-2.5">
                            <label class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">
                                    Chỉ mục
                                </span>
                                <span class="badge badge-xs badge-primary badge-outline">8</span>
                                <span class="checkbox-label font-normal">(0 open — 8 closed)</span>
                            </label>
                            <div class="flex items-center flex-wrap justify-between gap-2.5">
                                <div class="flex items-center justify-between gap-1">
                                    <div class="flex flex-wrap">
                                        <div>
                                            <span class="checkbox-label font-normal text-primary"><a href="/task/123">#12343:</a></span>
                                            <span class="checkbox-label font-normal">[TASK][DASHBOARD][WEB ORDER] Khởi tạo project reactjs - tạo layout tổng</span>
                                        </div>
                                        <div>
                                            <span class="checkbox-label font-normal">Đang mở</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">Vũ Quang Sơn</span>
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
                                                            <i class="ki-filled ki-search-list">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Xem
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-separator"></div>
                                                <div class="menu-item">
                                                    <a class="menu-link" href="#">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Chỉnh sửa
                                                        </span>
                                                    </a>
                                                </div>
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
                                <div class="flex items-center justify-between gap-1">
                                    <div class="flex flex-wrap">
                                        <div>
                                            <span class="checkbox-label font-normal text-gray-600 line-through"><a href="/task/123">#52102:</a></span>
                                            <span class="checkbox-label font-normal">[TASK][DASHBOARD][WEB ORDER] Khởi tạo project reactjs - tạo layout tổng</span>
                                        </div>
                                        <div>
                                            <span class="checkbox-label font-normal">Đã đóng</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">Vũ Quang Sơn</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">10/19/2023</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">10/19/2023</span>
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
                                                            <i class="ki-filled ki-search-list">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Xem
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-separator"></div>
                                                <div class="menu-item">
                                                    <a class="menu-link" href="#">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Chỉnh sửa
                                                        </span>
                                                    </a>
                                                </div>
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
                        <div class="hidden flex-col gap-4">
                            <div class="text-gray-900 text-2sm font-medium">
                                Phản hồi
                            </div>
                            <textarea class="textarea text-2sm text-gray-600 font-normal" name="message" rows="5" placeholder="Nhập phản hồi tại đây"></textarea>
                            <div class="flex gap-2.5">
                                <button class="btn btn-sm btn-primary">
                                    Gửi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="grid gap-5 lg:gap-7.5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Đính kèm
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-2.5 lg:gap-5">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center grow gap-2.5">
                                    <img src="/static/metronic/tailwind/dist/assets/media/file-types/pdf.svg">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                            Project-pitch.pdf
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            4.7 MB 26 Sep 2024 3:20 PM
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
                                                        <i class="ki-filled ki-file-down">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Tải xuống
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-delete-files">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Gỡ
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center grow gap-2.5">
                                    <img src="/static/metronic/tailwind/dist/assets/media/file-types/doc.svg">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                            Report-v1.docx
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            2.3 MB 1 Oct 2024 12:00 PM
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
                                                        <i class="ki-filled ki-document">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Details
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" data-modal-toggle="#share_profile_modal" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-share">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Share
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-file-up">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Export
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center grow gap-2.5">
                                    <img src="/static/metronic/tailwind/dist/assets/media/file-types/ai.svg">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                            Framework-App.js
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            0.8 MB 17 Oct 2024 6:46 PM
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
                                                        <i class="ki-filled ki-document">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Details
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" data-modal-toggle="#share_profile_modal" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-share">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Share
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-file-up">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Export
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center grow gap-2.5">
                                    <img src="/static/metronic/tailwind/dist/assets/media/file-types/js.svg">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                            Mobile-logo.ai
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            0.2 MB 4 Nov 2024 11:30 AM
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
                                                        <i class="ki-filled ki-document">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Details
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" data-modal-toggle="#share_profile_modal" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-share">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Share
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-file-up">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Export
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
                    <div class="card-header">
                        <h3 class="card-title">
                            Lịch sử
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col">
                            <div class="flex items-start relative">
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-800">
                                            Cập nhật bởi Nguyễn Thu Hằng
                                        </div>
                                        <ul class="ml-4 list-disc">
                                            <li class="text-xs">Đã thêm 2 hình ảnh</li>
                                        </ul>
                                        <span class="text-xs text-gray-600">
                                            Khoảng 1 năm trước
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start relative">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-cup text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-800">
                                            Cập nhật bởi Nguyễn Thu Hằng
                                        </div>
                                        <ul class="ml-4 list-disc">
                                            <li class="text-xs">Thêm mô tả</li>
                                            <li class="text-xs">Đã xoá 2 hình ảnh</li>
                                            <li class="text-xs">Đã thêm 2 hình ảnh</li>
                                            <li class="text-xs">Sửa tiêu đề "[TASK][DASHBOARD][WEB ORDER] Khởi tạo project reactjs - tạo layout tổng" thành "[TASK][DASHBOARD][WEB ORDER] Khởi tạo project reactjs - tạo layout tổng"</li>
                                            <li class="text-xs">Đã đặt thời gian dự kiến</li>
                                        </ul>
                                        <span class="text-xs text-gray-600">
                                            Khoảng 1 năm trước
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

</div>
@endsection