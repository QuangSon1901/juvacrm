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
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Thống kê chấm công tháng hiện tại -->
        <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-calendar-tick size-6 shrink-0 text-success"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Ngày làm việc
                                </div>
                                <div class="text-2sm text-gray-700">
                                    {{$attendanceStats['presentDays'] ?? 0}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-calendar-cross size-6 shrink-0 text-danger"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Ngày vắng mặt
                                </div>
                                <div class="text-2sm text-gray-700">
                                    {{$attendanceStats['absentDays'] ?? 0}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Số lần đi trễ
                                </div>
                                <div class="text-2sm text-gray-700">
                                    {{$attendanceStats['lateDays'] ?? 0}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-time size-6 shrink-0 text-primary"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Tổng giờ làm việc
                                </div>
                                <div class="text-2sm text-gray-700">
                                    {{$attendanceStats['totalHours'] ?? 0}} giờ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bảng chấm công -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Lịch sử chấm công
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <input class="input input-sm" type="text" id="month-filter" data-flatpickr="true" data-flatpickr-type="month" placeholder="Chọn tháng">
                    </div>
                    <button class="btn btn-primary btn-sm" id="btn-check-in-out">
                        <i class="ki-filled ki-time me-1"></i>
                        Check In/Out
                    </button>
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
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ghi chú</span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceRecords as $index => $record)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ formatDateTime($record->work_date, 'd/m/Y') }}</td>
                                    <td>{{ $record->check_in_time ? formatDateTime($record->check_in_time, 'H:i:s') : '--:--:--' }}</td>
                                    <td>{{ $record->check_out_time ? formatDateTime($record->check_out_time, 'H:i:s') : '--:--:--' }}</td>
                                    <td>{{ number_format($record->total_hours, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            switch($record->status) {
                                                case 'present':
                                                    $statusClass = 'success';
                                                    $statusText = 'Có mặt';
                                                    break;
                                                case 'absent':
                                                    $statusClass = 'danger';
                                                    $statusText = 'Vắng mặt';
                                                    break;
                                                case 'late':
                                                    $statusClass = 'warning';
                                                    $statusText = 'Đi trễ';
                                                    break;
                                                case 'early_leave':
                                                    $statusClass = 'info';
                                                    $statusText = 'Về sớm';
                                                    break;
                                                default:
                                                    $statusClass = 'gray';
                                                    $statusText = $record->status;
                                            }
                                        @endphp
                                        
                                        <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>{{ $record->note ?: '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $attendanceRecords->links() }}
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
        // Khởi tạo flatpickr cho bộ lọc tháng
        flatpickrMake($("#month-filter"), 'month');
        
        // Xử lý khi thay đổi tháng
        $("#month-filter").on('change', function() {
            const selectedMonth = $(this).val();
            if (selectedMonth) {
                window.location.href = "{{ route('dashboard.profile.my-timesheet') }}?month=" + selectedMonth;
            }
        });
        
        // Xử lý nút Check In/Out
        $("#btn-check-in-out").on('click', function() {
            window.location.href = "{{ route('dashboard.account.timekeeping.check-in-out') }}";
        });
    });
</script>
@endpush