<!-- Core Theme -->
<script src="{{asset('assets/js/core.bundle.js')}}"></script>

<!-- Moment JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- JS -->
<script src="{{asset('assets/js/global/index.js')}}"></script>
<script src="{{asset('assets/js/global/tooltip.js')}}"></script>
<script src="{{asset('assets/js/global/notify.js')}}"></script>
<script src="{{asset('assets/js/global/axios.js')}}"></script>
<script>
    const ipapiAccessKey = "{{env('IP_API_ACCESS_KEY')}}";
    const openWeatherApiKey = "{{env('OPEN_WEATHER_API_KEY')}}";
</script>