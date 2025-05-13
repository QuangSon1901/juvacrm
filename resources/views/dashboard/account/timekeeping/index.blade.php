@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý chấm công
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
        <div class="grid !grid-cols-1 lg:!grid-cols-5 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-people size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng nhân viên
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['totalEmployees'] ?? 0 }} nhân viên
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-calendar size-6 shrink-0 text-blue-500"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Có lịch hôm nay
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['scheduledToday'] ?? 0 }} nhân viên
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-calendar-tick size-6 shrink-0 text-success"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Đã chấm công
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['checkedInToday'] ?? 0 }} nhân viên
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Đi trễ hôm nay
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['lateToday'] ?? 0 }} nhân viên
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-user-x size-6 shrink-0 text-danger"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Vắng mặt hôm nay
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['absentToday'] ?? 0 }} nhân viên
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bảng dữ liệu chấm công -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Dữ liệu chấm công
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
                            <option value="present">Có mặt</option>
                            <option value="absent">Vắng mặt</option>
                            <option value="late">Đi trễ</option>
                            <option value="early_leave">Về sớm</option>
                            <option value="late_and_early_leave">Đi trễ và về sớm</option>
                        </select>
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-from-filter" data-filter="date_from" data-flatpickr="true" placeholder="Từ ngày">
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-to-filter" data-filter="date_to" data-flatpickr="true" placeholder="Đến ngày">
                    </div>
                    
                    <button id="btn-export-excel" class="btn btn-primary btn-sm">
                        <i class="ki-filled ki-file-down me-1"></i>
                        Xuất Excel
                    </button>
                    <button class="btn btn-danger btn-sm" data-modal-toggle="#mark-absent-modal">
                        <i class="ki-outline ki-cross-circle me-1"></i>
                        Đánh dấu vắng mặt
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="attendance-table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[50px] text-center">STT</th>
                                    <th class="w-[140px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Nhân viên</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ngày</span>
                                        </span>
                                    </th>
                                    <th class="w-[110px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Check In</span>
                                        </span>
                                    </th>
                                    <th class="w-[110px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Check Out</span>
                                        </span>
                                    </th>
                                    <th class="w-[70px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Tổng giờ</span>
                                        </span>
                                    </th>
                                    <th class="w-[70px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Giờ hợp lệ</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Đi trễ/Về sớm</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/account/timekeeping/data'])
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

