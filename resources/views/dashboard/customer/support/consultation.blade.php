@extends('dashboard.layouts.layout')
@section('dashboard_content')
<style>
    /* Styling cho phần upload file */
    .file-item-preview {
        transition: all 0.2s ease;
    }
    .file-item-preview:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .message-container {
        position: relative;
    }
    
    .message-action-buttons {
        display: none;
        position: absolute;
        right: 10px;
        top: 5px;
    }
    
    .message-container:hover .message-action-buttons {
        display: flex;
    }
    
    /* Loader styling */
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .attachments-droppable {
        border: 2px dashed #e2e8f0;
        transition: all 0.2s ease;
    }
    
    .attachments-droppable.highlight {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    /* Fix responsive design issues */
    @media (max-width: 768px) {
        .grid-cols-3 {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Loading overlay -->
<div id="loading-overlay" class="spinner-overlay !hidden">
    <div class="flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        <p class="mt-3 text-gray-800 font-medium">Đang xử lý...</p>
    </div>
</div>

<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">Quy trình tư vấn</h1>
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
                @if(isset($upcoming_appointments) && count($upcoming_appointments) > 0)
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
                                        @if($appointment['days_away'] == 0)
                                            Hôm nay
                                        @elseif($appointment['days_away'] == 1)
                                            Ngày mai
                                        @else
                                            Còn {{$appointment['days_away']}} ngày
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
                            <div data-id="{{$cons['id']}}" data-action-code="{{$cons['action_code']}}" class="consultation-tab flex items-center gap-3 cursor-pointer py-2 px-4 rounded-lg {{$cons['index']==1?'bg-gray-100 border border-blue-500':'hover:bg-gray-100'}}">
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
                                <!-- Phần hiển thị file đính kèm -->
                                <div id="attachment-preview" class="attachments-droppable grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 py-2 px-4 rounded mb-3"></div>
                                
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
                                
                                <div class="flex flex-col">
                                    <div class="input h-auto ps-4 bg-transparent rounded-lg">
                                        <div style="flex: 1;">
                                            <textarea class="py-4 outline-none w-full" name="message" rows="2" placeholder="Bổ sung quá trình tư vấn..." type="text"></textarea>
                                        </div>
                                        <div class="flex items-center gap-2.5 py-1">
                                            <div class="relative overflow-hidden">
                                                <button type="button" class="btn btn-sm btn-light btn-clear" id="file-upload-btn">
                                                    <i class="ki-filled ki-file-added"></i>
                                                </button>
                                                <input type="file" id="attachment-input" class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer" multiple>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm" id="send-message-btn">
                                                Đăng
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 italic mt-1">
                                        * Kéo thả file vào vùng trên hoặc nhấn nút để đính kèm file
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

<!-- Các modal cần thiết giữ nguyên -->
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

<!-- Template cho file preview -->
<template id="file-preview-template">
    <div class="file-item-preview relative flex items-center p-2 rounded-lg border border-gray-200 bg-white shadow-sm" data-file-id="">
        <div class="mr-3 w-10 h-10 flex items-center justify-center shrink-0">
            <img src="" class="max-w-full max-h-full object-contain" alt="File preview">
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate"></p>
            <p class="text-xs text-gray-500"></p>
        </div>
        <button type="button" class="remove-file-btn ml-2 p-1 text-red-500 hover:text-red-700 transition-colors duration-200">
            <i class="ki-filled ki-trash"></i>
        </button>
    </div>
</template>
@endsection

@push('scripts')
<script>
    let consulationDelete = 0;
    let consulationActive = 0;
    let uploadedFiles = [];  // Mảng lưu trữ các file đã upload
    const cloudinaryName = '{{env("CLOUDINARY_CLOUD_NAME")}}';
    
    $(function() {
        // Khởi tạo Flatpickr cho trường follow_up_date
        flatpickrMake($("input[name=follow_up_date]"), 'datetime');
        
        // Setup drag and drop cho upload file
        setupFileDragDrop();
        
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
            
            // Cập nhật trạng thái
            updateConsultationStatus($(this).data('action-code'));
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

        // Xử lý upload file
        $('#attachment-input').on('change', function(e) {
            handleSelectedFiles(e.target.files);
        });
        
        // Xóa file đính kèm
        $(document).on('click', '.remove-file-btn', function() {
            const fileId = $(this).closest('.file-item-preview').data('file-id');
            removeUploadedFile(fileId);
        });
        
        // Tạo lịch hẹn trực tiếp từ log message
        $(document).on('click', '.create-appointment-from-log', function() {
            const messageText = $(this).data('message');
            const messageDate = $(this).data('date');
            
            // Set giá trị vào form
            $('select[name=action]').val(5).trigger('change');
            
            // Lấy ngày giờ từ tin nhắn hoặc sử dụng ngày hiện tại + 1 ngày
            const appointmentDate = messageDate ? new Date(messageDate) : new Date();
            appointmentDate.setDate(appointmentDate.getDate() + 1);
            appointmentDate.setHours(9, 0, 0, 0);
            
            // Format datetime
            const formattedDate = appointmentDate.toISOString().slice(0, 16).replace('T', ' ');
            $('input[name=follow_up_date]').val(formattedDate);
            
            // Checked tạo lịch hẹn
            $('input[name=create_appointment]').prop('checked', true);
            
            // Set message có thông tin từ tin nhắn cũ
            $('textarea[name=message]').val('Hẹn lịch tư vấn: ' + messageText.substring(0, 100) + '...');
            
            // Cuộn đến form để người dùng có thể thấy
            $('html, body').animate({
                scrollTop: $("#add-log-form").offset().top - 100
            }, 500);
            
            // Focus vào textarea
            $('textarea[name=message]').focus();
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
            if (phoneIs.is('.no-phone')) {
                phoneNumber = '';
            } else if (phoneIs.is('.other-phone-radio')) {
                phoneNumber = $('#confirm-phone-consultation-modal .other-phone').val();
            } else if (phoneIs.is('.has-phone')) {
                phoneNumber = phoneIs.val();
            }
            postAddLog(document.getElementById('add-log-form'), phoneNumber);
        });

        // Hiển thị tab đầu tiên mặc định
        $('.consultation-tab:eq(0)').trigger('click');
    });
    
    // Thiết lập Drag & Drop
    function setupFileDragDrop() {
        const dropArea = document.getElementById('attachment-preview');
        
        // Ngăn chặn hành vi mặc định
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Highlight khi drag over
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropArea.classList.add('highlight');
        }
        
        function unhighlight() {
            dropArea.classList.remove('highlight');
        }
        
        // Xử lý khi drop
        dropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            handleSelectedFiles(files);
        }
    }
    
    // Xử lý các file được chọn (từ input hoặc drag & drop)
    function handleSelectedFiles(files) {
        if (!files || files.length === 0) return;
        
        Array.from(files).forEach(file => {
            uploadFile(file);
        });
        
        // Reset input để có thể chọn lại file
        $('#attachment-input').val('');
    }
    
    // Upload file lên server
    async function uploadFile(file) {
        // Tạo preview ngay lập tức
        const previewId = 'temp-' + Date.now();
        addFilePreview({
            id: previewId,
            name: file.name,
            size: formatFileSize(file.size),
            type: file.type,
            isImage: file.type.startsWith('image/'),
            url: file.type.startsWith('image/') ? URL.createObjectURL(file) : getFileIconPath(file.name)
        }, true);
        
        // Hiển thị loading
        showLoading();
        
        try {
            const formData = new FormData();
            formData.append('file', file);
            
            const response = await fetch('/consultation/upload-file', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.status === 200) {
                // Thay thế preview tạm thời bằng file thật
                replaceFilePreview(previewId, {
                    id: result.data.driver_id,
                    name: result.data.name,
                    size: result.data.size,
                    type: result.data.type,
                    isImage: result.data.is_image,
                    url: result.data.preview_url
                });
                
                // Thêm vào danh sách file đã upload
                uploadedFiles.push(result.data.driver_id);
            } else {
                // Xóa preview nếu upload thất bại
                removeFilePreview(previewId);
                showAlert('error', result.message || 'Lỗi khi tải file');
            }
        } catch (error) {
            // Xóa preview nếu có lỗi
            removeFilePreview(previewId);
            showAlert('error', 'Lỗi khi tải file: ' + error.message);
        } finally {
            hideLoading();
        }
    }
    
    // Thêm preview file
    function addFilePreview(fileData, isTemporary = false) {
        const template = document.getElementById('file-preview-template');
        const clone = document.importNode(template.content, true);
        
        const container = clone.querySelector('.file-item-preview');
        container.dataset.fileId = fileData.id;
        
        if (isTemporary) {
            container.classList.add('opacity-60');
            const loadingIcon = document.createElement('div');
            loadingIcon.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-50';
            loadingIcon.innerHTML = '<i class="ki-filled ki-loading animate-spin text-primary"></i>';
            container.appendChild(loadingIcon);
        }
        
        const img = clone.querySelector('img');
        img.src = fileData.url;
        img.alt = fileData.name;
        
        const filename = clone.querySelector('p.text-sm');
        filename.textContent = fileData.name;
        
        const fileInfo = clone.querySelector('p.text-xs');
        fileInfo.textContent = fileData.size;
        
        document.getElementById('attachment-preview').appendChild(clone);
    }
    
    // Thay thế preview tạm thời
    function replaceFilePreview(tempId, fileData) {
        const tempPreview = document.querySelector(`.file-item-preview[data-file-id="${tempId}"]`);
        if (!tempPreview) return;
        
        tempPreview.dataset.fileId = fileData.id;
        tempPreview.classList.remove('opacity-60');
        
        const loadingIcon = tempPreview.querySelector('.absolute');
        if (loadingIcon) loadingIcon.remove();
        
        const img = tempPreview.querySelector('img');
        img.src = fileData.url;
        img.alt = fileData.name;
        
        const filename = tempPreview.querySelector('p.text-sm');
        filename.textContent = fileData.name;
        
        const fileInfo = tempPreview.querySelector('p.text-xs');
        fileInfo.textContent = fileData.size;
    }
    
    // Xóa file preview
    function removeFilePreview(fileId) {
        const preview = document.querySelector(`.file-item-preview[data-file-id="${fileId}"]`);
        if (preview) preview.remove();
    }
    
    // Xóa file đã upload
    function removeUploadedFile(fileId) {
        // Xóa khỏi danh sách
        const index = uploadedFiles.indexOf(fileId);
        if (index > -1) {
            uploadedFiles.splice(index, 1);
        }
        
        // Xóa preview
        removeFilePreview(fileId);
    }
    
    // Format kích thước file
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Lấy đường dẫn icon theo loại file
    function getFileIconPath(filename) {
        const extension = filename.split('.').pop().toLowerCase();
        
        // Danh sách các extension phổ biến
        const commonExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
        
        if (commonExtensions.includes(extension)) {
            return `/assets/images/file-types/${extension}.svg`;
        }
        
        return '/assets/images/file-types/file.svg';
    }
    
    // Hiển thị loading overlay
    function showLoading() {
        console.log(1);
        
        document.getElementById('loading-overlay').classList.remove('!hidden');
    }
    
    // Ẩn loading overlay
    function hideLoading() {
        document.getElementById('loading-overlay').classList.add('!hidden');
    }

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
        // Disable button và hiển thị loading
        $('#send-message-btn').prop('disabled', true).html('<i class="ki-filled ki-loading animate-spin mr-1"></i> Đang gửi...');
        showLoading();
        
        let method = "post",
            url = "/consultation/add-log",
            params = null,
            data = $(_this).serialize() + '&consultation_id=' + consulationActive;

        // Thêm danh sách file đính kèm
        uploadedFiles.forEach(fileId => {
            data += `&attachment[]=${fileId}`;
        });
        
        if (phoneNumber) {
            data += '&phone=' + phoneNumber;
        }
            
        try {
            let res = await axiosTemplate(method, url, params, data);
            
            if (res.data.status === 200) {
                // Cập nhật UI
                $(`.consultation-tab[data-id=${consulationActive}]`).trigger('click');
                
                // Reset form
                $('#attachment-preview').html('');
                uploadedFiles = [];
                $('#add-log-form textarea').val('');
                $('input[name=follow_up_date]').val('');
                $('input[name=create_appointment]').prop('checked', false);
                $('.follow-up-container').hide();
                
                // Xử lý modal số điện thoại
                $('#confirm-phone-consultation-modal button[data-modal-dismiss]').trigger('click');
                
                // Cập nhật số điện thoại hiển thị nếu có
                if (phoneNumber != '') $('.details-phone-customer').text(phoneNumber);
                
                // Hiển thị thông báo thành công
                showAlert('success', res.data.message);
                
                // Nếu có tạo lịch hẹn, hiển thị thông báo
                if (res.data.data.appointment_id) {
                    setTimeout(() => {
                        showAlert('info', 'Đã tạo lịch hẹn thành công! <a href="/appointment/detail/' + res.data.data.customer_id + '" class="text-primary font-bold">Xem lịch hẹn</a>');
                    }, 1000);
                }
            } else {
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
            }
        } catch (error) {
            console.error(error);
            showAlert('error', 'Đã có lỗi xảy ra: ' + error.message);
        } finally {
            // Re-enable button
            $('#send-message-btn').prop('disabled', false).html('Đăng');
            hideLoading();
        }
    }

    async function postCreateConsultation(_this) {
        showLoading();
        
        let method = "post",
            url = "/consultation/create",
            params = null,
            data = $(_this).serialize() + "&customer_id={{$details['id']}}";
            
        try {
            let res = await axiosTemplate(method, url, params, data);
            
            if (res.data.status === 200) {
                showAlert('success', res.data.message);
                window.location.reload();
            } else {
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
            }
        } catch (error) {
            showAlert('error', 'Đã có lỗi xảy ra: ' + error.message);
        } finally {
            hideLoading();
        }
    }

    async function postUpdateNameConsultation(_this) {
        showLoading();
        
        let method = "post",
            url = "/consultation/update",
            params = null,
            data = $(_this).serialize() + "&id=" + consulationDelete;
            
        try {
            let res = await axiosTemplate(method, url, params, data);
            
            if (res.data.status === 200) {
                showAlert('success', res.data.message);
                window.location.reload();
            } else {
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
            }
        } catch (error) {
            showAlert('error', 'Đã có lỗi xảy ra: ' + error.message);
        } finally {
            hideLoading();
        }
    }

    async function postRemoveConsultation(id) {
        showLoading();
        
        let method = "post",
            url = "/consultation/remove",
            params = null,
            data = {
                id
            };
            
        try {
            let res = await axiosTemplate(method, url, params, data);
            
            if (res.data.status === 200) {
                showAlert('success', res.data.message);
                window.location.reload();
            } else {
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
            }
        } catch (error) {
            showAlert('error', 'Đã có lỗi xảy ra: ' + error.message);
        } finally {
            hideLoading();
        }
    }

    async function getConsultationLog(id) {
        // Hiển thị trạng thái loading
        $('#consoltation-logs-body').html(`
            <div class="flex items-center justify-center p-6">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        `);
        
        let method = "get",
            url = "/consultation/log",
            params = {
                id
            },
            data = null;
            
        try {
            let res = await axiosTemplate(method, url, params, data);
            
            if (res.data.status === 200) {
                $('#consoltation-logs-body').html(res.data.content);
                
                // Cập nhật trạng thái hiện tại
                const activeTab = $(`.consultation-tab[data-id=${id}]`);
                const actionCode = activeTab.data('action-code');
                updateConsultationStatus(actionCode);
                
                // Thêm chức năng tạo lịch hẹn từ tin nhắn
                setupMessageActions();
            } else {
                $('#consoltation-logs-body').html(`
                    <div class="flex flex-col items-center justify-center p-6">
                        <i class="ki-filled ki-cross-circle text-red-500 text-3xl mb-2"></i>
                        <p class="text-gray-700">Không thể tải dữ liệu. Vui lòng thử lại sau.</p>
                    </div>
                `);
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
            }
        } catch (error) {
            $('#consoltation-logs-body').html(`
                <div class="flex flex-col items-center justify-center p-6">
                    <i class="ki-filled ki-cross-circle text-red-500 text-3xl mb-2"></i>
                    <p class="text-gray-700">Đã xảy ra lỗi. Vui lòng thử lại sau.</p>
                </div>
            `);
            showAlert('error', 'Đã có lỗi xảy ra: ' + error.message);
        }
    }
    
    // Thiết lập các action khi hover vào message
    function setupMessageActions() {
        // Tìm tất cả phần tử message
        const messageContainers = document.querySelectorAll('.message-container');
        
        messageContainers.forEach(container => {
            // Tạo buttons container nếu chưa có
            if (!container.querySelector('.message-action-buttons')) {
                const message = container.querySelector('.text-sm:not(.text-gray-500)');
                const messageDate = container.querySelector('.text-xs.text-gray-600');
                
                if (message && messageDate) {
                    const dateText = messageDate.textContent.trim();
                    const messageText = message.textContent.trim();
                    
                    const buttonsContainer = document.createElement('div');
                    buttonsContainer.className = 'message-action-buttons gap-1';
                    
                    // Thêm nút tạo lịch hẹn
                    const createAppointmentBtn = document.createElement('button');
                    createAppointmentBtn.className = 'btn btn-xs btn-light create-appointment-from-log';
                    createAppointmentBtn.innerHTML = '<i class="ki-filled ki-calendar-add text-primary"></i>';
                    createAppointmentBtn.setAttribute('title', 'Tạo lịch hẹn từ tin nhắn này');
                    createAppointmentBtn.dataset.message = messageText;
                    createAppointmentBtn.dataset.date = dateText;
                    
                    buttonsContainer.appendChild(createAppointmentBtn);
                    container.appendChild(buttonsContainer);
                }
            }
        });
    }
</script>
@endpush