@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch làm việc cá nhân
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-calendar-add size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Lịch Part-time đã đăng ký
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $partTimeStats['total'] ?? 0 }} lịch
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-check size-6 shrink-0 text-success"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Lịch đã được duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $partTimeStats['approved'] ?? 0 }} lịch
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Lịch đang chờ duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $partTimeStats['pending'] ?? 0 }} lịch
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-time size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng giờ Part-time
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $partTimeStats['totalHours'] ?? 0 }} giờ
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lịch tháng hiện tại -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Lịch làm việc tháng {{ date('m/Y') }}
                </h3>
                <div class="flex flex-wrap gap-2">
                    <button class="btn btn-primary btn-sm" data-modal-toggle="#create-schedule-modal">
                        <i class="ki-filled ki-plus me-1"></i>
                        Đăng ký lịch Part-time
                    </button>
                </div>
            </div>
            <div class="card-body !p-5">
                <div id="calendar"></div>
            </div>
        </div>
        
        <!-- Danh sách lịch Part-time -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Lịch Part-time đã đăng ký
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <input class="input input-sm" type="text" id="month-filter" data-flatpickr="true" data-flatpickr-type="month" placeholder="Chọn tháng">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">STT</th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ngày</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Bắt đầu</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Kết thúc</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Tổng giờ</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ghi chú</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $index => $schedule)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ formatDateTime($schedule->schedule_date, 'd/m/Y') }}</td>
                                    <td>{{ formatDateTime($schedule->start_time, 'H:i') }}</td>
                                    <td>{{ formatDateTime($schedule->end_time, 'H:i') }}</td>
                                    <td>{{ number_format($schedule->total_hours, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            switch($schedule->status) {
                                                case 'pending':
                                                    $statusClass = 'warning';
                                                    $statusText = 'Chờ duyệt';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'success';
                                                    $statusText = 'Đã duyệt';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'danger';
                                                    $statusText = 'Đã từ chối';
                                                    break;
                                                default:
                                                    $statusClass = 'gray';
                                                    $statusText = $schedule->status;
                                            }
                                        @endphp
                                        
                                        <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>{{ $schedule->note ?: '-' }}</td>
                                    <td>
                                        <div class="menu" data-menu="true">
                                            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical"></i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                    @if($schedule->status == 'pending')
                                                    <div class="menu-item">
                                                        <button class="menu-link" onclick="cancelSchedule({{ $schedule->id }})">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-cross text-danger"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Hủy lịch
                                                            </span>
                                                        </button>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $schedules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đăng ký lịch Part-time -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-schedule-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Đăng ký lịch Part-time
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-schedule-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Ngày làm việc <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="text" name="schedule_date" data-flatpickr="true" placeholder="Chọn ngày làm việc" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Giờ bắt đầu <span class="text-red-500">*</span>
                        </label>
                        <input class="input" type="text" name="start_time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Chọn giờ bắt đầu" required>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Giờ kết thúc <span class="text-red-500">*</span>
                        </label>
                        <input class="input" type="text" name="end_time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Chọn giờ kết thúc" required>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Ghi chú
                    </label>
                    <textarea class="textarea" name="note" rows="3" placeholder="Nhập ghi chú (nếu có)"></textarea>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/vi.js"></script>
<script>
    $(function() {
        // Khởi tạo flatpickr cho các trường ngày và giờ
        flatpickrMake($("input[name='schedule_date']"), 'date');
        flatpickrMake($("input[name='start_time']"), 'time');
        flatpickrMake($("input[name='end_time']"), 'time');
        flatpickrMake($("#month-filter"), 'month');
        
        // Khởi tạo lịch fullcalendar
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: {!! json_encode($calendarEvents) !!},
            eventClick: function(info) {
                // Xử lý khi click vào sự kiện
                const eventId = info.event.id;
                const eventType = info.event.extendedProps.type;
                
                if (eventType === 'part-time') {
                    // Hiển thị thông tin chi tiết lịch part-time
                    showScheduleDetails(eventId);
                }
            }
        });
        calendar.render();
        
        // Xử lý form đăng ký lịch Part-time
        $("#create-schedule-form").on('submit', async function(e) {
            e.preventDefault();
            
            const scheduleDate = $(this).find('input[name="schedule_date"]').val();
            const startTime = $(this).find('input[name="start_time"]').val();
            const endTime = $(this).find('input[name="end_time"]').val();
            const note = $(this).find('textarea[name="note"]').val();
            
            // Kiểm tra giờ bắt đầu < giờ kết thúc
            const startDateTime = new Date(`2000-01-01 ${startTime}`);
            const endDateTime = new Date(`2000-01-01 ${endTime}`);
            
            if (startDateTime >= endDateTime) {
                showAlert('warning', 'Giờ bắt đầu phải nhỏ hơn giờ kết thúc');
                return;
            }
            
            try {
                const res = await axiosTemplate('post', '/account/schedule/create', null, {
                    schedule_date: scheduleDate,
                    start_time: startTime,
                    end_time: endTime,
                    note: note
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-schedule-modal')).hide();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi đăng ký lịch');
                console.error(error);
            }
        });
        
        // Xử lý khi thay đổi tháng
        $("#month-filter").on('change', function() {
            const selectedMonth = $(this).val();
            if (selectedMonth) {
                window.location.href = "{{ route('dashboard.profile.my-schedule') }}?month=" + selectedMonth;
            }
        });
    });
    
    // Hàm hủy lịch đăng ký
    async function cancelSchedule(id) {
        try {
            Notiflix.Confirm.show(
                'Hủy đăng ký lịch',
                'Bạn có chắc chắn muốn hủy lịch đăng ký này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/schedule/cancel', null, { id: id });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi hủy lịch');
            console.error(error);
        }
    }
    
    // Hàm hiển thị thông tin chi tiết lịch
    function showScheduleDetails(scheduleId) {
        // Triển khai logic hiển thị chi tiết lịch tại đây
    }
</script>
@endpush