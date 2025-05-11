@extends('dashboard.layouts.layout')
@section('dashboard_content')
<style>
    .calendar-day {
        min-height: 120px;
        position: relative;
    }
    .today-highlight {
        background-color: rgba(var(--primary), 0.1);
        border: 1px dashed var(--primary);
    }
    .appointment-item {
        margin-bottom: 2px;
        transition: all 0.2s;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .appointment-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch hẹn khách hàng
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
                            <span class="text-sm text-gray-500">Lịch hẹn</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-primary" data-modal-toggle="#create-appointment-modal">
                <i class="ki-filled ki-plus-squared"></i>
                Tạo lịch hẹn
            </button>
        </div>
    </div>
</div>
<div class="container-fixed">
    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-primary-100 p-3 mr-4">
                        <i class="ki-filled ki-calendar-add text-primary text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Tổng số lịch hẹn sắp tới</p>
                        <h3 class="text-2xl font-bold">{{ $statistics['upcoming'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-success-100 p-3 mr-4">
                        <i class="ki-filled ki-calendar-tick text-success text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Lịch hẹn hôm nay</p>
                        <h3 class="text-2xl font-bold">{{ $statistics['today'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-gray-100 p-3 mr-4">
                        <i class="ki-filled ki-calendar-8 text-gray-700 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Lịch hẹn đã qua</p>
                        <h3 class="text-2xl font-bold">{{ $statistics['past'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <div class="flex flex-wrap items-center gap-4">
                    <h3 class="card-title capitalize">
                        {{$currentDateFormat}}
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <input name="date_active" type="text" class="hidden" value="{{$currentDate}}">
                        <input name="customer_id" type="text" class="hidden" value="{{$selectedCustomerId}}">
                        <button class="btn btn-icon btn-xs btn-light prev-month-btn">
                            <i class="ki-outline ki-left"></i>
                        </button>
                        <button class="btn btn-icon btn-xs btn-light next-month-btn">
                            <i class="ki-outline ki-right"></i>
                        </button>
                        <button class="btn btn-light btn-xs today-btn">
                            Tháng hiện tại
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-700">Lọc theo khách hàng:</label>
                    <select id="customer-filter" class="select select-sm min-w-[200px]">
                        <option value="">Tất cả khách hàng</option>
                        @foreach($customers as $customer)
                        <option value="{{$customer->id}}" {{$selectedCustomerId == $customer->id ? 'selected' : ''}}>
                            {{$customer->name}} - {{$customer->phone ?? 'Không có SĐT'}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                <!-- Lịch theo tháng -->
                <div class="calendar-month">
                    <div class="grid grid-cols-7 gap-2 p-4">
                        @foreach (['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'] as $day)
                        <div class="text-center text-sm font-semibold text-gray-900">{{ $day }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 gap-2 p-4">
                        @php
                        $date = \Carbon\Carbon::parse($currentDate);
                        $currentMonth = $date->format('m');
                        $currentYear = $date->format('Y');
                        $daysInMonth = $date->daysInMonth;
                        $firstDayOfMonth = $date->startOfMonth()->dayOfWeekIso;
                        $today = \Carbon\Carbon::now()->format('Y-m-d');
                        @endphp

                        <!-- Thêm các ô trống cho ngày đầu tháng -->
                        @foreach (range(1, $firstDayOfMonth - 1) as $emptyDay)
                        <div class="border bg-gray-100 calendar-day"></div>
                        @endforeach

                        <!-- Hiển thị các ngày trong tháng -->
                        @foreach (range(1, $daysInMonth) as $day)
                        @php
                        $currentDate = "$currentYear-$currentMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $isToday = $currentDate == $today;
                        $dayAppointments = $appointments->filter(function($appointment) use ($currentDate) {
                            return date('Y-m-d', strtotime($appointment->start_time)) === $currentDate;
                        });
                        @endphp
                        <div class="border relative bg-white calendar-day p-2 {{$isToday ? 'today-highlight' : ''}}" data-date="{{$currentDate}}">
                            <div class="absolute top-1 right-2 text-xs {{$isToday ? 'text-primary font-bold' : 'text-gray-500'}}">{{ $day }}</div>
                            
                            <!-- Thêm nút + để nhanh chóng tạo lịch hẹn cho ngày này -->
                            <button class="absolute top-1 left-1 quick-add-btn text-primary text-xs hidden" 
                                    data-date="{{$currentDate}}" title="Thêm lịch hẹn">
                                <i class="ki-filled ki-plus-circle"></i>
                            </button>
                            
                            <div class="mt-5 space-y-1 overflow-y-auto max-h-[80px]">
                                @foreach ($dayAppointments as $appointment)
                                <div data-toggle="#reminder-{{$appointment->id}}" 
                                     data-toggle-class="hidden" 
                                     class="toggle-badge appointment-item badge badge-outline badge-{{ $appointment->color }} w-full flex-col items-start justify-start p-1 {{$appointment->is_completed ? 'opacity-60' : ''}}">
                                    <p class="text-xs font-semibold truncate w-full">
                                        @if($appointment->customer)
                                            <span class="font-bold">{{$appointment->customer->name}}</span>:
                                        @endif
                                        {{ $appointment->name }}
                                    </p>
                                    <span class="text-xs"><i class="ki-filled ki-time"></i> {{date('H:i', strtotime($appointment->start_time))}}</span>
                                    
                                    @if($appointment->is_completed)
                                    <span class="badge badge-xs badge-success">Hoàn thành</span>
                                    @endif
                                </div>
                                <div class="hidden absolute top-[20px] left-0 z-10 lg:w-max toggle-modal" id="reminder-{{$appointment->id}}">
                                    <div class="card p-4 min-w-[300px]">
                                        <div class="absolute top-2 right-2" onclick="$(this).closest('.toggle-modal').addClass('hidden')">
                                            <button class="btn btn-xs btn-icon btn-light">
                                                <i class="ki-outline ki-cross"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs text-gray-600">{{date('d/m/Y', strtotime($appointment->start_time))}}</span>
                                            @if($appointment->is_completed)
                                                <span class="badge badge-xs badge-success">Hoàn thành</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-sm font-semibold mb-2">{{$appointment->name}}</p>
                                        
                                        @if($appointment->customer)
                                        <div class="mb-2 flex items-center">
                                            <i class="ki-filled ki-profile-user mr-1 text-primary"></i>
                                            <a href="/customer/{{$appointment->customer->id}}" class="text-xs text-primary">
                                                {{$appointment->customer->name}}
                                            </a>
                                        </div>
                                        @endif
                                        
                                        <span class="text-xs mb-1 text-gray-900">
                                            <i class="ki-filled ki-time"></i> 
                                            {{date('H:i', strtotime($appointment->start_time))}} - {{date('H:i', strtotime($appointment->end_time))}}
                                        </span>
                                        
                                        @if($appointment->note)
                                        <p class="text-xs text-gray-900 mt-2 mb-3 bg-gray-50 p-2 rounded">{{$appointment->note}}</p>
                                        @endif
                                        
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            <button class="btn btn-sm btn-primary edit-appointment-btn"
                                                   data-id="{{$appointment->id}}"
                                                   data-name="{{$appointment->name}}"
                                                   data-note="{{$appointment->note}}"
                                                   data-start="{{date('Y-m-d H:i:s', strtotime($appointment->start_time))}}"
                                                   data-end="{{date('Y-m-d H:i:s', strtotime($appointment->end_time))}}"
                                                   data-color="{{$appointment->color}}"
                                                   data-customer-id="{{$appointment->customer_id}}"
                                                   data-is-completed="{{$appointment->is_completed}}"
                                                   data-modal-toggle="#edit-appointment-modal">
                                                <i class="ki-filled ki-pencil"></i> Chỉnh sửa
                                            </button>
                                            
                                            @if(!$appointment->is_completed)
                                            <button class="btn btn-sm btn-success complete-appointment-btn" data-id="{{$appointment->id}}">
                                                <i class="ki-filled ki-check"></i> Hoàn thành
                                            </button>
                                            @endif
                                            
                                            <button class="btn btn-sm btn-danger delete-appointment-btn" data-id="{{$appointment->id}}">
                                                <i class="ki-filled ki-trash"></i> Xóa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo lịch hẹn mới -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-appointment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Tạo lịch hẹn mới
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form id="appointment-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tiêu đề lịch hẹn <span class="text-danger">*</span>
                        </span>
                    </div>
                    <input class="input" name="name" type="text" placeholder="Nhập tiêu đề lịch hẹn" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khách hàng <span class="text-danger">*</span>
                        </span>
                    </div>
                    <select name="customer_id" class="select" required>
                        <option value="" disabled selected>Chọn khách hàng</option>
                        @foreach($customers as $customer)
                        <option value="{{$customer->id}}" {{$selectedCustomerId == $customer->id ? 'selected' : ''}}>
                            {{$customer->name}} - {{$customer->phone ?? 'Không có SĐT'}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Mô tả cuộc hẹn
                        </span>
                    </div>
                    <textarea class="textarea" name="note" rows="3" placeholder="Mô tả chi tiết về cuộc hẹn"></textarea>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Thời gian bắt đầu <span class="text-danger">*</span>
                            </span>
                        </div>
                        <input class="input" name="start_time" type="text" placeholder="Chọn ngày giờ bắt đầu" required>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Thời gian kết thúc <span class="text-danger">*</span>
                            </span>
                        </div>
                        <input class="input" name="end_time" type="text" placeholder="Chọn ngày giờ kết thúc" required>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Màu thẻ
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2 color-selector">
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="success" class="hidden">
                            <span class="badge badge-lg badge-success">Xanh lục</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="warning" class="hidden">
                            <span class="badge badge-lg badge-warning">Vàng cam</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="primary" class="hidden" checked>
                            <span class="badge badge-lg badge-primary">Xanh dương</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="gray" class="hidden">
                            <span class="badge badge-lg badge-gray">Xám nhạt</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="danger" class="hidden">
                            <span class="badge badge-lg badge-danger">Đỏ rực</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="neutral" class="hidden">
                            <span class="badge badge-lg badge-neutral">Đen nhám</span>
                        </label>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Tạo lịch hẹn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal chỉnh sửa lịch hẹn -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-appointment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chỉnh sửa lịch hẹn
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-appointment-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" value="">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tiêu đề lịch hẹn <span class="text-danger">*</span>
                        </span>
                    </div>
                    <input class="input" name="name" type="text" placeholder="Nhập tiêu đề lịch hẹn" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khách hàng <span class="text-danger">*</span>
                        </span>
                    </div>
                    <select name="customer_id" class="select" required>
                        <option value="" disabled>Chọn khách hàng</option>
                        @foreach($customers as $customer)
                        <option value="{{$customer->id}}">
                            {{$customer->name}} - {{$customer->phone ?? 'Không có SĐT'}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Mô tả cuộc hẹn
                        </span>
                    </div>
                    <textarea class="textarea" name="note" rows="3" placeholder="Mô tả chi tiết về cuộc hẹn"></textarea>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Thời gian bắt đầu <span class="text-danger">*</span>
                            </span>
                        </div>
                        <input class="input" name="start_time" type="text" placeholder="Chọn ngày giờ bắt đầu" required>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Thời gian kết thúc <span class="text-danger">*</span>
                            </span>
                        </div>
                        <input class="input" name="end_time" type="text" placeholder="Chọn ngày giờ kết thúc" required>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Màu thẻ
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2 color-selector">
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="success" class="hidden">
                            <span class="badge badge-lg badge-success">Xanh lục</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="warning" class="hidden">
                            <span class="badge badge-lg badge-warning">Vàng cam</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="primary" class="hidden">
                            <span class="badge badge-lg badge-primary">Xanh dương</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="gray" class="hidden">
                            <span class="badge badge-lg badge-gray">Xám nhạt</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="danger" class="hidden">
                            <span class="badge badge-lg badge-danger">Đỏ rực</span>
                        </label>
                        <label class="badge-selector cursor-pointer">
                            <input type="radio" name="color" value="neutral" class="hidden">
                            <span class="badge badge-lg badge-neutral">Đen nhám</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <label class="switch switch-sm">
                        <span class="switch-label">Đánh dấu hoàn thành</span>
                        <input name="is_completed" type="checkbox" value="1">
                    </label>
                </div>
                <div class="flex justify-between">
                    <button type="button" id="delete-appointment-btn" class="btn btn-danger">
                        <i class="ki-filled ki-trash"></i> Xóa
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-filled ki-disk"></i> Cập nhật
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
        // Khởi tạo datetime picker
        flatpickrMake($("#create-appointment-modal input[name=start_time]"), 'datetime');
        flatpickrMake($("#create-appointment-modal input[name=end_time]"), 'datetime');
        flatpickrMake($("#edit-appointment-modal input[name=start_time]"), 'datetime');
        flatpickrMake($("#edit-appointment-modal input[name=end_time]"), 'datetime');
        
        // Style cho color selector
        $('.badge-selector').on('click', function() {
            $(this).closest('.color-selector').find('.badge-selector span').removeClass('border-2 border-dashed');
            $(this).find('span').addClass('border-2 border-dashed');
        });
        
        // Mặc định chọn màu primary
        $('.badge-selector input[value="primary"]').closest('.badge-selector').trigger('click');

        // Xử lý khi thay đổi bộ lọc khách hàng
        $('#customer-filter').on('change', function() {
            const customerId = $(this).val();
            const currentDate = $('input[name="date_active"]').val();
            
            window.location.href = `/appointment/detail?date=${currentDate}&customer_id=${customerId}`;
        });

        // Xử lý khi nhấn nút "Prev Month"
        $('.prev-month-btn').click(function() {
            const currentDate = new Date($('input[name="date_active"]').val());
            const customerId = $('input[name="customer_id"]').val();
            
            // Giảm 1 tháng
            currentDate.setMonth(currentDate.getMonth() - 1);
            
            const newDate = currentDate.toISOString().split('T')[0];
            window.location.href = `/appointment/detail?date=${newDate}&customer_id=${customerId}`;
        });

        // Xử lý khi nhấn nút "Next Month"
        $('.next-month-btn').click(function() {
            const currentDate = new Date($('input[name="date_active"]').val());
            const customerId = $('input[name="customer_id"]').val();
            
            // Tăng 1 tháng
            currentDate.setMonth(currentDate.getMonth() + 1);
            
            const newDate = currentDate.toISOString().split('T')[0];
            window.location.href = `/appointment/detail?date=${newDate}&customer_id=${customerId}`;
        });

        // Xử lý khi nhấn nút "Tháng hiện tại"
        $('.today-btn').click(function() {
            const today = new Date();
            const customerId = $('input[name="customer_id"]').val();
            
            const newDate = today.toISOString().split('T')[0];
            window.location.href = `/appointment/detail?date=${newDate}&customer_id=${customerId}`;
        });
        
        // Hiện nút thêm nhanh khi hover vào ô ngày
        $('.calendar-day').hover(function() {
            $(this).find('.quick-add-btn').removeClass('hidden');
        }, function() {
            $(this).find('.quick-add-btn').addClass('hidden');
        });
        
        // Xử lý nút thêm nhanh
        $(document).on('click', '.quick-add-btn', function() {
            const selectedDate = $(this).data('date');
            
            // Tự động đặt ngày vào form
            const startDate = new Date(selectedDate);
            startDate.setHours(9, 0, 0); // Mặc định 9:00 AM
            
            const endDate = new Date(selectedDate);
            endDate.setHours(10, 0, 0); // Mặc định kết thúc sau 1 giờ
            
            // Format datetime cho form
            const startDateTime = startDate.toISOString().slice(0, 16).replace('T', ' ');
            const endDateTime = endDate.toISOString().slice(0, 16).replace('T', ' ');
            
            // Điền vào form và hiển thị modal
            $('#create-appointment-modal input[name=start_time]').val(startDateTime);
            $('#create-appointment-modal input[name=end_time]').val(endDateTime);
            
            // Hiển thị modal
            $('#create-appointment-modal').removeClass('hidden');
        });
        
        // Xử lý form tạo lịch hẹn
        $('#appointment-form').on('submit', function(e) {
            e.preventDefault();
            createAppointment($(this));
        });
        
        // Xử lý form chỉnh sửa lịch hẹn
        $('#edit-appointment-form').on('submit', function(e) {
            e.preventDefault();
            updateAppointment($(this));
        });
        
        // Xử lý nút xóa lịch hẹn
        $('#delete-appointment-btn').on('click', function() {
            const appointmentId = $('#edit-appointment-form input[name=id]').val();
            
            Notiflix.Confirm.show(
                'Xóa lịch hẹn',
                'Bạn có chắc chắn muốn xóa lịch hẹn này?',
                'Đúng',
                'Không',
                () => {
                    deleteAppointment(appointmentId);
                }
            );
        });
        
        // Xử lý nút đánh dấu hoàn thành
        $(document).on('click', '.complete-appointment-btn', function() {
            const appointmentId = $(this).data('id');
            
            Notiflix.Confirm.show(
                'Hoàn thành lịch hẹn',
                'Bạn có chắc chắn muốn đánh dấu lịch hẹn này là đã hoàn thành?',
                'Đúng',
                'Không',
                () => {
                    completeAppointment(appointmentId);
                }
            );
        });
        
        // Xử lý nút xóa trực tiếp
        $(document).on('click', '.delete-appointment-btn', function() {
            const appointmentId = $(this).data('id');
            
            Notiflix.Confirm.show(
                'Xóa lịch hẹn',
                'Bạn có chắc chắn muốn xóa lịch hẹn này?',
                'Đúng',
                'Không',
                () => {
                    deleteAppointment(appointmentId);
                }
            );
        });
        
        // Xử lý khi click vào lịch hẹn để chỉnh sửa
        $(document).on('click', '.edit-appointment-btn', function() {
            const appointmentId = $(this).data('id');
            const appointmentData = {
                id: appointmentId,
                name: $(this).data('name'),
                note: $(this).data('note'),
                start_time: $(this).data('start'),
                end_time: $(this).data('end'),
                color: $(this).data('color'),
                customer_id: $(this).data('customer-id'),
                is_completed: $(this).data('is-completed')
            };
            
            populateEditForm(appointmentData);
        });
    });
    
    // Tạo lịch hẹn mới
    async function createAppointment(form) {
        // Hiển thị loading
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="ki-filled ki-loading animate-spin mr-1"></i> Đang xử lý...').prop('disabled', true);
        
        const formData = form.serialize();
        
        try {
            const response = await axiosTemplate('post', '/appointment/create', null, formData);
            
            if (response.data.status === 200) {
                showAlert('success', response.data.message);
                $('button[data-modal-dismiss=true]').click();
                
                // Reload trang sau khi tạo thành công
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('warning', response.data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            showAlert('error', 'Có lỗi xảy ra: ' + error.message);
        } finally {
            // Khôi phục trạng thái nút submit
            submitBtn.html(originalText).prop('disabled', false);
        }
    }
    
    // Cập nhật lịch hẹn
    async function updateAppointment(form) {
        // Hiển thị loading
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="ki-filled ki-loading animate-spin mr-1"></i> Đang xử lý...').prop('disabled', true);
        
        const formData = form.serialize();
        
        try {
            const response = await axiosTemplate('post', '/appointment/update', null, formData);
            
            if (response.data.status === 200) {
                showAlert('success', response.data.message);
                $('button[data-modal-dismiss=true]').click();
                
                // Reload trang sau khi cập nhật thành công
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('warning', response.data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            showAlert('error', 'Có lỗi xảy ra: ' + error.message);
        } finally {
            // Khôi phục trạng thái nút submit
            submitBtn.html(originalText).prop('disabled', false);
        }
    }
    
    // Xóa lịch hẹn
    async function deleteAppointment(id) {
        try {
            const response = await axiosTemplate('post', '/appointment/delete', null, { id });
            
            if (response.data.status === 200) {
                showAlert('success', response.data.message);
                $('button[data-modal-dismiss=true]').click();
                
                // Reload trang sau khi xóa thành công
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('warning', response.data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            showAlert('error', 'Có lỗi xảy ra: ' + error.message);
        }
    }
    
    // Đánh dấu hoàn thành lịch hẹn
    async function completeAppointment(id) {
        try {
            const response = await axiosTemplate('post', '/appointment/complete', null, { id });
            
            if (response.data.status === 200) {
                showAlert('success', response.data.message || 'Đã đánh dấu hoàn thành lịch hẹn');
                
                // Reload trang sau khi cập nhật thành công
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('warning', response.data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            showAlert('error', 'Có lỗi xảy ra: ' + error.message);
        }
    }
    
    // Điền thông tin vào form chỉnh sửa
    function populateEditForm(data) {
        const form = $('#edit-appointment-form');
        
        form.find('input[name=id]').val(data.id);
        form.find('input[name=name]').val(data.name);
        form.find('textarea[name=note]').val(data.note);
        form.find('input[name=start_time]').val(data.start_time);
        form.find('input[name=end_time]').val(data.end_time);
        form.find('select[name=customer_id]').val(data.customer_id);
        
        // Đánh dấu hoàn thành
        form.find('input[name=is_completed]').prop('checked', data.is_completed == 1);
        
        // Chọn màu
        form.find(`input[name=color][value=${data.color}]`).prop('checked', true);
        form.find('.badge-selector span').removeClass('border-2 border-dashed');
        form.find(`input[name=color][value=${data.color}]`).closest('.badge-selector').find('span').addClass('border-2 border-dashed');
        
        // Hiển thị modal
        $('#edit-appointment-modal').removeClass('hidden');
    }
</script>
@endpush