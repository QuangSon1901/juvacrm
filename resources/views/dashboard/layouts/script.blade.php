<!-- Core Theme -->
<script src="{{asset('assets/js/core.bundle.js')}}"></script>

<!-- Moment JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Notiflix -->
<script src="{{asset('assets/notiflix/dist/notiflix-3.2.8.min.js')}}"></script>

<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Quill editor -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- JS -->
<script src="{{asset('assets/js/global/index.js')}}"></script>
<script src="{{asset('assets/js/global/tooltip.js')}}"></script>
<script src="{{asset('assets/js/global/notify.js')}}"></script>
<script src="{{asset('assets/js/global/axios.js')}}"></script>
<script src="{{asset('assets/js/global/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/global/quill.js')}}"></script>
<script src="{{asset('assets/js/global/utils.js')}}"></script>
<script>
    const ipapiAccessKey = "{{env('IP_API_ACCESS_KEY')}}";
    const openWeatherApiKey = "{{env('OPEN_WEATHER_API_KEY')}}";
</script>