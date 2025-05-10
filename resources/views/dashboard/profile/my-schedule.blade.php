@extends('dashboard.layouts.layout')
@section('dashboard_content')
@php
use Carbon\Carbon;
$now = Carbon::now();
@endphp
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch làm việc của tôi
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-primary btn-sm" data-modal-toggle="#create-schedule-modal">
                <i class="ki-filled ki-plus me-1"></i>
                Đăng ký lịch làm việc
            </button>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Thống kê -->
        <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-calendar size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng lịch đăng ký
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
                            Lịch đã duyệt
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
                            Đang chờ duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $partTimeStats['pending'] ?? 0 }} lịch
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-time size-6 shrink-0 text-info"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng giờ làm việc
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ number_format($partTimeStats['totalHours'] ?? 0, 2) }} giờ
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lịch làm việc -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Lịch làm việc tháng {{ date('m/Y') }}
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <input class="input input-sm" type="text" id="month-filter" data-flatpickr="true" data-flatpickr-type="month" placeholder="Chọn tháng" value="{{ date('Y-m') }}">
                    </div>
                </div>
            </div>
            <div class="card-body !p-5">
                <div id="calendar"></div>
            </div>
        </div>
        
        <!-- Danh sách lịch làm việc -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách lịch làm việc
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase">
                                <th class="min-w-100px">Ngày</th>
                                <th class="min-w-100px">Thời gian bắt đầu</th>
                                <th class="min-w-100px">Thời gian kết thúc</th>
                                <th class="min-w-100px">Tổng giờ</th>
                                <th class="min-w-100px">Trạng thái</th>
                                <th class="min-w-100px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                            <tr>
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
                                                $statusText = 'Từ chối';
                                                break;
                                            case 'cancel_requested':
                                                $statusClass = 'info';
                                                $statusText = 'Yêu cầu hủy';
                                                break;
                                            default:
                                                $statusClass = 'gray';
                                                $statusText = ucfirst($schedule->status);
                                        }
                                    @endphp
                                    
                                    <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    @if($schedule->status == 'pending')
                                    <button class="btn btn-xs btn-danger" onclick="cancelSchedule({{ $schedule->id }})">
                                        <i class="ki-filled ki-trash me-1"></i>Hủy
                                    </button>
                                    @elseif($schedule->status == 'approved' && Carbon::parse($schedule->schedule_date)->gt(Carbon::today()))
                                    <button class="btn btn-xs btn-warning" onclick="requestCancelSchedule({{ $schedule->id }})">
                                        <i class="ki-filled ki-cross me-1"></i>Yêu cầu hủy
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-5">
                    {{ $schedules->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal đăng ký lịch làm việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-schedule-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Đăng ký lịch làm việc
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

<!-- Modal yêu cầu hủy lịch -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="request-cancel-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Yêu cầu hủy lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="request-cancel-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="cancel-schedule-id">
                
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Lý do hủy <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="reason" rows="3" placeholder="Nhập lý do hủy lịch" required></textarea>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-danger justify-center">
                        Yêu cầu hủy
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
        
        // Khởi tạo flatpickr cho bộ lọc tháng
        $("#month-filter").flatpickr({
            dateFormat: 'Y-m',
            plugins: [],
            locale: 'vi',
            prevArrow: '<i class="ki-outline ki-left"></i>',
            nextArrow: '<i class="ki-outline ki-right"></i>',
            onChange: function(selectedDates, dateStr) {
                // Cập nhật lịch dựa trên tháng đã chọn
                calendar.gotoDate(new Date(dateStr + '-01'));
            }
        });
        
        // Khởi tạo lịch fullcalendar
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: {!! json_encode($calendarEvents) !!},
            eventClick: function(info) {
                // Xử lý khi click vào sự kiện
                const eventId = info.event.id;
                showScheduleDetails(eventId);
            }
        });
        calendar.render();
        
        // Xử lý form đăng ký lịch làm việc
        $('#create-schedule-form').on('submit', async function(e) {
            e.preventDefault();
            
            const scheduleDate = $(this).find('input[name="schedule_date"]').val();
            const startTime = $(this).find('input[name="start_time"]').val();
            const endTime = $(this).find('input[name="end_time"]').val();
            const note = $(this).find('textarea[name="note"]').val();
            
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
                showAlert('error', 'Đã xảy ra lỗi khi đăng ký lịch làm việc');
                console.error(error);
            }
        });
        
        // Xử lý form yêu cầu hủy lịch
        $('#request-cancel-form').on('submit', async function(e) {
            e.preventDefault();
            
            const id = $('#cancel-schedule-id').val();
            const reason = $(this).find('textarea[name="reason"]').val();
            
            try {
                const res = await axiosTemplate('post', '/account/schedule/request-cancel', null, {
                    id: id,
                    reason: reason
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#request-cancel-modal')).hide();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi yêu cầu hủy lịch làm việc');
                console.error(error);
            }
        });
    });
    
    // Hàm hủy lịch chưa được duyệt
    function cancelSchedule(id) {
        try {
            Notiflix.Confirm.show(
                'Hủy lịch làm việc',
                'Bạn có chắc chắn muốn hủy lịch làm việc này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/schedule/cancel', null, {
                        id: id
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi hủy lịch làm việc');
            console.error(error);
        }
    }
    
    // Hàm yêu cầu hủy lịch đã được duyệt
    function requestCancelSchedule(id) {
        $('#cancel-schedule-id').val(id);
        KTModal.getInstance(document.querySelector('#request-cancel-modal')).show();
    }
    
    // Hàm xem chi tiết lịch
    function showScheduleDetails(id) {
        try {
            window.location.href = `/account/schedule/${id}/detail`;
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi xem chi tiết lịch');
            console.error(error);
        }
    }
</script>
@endpush