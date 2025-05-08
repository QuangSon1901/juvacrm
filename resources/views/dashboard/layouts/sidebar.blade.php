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
            @if(hasPermission('view-dashboard'))
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
            @endif
            
            @if(hasPermission('view-customer'))
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
            @endif
            
            @if(hasPermission('view-contract') || hasPermission('view-service'))
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
                    @if(hasPermission('view-contract'))
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.contract.contract')}}">
                            <span class="menu-title">
                                Hợp đồng
                            </span>
                        </a>
                    </div>
                    @endif
                    
                    @if(hasPermission('view-service'))
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.service.services')}}">
                            <span class="menu-title">
                                Loại hình dịch vụ
                            </span>
                        </a>
                    </div>
                    @endif
                    
                    @if(hasPermission('view-transaction'))
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.accounting.deposit-receipt.deposit-receipt')}}">
                            <span class="menu-title">
                                Thanh toán hợp đồng
                            </span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            @if(hasPermission('view-team') || hasPermission('view-member') || hasPermission('view-timekeeping') || hasPermission('view-schedule') || hasPermission('view-salary') || hasPermission('view-task'))
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
                    @if(hasPermission('view-team') || hasPermission('view-member'))
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
                            @if(hasPermission('view-team'))
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.team.team')}}">
                                    <span class="menu-title">
                                        Bộ phận/phòng ban
                                    </span>
                                </a>
                            </div>
                            @endif
                            
                            @if(hasPermission('view-member'))
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.member.member')}}">
                                    <span class="menu-title">
                                        Nhân sự
                                    </span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if(hasPermission('view-timekeeping') || hasPermission('view-schedule'))
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Quản lý thời gian làm việc
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            @if(hasPermission('view-timekeeping'))
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.timekeeping.timekeeping')}}">
                                    <span class="menu-title">
                                        Quản lý chấm công
                                    </span>
                                </a>
                            </div>
                            @endif
                            
                            @if(hasPermission('view-schedule'))
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.schedule.schedule')}}">
                                    <span class="menu-title">
                                        Lịch làm việc
                                    </span>
                                </a>
                            </div>
                            
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.schedule.part-time')}}">
                                    <span class="menu-title">
                                        Đăng ký lịch part-time
                                    </span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if(hasPermission('view-salary'))
                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Quản lý lương
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.salary.configuration')}}">
                                    <span class="menu-title">
                                        Cấu hình tính lương
                                    </span>
                                </a>
                            </div>
                            
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.salary.payroll')}}">
                                    <span class="menu-title">
                                        Bảng lương nhân viên
                                    </span>
                                </a>
                            </div>
                            
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.account.salary.advance')}}">
                                    <span class="menu-title">
                                        Tạm ứng lương
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(hasPermission('view-task'))
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
                                <a class="menu-link" href="{{route('dashboard.account.task.task.config')}}">
                                    <span class="menu-title">
                                        Thiết lập công việc
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            @if(hasPermission('view-assets'))
            <div class="menu-item" data-menu-item-offset="-10px, 14px" data-menu-item-overflow="true" data-menu-item-placement="right-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:hover">
                <div class="menu-link rounded-[9px] border border-transparent menu-item-here:border-gray-200 menu-item-here:bg-light menu-link-hover:bg-light menu-link-hover:border-gray-200 w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                    <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                        <i class="ki-filled ki-office-bag text-1.5xl">
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
            @endif
            
            @if(hasPermission('view-transaction'))
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
                                <a class="menu-link" href="{{route('dashboard.accounting.transaction.transaction')}}">
                                    <span class="menu-title">
                                        Phiếu thu chi
                                    </span>
                                </a>
                            </div>
                            
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.accounting.category.category')}}">
                                    <span class="menu-title">
                                        Danh mục thu chi
                                    </span>
                                </a>
                            </div>
                            
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.accounting.report.financial')}}">
                                    <span class="menu-title">
                                        Báo cáo tài chính
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="menu-item">
                        <a class="menu-link" href="{{route('dashboard.accounting.deposit-receipt.deposit-receipt')}}">
                            <span class="menu-title">
                                Biên nhận thanh toán
                            </span>
                        </a>
                    </div>

                    <div class="menu-item" data-menu-item-placement="right-start" data-menu-item-toggle="accordion|lg:dropdown" data-menu-item-trigger="click|lg:hover">
                        <div class="menu-link grow cursor-pointer">
                            <span class="menu-title">
                                Quản lý hoa hồng
                            </span>
                            <span class="menu-arrow">
                                <i class="ki-filled ki-right text-3xs rtl:translate rtl:rotate-180">
                                </i>
                            </span>
                        </div>
                        <div class="menu-default menu-dropdown gap-0.5 w-[220px]">
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.accounting.commissions.report')}}">
                                    <span class="menu-title">
                                        Báo cáo hoa hồng
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
                                <a class="menu-link" href="{{route('dashboard.accounting.payment-method.payment-method')}}">
                                    <span class="menu-title">
                                        Phương thức thanh toán
                                    </span>
                                </a>
                            </div>
                            
                            <div class="menu-item">
                                <a class="menu-link" href="{{route('dashboard.accounting.currency.currency')}}">
                                    <span class="menu-title">
                                        Đơn vị tiền tệ
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!-- End of Sidebar Menu -->
    </div>
    <!-- Footer không thay đổi -->
    <div class="flex flex-col gap-5 items-center shrink-0 pb-4" id="sidebar_footer">
        <!-- Phần thông báo không đổi -->
        <div class="flex flex-col gap-1.5">
            <div class="dropdown" data-dropdown="true" data-dropdown-offset="-20px, 30px" data-dropdown-placement="right-end" data-dropdown-trigger="click|lg:click">
                <button class="dropdown-toggle btn btn-icon btn-icon-xl relative rounded-md size-9 border border-transparent hover:bg-light hover:text-primary hover:border-gray-200 dropdown-open:bg-gray-200 text-gray-600">
                    <span class="menu-icon">
                        <i class="ki-filled ki-notification text-1.5xl"></i>
                    </span>
                </button>
                <div class="dropdown-content light:border-gray-300 w-screen max-w-[320px]">
                    <!-- Nội dung thông báo giữ nguyên -->
                    <!-- ... -->
                </div>
            </div>
            @if(hasPermission('view-setting'))
            <a href="{{route('dashboard.setting.setting')}}" class="btn btn-icon btn-icon-xl relative rounded-md size-9 border border-transparent hover:bg-light hover:text-primary hover:border-gray-200 dropdown-open:bg-gray-200 text-gray-600">
                <span class="menu-icon menu-item-here:text-primary menu-item-active:text-primary menu-link-hover:text-primary text-gray-600">
                    <i class="ki-filled ki-setting-2 text-1.5xl"></i>
                </span>
            </a>
            @endif
        </div>
        
        <!-- Menu profile giữ nguyên -->
        <div class="menu" data-menu="true">
            <!-- ... -->
        </div>
    </div>
</div>
<!-- End of Sidebar -->