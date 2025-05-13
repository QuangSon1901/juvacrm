@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Chấm công
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <div class="text-lg font-semibold text-gray-800">
                <span id="current-time">{{ date('H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        @if(count($incompleteRecords) > 0)
        <!-- Hiển thị cảnh báo về các record chưa checkout -->
        <div class="card bg-light-warning">
            <div class="card-header">
                <h3 class="card-title text-warning">
                    <i class="ki-outline ki-warning-2 me-2"></i>
                    Bản ghi chấm công chưa hoàn thành
                </h3>
            </div>
            <div class="card-body">
                <div class="alert-not-hide alert-warning mb-5">
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-warning">Cần cập nhật checkout cho các bản ghi chấm công sau:</h4>
                        <span>Bạn cần cập nhật checkout cho các bản ghi sau trước khi có thể check-in hôm nay.</span>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-rounded table-striped">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Ca làm việc</th>
                                <th>Check-in</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incompleteRecords as $record)
                            <tr>
                                <td>{{ formatDateTime($record->work_date, 'd/m/Y') }}</td>
                                <td>
                                    @if($record->schedule)
                                    {{ formatDateTime($record->schedule->start_time, 'H:i') }} - 
                                    {{ formatDateTime($record->schedule->end_time, 'H:i') }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>{{ formatDateTime($record->check_in_time, 'H:i:s') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="openFixCheckoutModal({{ $record->id }}, '{{ formatDateTime($record->work_date, 'd/m/Y') }}')">
                                        Cập nhật checkout
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <div class="grid grid-cols-1 gap-5">
            <!-- Thẻ chấm công -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ki-filled ki-time-check text-primary text-2xl"></i>&nbsp;Chấm công hôm nay
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 items-center gap-5">
                        <div class="grid gap-3 mb-6" style="grid-template-columns: repeat(6, minmax(0, 1fr));">
                            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                                <p class="text-xs text-gray-600">Tổng ngày làm việc</p>
                                <p class="text-xl font-bold text-primary">{{ $stats['presentDays'] }}/{{ $stats['workingDaysInMonth'] }}</p>
                            </div>
                            
                            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                                <p class="text-xs text-gray-600">Tổng giờ làm việc</p>
                                <p class="text-xl font-bold text-primary">{{ number_format($stats['totalHours'], 2) }}</p>
                            </div>
                            
                            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                                <p class="text-xs text-gray-600">Số ngày đi trễ</p>
                                <p class="text-xl font-bold text-warning">{{ $stats['lateDays'] }}</p>
                            </div>
                            
                            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                                <p class="text-xs text-gray-600">Số ngày về sớm</p>
                                <p class="text-xl font-bold text-info">{{ $stats['earlyLeaveDays'] }}</p>
                            </div>
                            
                            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                                <p class="text-xs text-gray-600">Giờ tăng ca</p>
                                <p class="text-xl font-bold text-primary">{{ number_format($stats['totalOvertimeHours'], 2) }}</p>
                            </div>
                            
                            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                                <p class="text-xs text-gray-600">Giờ hợp lệ</p>
                                <p class="text-xl font-bold text-success">{{ number_format($stats['totalValidHours'], 2) }}</p>
                            </div>
                        </div>
                        
                        <!-- Danh sách ca làm việc hôm nay -->
                        <div class="w-full">
                            <h4 class="text-gray-700 font-medium mb-3">Lịch làm việc hôm nay</h4>
                            
                            @if(count($schedules) > 0)
                                <div class="overflow-x-auto">
                                    <table class="table table-rounded table-row-bordered table-hover">
                                        <thead>
                                            <tr class="fw-bold fs-6 text-gray-800">
                                                <th>Ca làm việc</th>
                                                <th>Vào</th>
                                                <th>Ra</th>
                                                <th>Giờ làm</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $schedule)
                                                @php
                                                    $attendance = $attendanceRecords->firstWhere('schedule_id', $schedule->id);
                                                    $now = \Carbon\Carbon::now();
                                                    $scheduleStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' ' . date('H:i:s', strtotime($schedule->start_time)));
                                                    $scheduleEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' ' . date('H:i:s', strtotime($schedule->end_time)));
                                                    
                                                    // Lấy giới hạn check-in sớm từ cấu hình
                                                    $earlyCheckInMinutes = \App\Models\SystemConfig::getValue('early_checkin_minutes', 60);
                                                    $allowedCheckInTime = $scheduleStart->copy()->subMinutes($earlyCheckInMinutes);
                                                    
                                                    $canCheckIn = !($attendance && $attendance->check_in_time) && 
                                                                 $now->gte($allowedCheckInTime) && 
                                                                 $now->lte($scheduleEnd) &&
                                                                 count($incompleteRecords) === 0;
                                                    
                                                    $canCheckOut = ($attendance && $attendance->check_in_time && 
                                                                   !$attendance->check_out_time);
                                                @endphp
                                                <tr>
                                                    <td>{{ formatDateTime($schedule->start_time, 'H:i') }} - {{ formatDateTime($schedule->end_time, 'H:i') }}</td>
                                                    <td>
                                                        @if($attendance && $attendance->check_in_time)
                                                            <span class="text-success">{{ formatDateTime($attendance->check_in_time, 'H:i:s') }}</span>
                                                            @if($attendance->late_minutes > 0)
                                                                <span class="badge badge-sm badge-warning ms-1">Trễ {{ $attendance->late_minutes }} phút</span>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-500">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attendance && $attendance->check_out_time)
                                                            <span class="text-danger">{{ formatDateTime($attendance->check_out_time, 'H:i:s') }}</span>
                                                            @if($attendance->early_leave_minutes > 0)
                                                                <span class="badge badge-sm badge-info ms-1">Sớm {{ $attendance->early_leave_minutes }} phút</span>
                                                            @endif
                                                            @if($attendance->overtime_hours > 0)
                                                                <span class="badge badge-sm badge-primary ms-1">Tăng ca {{ number_format($attendance->overtime_hours, 2) }} giờ</span>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-500">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attendance && $attendance->total_hours > 0)
                                                            {{ number_format($attendance->total_hours, 2) }}
                                                        @else
                                                            <span class="text-gray-500">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attendance)
                                                            <span class="badge badge-sm badge-{{ $attendance->getStatusClass() }}">
                                                                {{ $attendance->getStatusText() }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-sm badge-gray">Chưa chấm công</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($canCheckIn)
                                                            <button class="btn btn-xs btn-success" onclick="openCheckInModal({{ $schedule->id }})">
                                                                Check In
                                                            </button>
                                                        @endif
                                                        
                                                        @if($canCheckOut)
                                                            <button class="btn btn-xs btn-danger" onclick="openCheckOutModal({{ $schedule->id }})">
                                                                Check Out
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert-not-hide alert-warning">
                                    <div class="flex items-center">
                                        <div>Bạn không có lịch làm việc được duyệt nào cho hôm nay</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lịch sử chấm công gần đây -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ki-filled ki-time text-primary text-2xl"></i>&nbsp;Lịch sử chấm công gần đây
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Ca làm việc</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Giờ làm</th>
                                <th>Giờ hợp lệ</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendance as $record)
                                <tr>
                                    <td>{{ formatDateTime($record->work_date, 'd/m/Y') }}</td>
                                    <td>
                                        @if($record->schedule)
                                            {{ formatDateTime($record->schedule->start_time, 'H:i') }} - 
                                            {{ formatDateTime($record->schedule->end_time, 'H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->check_in_time)
                                            {{ formatDateTime($record->check_in_time, 'H:i:s') }}
                                            @if($record->late_minutes > 0)
                                                <span class="badge badge-sm badge-warning ms-1">Trễ {{ $record->late_minutes }} phút</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->check_out_time)
                                            {{ formatDateTime($record->check_out_time, 'H:i:s') }}
                                            @if($record->early_leave_minutes > 0)
                                                <span class="badge badge-sm badge-info ms-1">Sớm {{ $record->early_leave_minutes }} phút</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($record->total_hours, 2) }}</td>
                                    <td>{{ number_format($record->valid_hours, 2) }}</td>
                                    <td>
                                        <span class="badge badge-sm badge-{{ $record->getStatusClass() }}">
                                            {{ $record->getStatusText() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Chưa có dữ liệu chấm công</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Fix Checkout -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="fix-checkout-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật checkout quên
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="fix-checkout-form" class="grid gap-4 py-4">
                <input type="hidden" name="record_id" id="fix-checkout-record-id">
                
                <div class="alert-not-hide alert-warning mb-3">
                    <div class="d-flex">
                        <div>
                            <h4 class="mb-1 text-warning">Vui lòng cập nhật checkout</h4>
                            <div>Bạn cần cập nhật checkout cho ngày <span id="fix-checkout-date" class="fw-bold"></span> trước khi có thể check-in hôm nay.</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Giờ checkout <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="text" name="check_out_time" id="fix-checkout-time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Chọn giờ checkout" required>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Lý do quên checkout <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="reason" rows="3" placeholder="Nhập lý do quên checkout..." required></textarea>
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

<!-- Modal Check In -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="check-in-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Check In
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="check-in-form" class="grid gap-4 py-4">
                <input type="hidden" name="schedule_id" id="check-in-schedule-id">
                
                <div class="alert-not-hide alert-info">
                    <div class="d-flex align-items-center">
                        <div>
                            Bạn đang check-in cho ca làm việc ngày hôm nay. Thời gian hiện tại: <span id="check-in-current-time" class="fw-bold"></span>
                        </div>
                    </div>
                </div>
                
                <div id="late-reason-container" class="flex flex-col gap-2 hidden">
                    <div class="alert-not-hide alert-warning mb-0">
                        <div class="d-flex">
                            <div>
                                <h4 class="mb-1 text-warning">Bạn đang đi trễ!</h4>
                                <div>Đi trễ <span id="late-minutes" class="fw-bold"></span> phút so với giờ làm việc. Vui lòng nhập lý do.</div>
                            </div>
                        </div>
                    </div>
                    
                    <label class="font-medium text-sm mb-1 mt-3">
                        Lý do đi trễ <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="late_reason" rows="3" placeholder="Nhập lý do đi trễ..."></textarea>
                </div>
                
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-success justify-center">
                        Check In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Check Out -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="check-out-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Check Out
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="check-out-form" class="grid gap-4 py-4">
                <input type="hidden" name="schedule_id" id="check-out-schedule-id">
                
                <div class="alert-not-hide alert-info">
                    <div class="d-flex align-items-center">
                        <div>
                            Bạn đang check-out cho ca làm việc ngày hôm nay. Thời gian hiện tại: <span id="check-out-current-time" class="fw-bold"></span>
                        </div>
                    </div>
                </div>
                
                <div id="early-leave-reason-container" class="flex flex-col gap-2 hidden">
                    <div class="alert-not-hide alert-warning mb-0">
                        <div class="d-flex">
                            <div>
                                <h4 class="mb-1 text-warning">Bạn đang về sớm!</h4>
                                <div>Về sớm <span id="early-leave-minutes" class="fw-bold"></span> phút so với giờ làm việc. Vui lòng nhập lý do.</div>
                            </div>
                        </div>
                    </div>
                    
                    <label class="font-medium text-sm mb-1 mt-3">
                        Lý do về sớm <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="early_leave_reason" rows="3" placeholder="Nhập lý do về sớm..."></textarea>
                </div>
                
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-danger justify-center">
                        Check Out
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
        // Khởi tạo flatpickr cho các trường thời gian
        flatpickrMake($('#fix-checkout-time'), 'time', {enableSeconds: false, noCalendar: true, enableTime: true, dateFormat: 'H:i:S'});
        
        // Cập nhật thời gian hiện tại mỗi giây
        setInterval(function() {
            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' +
                            now.getMinutes().toString().padStart(2, '0') + ':' +
                            now.getSeconds().toString().padStart(2, '0');
            $('#current-time').text(timeStr);
            $('#check-in-current-time').text(timeStr);
            $('#check-out-current-time').text(timeStr);
        }, 1000);
        
        // Xử lý form cập nhật checkout quên
        $('#fix-checkout-form').on('submit', async function(e) {
            e.preventDefault();
            
            const recordId = $('#fix-checkout-record-id').val();
            const checkOutTime = $('#fix-checkout-time').val();
            const reason = $(this).find('textarea[name="reason"]').val();
            
            if (!checkOutTime || !reason) {
                showAlert('warning', 'Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/timekeeping/fix-incomplete-checkout', null, {
                    record_id: recordId,
                    check_out_time: checkOutTime,
                    reason: reason
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#fix-checkout-modal')).hide();
                    
                    // Reload trang sau 1.5 giây
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi cập nhật checkout');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Cập nhật');
            }
        });
        
        // Xử lý form check-in
        $('#check-in-form').on('submit', async function(e) {
            e.preventDefault();
            
            const scheduleId = $('#check-in-schedule-id').val();
            const lateReason = $(this).find('textarea[name="late_reason"]').val();
            
            // Kiểm tra nếu phần lý do đi trễ hiển thị nhưng không nhập
            if ($('#late-reason-container').is(':visible') && !lateReason) {
                showAlert('warning', 'Vui lòng nhập lý do đi trễ');
                return;
            }
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/timekeeping/do-check-in', null, {
                    schedule_id: scheduleId,
                    late_reason: lateReason
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#check-in-modal')).hide();
                    
                    // Reload trang sau 1.5 giây
                    setTimeout(() => window.location.reload(), 1500);
                } else if (res.data.status === 422 && res.data.require_reason) {
                    // Hiển thị phần nhập lý do nếu cần
                    $('#late-reason-container').removeClass('hidden');
                    $('#late-minutes').text(res.data.late_minutes);
                    $(this).find('button[type="submit"]').prop('disabled', false).html('Check In');
                } else if (res.data.status === 400 && res.data.require_fix) {
                    // Hiển thị modal sửa checkout
                    KTModal.getInstance(document.querySelector('#check-in-modal')).hide();
                    openFixCheckoutModal(res.data.record_id, res.data.date);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi check in');
                console.error(error);
            } finally {
                if (!$('#late-reason-container').is(':visible')) {
                    $(this).find('button[type="submit"]').prop('disabled', false).html('Check In');
                }
            }
        });
        
        // Xử lý form check-out
        $('#check-out-form').on('submit', async function(e) {
            e.preventDefault();
            
            const scheduleId = $('#check-out-schedule-id').val();
            const earlyLeaveReason = $(this).find('textarea[name="early_leave_reason"]').val();
            
            // Kiểm tra nếu phần lý do về sớm hiển thị nhưng không nhập
            if ($('#early-leave-reason-container').is(':visible') && !earlyLeaveReason) {
                showAlert('warning', 'Vui lòng nhập lý do về sớm');
                return;
            }
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/timekeeping/do-check-out', null, {
                    schedule_id: scheduleId,
                    early_leave_reason: earlyLeaveReason
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#check-out-modal')).hide();
                    
                    // Reload trang sau 1.5 giây
                    setTimeout(() => window.location.reload(), 1500);
                } else if (res.data.status === 422 && res.data.require_reason) {
                    // Hiển thị phần nhập lý do nếu cần
                    $('#early-leave-reason-container').removeClass('hidden');
                    $('#early-leave-minutes').text(res.data.early_leave_minutes);
                    $(this).find('button[type="submit"]').prop('disabled', false).html('Check Out');
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi check out');
                console.error(error);
            } finally {
                if (!$('#early-leave-reason-container').is(':visible')) {
                    $(this).find('button[type="submit"]').prop('disabled', false).html('Check Out');
                }
            }
        });
    });
    
    // Hàm mở modal fix checkout
    function openFixCheckoutModal(recordId, date) {
        $('#fix-checkout-record-id').val(recordId);
        $('#fix-checkout-date').text(date);
        KTModal.getInstance(document.querySelector('#fix-checkout-modal')).show();
    }
    
    // Hàm mở modal check-in
    function openCheckInModal(scheduleId) {
        // Reset form
        $('#check-in-form')[0].reset();
        $('#late-reason-container').addClass('hidden');
        
        // Cập nhật ID lịch
        $('#check-in-schedule-id').val(scheduleId);
        
        // Hiển thị modal
        KTModal.getInstance(document.querySelector('#check-in-modal')).show();
    }
    
    // Hàm mở modal check-out
    function openCheckOutModal(scheduleId) {
        // Reset form
        $('#check-out-form')[0].reset();
        $('#early-leave-reason-container').addClass('hidden');
        
        // Cập nhật ID lịch
        $('#check-out-schedule-id').val(scheduleId);
        
        // Hiển thị modal
        KTModal.getInstance(document.querySelector('#check-out-modal')).show();
    }
</script>
@endpush