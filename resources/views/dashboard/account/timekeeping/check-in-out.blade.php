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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Thẻ chấm công -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ki-filled ki-time-check text-primary text-2xl"></i>&nbsp;Chấm công hôm nay
                    </h3>
                </div>
                <div class="card-body">
                    <div class="flex flex-col items-center gap-5">
                        <div class="text-center">
                            <div class="text-xl font-semibold text-gray-900 mb-2">{{ date('d/m/Y') }}</div>
                        </div>
                        
                        <!-- Danh sách ca làm việc hôm nay -->
                        <div class="w-full">
                            <h4 class="text-gray-700 font-medium mb-3">Lịch làm việc hôm nay</h4>
                            
                            @php
                                $todaySchedules = \App\Models\PartTimeSchedule::where('user_id', Session::get(ACCOUNT_CURRENT_SESSION)['id'])
                                    ->where('schedule_date', date('Y-m-d'))
                                    ->where('status', 'approved')
                                    ->orderBy('start_time')
                                    ->get();
                                    
                                // Lấy các bản ghi chấm công hôm nay
                                $todayAttendances = \App\Models\AttendanceRecord::where('user_id', Session::get(ACCOUNT_CURRENT_SESSION)['id'])
                                    ->where('work_date', date('Y-m-d'))
                                    ->get()
                                    ->keyBy('schedule_id');
                            @endphp
                            
                            @if(count($todaySchedules) > 0)
                                <div class="overflow-x-auto">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ca làm việc</th>
                                                <th>Vào</th>
                                                <th>Ra</th>
                                                <th>Giờ làm</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($todaySchedules as $schedule)
                                                @php
                                                    $attendance = $todayAttendances[$schedule->id] ?? null;
                                                    $now = \Carbon\Carbon::now();
                                                    $scheduleStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' ' . date('H:i:s', strtotime($schedule->start_time)));
                                                    $scheduleEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' ' . date('H:i:s', strtotime($schedule->end_time)));
                                                    $allowedCheckInTime = $scheduleStart->copy()->subMinutes(60);
                                                    
                                                    $canCheckIn = !($attendance && $attendance->check_in_time) && 
                                                                 $now->gte($allowedCheckInTime) && 
                                                                 $now->lte($scheduleEnd);
                                                    
                                                    $canCheckOut = ($attendance && $attendance->check_in_time && 
                                                                   !$attendance->check_out_time);
                                                @endphp
                                                <tr>
                                                    <td>{{ formatDateTime($schedule->start_time, 'H:i') }} - {{ formatDateTime($schedule->end_time, 'H:i') }}</td>
                                                    <td>
                                                        @if($attendance && $attendance->check_in_time)
                                                            <span class="text-success">{{ formatDateTime($attendance->check_in_time, 'H:i:s') }}</span>
                                                            @if($attendance->isLate())
                                                                <span class="badge badge-sm badge-warning ml-1">Trễ</span>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-500">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attendance && $attendance->check_out_time)
                                                            <span class="text-danger">{{ formatDateTime($attendance->check_out_time, 'H:i:s') }}</span>
                                                            @if($attendance->isEarlyLeave())
                                                                <span class="badge badge-sm badge-info ml-1">Sớm</span>
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
                                                            <button class="btn btn-xs btn-success" onclick="doCheckIn({{ $schedule->id }})">
                                                                Check In
                                                            </button>
                                                        @endif
                                                        
                                                        @if($canCheckOut)
                                                            <button class="btn btn-xs btn-danger" onclick="doCheckOut({{ $schedule->id }})">
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
                                <div class="alert alert-warning">
                                    <div class="flex items-center">
                                        <i class="ki-outline ki-information-5 fs-2hx text-warning me-4"></i>
                                        <div>Bạn không có lịch làm việc được duyệt nào cho hôm nay</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin nhân viên và trạng thái -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ki-filled ki-user text-primary text-2xl"></i>&nbsp;Thông tin làm việc
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Phần thông tin nhân viên (giữ nguyên) -->
                </div>
            </div>
        </div>
        
        <!-- Lịch sử chấm công (giữ nguyên) -->
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Cập nhật thời gian hiện tại mỗi giây
        setInterval(function() {
            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' +
                            now.getMinutes().toString().padStart(2, '0') + ':' +
                            now.getSeconds().toString().padStart(2, '0');
            $('#current-time').text(timeStr);
        }, 1000);
    });
    
    // Hàm check-in cho ca cụ thể
    function doCheckIn(scheduleId) {
        Notiflix.Confirm.show(
            'Xác nhận Check-in',
            'Bạn có chắc chắn muốn check-in cho ca này?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                try {
                    const res = await axiosTemplate('post', '/account/timekeeping/do-check-in', null, {
                        schedule_id: scheduleId
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert('warning', res.data.message);
                    }
                } catch (error) {
                    showAlert('error', 'Đã xảy ra lỗi khi check in');
                    console.error(error);
                }
            }
        );
    }
    
    // Hàm check-out cho ca cụ thể
    function doCheckOut(scheduleId) {
        Notiflix.Confirm.show(
            'Xác nhận Check-out',
            'Bạn có chắc chắn muốn check-out cho ca này?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                try {
                    const res = await axiosTemplate('post', '/account/timekeeping/do-check-out', null, {
                        schedule_id: scheduleId
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert('warning', res.data.message);
                    }
                } catch (error) {
                    showAlert('error', 'Đã xảy ra lỗi khi check out');
                    console.error(error);
                }
            }
        );
    }
</script>
@endpush