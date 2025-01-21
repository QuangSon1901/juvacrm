<!-- Sidebar -->
<div class="fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0" data-drawer="true" data-drawer-class="drawer drawer-start flex" data-drawer-enable="true|lg:false" id="sidebar">
    <div class="hidden lg:flex items-center justify-center shrink-0 pt-8 pb-3.5" id="sidebar_header">
        <a href="/">
            <img class="rounded-lg border border-gray-500 shrink-0 dark:hidden h-[42px]" src="{{asset('assets/images/logo/favicon.png')}}" />
            <img class="rounded-lg border border-gray-500 shrink-0 hidden dark:block h-[42px]" src="{{asset('assets/images/logo/favicon.png')}}" />
        </a>
    </div>
    <div class="scrollable-y-hover grow gap-2.5 shrink-0 flex items-center pt-5 lg:pt-0 ps-3 pe-3 lg:pe-0 flex-col" data-scrollable="true" data-scrollable-dependencies="#sidebar_header,#sidebar_footer" data-scrollable-height="auto" data-scrollable-offset="80px" data-scrollable-wrappers="#sidebar_menu_wrapper" id="sidebar_menu_wrapper">
        <!-- Sidebar Menu -->
        <div class="menu flex flex-col gap-2.5 grow" data-menu="true" id="sidebar_menu">
            <div class="menu-item {{ isActiveRoute('dashboard.overview.overview') }}">
                <a class="menu-link rounded-[9px] border border-transparent menu-item-active:border-gray-200 menu-item-active:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2" href="{{route('dashboard.overview.overview')}}">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-chart-line-star text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title text-xs menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600 font-medium w-max">
                        Tổng quan
                    </span>
                </a>
            </div>
            <div class="menu-item {{ isActiveRoute(['dashboard.customer.support.customer-support', 'dashboard.customer.client.customer-leads', 'dashboard.customer.manage.leads', 'dashboard.customer.manage.customer-type']) }}" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-address-book text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600 w-max">
                        CRM
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.customer.support.customer-support')}}">
                            <span class="menu-title">
                                Chăm sóc khách hàng
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.customer.client.customer-leads')}}">
                            <span class="menu-title">
                                Khách hàng tiềm năng
                            </span>
                        </a>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Dữ liệu khách hàng
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.customer.manage.leads')}}">
                                    <span class="menu-title">
                                        Lead khách hàng
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.customer.manage.customer-type')}}">
                                    <span class="menu-title">
                                        Phân loại khách hàng
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-questionnaire-tablet text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600 w-max">
                        Hợp đồng
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.contract.contract')}}">
                            <span class="menu-title">
                                Hợp đồng
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.service.services')}}">
                            <span class="menu-title">
                                Loại hình dịch vụ
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.accounting.deposit-receipt.deposit-receipt')}}">
                            <span class="menu-title">
                                Biên nhận cọc
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-users text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600 w-max">
                        Nhân sự
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Quản lý nhân viên
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.team.team')}}">
                                    <span class="menu-title">
                                        Bộ phận/phòng ban
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.member.member')}}">
                                    <span class="menu-title">
                                        Nhân sự
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.account.timekeeping.timekeeping')}}">
                            <span class="menu-title">
                                Quản lý chấm công
                            </span>
                        </a>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Công việc
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.task.task')}}">
                                    <span class="menu-title">
                                        Danh sách công việc
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.task.config')}}">
                                    <span class="menu-title">
                                        Thiết lập công việc
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Đào tạo nhân sự
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.training.document.document')}}">
                                    <span class="menu-title">
                                        File tài liệu hướng dẫn
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.training.document.blank')}}">
                                    <span class="menu-title">
                                        Lịch sử đào tạo
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-lots-shopping text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600 w-max">
                        Tài sản
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item">
                        <a class="menu-link" href="/">
                            <span class="menu-title">
                                Danh mục tài sản
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/">
                            <span class="menu-title">
                                Danh sách sản phẩm
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.assets.file-explorer.file-explorer')}}">
                            <span class="menu-title">
                                Quản lý media
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-brifecase-tick text-1.5xl">
                        </i>
                    </span>
                    <span class="menu-title menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary font-medium text-xs text-gray-600 w-max">
                        Kế toán
                    </span>
                </div>
                <div class="menu-default menu-dropdown gap-0.5 w-[220px] scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Thu chi
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Phiếu thu
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Phiếu chi
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Hạng mục thu
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Hạng mục chi
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Lương nhân viên
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Bảng lương
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Chi ứng lương
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Phương thức thanh toán
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Phương thức thanh toán
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Đơn vị tiền tệ
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link" href="/">
                                    <span class="menu-title">
                                        Ngôn ngữ
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Sidebar Menu -->
    </div>
    <div class="flex flex-col gap-5 items-center shrink-0 pb-4" id="sidebar_footer">
        <div class="flex flex-col gap-1.5">
            <div class="dropdown" data-dropdown="true" data-dropdown-offset="-20px, 30px" data-dropdown-placement="right-end" data-dropdown-trigger="click|lg:click">
                <button class="dropdown-toggle btn btn-icon btn-icon-xl relative rounded-md size-9 border border-transparent hover:bg-light hover:text-primary hover:border-gray-200 dropdown-open:bg-gray-200 text-gray-600">
                    <span class="menu-icon">
                        <i class="ki-filled ki-notification text-1.5xl"></i>
                    </span>
                </button>
                <div class="dropdown-content light:border-gray-300 w-screen max-w-[320px]">
                    <div class="flex items-center justify-between gap-2.5 text-sm text-gray-900 font-semibold px-5 py-2.5 border-b border-b-gray-200" id="notifications_header">
                        Thông báo
                        <button class="btn btn-sm btn-icon btn-light btn-clear shrink-0" data-dropdown-dismiss="true">
                            <i class="ki-filled ki-cross">
                            </i>
                        </button>
                    </div>
                    <div class="pt-3 pb-4 flex flex-col gap-5 scrollable-y-auto max-h-[400px] divide-y divide-gray-200">
                        <div class="flex grow gap-2.5 px-5">
                            <div class="relative shrink-0 mt-0.5">
                                <img alt="" class="rounded-full size-8" src="{{asset('assets/images/logo/favicon.png')}}">
                                <span class="size-1.5 badge badge-circle badge-success absolute top-7 end-0.5 ring-1 ring-light transform -translate-y-1/2">
                                </span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="text-2sm font-medium mb-px">
                                    <span class="text-gray-700">
                                        Khách hàng
                                    </span>
                                    <a class="hover:text-primary-active text-gray-900 font-semibold" href="#">
                                        Trung Hiếu
                                    </a>
                                    <span class="text-gray-700">
                                        vừa yêu cầu liên hệ
                                    </span>
                                </div>
                                <span class="flex items-center text-2xs font-medium text-gray-500">
                                    1 phút trước
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-b-gray-200"></div>
                    <div class="pt-3 pb-4 flex flex-col gap-5 scrollable-y-auto max-h-[400px] divide-y divide-gray-200">
                        <div class="flex grow gap-2.5 px-5">
                            <div class="relative shrink-0 mt-0.5">
                                <img alt="" class="rounded-full size-8" src="{{asset('assets/images/logo/favicon.png')}}">
                                <span class="size-1.5 badge badge-circle badge-success absolute top-7 end-0.5 ring-1 ring-light transform -translate-y-1/2">
                                </span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="text-2sm font-medium mb-px">
                                    <span class="text-gray-700">
                                        Khách hàng
                                    </span>
                                    <a class="hover:text-primary-active text-gray-900 font-semibold" href="#">
                                        Trung Hiếu
                                    </a>
                                    <span class="text-gray-700">
                                        vừa yêu cầu liên hệ
                                    </span>
                                </div>
                                <span class="flex items-center text-2xs font-medium text-gray-500">
                                    5 phút trước
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="grid p-5 border-t border-t-gray-200">
                        <a class="btn btn-sm btn-light justify-center" href="/">
                            Xem tất cả
                        </a>
                    </div> -->
                </div>
            </div>
            <a href="{{route('dashboard.setting.setting')}}" class="btn btn-icon btn-icon-xl relative rounded-md size-9 border border-transparent hover:bg-light hover:text-primary hover:border-gray-200 dropdown-open:bg-gray-200 text-gray-600">
                <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                    <i class="ki-filled ki-setting-2 text-1.5xl"></i>
                </span>
            </a>
        </div>
        <div class="menu" data-menu="true">
            <div class="menu-item" data-menu-item-offset="-20px, 28px" data-menu-item-overflow="true" data-menu-item-placement="right-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                <div class="menu-toggle btn btn-icon">
                    <img alt="" class="size-8 justify-center rounded-lg border border-gray-500 shrink-0" src="{{asset('assets/images/logo/favicon.png')}}"></img>
                </div>
                <div class="menu-dropdown menu-default light:border-gray-300 w-screen max-w-[250px]">
                    <div class="flex items-center justify-between px-5 py-1.5 gap-1.5">
                        <div class="flex items-center gap-2">
                            <img alt="" class="size-9 rounded-full border-2 border-success" src="{{asset('assets/images/logo/favicon.png')}}">
                            <div class="flex flex-col gap-1.5">
                                <span class="text-sm text-gray-800 font-semibold leading-none">
                                    {{Session::get(ACCOUNT_CURRENT_SESSION)['name']}}
                                </span>
                                <span class="text-xs text-gray-600 font-medium leading-none">
                                {{Session::get(ACCOUNT_CURRENT_SESSION)['email']}}
                                </span>
                            </div>
                            </img>
                        </div>
                        <!-- <span class="badge badge-xs badge-primary badge-outline">
                            Admin
                        </span> -->
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="flex flex-col">
                        <div class="menu-item">
                            <a class="menu-link" href="{{route('dashboard.profile.profile')}}">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-profile-circle">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    Trang cá nhân
                                </span>
                            </a>
                        </div>
                        <div class="menu-item" data-menu-item-offset="-10px, 0" data-menu-item-placement="left-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                            <div class="menu-link">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-icon"></i>
                                </span>
                                <span class="menu-title">
                                    Ngôn ngữ
                                </span>
                                <div class="flex items-center gap-1.5 rounded-md border border-gray-300 text-gray-600 p-1.5 text-2xs font-medium shrink-0">
                                    Tiếng Việt
                                    <svg class="size-3.5" enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="256" cy="256" fill="#d80027" r="256" />
                                        <path d="m256 133.565 27.628 85.029h89.405l-72.331 52.55 27.628 85.03-72.33-52.551-72.33 52.551 27.628-85.03-72.33-52.55h89.404z" fill="#ffda44" />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                        <g />
                                    </svg>
                                </div>
                            </div>
                            <div class="menu-dropdown menu-default light:border-gray-300 w-full max-w-[170px]">
                                <div class="menu-item active">
                                    <button class="menu-link h-10">
                                        <span class="menu-icon">
                                            <svg class="size-4" enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="256" cy="256" fill="#d80027" r="256" />
                                                <path d="m256 133.565 27.628 85.029h89.405l-72.331 52.55 27.628 85.03-72.33-52.551-72.33 52.551 27.628-85.03-72.33-52.55h89.404z" fill="#ffda44" />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                            </svg>
                                        </span>
                                        <span class="menu-title">
                                            Tiếng Việt
                                        </span>
                                        <span class="menu-badge">
                                            <i class="ki-solid ki-check-circle text-success text-base">
                                            </i>
                                        </span>
                                    </button>
                                </div>
                                <!-- <div class="menu-item">
                                    <a class="menu-link h-10" href="?dir=rtl">
                                        <span class="menu-icon">
                                            <svg class="size-4" enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="256" cy="256" fill="#f0f0f0" r="256" />
                                                <g fill="#d80027">
                                                    <path d="m244.87 256h267.13c0-23.106-3.08-45.49-8.819-66.783h-258.311z" />
                                                    <path d="m244.87 122.435h229.556c-15.671-25.572-35.708-48.175-59.07-66.783h-170.486z" />
                                                    <path d="m256 512c60.249 0 115.626-20.824 159.356-55.652h-318.712c43.73 34.828 99.107 55.652 159.356 55.652z" />
                                                    <path d="m37.574 389.565h436.852c12.581-20.529 22.338-42.969 28.755-66.783h-494.362c6.417 23.814 16.174 46.254 28.755 66.783z" />
                                                </g>
                                                <path d="m118.584 39.978h23.329l-21.7 15.765 8.289 25.509-21.699-15.765-21.699 15.765 7.16-22.037c-19.106 15.915-35.852 34.561-49.652 55.337h7.475l-13.813 10.035c-2.152 3.59-4.216 7.237-6.194 10.938l6.596 20.301-12.306-8.941c-3.059 6.481-5.857 13.108-8.372 19.873l7.267 22.368h26.822l-21.7 15.765 8.289 25.509-21.699-15.765-12.998 9.444c-1.301 10.458-1.979 21.11-1.979 31.921h256c0-141.384 0-158.052 0-256-50.572 0-97.715 14.67-137.416 39.978zm9.918 190.422-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822zm100.115 100.083-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822zm0-74.574 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822z" fill="#0052b4" />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                                <g />
                                            </svg>
                                        </span>
                                        <span class="menu-title">
                                            English
                                        </span>
                                    </a>
                                </div> -->
                            </div>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{route('dashboard.logs.activity.activity')}}">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-time">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    Lịch sử hoạt động
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="menu-separator">
                    </div>
                    <div class="flex flex-col">
                        <div class="menu-item mb-0.5">
                            <div class="menu-link">
                                <span class="menu-icon">
                                    <i class="ki-filled ki-moon">
                                    </i>
                                </span>
                                <span class="menu-title">
                                    Dark Mode
                                </span>
                                <label class="switch switch-sm">
                                    <input data-theme-state="dark" data-theme-toggle="true" name="check" type="checkbox" value="1">
                                    </input>
                                </label>
                            </div>
                        </div>
                        <div class="menu-item px-4 py-1.5">
                            <form class="w-full" action="{{ route('auth.logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-light justify-center w-full" type="submit">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Sidebar -->