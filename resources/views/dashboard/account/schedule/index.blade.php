@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý lịch làm việc
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
        <!-- Bảng điều khiển thống kê -->
        <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-calendar-tick size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng lịch đăng ký
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['totalSchedules'] ?? 0 }} lịch
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
                            {{ $stats['approvedSchedules'] ?? 0 }} lịch
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Lịch chờ duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['pendingSchedules'] ?? 0 }} lịch
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-cross-square size-6 shrink-0 text-danger"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Lịch từ chối
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['rejectedSchedules'] ?? 0 }} lịch
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lịch làm việc -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Lịch làm việc
                </h3>
                <div class="flex flex-wrap gap-2">
                    <button class="btn btn-primary btn-sm" data-modal-toggle="#create-schedule-modal">
                        <i class="ki-filled ki-plus me-1"></i>
                        Tạo lịch làm việc
                    </button>
                </div>
            </div>
            <div class="card-body !p-5">
                <div id="calendar"></div>
            </div>
        </div>
        
        <!-- Danh sách lịch đăng ký -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách lịch đăng ký
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <select class="select select-sm" id="user-filter" data-filter="user_id">
                            <option value="">Tất cả nhân viên</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="relative">
                        <select class="select select-sm" id="status-filter" data-filter="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="rejected">Từ chối</option>
                        </select>
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-from-filter" data-filter="date_from" data-flatpickr="true" placeholder="Từ ngày">
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-to-filter" data-filter="date_to" data-flatpickr="true" placeholder="Đến ngày">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="schedule-table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">STT</th>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Nhân viên</span>
                                        </span>
                                    </th>
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
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/account/schedule/data'])
                        </table>
                    </div>
                    <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                        <div class="flex items-center gap-2 order-2 md:order-1">
                            Hiển thị {{TABLE_PERPAGE_NUM}} mỗi trang
                        </div>
                        <div class="flex items-center gap-4 order-1 md:order-2">
                            <p><span class="sorterlow"></span> - <span class="sorterhigh"></span> trong <span class="sorterrecords"></span></p>
                            <div class="pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo lịch làm việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-schedule-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Tạo lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-schedule-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Nhân viên <span class="text-red-500">*</span>
                    </label>
                    <select class="select" name="user_id" required>
                        <option value="">Chọn nhân viên</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
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
                        Tạo lịch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal chi tiết lịch làm việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="schedule-detail-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chi tiết lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="schedule-detail-content" class="grid gap-5 px-0 py-5">
                <!-- Nội dung chi tiết sẽ được load bằng AJAX -->
            </div>
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
        flatpickrMake($("#date-from-filter"), 'date');
        flatpickrMake($("#date-to-filter"), 'date');
        
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
                showScheduleDetails(eventId);
            }
        });
        calendar.render();
        
        // Xử lý sự kiện khi thay đổi bộ lọc
        $('[data-filter]').on('change', function() {
            callAjaxDataTable($('.updater'));
        });
        
        // Xử lý form tạo lịch làm việc
        $('#create-schedule-form').on('submit', async function(e) {
            e.preventDefault();
            
            const userId = $(this).find('select[name="user_id"]').val();
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
                    user_id: userId,
                    schedule_date: scheduleDate,
                    start_time: startTime,
                    end_time: endTime,
                    note: note
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-schedule-modal')).hide();
                    
                    // Thêm sự kiện mới vào lịch
                    if (res.data.schedule) {
                        calendar.addEvent({
                            id: res.data.schedule.id,
                            title: res.data.schedule.user_name,
                            start: `${res.data.schedule.schedule_date}T${res.data.schedule.start_time}`,
                            end: `${res.data.schedule.schedule_date}T${res.data.schedule.end_time}`,
                            color: '#3788d8',
                            extendedProps: {
                                status: 'pending'
                            }
                        });
                    }
                    
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi tạo lịch làm việc');
                console.error(error);
            }
        });
    });
    
    // Hàm xem chi tiết lịch
    function showScheduleDetails(scheduleId) {
        try {
            // Hiển thị modal
            KTModal.getInstance(document.querySelector('#schedule-detail-modal')).show();
            
            // Hiển thị loading
            $('#schedule-detail-content').html(`
                <div class="flex justify-center items-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);
            
            // Gọi API lấy chi tiết
            axiosTemplate('get', `/account/schedule/${scheduleId}/detail`, null, null)
                .then(res => {
                    if (res.data.status === 200) {
                        $('#schedule-detail-content').html(res.data.content);
                    } else {
                        $('#schedule-detail-content').html(`
                            <div class="text-center text-danger">
                                ${res.data.message || 'Không thể tải thông tin chi tiết'}
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    $('#schedule-detail-content').html(`
                        <div class="text-center text-danger">
                            Đã xảy ra lỗi khi tải thông tin
                        </div>
                    `);
                    console.error(error);
                });
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi xem chi tiết lịch');
            console.error(error);
        }
    }
    
    // Hàm duyệt lịch
    async function approveSchedule(id) {
        try {
            Notiflix.Confirm.show(
                'Duyệt lịch làm việc',
                'Bạn có chắc chắn muốn duyệt lịch làm việc này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/schedule/update-status', null, {
                        id: id,
                        status: 'approved',
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                        
                        // Cập nhật màu cho sự kiện trên lịch
                        const event = calendar.getEventById(id);
                        if (event) {
                            event.setProp('color', '#198754');
                            event.setExtendedProp('status', 'approved');
                        }
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi duyệt lịch');
            console.error(error);
        }
    }
    
    // Hàm từ chối lịch
    async function rejectSchedule(id) {
        try {
            Notiflix.Confirm.show(
                'Từ chối lịch làm việc',
                'Bạn có chắc chắn muốn từ chối lịch làm việc này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/schedule/update-status', null, {
                        id: id,
                        status: 'rejected',
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                        
                        // Cập nhật màu cho sự kiện trên lịch
                        const event = calendar.getEventById(id);
                        if (event) {
                            event.setProp('color', '#dc3545');
                            event.setExtendedProp('status', 'rejected');
                        }
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi từ chối lịch');
            console.error(error);
        }
    }
    
    // Hàm chỉnh sửa lịch
    function editSchedule(id) {
        try {
            // Lấy thông tin lịch
            axiosTemplate('get', `/account/schedule/${id}/edit`, null, null)
                .then(res => {
                    if (res.data.status === 200) {
                        const schedule = res.data.schedule;
                        
                        // Điền thông tin vào form
                        const modal = $('#create-schedule-modal');
                        modal.find('.modal-title').text('Chỉnh sửa lịch làm việc');
                        modal.find('select[name="user_id"]').val(schedule.user_id);
                        modal.find('input[name="schedule_date"]').val(schedule.schedule_date);
                        modal.find('input[name="start_time"]').val(schedule.start_time);
                        modal.find('input[name="end_time"]').val(schedule.end_time);
                        modal.find('textarea[name="note"]').val(schedule.note);
                        
                        // Thêm id cho form submit
                        modal.find('form').append(`<input type="hidden" name="id" value="${id}">`);
                        
                        // Đổi nút submit
                        modal.find('button[type="submit"]').text('Cập nhật lịch');
                        
                        // Hiển thị modal
                        KTModal.getInstance(document.querySelector('#create-schedule-modal')).show();
                    } else {
                        showAlert('warning', res.data.message);
                    }
                })
                .catch(error => {
                    showAlert('error', 'Đã xảy ra lỗi khi tải thông tin lịch');
                    console.error(error);
                });
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi chỉnh sửa lịch');
            console.error(error);
        }
    }
    
    // Hàm xóa lịch
    function deleteSchedule(id) {
        try {
            Notiflix.Confirm.show(
                'Xóa lịch làm việc',
                'Bạn có chắc chắn muốn xóa lịch làm việc này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/schedule/delete', null, {
                        id: id
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                        
                        // Xóa sự kiện trên lịch
                        const event = calendar.getEventById(id);
                        if (event) {
                            event.remove();
                        }
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi xóa lịch');
            console.error(error);
        }
    }
</script>
@endpush