<!-- Modal Chi tiết bản ghi chấm công -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="attendance-detail-modal" style="z-index: 90;">
    <div class="modal-content max-w-[700px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chi tiết chấm công
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="attendance-detail-content">
                <!-- Nội dung chi tiết sẽ được tải bằng Ajax -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Chỉnh sửa chấm công -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-attendance-modal" style="z-index: 90;">
    <div class="modal-content max-w-[700px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chỉnh sửa dữ liệu chấm công
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-attendance-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="edit-attendance-id">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Giờ Check In
                        </label>
                        <input class="input" type="text" name="check_in_time" id="edit-check-in-time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Chọn giờ check in">
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Giờ Check Out
                        </label>
                        <input class="input" type="text" name="check_out_time" id="edit-check-out-time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Chọn giờ check out">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Trạng thái
                        </label>
                        <select class="select" name="status" id="edit-status">
                            <option value="present">Có mặt</option>
                            <option value="absent">Vắng mặt</option>
                            <option value="late">Đi trễ</option>
                            <option value="early_leave">Về sớm</option>
                            <option value="late_and_early_leave">Đi trễ và về sớm</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Giờ làm hợp lệ
                        </label>
                        <input class="input" type="number" name="valid_hours" id="edit-valid-hours" step="0.01" min="0" placeholder="Nhập giờ làm hợp lệ">
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Số phút đi trễ
                        </label>
                        <input class="input" type="number" name="late_minutes" id="edit-late-minutes" min="0" placeholder="Phút đi trễ">
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Số phút về sớm
                        </label>
                        <input class="input" type="number" name="early_leave_minutes" id="edit-early-leave-minutes" min="0" placeholder="Phút về sớm">
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Giờ tăng ca
                        </label>
                        <input class="input" type="number" name="overtime_hours" id="edit-overtime-hours" step="0.01" min="0" placeholder="Giờ tăng ca">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Lý do đi trễ
                        </label>
                        <textarea class="textarea" name="late_reason" id="edit-late-reason" rows="2" placeholder="Nhập lý do đi trễ"></textarea>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Lý do về sớm
                        </label>
                        <textarea class="textarea" name="early_leave_reason" id="edit-early-leave-reason" rows="2" placeholder="Nhập lý do về sớm"></textarea>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Ghi chú
                    </label>
                    <textarea class="textarea" name="note" id="edit-note" rows="3" placeholder="Nhập ghi chú"></textarea>
                </div>
                
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-primary justify-center">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="mark-absent-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Đánh dấu nhân viên vắng mặt
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="mark-absent-form" class="grid gap-4 py-4">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Nhân viên <span class="text-red-500">*</span>
                    </label>
                    <select class="select" name="user_id" id="user_id" required>
                        <option value="">Chọn nhân viên</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Ngày vắng mặt <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="text" name="absent_date" id="absent_date" data-flatpickr="true" placeholder="Chọn ngày" required>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Ca làm việc <span class="text-red-500">*</span>
                    </label>
                    <select class="select" name="schedule_id" id="schedule_select" required disabled>
                        <option value="">Chọn nhân viên và ngày trước</option>
                    </select>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Ghi chú
                    </label>
                    <textarea class="textarea" name="note" rows="3" placeholder="Lý do vắng mặt (nếu có)"></textarea>
                </div>
                
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-danger justify-center">
                        Đánh dấu vắng mặt
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
        // Khởi tạo flatpickr cho các trường ngày
        flatpickrMake($("#date-from-filter"), 'date');
        flatpickrMake($("#date-to-filter"), 'date');
        flatpickrMake($("input[name='absent_date']"), 'date');
        
        // Xử lý sự kiện khi thay đổi bộ lọc
        $('[data-filter]').on('change', function() {
            callAjaxDataTable($('.updater'));
        });
        
        // Xử lý nút xuất Excel
        $('#btn-export-excel').on('click', function() {
            const filters = {};
            
            $('[data-filter]').each(function() {
                const filter = $(this).data('filter');
                const value = $(this).val();
                
                if (value) {
                    filters[filter] = value;
                }
            });
            
            let url = '/account/timekeeping/export-excel';
            const queryParams = new URLSearchParams(filters).toString();
            
            if (queryParams) {
                url += `?${queryParams}`;
            }
            
            window.location.href = url;
        });
        
        // Xử lý form chỉnh sửa chấm công
        $('#edit-attendance-form').on('submit', async function(e) {
            e.preventDefault();
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const formData = {
                    id: $('#edit-attendance-id').val(),
                    check_in_time: $('#edit-check-in-time').val(),
                    check_out_time: $('#edit-check-out-time').val(),
                    status: $('#edit-status').val(),
                    valid_hours: $('#edit-valid-hours').val(),
                    late_minutes: $('#edit-late-minutes').val(),
                    early_leave_minutes: $('#edit-early-leave-minutes').val(),
                    overtime_hours: $('#edit-overtime-hours').val(),
                    late_reason: $('#edit-late-reason').val(),
                    early_leave_reason: $('#edit-early-leave-reason').val(),
                    note: $('#edit-note').val()
                };
                
                const res = await axiosTemplate('post', '/account/timekeeping/update', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#edit-attendance-modal')).hide();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi cập nhật dữ liệu chấm công');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Cập nhật');
            }
        });

        // Xử lý khi thay đổi nhân viên và ngày vắng mặt
        $('#user_id, #absent_date').on('change', async function() {
            const userId = $('#user_id').val();
            const absentDate = $('#absent_date').val();
            
            if (userId && absentDate) {
                try {
                    $(this).prop('disabled', true);
                    $('#schedule_select').html('<option value="">Đang tải...</option>').prop('disabled', true);
                    
                    // Load danh sách ca làm việc
                    const res = await axiosTemplate('get', '/account/schedule/get-user-schedules', {
                        user_id: userId,
                        date: absentDate
                    }, null);
                    
                    if (res.data.status === 200) {
                        $('#schedule_select').empty().prop('disabled', false);
                        
                        if (res.data.schedules.length === 0) {
                            $('#schedule_select').append('<option value="">Không có lịch làm việc nào</option>');
                        } else {
                            $('#schedule_select').append('<option value="">Chọn ca làm việc</option>');
                            
                            res.data.schedules.forEach(schedule => {
                                $('#schedule_select').append(`<option value="${schedule.id}">${formatTime(schedule.start_time)} - ${formatTime(schedule.end_time)}</option>`);
                            });
                        }
                    } else {
                        showAlert('warning', res.data.message);
                        $('#schedule_select').html('<option value="">Chọn nhân viên và ngày trước</option>').prop('disabled', true);
                    }
                } catch (error) {
                    console.error(error);
                    showAlert('error', 'Không thể tải danh sách ca làm việc');
                    $('#schedule_select').html('<option value="">Chọn nhân viên và ngày trước</option>').prop('disabled', true);
                } finally {
                    $(this).prop('disabled', false);
                }
            } else {
                $('#schedule_select').html('<option value="">Chọn nhân viên và ngày trước</option>').prop('disabled', true);
            }
        });
        
        // Helper để format thời gian
        function formatTime(isoTimeString) {
            if (isoTimeString.includes('T')) {
                const date = new Date(isoTimeString);
                return date.getHours().toString().padStart(2, '0') + ':' + 
                    date.getMinutes().toString().padStart(2, '0');
            } else {
                return isoTimeString.substring(0, 5);
            }
        }
        
        // Xử lý submit form
        $('#mark-absent-form').on('submit', async function(e) {
            e.preventDefault();
            
            const userId = $('#mark-absent-form #user_id').val();
            const scheduleId = $('#mark-absent-form #schedule_select').val();
            const note = $('#mark-absent-form textarea[name="note"]').val();
            
            if (!userId || !scheduleId) {
                showAlert('warning', 'Vui lòng chọn nhân viên và ca làm việc');
                return;
            }
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/timekeeping/mark-absent', null, {
                    user_id: userId,
                    schedule_id: scheduleId,
                    note: note
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#mark-absent-modal')).hide();
                    
                    // Làm mới bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                    
                    // Reset form
                    $(this).trigger('reset');
                    $('#schedule_select').empty().prop('disabled', true);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi đánh dấu vắng mặt');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Đánh dấu vắng mặt');
            }
        });
    });
    
    // Hàm mở modal chỉnh sửa chấm công
    function openEditAttendanceModal(id) {
        // Hiển thị loading
        $('#edit-attendance-form').find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang tải...');
        
        // Hiển thị modal
        KTModal.getInstance(document.querySelector('#edit-attendance-modal')).show();
        
        // Fetch dữ liệu bản ghi
        axiosTemplate('get', `/account/timekeeping/detail/${id}`, null, null)
            .then(res => {
                if (res.data.status === 200) {
                    // Parse HTML để lấy dữ liệu
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = res.data.content;
                    
                    // Lấy dữ liệu từ các phần tử trong HTML
                    const attendance = {
                        id: id,
                        check_in_time: tempDiv.querySelector('[data-check-in-time]') ? tempDiv.querySelector('[data-check-in-time]').getAttribute('data-check-in-time') : '',
                        check_out_time: tempDiv.querySelector('[data-check-out-time]') ? tempDiv.querySelector('[data-check-out-time]').getAttribute('data-check-out-time') : '',
                        status: tempDiv.querySelector('[data-status]') ? tempDiv.querySelector('[data-status]').getAttribute('data-status') : 'present',
                        valid_hours: tempDiv.querySelector('[data-valid-hours]') ? tempDiv.querySelector('[data-valid-hours]').getAttribute('data-valid-hours') : '0',
                        late_minutes: tempDiv.querySelector('[data-late-minutes]') ? tempDiv.querySelector('[data-late-minutes]').getAttribute('data-late-minutes') : '0',
                        early_leave_minutes: tempDiv.querySelector('[data-early-leave-minutes]') ? tempDiv.querySelector('[data-early-leave-minutes]').getAttribute('data-early-leave-minutes') : '0',
                        overtime_hours: tempDiv.querySelector('[data-overtime-hours]') ? tempDiv.querySelector('[data-overtime-hours]').getAttribute('data-overtime-hours') : '0',
                        late_reason: tempDiv.querySelector('[data-late-reason]') ? tempDiv.querySelector('[data-late-reason]').textContent : '',
                        early_leave_reason: tempDiv.querySelector('[data-early-leave-reason]') ? tempDiv.querySelector('[data-early-leave-reason]').textContent : '',
                        note: tempDiv.querySelector('[data-note]') ? tempDiv.querySelector('[data-note]').textContent : '',
                    };
                    
                    // Điền dữ liệu vào form
                    $('#edit-attendance-id').val(attendance.id);
                    $('#edit-check-in-time').val(attendance.check_in_time);
                    $('#edit-check-out-time').val(attendance.check_out_time);
                    $('#edit-status').val(attendance.status);
                    $('#edit-valid-hours').val(attendance.valid_hours);
                    $('#edit-late-minutes').val(attendance.late_minutes);
                    $('#edit-early-leave-minutes').val(attendance.early_leave_minutes);
                    $('#edit-overtime-hours').val(attendance.overtime_hours);
                    $('#edit-late-reason').val(attendance.late_reason);
                    $('#edit-early-leave-reason').val(attendance.early_leave_reason);
                    $('#edit-note').val(attendance.note);
                    
                    // Khởi tạo lại flatpickr cho các trường thời gian
                    flatpickrMake($("#edit-check-in-time"), 'time', {enableSeconds: true, noCalendar: true, enableTime: true, dateFormat: 'H:i:s'});
                    flatpickrMake($("#edit-check-out-time"), 'time', {enableSeconds: true, noCalendar: true, enableTime: true, dateFormat: 'H:i:s'});
                } else {
                    showAlert('warning', 'Không thể tải thông tin bản ghi');
                    KTModal.getInstance(document.querySelector('#edit-attendance-modal')).hide();
                }
            })
            .catch(error => {
                showAlert('error', 'Đã xảy ra lỗi khi tải dữ liệu');
                console.error(error);
                KTModal.getInstance(document.querySelector('#edit-attendance-modal')).hide();
            })
            .finally(() => {
                $('#edit-attendance-form').find('button[type="submit"]').prop('disabled', false).html('Cập nhật');
            });
    }
    
    // Hàm xem chi tiết bản ghi chấm công
    function viewAttendanceDetail(id) {
        // Hiển thị loading
        $('#attendance-detail-content').html(`
            <div class="d-flex justify-content-center align-items-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        // Hiển thị modal
        KTModal.getInstance(document.querySelector('#attendance-detail-modal')).show();
        
        // Fetch dữ liệu chi tiết
        axiosTemplate('get', `/account/timekeeping/detail/${id}`, null, null)
            .then(res => {
                if (res.data.status === 200) {
                    $('#attendance-detail-content').html(res.data.content);
                } else {
                    $('#attendance-detail-content').html(`
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4"></i>
                                <div>${res.data.message || 'Không thể tải thông tin chi tiết'}</div>
                            </div>
                        </div>
                    `);
                }
            })
            .catch(error => {
                $('#attendance-detail-content').html(`
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4"></i>
                            <div>Đã xảy ra lỗi khi tải dữ liệu</div>
                        </div>
                    </div>
                `);
                console.error(error);
            });
    }
</script>
@endpush