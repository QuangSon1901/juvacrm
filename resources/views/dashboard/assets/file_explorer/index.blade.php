@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý tập tin
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
    <div class="grid gap-5">
        <div class="flex flex-col gap-4 border border-gray-200 rounded-xl p-5 w-full">
            <div class="lg:w-1/2">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-normal text-gray-700">
                        Dung lượng khả dụng
                    </span>
                    <div class="flex items-end">
                        <span class="font-semibold text-gray-900">
                            Đã sử dụng {{formatBytes($storage['usage'] ?? 0)}} / {{formatBytes($storage['limit'] ?? 0)}}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1 my-1.5">
                    <div class="progress progress-primary">
                        <div class="progress-bar" style="width: {{$storage['usage']/$storage['limit']*100}}%">
                        </div>
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-4 mb-1">
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-primary">
                        </span>
                        <span class="text-sm font-normal text-gray-800">
                            {{formatBytes($storage['usage'] ?? 0)}} đã sử dụng
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-gray">
                        </span>
                        <span class="text-sm font-normal text-gray-900">
                            {{formatBytes($storage['remaining'] ?? 0)}} còn trống
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center flex-wrap md:flex-nowrap lg:items-end justify-between border-b border-b-gray-200 dark:border-b-coal-100 gap-3">
            <div class="grid">
                <div class="scrollable-x-auto">
                    <div class="tabs gap-6" data-tabs="true">
                        <div class="tab cursor-pointer active" data-tab-toggle="#tab-all-file">
                            <span class="text-nowrap text-sm">
                                Tất cả tệp
                            </span>
                        </div>
                        <div class="tab cursor-pointer" data-tab-toggle="#tab_my_file">
                            <span class="text-nowrap text-sm">
                                Tệp của tôi
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end grow lg:grow-0 lg:pb-4 gap-2.5 mb-3 lg:mb-0">
                <button class="btn btn-sm btn-light" data-modal-toggle="#upload-file-modal">
                    <i class="ki-outline ki-exit-up"></i>
                    Tải lên
                </button>
            </div>
        </div>
        <div class="transition-opacity duration-700" id="tab-all-file">
            <div class="flex flex-col items-stretch gap-5">
                <div class="flex flex-wrap items-center gap-5 justify-between">
                    <h3 class="text-lg text-gray-900 font-semibold">
                        {{$count_all}} tệp
                    </h3>
                    <div class="btn-tabs" data-tabs="true">
                        <button class="btn btn-icon active" data-tab-toggle="#all-file-cards">
                            <i class="ki-filled ki-category"></i>
                        </button>
                        <button class="btn btn-icon" data-tab-toggle="#all-file-list">
                            <i class="ki-filled ki-row-horizontal"></i>
                        </button>
                    </div>
                </div>
                <div id="all-file-cards">
                    <div class="grid grid-cols-1 md:!grid-cols-4 gap-5">
                        @foreach ($data as $file)
                        <div class="card p-5">
                            <div class="flex items-center justify-between mb-3">
                                <a href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank" class="flex items-center justify-center size-[50px] rounded-lg bg-gray-100">
                                    @if (Str::startsWith($file['type'], 'image/') && $file['extension'] != 'svg')
                                    <img class="rounded-lg size-[50px] object-cover" alt="{{$file['extension']}}.svg" src="https://drive.google.com/thumbnail?id={{ $file['driver_id'] }}&sz=w50">
                                    @else
                                    <img alt="{{$file['extension']}}.svg" src="{{asset('assets/images/file-types/' . $file['extension'] . '.svg')}}">
                                    @endif
                                </a>
                                <div class="dropdown" data-dropdown="true" data-dropdown-placement="bottom-end" data-dropdown-placement-rtl="bottom-start" data-dropdown-trigger="click">
                                    <button class="dropdown-toggle btn btn-sm btn-icon btn-light">
                                        <i class="ki-filled ki-dots-vertical">
                                        </i>
                                    </button>
                                    <div class="dropdown-content menu-default w-full max-w-[220px] hidden" style="opacity: 0;">
                                        <div class="menu-item" data-dropdown-dismiss="true">
                                            <a href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank" class="menu-link">
                                                <span class="menu-icon">
                                                    <i class="ki-filled ki-exit-down"></i>
                                                </span>
                                                <span class="menu-title">
                                                    Tải xuống
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col mb-3">
                                <a style="overflow-wrap: anywhere;" class="text-sm font-media/brand text-gray-900 hover:text-primary-active mb-px" href="https://drive.google.com/uc?id={{ $file['driver_id'] }}&export=download" download>
                                    <b>{{$file['name']}}</b>
                                </a>
                                <span class="text-xs text-gray-700">
                                </span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-600">
                                    Kích cỡ:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{formatBytes($file['size'])}}
                                    </span>
                                </span>
                                <span class="text-xs text-gray-600">
                                    Đuôi mở rộng:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{$file['extension']}}
                                    </span>
                                </span>
                                <span style="overflow-wrap: anywhere;" class="text-xs text-gray-600">
                                    Loại tệp:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{$file['type']}}
                                    </span>
                                </span>
                                <span class="text-xs text-gray-600">
                                    Ngày tạo:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{formatDateTime($file['created_at'])}}
                                    </span>
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div id="all-file-list" class="hidden">
                    <div class="grid grid-cols-1 gap-5">
                        @foreach ($data as $file)
                        <div class="card p-5">
                            <div class="flex items-center flex-wrap justify-between gap-5">
                                <div class="flex items-center gap-3.5">
                                    <a href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank" class="flex items-center justify-center size-14 shrink-0 rounded-lg bg-gray-100">
                                        @if (Str::startsWith($file['type'], 'image/') && $file['extension'] != 'svg')
                                        <img class="rounded-lg size-14 object-cover" alt="{{$file['extension']}}.svg" src="https://drive.google.com/thumbnail?id={{ $file['driver_id'] }}&sz=w56">
                                        @else
                                        <img alt="{{$file['extension']}}.svg" src="{{asset('assets/images/file-types/' . $file['extension'] . '.svg')}}">
                                        @endif
                                    </a>
                                    <div class="flex flex-col">
                                        <a style="overflow-wrap: anywhere;" class="text-sm font-media/brand text-gray-900 hover:text-primary-active mb-px" href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank">
                                            <b>{{$file['name']}}</b>
                                        </a>
                                        <span class="text-xs text-gray-700">
                                            Đuôi mở rộng: {{$file['extension']}}
                                        </span>
                                        <span style="overflow-wrap: anywhere;" class="text-xs text-gray-700">
                                            Loại tệp: {{$file['type']}}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center flex-wrap gap-5 lg:gap-20">
                                    <div class="flex items-center gap-5 lg:gap-14">
                                        <div class="flex justify-end gap-5">
                                            <span class="text-xs text-gray-600">
                                                Kích cỡ:
                                                <span class="text-xs font-medium text-gray-800">
                                                    {{formatBytes($file['size'])}}
                                                </span>
                                            </span>
                                            <span class="text-xs text-gray-600">
                                                Ngày tạo:
                                                <span class="text-xs font-medium text-gray-800">
                                                    {{formatDateTime($file['created_at'])}}
                                                </span>
                                            </span>
                                        </div>
                                        <div class="menu" data-menu="true">
                                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical">
                                                    </i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="https://drive.google.com/uc?id={{ $file['driver_id'] }}&export=download" download>
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-exit-down"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Tải xuống
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
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden transition-opacity duration-700" id="tab_my_file">
            <div class="flex flex-col items-stretch gap-5">
                <div class="flex flex-wrap items-center gap-5 justify-between">
                    <h3 class="text-lg text-gray-900 font-semibold">
                        {{$count_owner}} tệp
                    </h3>
                    <div class="btn-tabs" data-tabs="true">
                        <button class="btn btn-icon active" data-tab-toggle="#my-file-cards">
                            <i class="ki-filled ki-category"></i>
                        </button>
                        <button class="btn btn-icon" data-tab-toggle="#my-file-list">
                            <i class="ki-filled ki-row-horizontal"></i>
                        </button>
                    </div>
                </div>
                <div id="my-file-cards">
                    <div class="grid grid-cols-1 md:!grid-cols-4 gap-5">
                        @foreach ($data as $file)
                        @if ($file['user']['id'] == Session::get(ACCOUNT_CURRENT_SESSION)['id'])
                        <div class="card p-5">
                            <div class="flex items-center justify-between mb-3">
                                <a href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank" class="flex items-center justify-center size-[50px] rounded-lg bg-gray-100">
                                    @if (Str::startsWith($file['type'], 'image/') && $file['extension'] != 'svg')
                                    <img class="rounded-lg size-[50px] object-cover" alt="{{$file['extension']}}.svg" src="https://drive.google.com/thumbnail?id={{ $file['driver_id'] }}&sz=w50">
                                    @else
                                    <img alt="{{$file['extension']}}.svg" src="{{asset('assets/images/file-types/' . $file['extension'] . '.svg')}}">
                                    @endif
                                </a>
                                <div class="dropdown" data-dropdown="true" data-dropdown-placement="bottom-end" data-dropdown-placement-rtl="bottom-start" data-dropdown-trigger="click">
                                    <button class="dropdown-toggle btn btn-sm btn-icon btn-light">
                                        <i class="ki-filled ki-dots-vertical">
                                        </i>
                                    </button>
                                    <div class="dropdown-content menu-default w-full max-w-[220px] hidden" style="opacity: 0;">
                                        <div class="menu-item" data-dropdown-dismiss="true">
                                            <a href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank" class="menu-link">
                                                <span class="menu-icon">
                                                    <i class="ki-filled ki-exit-down"></i>
                                                </span>
                                                <span class="menu-title">
                                                    Tải xuống
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col mb-3">
                                <a style="overflow-wrap: anywhere;" class="text-sm font-media/brand text-gray-900 hover:text-primary-active mb-px" href="https://drive.google.com/uc?id={{ $file['driver_id'] }}&export=download" download>
                                    <b>{{$file['name']}}</b>
                                </a>
                                <span class="text-xs text-gray-700">
                                </span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-600">
                                    Kích cỡ:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{formatBytes($file['size'])}}
                                    </span>
                                </span>
                                <span class="text-xs text-gray-600">
                                    Đuôi mở rộng:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{$file['extension']}}
                                    </span>
                                </span>
                                <span style="overflow-wrap: anywhere;" class="text-xs text-gray-600">
                                    Loại tệp:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{$file['type']}}
                                    </span>
                                </span>
                                <span class="text-xs text-gray-600">
                                    Ngày tạo:
                                    <span class="text-xs font-medium text-gray-800">
                                        {{formatDateTime($file['created_at'])}}
                                    </span>
                                </span>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                <div id="my-file-list" class="hidden">
                    <div class="grid grid-cols-1 gap-5">
                        @foreach ($data as $file)
                        @if ($file['user']['id'] == Session::get(ACCOUNT_CURRENT_SESSION)['id'])
                        <div class="card p-5">
                            <div class="flex items-center flex-wrap justify-between gap-5">
                                <div class="flex items-center gap-3.5">
                                    <a href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank" class="flex items-center justify-center size-14 shrink-0 rounded-lg bg-gray-100">
                                        @if (Str::startsWith($file['type'], 'image/') && $file['extension'] != 'svg')
                                        <img class="rounded-lg size-14 object-cover" alt="{{$file['extension']}}.svg" src="https://drive.google.com/thumbnail?id={{ $file['driver_id'] }}&sz=w56">
                                        @else
                                        <img alt="{{$file['extension']}}.svg" src="{{asset('assets/images/file-types/' . $file['extension'] . '.svg')}}">
                                        @endif
                                    </a>
                                    <div class="flex flex-col">
                                        <a style="overflow-wrap: anywhere;" class="text-sm font-media/brand text-gray-900 hover:text-primary-active mb-px" href="https://drive.google.com/file/d/{{ $file['driver_id'] }}/view" target="_blank">
                                            <b>{{$file['name']}}</b>
                                        </a>
                                        <span class="text-xs text-gray-700">
                                            Đuôi mở rộng: {{$file['extension']}}
                                        </span>
                                        <span style="overflow-wrap: anywhere;" class="text-xs text-gray-700">
                                            Loại tệp: {{$file['type']}}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center flex-wrap gap-5 lg:gap-20">
                                    <div class="flex items-center gap-5 lg:gap-14">
                                        <div class="flex justify-end gap-5">
                                            <span class="text-xs text-gray-600">
                                                Kích cỡ:
                                                <span class="text-xs font-medium text-gray-800">
                                                    {{formatBytes($file['size'])}}
                                                </span>
                                            </span>
                                            <span class="text-xs text-gray-600">
                                                Ngày tạo:
                                                <span class="text-xs font-medium text-gray-800">
                                                    {{formatDateTime($file['created_at'])}}
                                                </span>
                                            </span>
                                        </div>
                                        <div class="menu" data-menu="true">
                                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical">
                                                    </i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="https://drive.google.com/uc?id={{ $file['driver_id'] }}&export=download" download>
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-exit-down"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Tải xuống
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
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="upload-file-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Tải lên tệp
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5" enctype="multipart/form-data">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Chọn tệp đính kèm
                        </span>
                    </div>
                    <input name="file" class="file-input" type="file" />
                    <input hidden name="action" class="input hidden" type="text" value="MEDIA_DRIVER_UPLOAD" />
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Tải lên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(function() {
        let modalUploadFile = document.querySelector('#upload-file-modal');
        let instanceUploadFile = KTModal.getInstance(modalUploadFile);

        instanceUploadFile.on('hidden', () => {
            $('#upload-file-modal input[name=file]').val('');
        });

        $('#upload-file-modal form').on('submit', function(e) {
            e.preventDefault();
            postUploadFile(this);
        })
    })

    async function postUploadFile(_this) {
        let method = "post",
            url = "/upload-file",
            params = null,
            data = new FormData(_this);
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }
</script>
@endpush