@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quy trình tư vấn
            </h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/customer-support" class="text-sm text-gray-700 hover:text-primary">
                            Khách hàng
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="mx-2 text-gray-400">/</span>
                            <span class="text-sm text-gray-500">Quy trình tư vấn</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <a href="/appointment/detail/{{$details['id']}}" class="btn btn-primary btn-sm">
                <i class="ki-filled ki-calendar"></i>
                Lịch hẹn
            </a>
            <a href="/customer/{{$details['id']}}" class="btn btn-light btn-sm">
                <i class="ki-filled ki-user"></i>
                Thông tin KH
            </a>
        </div>
    </div>
</div>
<div class="container-fixed">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Cột bên trái - Thông tin khách hàng & nhật ký -->
        <div class="col-span-1">
            <div class="grid gap-5">
                <!-- Thông tin khách hàng -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin khách hàng</h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base"></i>
                                </div>
                                <div class="flex flex-col flex-1 gap-0.5">
                                    <a class="leading-none font-semibold text-md text-gray-900 hover:text-primary" href="/customer/{{$details['id']}}">
                                        {{$details['name']}}
                                    </a>
                                    <div class="flex flex-wrap gap-1">
                                        <span class="badge badge-sm badge-light">{{$details['type_name']}}</span>
                                        @if ($details['classification']['id'] != 0)
                                        <span class="badge badge-sm badge-outline badge-{{$details['classification']['color']}}">
                                            {{$details['classification']['name']}}
                                        </span>
                                        @endif
                                        @if ($details['status']['id'] != 0)
                                        <span class="badge badge-sm badge-outline badge-{{$details['status']['color']}}">
                                            {{$details['status']['name']}}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-2 mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled ki-sms text-gray-500"></i>
                                    <span class="text-sm text-gray-700 details-email-customer">{{$details['email'] ?: 'Chưa có email'}}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled ki-phone text-gray-500"></i>
                                    <span class="text-sm text-gray-700 details-phone-customer">{{$details['phone'] ?: 'Chưa có SĐT'}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lịch hẹn sắp tới -->
                @if(count($upcoming_appointments) > 0)
                <div class="card bg-light-primary border border-primary-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-filled ki-calendar-tick text-primary mr-2"></i>
                            Lịch hẹn sắp tới
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-3">
                            @foreach($upcoming_appointments as $appointment)
                            <div class="flex items-center gap-2 p-2 bg-white rounded-lg shadow-sm">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{$appointment['name']}}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-600">
                                            <i class="ki-filled ki-calendar text-{{$appointment['color']}}"></i>
                                            {{$appointment['formatted_date']}}
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            <i class="ki-filled ki-time text-{{$appointment['color']}}"></i>
                                            {{$appointment['formatted_time']}}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-{{$appointment['color']}}">
                                        @if($appointment['days_away'] == 1)
                                            Hôm nay
                                        @elseif($appointment['days_away'] == 2)
                                            Ngày mai
                                        @else
                                            Còn {{$appointment['days_away'] - 1}} ngày
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Nhật ký tư vấn -->
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Nhật ký tư vấn
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
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
                                                Thêm nhật ký
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
                            <div data-id="{{$cons['id']}}" class="consultation-tab flex items-center gap-3 cursor-pointer py-2 px-4 rounded-lg {{$cons['index']==1?'bg-gray-100 border border-blue-500':'hover:bg-gray-100'}}">
                                <div class="flex items-center grow gap-2.5">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900 mb-px">
                                                {{$cons['name']}}
                                            </span>
                                            <span class="badge badge-sm badge-{{$cons['status_color']}}">
                                                {{$cons['status']}}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-700">
                                            Cập nhật: {{date('d-m-Y H:i', strtotime($cons['updated_at']))}}
                                        </span>
                                    </div>
                                </div>
                                <div class="menu" data-menu="true">
                                    <div class="menu-item menu-item-dropdown" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-dots-vertical">
                                            </i>
                                        </button>
                                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                            <div class="menu-item">
                                                <button data-id="{{$cons['id']}}" class="menu-link" data-modal-toggle="#update-name-consultation-modal">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-pencil">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title text-left">
                                                        Chỉnh sửa
                                                    </span>
                                                </button>
                                            </div>
                                            @if(count($details['consultations']) > 1)
                                            <div class="menu-item">
                                                <button data-id="{{$cons['id']}}" class="menu-link remove-consulation">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-trash !text-red-600">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title !text-red-600">
                                                        Xoá
                                                    </span>
                                                </button>
                                            </div>
                                            @endif
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
        
        <!-- Cột bên phải - Quá trình tư vấn -->
        <div class="col-span-1 lg:col-span-2">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Quá trình tư vấn
                        </h3>
                        <div id="consultation-status-indicator" class="ml-auto">
                            <!-- Status will be injected here -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="consoltation-logs-body" class="flex-col">
                            <div class="flex items-center justify-center p-6">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="flex flex-col w-full">
                            <form id="add-log-form" class="relative grow">
                                <div id="attachment-preview" class="grid gap-2 py-2"></div>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <select class="select select-sm w-full lg:w-auto" name="action">
                                        <option value="0">Hỏi nhu cầu khách hàng</option>
                                        <option value="1">Tư vấn gói</option>
                                        <option value="2">Lập hợp đồng</option>
                                        <option value="3">Gửi bảng giá</option>
                                        <option value="4">Khách từ chối</option>
                                        <option value="5">Đặt lịch tư vấn lại</option>
                                    </select>
                                    
                                    <!-- Follow-up date picker -->
                                    <div class="flex follow-up-container" style="display: none;">
                                        <div class="relative flex items-center">
                                            <input type="text" name="follow_up_date" class="input input-sm pl-10" placeholder="Ngày hẹn lại">
                                            <label class="absolute left-2 top-1/2 transform -translate-y-1/2">
                                                <i class="ki-filled ki-calendar text-gray-500"></i>
                                            </label>
                                        </div>
                                        <label class="switch switch-sm ml-2 mt-1">
                                            <span class="switch-label text-xs">Tạo lịch hẹn</span>
                                            <input name="create_appointment" type="checkbox" value="1">
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="flex input h-auto ps-4 bg-transparent rounded-lg">
                                    <div style="flex: 1;"><textarea class="py-4 outline-none w-full" name="message" rows="1" placeholder="Bổ sung quá trình tư vấn..." type="text"></textarea></div>
                                    <div class="flex items-center gap-2.5">
                                        <label type="button" class="btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-exit-up"></i>
                                            <input id="attachment-input" type="file" class="hidden">
                                        </label>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Đăng
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm nhật ký tư vấn mới -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-consultation-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thêm nhật ký mới
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
                    <input class="input" name="name" type="text" placeholder="Vui lòng nhập tiêu đề nhật ký">
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

