@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Bảng chấm công cá nhân
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <a href="{{ route('dashboard.profile.profile') }}" class="btn btn-sm btn-light">
                <i class="ki-outline ki-arrow-left me-1"></i>
                Quay lại
            </a>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Thống kê tháng hiện tại -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ki-outline ki-chart text-primary me-2"></i>Thống kê tháng {{ $monthStats['month'] }}/{{ $monthStats['year'] }}
                </h3>
            </div>
            <div class="card-body">
                <div class="grid !grid-cols-2 md:!grid-cols-4 gap-3 mb-6">
                    <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                        <p class="text-xs text-gray-600">Ngày làm việc</p>
                        <p class="text-xl font-bold text-primary">{{ $monthStats['presentDays'] }}/{{ $monthStats['workingDaysInMonth'] }}</p>
                        <div class="flex justify-between text-xs mt-1">
                            <span>Tỉ lệ:</span>
                            <span class="font-medium">{{ number_format(($monthStats['presentDays'] / max(1, $monthStats['workingDaysInMonth'])) * 100, 1) }}%</span>
                        </div>
                    </div>

                    <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                        <p class="text-xs text-gray-600">Tổng giờ làm việc</p>
                        <p class="text-xl font-bold text-success">{{ number_format($monthStats['totalWorkHours'], 2) }}</p>
                        <div class="flex justify-between text-xs mt-1">
                            <span>Giờ hợp lệ:</span>
                            <span class="font-medium">{{ number_format($monthStats['totalValidHours'], 2) }}</span>
                        </div>
                    </div>

                    <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                        <p class="text-xs text-gray-600">Đi trễ / Về sớm</p>
                        <p class="text-xl font-bold">
                            <span class="text-warning">{{ $monthStats['lateDays'] }}</span> / 
                            <span class="text-danger">{{ $monthStats['earlyLeaveDays'] }}</span>
                        </p>
                        <div class="flex justify-between text-xs mt-1">
                            <span>Đi trễ / Về sớm</span>
                            <p class="font-medium">
                                <span class="text-warning">{{ $monthStats['totalLateMinutes'] }}</span> / 
                                <span class="text-danger">{{ $monthStats['totalEarlyLeaveMinutes'] }}</span> phút    
                            </p>
                        </div>
                    </div>

                    <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                        <p class="text-xs text-gray-600">Giờ tăng ca</p>
                        <p class="text-xl font-bold text-success">{{ number_format($monthStats['totalOvertimeHours'], 2) }}</p>
                        <div class="flex justify-between text-xs mt-1">
                            <span>Vắng mặt:</span>
                            <span class="font-medium">{{ $monthStats['absentDays'] }} ngày</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bảng dữ liệu chấm công -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lịch sử chấm công</h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <input class="input input-sm" value="{{now()}}" type="text" id="month-filter" data-flatpickr="true" data-flatpickr-type="month" placeholder="Chọn tháng">
                    </div>
                    <a href="{{ route('dashboard.account.timekeeping.check-in-out') }}" class="btn btn-primary btn-sm" id="btn-check-in-out">
                        <i class="ki-filled ki-time me-1"></i>
                        Check In/Out
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-rounded table-row-bordered table-hover">
                        <thead>
                            <tr class="fw-bold fs-7 text-gray-800">
                                <th>Ngày</th>
                                <th>Ca làm việc</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Tổng giờ</th>
                                <th>Giờ hợp lệ</th>
                                <th>Trạng thái</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceRecords as $record)
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
                                    @if($record->overtime_hours > 0)
                                    <span class="badge badge-sm badge-primary ms-1">Tăng ca {{ number_format($record->overtime_hours, 2) }}h</span>
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>{{ number_format($record->total_hours, 2) }}</td>
                                <td>{{ number_format($record->valid_hours, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $record->getStatusClass() }}">
                                        {{ $record->getStatusText() }}
                                    </span>
                                    @if($record->forgot_checkout)
                                    <span class="badge badge-danger ms-1">Quên checkout</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->note)
                                    <span class="text-muted text-nowrap">{{ Str::limit($record->note, 30) }}</span>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Không có dữ liệu chấm công</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-5">
                    {{ $attendanceRecords->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection