<!DOCTYPE html>
<html lang="en">
@include('auth.layouts.head')

<body class="fix-menu">
    @include('auth.layouts.loading')
    
    @yield('auth_content')

    @include('auth.layouts.footer')
    @include('auth.layouts.script')
    @stack('scripts')
</body>

</html>