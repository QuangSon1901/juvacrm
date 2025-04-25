@extends('dashboard.layouts.layout')
@section('dashboard_content')
<style>
    .bg-dashed {
        background-image: repeating-linear-gradient(90deg,
                currentColor 0,
                currentColor 4px,
                transparent 4px,
                transparent 8px);
    }
    .today-highlight {
        background-color: rgba(var(--primary), 0.1);
        border: 1px dashed var(--primary);
    }
</style>
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch hẹn
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
                        <input name="type" type="text" class="hidden" value="{{$type}}">
                        <button class="btn btn-icon btn-xs btn-light prev-date-btn">
                            <i class="ki-outline ki-left"></i>
                        </button>
                        <button class="btn btn-icon btn-xs btn-light next-date-btn">
                            <i class="ki-outline ki-right"></i>
                        </button>
                        <button class="btn btn-light btn-xs today-btn">
                            Hôm nay
                        </button>
                    </div>
                </div>
                <div>
                    <div class="flex gap-2" data-tabs="true">
                        <a data-type-tab="day" class="type-tab-btn btn btn-info btn-clear btn-xs {{$type == 'day' ? 'active' : ''}}" data-tab-toggle="true" href="#tab-by-day">
                            <i class="ki-outline ki-calendar-8"></i>
                            Theo ngày
                        </a>
                        <a data-type-tab="week" class="type-tab-btn btn btn-info btn-clear btn-xs {{$type == 'week' ? 'active' : ''}}" data-tab-toggle="true" href="#tab-by-week">
                            <i class="ki-outline ki-calendar-2"></i>
                            Theo tuần
                        </a>
                        <a data-type-tab="month" class="type-tab-btn btn btn-info btn-clear btn-xs {{$type == 'month' ? 'active' : ''}}" data-tab-toggle="true" href="#tab-by-month">
                            <i class="ki-outline ki-calendar"></i>
                            Theo tháng
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="tab-by-day" class="{{$type == 'day' ? '' : 'hidden'}}">
                    @include('dashboard.customer.support.appointment.calendar_day', ['appointments' => $appointments['day']])
                </div>
                <div id="tab-by-week" class="{{$type == 'week' ? '' : 'hidden'}}">
                    @include('dashboard.customer.support.appointment.calendar_week', ['appointments' => $appointments['week'], 'week_days' => $appointments['week_days']])
                </div>
                <div id="tab-by-month" class="{{$type == 'month' ? '' : 'hidden'}}">
                    @include('dashboard.customer.support.appointment.calendar_month', ['appointments' => $appointments['month'], 'currentDate' => $currentDate])
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
                            Tiêu đề lịch hẹn
                        </span>
                    </div>
                    <input class="input" name="name" type="text" placeholder="Nhập tiêu đề lịch hẹn">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khách hàng
                        </span>
                    </div>
                    <select name="customer_id" class="select">
                        <option value="" disabled selected>Chọn khách hàng</option>
                        @foreach($customers as $customer)
                        <option value="{{$customer->id}}">{{$customer->name}} - {{$customer->phone ?? 'Không có SĐT'}}</option>
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
                                Thời gian bắt đầu
                            </span>
                        </div>
                        <input class="input" name="start_time" type="text" placeholder="DD-MM-YYYY H:i:s">
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Thời gian kết thúc
                            </span>
                        </div>
                        <input class="input" name="end_time" type="text" placeholder="DD-MM-YYYY H:i:s">
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
                            Tiêu đề lịch hẹn
                        </span>
                    </div>
                    <input class="input" name="name" type="text" placeholder="Nhập tiêu đề lịch hẹn">
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
                                Thời gian bắt đầu
                            </span>
                        </div>
                        <input class="input" name="start_time" type="text" placeholder="DD-MM-YYYY H:i:s">
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Thời gian kết thúc
                            </span>
                        </div>
                        <input class="input" name="end_time" type="text" placeholder="DD-MM-YYYY H:i:s">
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
                <div class="flex justify-between">
                    <button type="button" id="delete-appointment-btn" class="btn btn-danger">
                        Huỷ lịch hẹn
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const dateInput = $('input[name="date_active"]');
    const typeInput = $('input[name="type"]');
    
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

        // Xử lý tab chuyển đổi
        $('.type-tab-btn').on('click', function() {
            typeInput.val($(this).attr('data-type-tab'));
            updateTitle(new Date(dateInput.val()));
        });

        // Xử lý khi nhấn nút "Prev"
        $('.prev-date-btn').click(function() {
            const currentDate = new Date(dateInput.val());
            const type = typeInput.val();

            if (type === 'day') {
                currentDate.setDate(currentDate.getDate() - 1);
            } else if (type === 'week') {
                currentDate.setDate(currentDate.getDate() - 7);
            } else if (type === 'month') {
                currentDate.setMonth(currentDate.getMonth() - 1);
            }

            const newDate = currentDate.toISOString().split('T')[0];
            window.location.href = location.pathname + '?type=' + type + '&datetime=' + newDate
        });

        // Xử lý khi nhấn nút "Next"
        $('.next-date-btn').click(function() {
            const currentDate = new Date(dateInput.val());
            const type = typeInput.val();

            if (type === 'day') {
                currentDate.setDate(currentDate.getDate() + 1);
            } else if (type === 'week') {
                currentDate.setDate(currentDate.getDate() + 7);
            } else if (type === 'month') {
                currentDate.setMonth(currentDate.getMonth() + 1);
            }

            const newDate = currentDate.toISOString().split('T')[0];
            window.location.href = location.pathname + '?type=' + type + '&datetime=' + newDate
        });

        // Xử lý khi nhấn nút "Hôm nay"
        $('.today-btn').click(function() {
            const type = typeInput.val();
            const today = new Date();
            const newDate = today.toISOString().split('T')[0];
            window.location.href = location.pathname + '?type=' + type + '&datetime=' + newDate
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
                'Huỷ lịch hẹn',
                'Bạn có chắc chắn muốn huỷ lịch hẹn này?',
                'Đúng',
                'Không',
                () => {
                    deleteAppointment(appointmentId);
                }
            );
        });
        
        // Highlight ngày hiện tại
        highlightToday();
        
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
            };
            
            populateEditForm(appointmentData);
        });
    });
    
    // Tạo lịch hẹn mới
    async function createAppointment(form) {
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
        }
    }
    
    // Cập nhật lịch hẹn
    async function updateAppointment(form) {
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
    
    // Điền thông tin vào form chỉnh sửa
    function populateEditForm(data) {
        const form = $('#edit-appointment-form');
        
        form.find('input[name=id]').val(data.id);
        form.find('input[name=name]').val(data.name);
        form.find('textarea[name=note]').val(data.note);
        form.find('input[name=start_time]').val(data.start_time);
        form.find('input[name=end_time]').val(data.end_time);
        
        // Chọn màu
        form.find(`input[name=color][value=${data.color}]`).prop('checked', true);
        form.find('.badge-selector span').removeClass('border-2 border-dashed');
        form.find(`input[name=color][value=${data.color}]`).closest('.badge-selector').find('span').addClass('border-2 border-dashed');
        
        // Hiển thị modal
        $('#edit-appointment-modal').removeClass('hidden');
    }

    // Hàm tính số tuần trong năm
    const getWeekNumber = (date) => {
        const startDate = new Date(date.getFullYear(), 0, 1);
        const days = Math.floor((date - startDate) / (24 * 60 * 60 * 1000));
        return Math.ceil((days + startDate.getDay() + 1) / 7);
    };

    // Highlight ngày hiện tại
    function highlightToday() {
        const today = new Date().toISOString().split('T')[0];
        
        // Highlight trong view tháng
        $(`[data-date="${today}"]`).addClass('today-highlight');
        
        // Highlight trong view tuần
        const currentDayOfWeek = new Date().getDay();
        $(`.week-day-cell:eq(${currentDayOfWeek === 0 ? 6 : currentDayOfWeek - 1})`).addClass('today-highlight');
    }

    // Hàm cập nhật ngày trên tiêu đề
    const updateTitle = (newDate) => {
        const type = typeInput.val();
        const formattedTitle = formatDateTitle(newDate, type);
        $(".card-title").text(formattedTitle);
    };
    
    const formatDateTitle = (date, type) => {
        const days = ["Chủ nhật", "Thứ hai", "Thứ ba", "Thứ tư", "Thứ năm", "Thứ sáu", "Thứ bảy"];
        const months = ["tháng 01", "tháng 02", "tháng 03", "tháng 04", "tháng 05", "tháng 06", "tháng 07", "tháng 08", "tháng 09", "tháng 10", "tháng 11", "tháng 12"];

        const dayName = days[date.getDay()];
        const day = date.getDate();
        const monthName = months[date.getMonth()];
        const year = date.getFullYear();

        if (type === 'day') {
            return `${dayName}, ${day} ${monthName}, ${year}`;
        } else if (type === 'week') {
            const weekNumber = getWeekNumber(date);
            return `Tuần ${weekNumber}, ${monthName}, ${year}`;
        } else if (type === 'month') {
            return `${monthName}, ${year}`;
        }
    };
</script>
@endpush