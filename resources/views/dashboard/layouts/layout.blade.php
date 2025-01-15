<!DOCTYPE html>
<html class="h-full" data-theme="true" data-theme-mode="light" dir="ltr" lang="en">
@include('dashboard.layouts.head')

<body class="antialiased flex h-full text-base text-gray-700 [--tw-page-bg:#F6F6F9] [--tw-page-bg-dark:var(--tw-coal-200)] [--tw-content-bg:var(--tw-light)] [--tw-content-bg-dark:var(--tw-coal-500)] [--tw-content-scrollbar-color:#e8e8e8] [--tw-header-height:60px] [--tw-sidebar-width:90px] bg-custom-gradient">
    @include('dashboard.layouts.loader')
    <div class="flex grow">
        @include('dashboard.layouts.header')
        <div class="flex flex-col lg:flex-row grow pt-[--tw-header-height] lg:pt-0">
            @include('dashboard.layouts.sidebar')
            <div class="flex flex-col grow rounded-xl bg-[--tw-content-bg] dark:bg-[--tw-content-bg-dark] border border-gray-300 dark:border-gray-200 lg:ms-[--tw-sidebar-width] mt-0 lg:mt-5 m-5">
                <div class="flex flex-col grow lg:scrollable-y-auto lg:[scrollbar-width:auto] lg:light:[--tw-scrollbar-thumb-color:var(--tw-content-scrollbar-color)] pt-5" id="scrollable_content">
                    <main class="grow" role="content">
                        @yield('dashboard_content')
                    </main>
                </div>
                @include('dashboard.layouts.footer')
            </div>
        </div>
    </div>
    <div class="absolute hidden group-hover:block bg-gray-800 text-white text-sm px-4 py-2 rounded shadow-lg 
                transform -translate-x-1/2 translate-y-2 z-50"
        style="min-width: 150px;"
        id="tooltip-content">
        Tooltip nội dung
    </div>

    <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>
    @include('dashboard.layouts.script')
    @stack('scripts')
</body>

</html>