@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quy trình tư vấn
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
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-body">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base">
                                    </i>
                                </div>
                                <div class="flex flex-col flex-1 gap-0.5">
                                    <a class="leading-none font-semibold text-2xl text-gray-900 hover:text-primary" href="/customer/{{$details['id']}}">
                                        {{$details['name']}}
                                    </a>
                                    <span class="text-2sm text-gray-700 font-normal">
                                        0 hợp đồng
                                    </span>
                                    <div>
                                        <span class="badge badge-sm badge-success badge-outline">
                                            {{$details['classification']}}
                                        </span>
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
                                                        <i class="ki-filled ki-sms">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                    {{$details['email']}}
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-phone">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                    {{$details['phone']}}
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
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Nhật ký tư vấn
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <button class="menu-link" data-modal-toggle="#create-consultation-modal">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm lần tư vấn
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-2.5">
                            @foreach ($details['consultations'] as $cons)
                            <div data-id="{{$cons['id']}}" class="consultation-tab flex items-center gap-3 cursor-pointer py-2 px-4 rounded-lg hover:bg-gray-100">
                                <div class="flex items-center grow gap-2.5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 mb-px">
                                            {{$cons['name']}}
                                        </span>
                                        <span class="text-xs text-gray-700">
                                        {{$cons['created_at']}}
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
                                                        <i class="ki-filled ki-trash !text-red-600">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title !text-red-600">
                                                        Xoá
                                                    </span>
                                                </a>
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
        </div>
        <div class="col-span-2">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Quá trình tư vấn
                        </h3>
                    </div>
                    <div class="card-body">
                        @foreach ($details['consultations'] as $consultation)
                        <div data-id="{{$consultation['id']}}" class="flex-col body-log hidden">
                            @foreach ($consultation['logs'] as $log)
                            <div class="flex items-start relative">
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-people text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-800">
                                            {{$log['message']}}
                                            .
                                        </div>
                                        <span class="text-xs text-gray-600">
                                        {{$log['created_at']}}
                                        </span>
                                        <div>
                                            <span class="badge badge-sm badge-outline">
                                            @if ($log['status'] == 0)
                                                Hỏi nhu cầu
                                            @elseif ($log['status'] == 1)
                                                Tư vấn gói
                                            @elseif ($log['status'] == 2)
                                                Lập hợp đồng
                                            @elseif ($log['status'] == 3)
                                                Gửi bảng giá
                                            @elseif ($log['status'] == 4)
                                                Khách từ chối
                                            @elseif ($log['status'] == 5)
                                                Đặt lịch tư vấn lại
                                            @else
                                                Hành động không xác định
                                            @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <div class="flex flex-col w-full">
                            <form id="add-log-form" class="relative grow">
                                <input name="message-log" class="input h-auto py-4 ps-4 bg-transparent rounded-lg" placeholder="Bổ sung quá trình tư vấn..." type="text" value="">
                                <div class="flex items-center gap-2.5 absolute end-3 top-1/2 -translate-y-1/2">
                                <select class="text-sm text-gray-900 outline-none" name="action-log">
                                    <option value="0">Hỏi nhu cầu</option>
                                    <option value="1">Tư vấn gói</option>
                                    <option value="2">Lập hợp đồng</option>
                                    <option value="3">Gửi bảng giá</option>
                                    <option value="4">Khách từ chối</option>
                                    <option value="5">Đặt lịch tư vấn lại</option>
                                </select>
                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-clear">
                                        <i class="ki-filled ki-exit-up">
                                        </i>
                                    </button>
                                    <button type="submit" class="btn btn-dark btn-sm" href="#">
                                        Đăng
</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-consultation-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thêm lần tư vấn
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="flex flex-center gap-1">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Tiêu đề
                        </label>
                    </div>
                    <input class="input" name="consultation-name" type="text" placeholder="Vui lòng nhập">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        const details = @json($details);

        $(function() {
            $('.consultation-tab').on('click', function() {
                $('.consultation-tab').removeClass('bg-gray-100 border border-blue-500 active');
                $(this).addClass('bg-gray-100 border border-blue-500 active');

                let id = $(this).attr('data-id');
                $(`.body-log`).addClass('hidden').removeClass('flex active');
                $(`.body-log[data-id=${id}]`).removeClass('hidden').addClass('flex active');
            })

            $('#create-consultation-modal form').on('submit', async function() {
                let method = "post",
                    url = "/consultation/create",
                    params = null,
                    data = {
                        id: details.id,
                        name: $(this).find('input[name=consultation-name]').val()
                    }
                let res = await axiosTemplate(method, url, params, data);
                switch (res.data.status) {
                    case 200:
                        window.location.reload();
                        break;
                    default:
                        alert(res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                        break;
                }
            })

            $('#add-log-form').on('submit', async function() {
                let method = "post",
                    url = "/consultation/add-log",
                    params = null,
                    data = {
                        consultation_id: $('.consultation-tab.active').attr('data-id'),
                        user_id: 0,
                        message: $(this).find('input[name=message-log]').val(),
                        status: $(this).find('input[name=action-log]').val(),
                    }
                let res = await axiosTemplate(method, url, params, data);
                switch (res.data.status) {
                    case 200:
                        window.location.reload();
                        break;
                    default:
                        alert(res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                        break;
                }
            })

            $('.consultation-tab:eq(0)').trigger('click');
        })
    </script>
@endpush