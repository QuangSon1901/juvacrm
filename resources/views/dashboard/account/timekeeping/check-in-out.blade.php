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
                            <div class="text-sm text-gray-600">
                                @if(isset($schedule))
                                    <span class="badge badge-sm badge-success">Lịch làm việc đã duyệt</span>
                                    <p class="mt-1">Thời gian: {{ formatDateTime($schedule->start_time, 'H:i') }} - {{ formatDateTime($schedule->end_time, 'H:i') }}</p>
                                @else
                                    <span class="badge badge-sm badge-warning">Không có lịch làm việc hôm nay</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-table scrollable-x-auto pb-3 w-full">
                            <table class="table align-middle text-sm text-gray-500">
                                <tbody>
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Giờ vào
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            @if(isset($attendanceRecord) && $attendanceRecord->check_in_time)
                                                <span class="text-success font-medium">{{ formatDateTime($attendanceRecord->check_in_time, 'H:i:s') }}</span>
                                                @if($attendanceRecord->isLate())
                                                    <span class="badge badge-sm badge-warning ml-2">Trễ</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500">Chưa chấm công</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Giờ ra
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            @if(isset($attendanceRecord) && $attendanceRecord->check_out_time)
                                                <span class="text-danger font-medium">{{ formatDateTime($attendanceRecord->check_out_time, 'H:i:s') }}</span>
                                                @if($attendanceRecord->isEarlyLeave())
                                                    <span class="badge badge-sm badge-info ml-2">Sớm</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500">Chưa chấm công</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if(isset($attendanceRecord) && $attendanceRecord->check_in_time && $attendanceRecord->check_out_time)
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Tổng thời gian
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            <span class="text-primary font-medium">{{ number_format($attendanceRecord->total_hours, 2) }} giờ</span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Trạng thái
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            @if(isset($attendanceRecord))
                                                <span class="badge badge-sm badge-{{ $attendanceRecord->getStatusClass() }}">
                                                    {{ $attendanceRecord->getStatusText() }}
                                                </span>
                                            @else
                                                <span class="badge badge-sm badge-outline badge-gray">
                                                    Chưa chấm công
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="flex gap-4">
                            <button id="btn-check-in" class="btn btn-success {{ !isset($schedule) || (isset($attendanceRecord) && $attendanceRecord->check_in_time) ? 'disabled' : '' }}" 
                                    {{ !isset($schedule) || (isset($attendanceRecord) && $attendanceRecord->check_in_time) ? 'disabled' : '' }}>
                                <i class="ki-filled ki-arrow-right me-2"></i>Check In
                            </button>
                            <button id="btn-check-out" class="btn btn-danger {{ !isset($attendanceRecord) || !$attendanceRecord->check_in_time || isset($attendanceRecord) && $attendanceRecord->check_out_time ? 'disabled' : '' }}"
                                    {{ !isset($attendanceRecord) || !$attendanceRecord->check_in_time || isset($attendanceRecord) && $attendanceRecord->check_out_time ? 'disabled' : '' }}>
                                <i class="ki-filled ki-arrow-left me-2"></i>Check Out
                            </button>
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
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4">
                            <div class="avatar avatar-rounded" style="background-image: url('{{ $user->avatar ? asset($user->avatar) : asset('assets/images/logo/favicon.png') }}')"></div>
                            <div>
                                <div class="font-semibold text-lg">{{ $user->name }}</div>
                                <div class="text-gray-600">{{ $user->email }}</div>
                            </div>
                        </div>
                        
                        <div class="card-table scrollable-x-auto pb-3 w-full">
                            <table class="table align-middle text-sm text-gray-500">
                                <tbody>
                                    @foreach($departments as $department)
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Phòng ban
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            {{ $department->name }} ({{ $department->level_name }})
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Loại lương
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            {{ isset($salaryConfig) && $salaryConfig->type == 'fulltime' ? 'Lương cố định' : 'Lương theo giờ' }}
                                        </td>
                                    </tr>
                                    @if(isset($salaryConfig) && $salaryConfig->type == 'part-time' && $salaryConfig->hourly_rate)
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Lương theo giờ
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            {{ number_format($salaryConfig->hourly_rate, 0, ',', '.') }} đ/giờ
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Ngày làm việc tháng này
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            {{ $stats['presentDays'] ?? 0 }} / {{ $stats['workingDaysInMonth'] ?? 0 }} ngày
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            Tổng giờ làm việc tháng này
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            {{ number_format($stats['totalHours'] ?? 0, 2) }} giờ
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lịch sử chấm công gần đây -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Lịch sử chấm công gần đây
                </h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.profile.my-timesheet') }}" class="btn btn-primary btn-sm">
                        <i class="ki-filled ki-calendar me-1"></i>
                        Xem tất cả
                    </a>
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
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Check In</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Check Out</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Giờ làm</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendance as $index => $record)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ formatDateTime($record->work_date, 'd/m/Y') }}</td>
                                    <td>{{ $record->check_in_time ? formatDateTime($record->check_in_time, 'H:i:s') : '--:--:--' }}</td>
                                    <td>{{ $record->check_out_time ? formatDateTime($record->check_out_time, 'H:i:s') : '--:--:--' }}</td>
                                    <td>{{ number_format($record->total_hours, 2) }}</td>
                                    <td>
                                        <span class="badge badge-sm badge-{{ $record->getStatusClass() }}">
                                            {{ $record->getStatusText() }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                
                                @if(count($recentAttendance) == 0)
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-gray-500">Không có dữ liệu chấm công</p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
        
        // Xử lý check in
        $('#btn-check-in').on('click', async function() {
            if ($(this).hasClass('disabled')) return;
            
            try {
                Notiflix.Confirm.show(
                    'Xác nhận Check-in',
                    'Bạn có chắc chắn muốn check-in ngay bây giờ?',
                    'Đồng ý',
                    'Hủy bỏ',
                    async function() {
                        const btn = $('#btn-check-in');
                        btn.prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                        
                        const res = await axiosTemplate('post', '/account/timekeeping/do-check-in', null, {});
                        
                        if (res.data.status === 200) {
                            showAlert('success', res.data.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('warning', res.data.message);
                            btn.prop('disabled', false).html('<i class="ki-filled ki-arrow-right me-2"></i>Check In');
                        }
                    }
                );
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi check in');
                console.error(error);
            }
        });
        
        // Xử lý check out
        $('#btn-check-out').on('click', async function() {
            if ($(this).hasClass('disabled')) return;
            
            try {
                Notiflix.Confirm.show(
                    'Xác nhận Check-out',
                    'Bạn có chắc chắn muốn check-out ngay bây giờ?',
                    'Đồng ý',
                    'Hủy bỏ',
                    async function() {
                        const btn = $('#btn-check-out');
                        btn.prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                        
                        const res = await axiosTemplate('post', '/account/timekeeping/do-check-out', null, {});
                        
                        if (res.data.status === 200) {
                            showAlert('success', res.data.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('warning', res.data.message);
                            btn.prop('disabled', false).html('<i class="ki-filled ki-arrow-left me-2"></i>Check Out');
                        }
                    }
                );
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi check out');
                console.error(error);
            }
        });
    });
</script>
@endpush