<!-- Modal chỉnh sửa tên nhật ký -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-name-consultation-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chỉnh sửa nhật ký
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
                    <input class="input" name="name" type="text" placeholder="Vui lòng nhập tiêu đề">
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

<!-- Modal xác nhận SĐT từ nội dung tin nhắn -->
<button id="confirm-phone-btn" data-modal-toggle="#confirm-phone-consultation-modal" class="hidden"></button>
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="confirm-phone-consultation-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <style>
            #confirm-phone-consultation-modal:not(:has(.other-phone-radio:checked)) .other-phone {
                display: none;
            }
        </style>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <button type="button" class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0 hidden" data-modal-dismiss="true">
                        <i class="ki-filled ki-cross">
                        </i>
                    </button>
                    <div class="flex flex-center gap-1">
                        <label class="text-gray-900 text-2sm">
                            <span class="text-danger">Thông báo:</span> Nội dung bạn gửi có chứa số điện thoại, bạn có muốn lưu số cho khách hàng này?
                        </label>
                    </div>
                    <div class="flex flex-col items-start gap-2.5 confirm-phone-body"></div>
                    <div class="flex flex-col items-start gap-2.5">
                        <label class="form-label flex items-center gap-2.5">
                            <input checked="" class="radio radio-sm no-phone" name="phone" type="radio" value="" />
                            Không lưu
                        </label>
                        <label class="form-label flex items-center gap-2.5">
                            <input class="radio radio-sm other-phone-radio" name="phone" type="radio" value="" />
                            Lưu số khác
                        </label>
                    </div>
                    <input type="text" class="input other-phone" name="other-phone" placeholder="Nhập số bạn muốn lưu">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Đăng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let consulationDelete = 0;
    let consulationActive = 0;
    $(function() {
        // Khởi tạo Flatpickr cho trường follow_up_date
        flatpickrMake($("input[name=follow_up_date]"), 'datetime');
        
        // Hiển thị/ẩn trường follow_up_date khi chọn action = 5 (đặt lịch tư vấn lại)
        $('select[name=action]').on('change', function() {
            if ($(this).val() == 5) {
                $('.follow-up-container').show();
            } else {
                $('.follow-up-container').hide();
            }
        });

        $('.consultation-tab').on('click', function() {
            $('.consultation-tab').removeClass('bg-gray-100 border border-blue-500');
            $(this).addClass('bg-gray-100 border border-blue-500');
            consulationActive = $(this).attr('data-id');
            getConsultationLog($(this).attr('data-id'));
        });

        $('[data-modal-toggle="#update-name-consultation-modal"]').on('click', function() {
            consulationDelete = $(this).attr('data-id');
        });

        $('#create-consultation-modal form').on('submit', function(e) {
            e.preventDefault();
            postCreateConsultation(this);
        });

        $('#update-name-consultation-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateNameConsultation(this);
        });

        $('.remove-consulation').on('click', function() {
            Notiflix.Confirm.show(
                'Xoá nhật ký',
                'Bạn có chắc chắn muốn xoá nhật ký này?',
                'Đúng',
                'Huỷ',
                () => {
                    postRemoveConsultation($(this).attr('data-id'));
                },
                () => {}, {},
            );
        });

        $('#attachment-input').on('change', function() {
            postUploadAttachment(this);
        });

        $(document).on('click', '.attachment-remove-btn', function() {
            $(this).closest('.attachment-preview-item').remove();
        });

        $('#add-log-form').on('submit', function(e) {
            e.preventDefault();

            let messageText = $(this).find('textarea[name=message]').val();
            let phoneNumbers = messageText.match(/\b\d{10}\b/g);
            if (phoneNumbers) {
                $('#confirm-phone-btn').trigger('click');

                let phoneContent = '';
                $(phoneNumbers).each((_, phone) => {
                    phoneContent += `<label class="form-label flex items-center gap-2.5">
                            <input class="radio radio-sm has-phone" name="phone" type="radio" value="${phone}" />
                            ${phone}
                        </label>`;
                });

                $('#confirm-phone-consultation-modal .confirm-phone-body').html(phoneContent);
                $('#confirm-phone-consultation-modal .no-phone').prop('checked', true);
                $('#confirm-phone-consultation-modal .other-phone').val('');
            } else {
                postAddLog(this);
            }
        });

        $('#confirm-phone-consultation-modal form').on('submit', function(e) {
            e.preventDefault();
            let phoneIs = $('#confirm-phone-consultation-modal input[type=radio]:checked');
            let phoneNumber = '';
            if (phoneIs.is('.no-phone'))
                phoneNumber = '';
            else if (phoneIs.is('.other-phone-radio'))
                phoneNumber = $('#confirm-phone-consultation-modal .other-phone').val();
            else if (phoneIs.is('.has-phone'))
                phoneNumber = phoneIs.val();
            postAddLog(document.getElementById('add-log-form'), phoneNumber);
        });

        // Hiển thị tab đầu tiên mặc định
        $('.consultation-tab:eq(0)').trigger('click');
    });

    // Hiển thị trạng thái tư vấn của nhật ký hiện tại
    function updateConsultationStatus(actionCode) {
        let statusHTML = '';
        
        if (actionCode !== null) {
            let statusText = '';
            let statusClass = '';
            
            switch (parseInt(actionCode)) {
                case 0:
                    statusText = 'Đang hỏi nhu cầu';
                    statusClass = 'primary';
                    break;
                case 1:
                    statusText = 'Đang tư vấn gói';
                    statusClass = 'warning';
                    break;
                case 2:
                    statusText = 'Đã lập hợp đồng';
                    statusClass = 'success';
                    break;
                case 3:
                    statusText = 'Đã gửi bảng giá';
                    statusClass = 'info';
                    break;
                case 4:
                    statusText = 'Khách đã từ chối';
                    statusClass = 'danger';
                    break;
                case 5:
                    statusText = 'Đã hẹn tư vấn lại';
                    statusClass = 'gray';
                    break;
            }
            
            statusHTML = `<span class="badge badge-${statusClass} badge-lg">${statusText}</span>`;
        }
        
        $('#consultation-status-indicator').html(statusHTML);
    }

    async function postAddLog(_this, phoneNumber = '') {
        let method = "post",
            url = "/consultation/add-log",
            params = null,
            data = $(_this).serialize() + '&consultation_id=' + consulationActive

        if (phoneNumber) {
            data += '&phone=' + phoneNumber;
        }
            
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                $(`.consultation-tab[data-id=${consulationActive}]`).trigger('click');
                $('#attachment-preview').html('');
                $('#add-log-form textarea').val('');
                $('input[name=follow_up_date]').val('');
                $('input[name=create_appointment]').prop('checked', false);
                $('.follow-up-container').hide();
                $('#confirm-phone-consultation-modal button[data-modal-dismiss]').trigger('click');
                if (phoneNumber != '') $('.details-phone-customer').text(phoneNumber);
                showAlert('success', res.data.message);
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function postCreateConsultation(_this) {
        let method = "post",
            url = "/consultation/create",
            params = null,
            data = $(_this).serialize() + "&customer_id={{$details['id']}}";
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function postUpdateNameConsultation(_this) {
        let method = "post",
            url = "/consultation/update",
            params = null,
            data = $(_this).serialize() + "&id=" + consulationDelete;
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function postRemoveConsultation(id) {
        let method = "post",
            url = "/consultation/remove",
            params = null,
            data = {
                id
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function getConsultationLog(id) {
        let method = "get",
            url = "/consultation/log",
            params = {
                id
            },
            data = null;
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                $('#consoltation-logs-body').html(res.data.content);
                
                // Cập nhật trạng thái hiện tại
                const activeTab = $(`.consultation-tab[data-id=${id}]`);
                const actionCode = activeTab.data('action-code');
                updateConsultationStatus(actionCode);
                
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function postUploadAttachment(_this) {
        try {
            let file = $(_this).prop("files")[0];
            if (file) {
                let url = URL.createObjectURL(file);
                let res = await uploadFileTemplate(file);
                if (res.data.status == 200) {
                    let img = '';
                    if (res.data.data.type.startsWith('image/') && res.data.data.extension !== 'svg') {
                        img = `<img class="w-[30px]" alt="${res.data.data.extension}" src="https://drive.google.com/thumbnail?id=${res.data.data.driver_id}&sz=w56">`;
                    } else {
                        img = `<img class="w-[30px]" alt="${res.data.data.extension}" src="/assets/images/file-types/${res.data.data.extension}.svg">`;
                    }

                    $('#attachment-preview').append(`<div class="attachment-preview-item flex items-center gap-4">
                            <input name="attachment[]" value="${res.data.data.driver_id}" type="text" class="hidden">
                            <div class="flex items-center gap-2.5">
                                ${img}
                                <div class="flex flex-col">
                                    <a href="https://drive.google.com/file/d/${res.data.data.driver_id}/view" target="_blank" style="overflow-wrap: anywhere;" class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                        ${res.data.data.name}
                                    </a>
                                    <span class="text-xs text-gray-700">
                                        ${res.data.data.size} - <span class="attachment-remove-btn text-danger text-xs font-semibold cursor-pointer">Xoá</span>
                                    </span>
                                </div>
                            </div>
                        </div>`);
                } else {
                    showAlert('error', 'Upload failed!');
                }
            }
        } catch (error) {
            showAlert('error', error);
        }
    }
</script>
@endpush