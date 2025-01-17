<!DOCTYPE html>
<html class="h-full light" lang="en">
@include('auth.layouts.head')

<body class="antialiased flex h-full text-base text-gray-700 dark:bg-coal-500">
    @include('auth.layouts.loading')
    
    @yield('auth_content')

    <div class="absolute hidden group-hover:block bg-gray-800 text-white text-sm px-4 py-2 rounded shadow-lg 
                transform -translate-x-1/2 translate-y-2 z-50"
        style="min-width: 150px;"
        id="tooltip-content">
        Tooltip nội dung
    </div>
    <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>
    @include('auth.layouts.script')
    @stack('scripts')
</body>

</html>