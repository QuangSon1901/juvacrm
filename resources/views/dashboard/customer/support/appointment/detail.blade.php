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
</style>
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch hẹn
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
            <button class="btn btn-primary" data-modal-toggle="#create-appointment-modal">
                <i class="ki-filled ki-plus-squared"></i>
                Tạo lịch hẹn
            </button>
        </div>
    </div>
</div>
<div class="container-fixed">
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
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tiêu đề lịch hẹn
                        </span>
                    </div>
                    <input class="input" name="name" type="text" placeholder="">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Mô tả cuộc hẹn
                        </span>
                    </div>
                    <input class="input" name="note" type="text" placeholder="">
                </div>
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
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Màu thẻ
                        </span>
                    </div>
                    <select name="color" class="select">
                        <option value="success" selected="">Xanh lục (success)</option>
                        <option value="warning" selected="">Vàng cam (warning)</option>
                        <option value="primary" selected="">Xanh dương (primary)</option>
                        <option value="gray" selected="">Xám nhạt (gray)</option>
                        <option value="danger" selected="">Đỏ rực (danger)</option>
                        <option value="neutral" selected="">Đen nhám (neutral)</option>
                    </select>
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
    const dateInput = $('input[name="date_active"]');
    const typeInput = $('input[name="type"]');
    const togglerEl = document.querySelectorAll('.toggle-badge');

    $(function() {
        flatpickrMake($("#create-appointment-modal input[name=start_time]"), 'datetime');
        flatpickrMake($("#create-appointment-modal input[name=end_time]"), 'datetime');

        $('.type-tab-btn').on('click', function() {
            typeInput.val($(this).attr('data-type-tab'));
            updateTitle(new Date(dateInput.val()));
        })

        $.each(togglerEl, (_, item) => {
            const toggle = KTToggle.getInstance(item);

            toggle.on('toggle', () => {
                $('.toggle-modal:not(.hidden)').addClass('hidden');
            });
        })

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
    });

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

    // Hàm tính số tuần trong năm
    const getWeekNumber = (date) => {
        const startDate = new Date(date.getFullYear(), 0, 1);
        const days = Math.floor((date - startDate) / (24 * 60 * 60 * 1000));
        return Math.ceil((days + startDate.getDay() + 1) / 7);
    };

    // Hàm cập nhật ngày trên tiêu đề
    const updateTitle = (newDate) => {
        const type = typeInput.val();
        const formattedTitle = formatDateTitle(newDate, type);
        $(".card-title").text(formattedTitle);
    };
</script>


@endpush