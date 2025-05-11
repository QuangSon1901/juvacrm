@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <!-- Header -->
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Tổng quan
            </h1>
            <div class="flex items-center gap-2">
                @if(isset($attendanceRecord) && $attendanceRecord->check_in_time && !$attendanceRecord->check_out_time)
                    <button id="btn-attendance" class="btn btn-sm btn-danger" onclick="doCheckOut()">
                        <i class="ki-filled ki-time me-1"></i>
                        Check Out
                    </button>
                    <span class="text-xs text-success">
                        <i class="ki-solid ki-arrow-right"></i> {{ \Carbon\Carbon::parse($attendanceRecord->check_in_time)->format('H:i') }}
                    </span>
                @elseif(isset($attendanceRecord) && $attendanceRecord->check_in_time && $attendanceRecord->check_out_time)
                    <button class="btn btn-sm btn-light" disabled>
                        <i class="ki-filled ki-check me-1"></i>
                        Đã chấm công
                    </button>
                    <span class="text-xs text-gray-600">
                        {{ \Carbon\Carbon::parse($attendanceRecord->check_in_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($attendanceRecord->check_out_time)->format('H:i') }}
                    </span>
                @elseif(isset($approvedSchedule) && $canCheckIn)
                    <button id="btn-attendance" class="btn btn-sm btn-success" onclick="doCheckIn()">
                        <i class="ki-filled ki-time me-1"></i>
                        Check In
                    </button>
                    <span class="text-xs text-gray-600">
                        Lịch: {{ \Carbon\Carbon::parse($approvedSchedule->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($approvedSchedule->end_time)->format('H:i') }}
                    </span>
                @elseif(isset($approvedSchedule) && !$canCheckIn)
                    <button class="btn btn-sm btn-light" disabled>
                        <i class="ki-filled ki-timer me-1"></i>
                        Chưa tới giờ làm
                    </button>
                    <span class="text-xs text-gray-600">
                        Lịch: {{ \Carbon\Carbon::parse($approvedSchedule->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($approvedSchedule->end_time)->format('H:i') }}
                    </span>
                @else
                    <button class="btn btn-sm btn-light" disabled>
                        <i class="ki-filled ki-calendar-x me-1"></i>
                        Không có lịch
                    </button>
                    <a href="{{ route('dashboard.profile.my-schedule') }}" class="text-xs text-primary">
                        Đăng ký lịch làm việc <i class="ki-solid ki-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <span class="text-sm text-gray-600">{{ now()->format('d/m/Y') }}</span>
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
        </div>
    </div>
    <!-- End of Header -->
</div>

<div class="container-fixed">
    <!-- Summary Cards -->
    <div class="grid !grid-cols-2 md:!grid-cols-4 gap-5 mb-7">
        <div class="card bg-blue-50">
            <div class="card-body p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Hợp đồng đang thực hiện</p>
                        <h2 class="text-2xl font-bold">{{ $contractStats['active'] }}</h2>
                        <p class="text-xs text-gray-500">Tổng số: {{ $contractStats['total'] }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="ki-filled ki-document text-blue-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-yellow-50">
            <div class="card-body p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Lịch chờ duyệt</p>
                        <h2 class="text-2xl font-bold">{{ $pendingSchedulesCount }}</h2>
                        <p class="text-xs text-gray-500">Yêu cầu hủy: {{ $cancelRequestsCount }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="ki-filled ki-calendar-tick text-yellow-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-green-50">
            <div class="card-body p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Công việc trong tuần</p>
                        <h2 class="text-2xl font-bold">{{ $taskStats['due_this_week'] }}</h2>
                        <p class="text-xs text-gray-500">Quá hạn: {{ $taskStats['overdue'] }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="ki-filled ki-calendar-8 text-green-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-orange-50">
            <div class="card-body p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Khách hàng cần chăm sóc</p>
                        <h2 class="text-2xl font-bold">{{ $customerStats['need_follow_up'] }}</h2>
                        <p class="text-xs text-gray-500">Hôm nay: {{ $customerStats['new_today'] }} mới</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="ki-filled ki-user-square text-orange-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-purple-50">
            <div class="card-body p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Tổng doanh thu</p>
                        <h2 class="text-2xl font-bold">{{ number_format($financialStats['total_income'], 0, ',', '.') }}đ</h2>
                        <p class="text-xs text-gray-500">Dư: {{ number_format($financialStats['balance'], 0, ',', '.') }}đ</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <i class="ki-filled ki-dollar text-purple-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Weather Card -->
    <div id="weather-card" class="card h-32 relative overflow-hidden bg-center mb-7 hidden" style="background-image: url({{asset('assets/images/background/hochiminhcity.png')}});">
        <div class="absolute inset-0 opacity-60 bg-black"></div>
        <div class="card-body z-10 flex items-center justify-between">
            <div class="text-white">
                <p class="text-lg font-semibold">Thời tiết hôm nay</p>
                <p class="status text-sm">Trời nắng</p>
                <p class="city text-sm font-medium">Hồ Chí Minh</p>
            </div>
            <div class="flex items-center">
                <img class="icon h-16" src="https://openweathermap.org/img/wn/02d@2x.png" alt="Thời tiết">
                <span class="text-white text-4xl temp">22°C</span>
            </div>
        </div>
    </div>
    
    <!-- Contract Overview Section -->
    <div class="card mb-7">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ki-filled ki-cheque text-primary text-2xl"></i>&nbsp;Tổng quan hợp đồng
            </h3>
            <a href="{{ route('dashboard.contract.contract') }}" class="btn btn-sm btn-light">
                Xem tất cả
            </a>
        </div>
        <div class="card-body">
            <div class="grid !grid-cols-1 md:!grid-cols-4 gap-4 mb-6">
                <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-blue-100 dark:bg-blue-900 rounded-xl gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Tổng giá trị hợp đồng
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ number_format($contractStats['total_value'], 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-green-100 dark:bg-green-900 rounded-xl gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Đã thanh toán
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ number_format($contractStats['paid_value'], 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-pink-100 dark:bg-pink-900 rounded-xl gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Chưa thanh toán
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ number_format($contractStats['total_value'] - $contractStats['paid_value'], 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 bg-purple-100 dark:bg-purple-900 rounded-xl gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Thanh toán trong 7 ngày tới
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ $financialStats['pending_payments'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-medium text-sm mb-2">Trạng thái hợp đồng</h4>
                <div class="flex items-center gap-2 mb-2">
                    <div class="bg-blue-500 h-2.5 rounded-sm" style="width: {{ ($contractStats['active'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                    <div class="bg-green-500 h-2.5 rounded-sm" style="width: {{ ($contractStats['completed'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                    <div class="bg-gray-500 h-2.5 rounded-sm" style="width: {{ ($contractStats['pending_approval'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                    <div class="bg-red-500 h-2.5 rounded-sm" style="width: {{ ($contractStats['canceled'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                </div>
                <div class="flex items-center flex-wrap gap-4">
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-primary"></span>
                        <span class="text-xs">Đang thực hiện ({{ $contractStats['active'] }})</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-success"></span>
                        <span class="text-xs">Hoàn thành ({{ $contractStats['completed'] }})</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-gray"></span>
                        <span class="text-xs">Chờ duyệt ({{ $contractStats['pending_approval'] }})</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="badge badge-dot size-2 badge-danger"></span>
                        <span class="text-xs">Đã hủy ({{ $contractStats['canceled'] }})</span>
                    </div>
                </div>
            </div>
            
            <h4 class="font-medium text-sm mb-2">Hợp đồng gần đây</h4>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Số HĐ</th>
                            <th>Khách hàng</th>
                            <th>Nhân viên</th>
                            <th>Giá trị</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contractStats['recent'] as $contract)
                        <tr>
                            <td>
                                <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary hover:text-primary-hover">
                                    {{ $contract->contract_number }}
                                </a>
                            </td>
                            <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                            <td>{{ $contract->user->name ?? 'N/A' }}</td>
                            <td>{{ number_format($contract->total_value, 0, ',', '.') }}đ</td>
                            <td class="text-gray-800 font-normal">
                                @if ($contract->status == 0)
                                    <span class="badge badge-sm badge-outline badge-warning">Chờ duyệt</span>
                                @elseif ($contract->status == 1)
                                    <span class="badge badge-sm badge-outline badge-primary">Đang triển khai</span>
                                @elseif ($contract->status == 2)
                                    <span class="badge badge-sm badge-outline badge-neutral">Đã kết thúc</span>
                                @elseif ($contract->status == 3)
                                    <span class="badge badge-sm badge-outline badge-danger">Đã hủy</span>
                                @endif    
                            </td>
                            <td>{{ $contract->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Task Overview Section -->
    <div class="card mb-7">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ki-filled ki-kanban text-green-900 text-2xl"></i>&nbsp;<span>Tổng quan công việc</span>
            </h3>
            <a class="btn btn-sm btn-light" href="{{ route('dashboard.account.task.task') }}">
                Xem tất cả
            </a>
        </div>
        <div class="card-body">
            <div class="grid !grid-cols-2 md:!grid-cols-4 gap-4 mb-6">
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-red-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-red-100 rounded-full">
                            <i class="ki-filled ki-calendar-tick text-red-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Task quá hạn
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ $taskStats['overdue'] }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-orange-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-orange-100 rounded-full">
                            <i class="ki-filled ki-timer text-orange-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Task hôm nay
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ $taskStats['due_today'] }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-green-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-green-100 rounded-full">
                            <i class="ki-filled ki-check-circle text-green-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Task đã hoàn thành
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ $taskStats['completed'] }}/{{ $taskStats['total'] }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-purple-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-purple-100 rounded-full">
                            <i class="ki-filled ki-arrow-up-down text-purple-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Task cần chỉnh sửa
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ $taskStats['need_revision'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-medium text-sm mb-3">Công việc gần đây</h4>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tên công việc</th>
                                <th>Người quản lý</th>
                                <th>Trạng thái</th>
                                <th>Hạn xử lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($taskStats['recent_tasks'] as $task)
                            <tr>
                                <td>
                                    <a class="text-primary hover:text-primary-hover">
                                        {{ \Illuminate\Support\Str::limit($task->name, 40) }}
                                    </a>
                                </td>
                                <td>{{ $task->assign->name ?? 'Chưa gán' }}</td>
                                <td>
                                    <span class="badge badge-{{ $task->status->color ?? 'gray' }}">
                                        {{ $task->status->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($task->due_date)
                                        @if(strtotime($task->due_date) < time() && $task->status_id < 4)
                                            <span class="text-red-600">
                                                {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                            </span>
                                        @else
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                        @endif
                                    @else
                                        Không có
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Customer Support Section -->
    <div class="card mb-7">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ki-filled ki-user text-blue-600 text-2xl"></i>&nbsp;Chăm sóc khách hàng
            </h3>
            <a href="{{ route('dashboard.customer.support.customer-support') }}" class="btn btn-sm btn-light">Xem tất cả</a>
        </div>
        <div class="card-body">
            <div class="grid !grid-cols-2 md:!grid-cols-3 gap-3 mb-6">
                <div class="bg-orange-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600">Nguồn khách hàng mới</p>
                    <p class="text-xl font-bold text-orange-700">{{ $customerStats['leads'] }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600">Khách hàng đang hỗ trợ</p>
                    <p class="text-xl font-bold text-green-700">{{ $customerStats['prospects'] }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600">Khách hàng đã sử dụng dịch vụ</p>
                    <p class="text-xl font-bold text-blue-700">{{ $customerStats['customers'] }}</p>
                </div>
            </div>
            
            <div class="grid !grid-cols-1 md:!grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-medium text-sm mb-3">Cuộc hẹn sắp tới</h4>
                    <div class="space-y-3">
                        @foreach($customerStats['upcoming_appointments'] as $appointment)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-sm">{{ $appointment->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $appointment->customer->name ?? 'N/A' }}</p>
                                </div>
                                <span class="badge badge-{{ $appointment->color }}">
                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('d/m H:i') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                        
                        @if(count($customerStats['upcoming_appointments']) === 0)
                            <p class="text-center text-gray-500 text-sm">Không có cuộc hẹn sắp tới</p>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-sm mb-3">Khách hàng cần chăm sóc</h4>
                    <div class="border border-orange-200 rounded-lg p-4 bg-orange-50">
                        <div class="flex items-center justify-between mb-3">
                            <p class="font-medium">Khách hàng không tương tác > 14 ngày</p>
                            <span class="badge badge-warning">{{ $customerStats['need_follow_up'] }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Cần liên hệ lại để duy trì mối quan hệ và tăng khả năng ký hợp đồng</p>
                        <a href="{{ route('dashboard.customer.support.customer-support') }}?filter[interaction]=old" class="btn btn-sm btn-warning">Xem danh sách</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Overview Section -->
    <div class="card mb-7">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ki-filled ki-dollar text-green-900 text-2xl"></i>&nbsp;<span>Tổng quan tài chính</span>
            </h3>
            <a class="btn btn-sm btn-light" href="{{ route('dashboard.accounting.report.financial') }}">
                Xem báo cáo
            </a>
        </div>
        <div class="card-body">
            <div class="grid !grid-cols-2 md:!grid-cols-4 gap-4 mb-6">
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-green-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-green-100 rounded-full">
                            <i class="ki-filled ki-arrow-up text-green-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Tổng thu
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ number_format($financialStats['total_income'], 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-red-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-red-100 rounded-full">
                            <i class="ki-filled ki-arrow-down text-red-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Tổng chi
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ number_format($financialStats['total_expense'], 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-blue-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-blue-100 rounded-full">
                            <i class="ki-filled ki-chart-line text-blue-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Thu tháng này
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ number_format($financialStats['this_month_income'], 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-orange-50 gap-2 p-3.5">
                    <div class="flex items-center flex-wrap gap-3.5">
                        <div class="h-8 w-8 flex items-center justify-center bg-orange-100 rounded-full">
                            <i class="ki-filled ki-credit-cart text-orange-600"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5">
                                <span class="text-2sm font-medium text-gray-600">
                                    Chi tháng này
                                </span>
                            </div>
                            <span class="text-2sm font-bold text-gray-900">
                                {{ number_format($financialStats['this_month_expense'], 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="font-medium text-sm mb-3">Giao dịch gần đây</h4>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Loại</th>
                                <th>Danh mục</th>
                                <th>Đối tượng</th>
                                <th>Số tiền</th>
                                <th>Ngày GD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($financialStats['recent_transactions'] as $transaction)
                            <tr>
                                <td>
                                    @if($transaction->type == 0)
                                        <span class="badge badge-success">Thu</span>
                                    @else
                                        <span class="badge badge-danger">Chi</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->category->name ?? 'N/A' }}</td>
                                <td>
                                    @if($transaction->target_client_id)
                                        {{ $transaction->targetClient->name ?? 'N/A' }}
                                    @elseif($transaction->target_employee_id)
                                        {{ $transaction->targetEmployee->name ?? 'N/A' }}
                                    @else
                                        {{ $transaction->target_other ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="{{ $transaction->type == 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($transaction->amount, 0, ',', '.') }}đ
                                </td>
                                <td>{{ \Carbon\Carbon::parse($transaction->paid_date)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <h4 class="font-medium text-sm mb-3">Biểu đồ thu chi theo tháng</h4>
            <div class="h-60">
                <canvas id="financialChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Employee Overview Section -->
    <div class="card mb-7">
        <div class="card-header">
            <div class="flex items-center">
                <h3 class="card-title">
                    <i class="ki-filled ki-people text-blue-800 text-2xl"></i>&nbsp;Tổng quan nhân sự
                </h3>
                &nbsp;
                <span class="badge badge-xs badge-primary badge-outline">{{ $employeeStats['total'] }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="grid !grid-cols-3 gap-3 mb-6">
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600">Tổng nhân viên</p>
                    <p class="text-xl font-bold text-blue-700">{{ $employeeStats['total'] }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600">Đi làm hôm nay</p>
                    <p class="text-xl font-bold text-green-700">{{ $employeeStats['active_today'] }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600">Đang làm việc</p>
                    <p class="text-xl font-bold text-purple-700">{{ $employeeStats['working_now'] }}</p>
                </div>
            </div>
            
            <h4 class="font-medium text-sm mb-3">Top nhân viên hoàn thành công việc</h4>
            <div class="grid !grid-cols-1 md:!grid-cols-2 gap-3">
                @foreach($employeeStats['top_performers'] as $employee)
                <div class="flex items-center justify-between gap-2.5 border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center gap-2.5">
                        <div>
                            <img class="h-9 rounded-full border border-gray-300" src="{{ $employee->avatar ?? asset('assets/images/logo/favicon.png') }}">
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <a class="flex items-center gap-1.5 leading-none font-medium text-sm text-gray-900 hover:text-primary" href="{{ route('dashboard.account.member.detail', $employee->id) }}">
                                {{ $employee->name }}
                            </a>
                            <span class="text-2xs text-gray-700">
                                {{ $employee->task_mission_reports_count }} báo cáo hoàn thành
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Lịch làm việc chờ duyệt Section -->
    @if($pendingSchedulesCount > 0 || $cancelRequestsCount > 0)
    <div class="card mb-7">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ki-filled ki-calendar text-warning text-2xl"></i>&nbsp;Lịch làm việc cần xử lý
            </h3>
            <a href="{{ route('dashboard.account.schedule.schedule') }}" class="btn btn-sm btn-primary">
                Quản lý lịch
            </a>
        </div>
        <div class="card-body">
            @if(count($pendingSchedules) > 0)
                <h4 class="font-medium text-sm mb-3">Lịch đang chờ duyệt ({{ $pendingSchedulesCount }})</h4>
                <div class="overflow-x-auto mb-5">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Ngày</th>
                                <th>Thời gian</th>
                                <th>Giờ làm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingSchedules as $schedule)
                            <tr>
                                <td>{{ $schedule->user->name }}</td>
                                <td>{{ formatDateTime($schedule->schedule_date, 'd/m/Y') }}</td>
                                <td>{{ formatDateTime($schedule->start_time, 'H:i') }} - {{ formatDateTime($schedule->end_time, 'H:i') }}</td>
                                <td>{{ number_format($schedule->total_hours, 2) }} giờ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($pendingSchedulesCount > 5)
                <div class="text-center">
                    <a href="{{ route('dashboard.account.schedule.schedule') }}?filter[status]=pending" class="btn btn-sm btn-light">
                        Xem tất cả {{ $pendingSchedulesCount }} lịch chờ duyệt
                    </a>
                </div>
                @endif
            @endif
            
            @if($cancelRequestsCount > 0)
                <div class="mt-4">
                    <div class="alert alert-warning d-flex align-items-center p-5">
                        <i class="ki-solid ki-information-5 fs-2hx text-warning me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-warning">Có {{ $cancelRequestsCount }} yêu cầu hủy lịch cần xử lý</h4>
                            <span>Vui lòng kiểm tra và xử lý các yêu cầu hủy lịch từ nhân viên</span>
                        </div>
                        <a href="{{ route('dashboard.account.schedule.schedule') }}?filter[status]=cancel_requested" class="btn btn-sm btn-warning ms-auto">
                            Xem yêu cầu hủy
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push("scripts")
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // fetchLocation();
        initCharts();
    });

    async function fetchLocation() {
        let method = "get",
            url = `https://api.ipapi.com/api/check?access_key=${ipapiAccessKey}`,
            data = null,
            params = {};
        let res = await axiosTemplate(method, url, params, data);
        let {
            latitude,
            longitude
        } = res.data;

        fetchWeather(latitude, longitude);
    }

    async function fetchWeather(lat, lon) {
        let method = "get",
            url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${openWeatherApiKey}&units=metric&lang=vi`,
            data = null,
            params = {};
        let res = await axiosTemplate(method, url, params, data);
        switch (res.status) {
            case 200:
                if (res.data.cod == 200) {
                    $("#weather-card").removeClass("hidden");
                    $("#weather-card .icon").attr("src", `https://openweathermap.org/img/wn/${res.data.weather[0].icon}@2x.png`);
                    $("#weather-card .temp").text(res.data.main.temp + "°C");
                    $("#weather-card .status").text(res.data.weather[0].description);
                    $("#weather-card .city").text(res.data.name);
                }
                break;
            default:
                break;
        }
    }

    async function doCheckIn() {
    try {
        // Hiển thị hộp thoại xác nhận
        Notiflix.Confirm.show(
            'Xác nhận Check-in',
            'Bạn có chắc chắn muốn check-in ngay bây giờ?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                // Vô hiệu hóa nút khi đang xử lý
                const btn = document.getElementById('btn-attendance');
                btn.disabled = true;
                btn.innerHTML = '<i class="ki-solid ki-spinner ki-spin me-1"></i> Đang xử lý...';
                
                const res = await axiosTemplate('post', '/account/timekeeping/do-check-in', null, {});
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    // Tải lại trang sau 1.5 giây
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('warning', res.data.message);
                    // Khôi phục nút
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ki-filled ki-time me-1"></i> Check In';
                }
            },
            function() {
                // Người dùng đã hủy bỏ
            }
        );
    } catch (error) {
        showAlert('error', 'Đã xảy ra lỗi khi check in');
        console.error(error);
    }
}
    
    // Función para realizar Check Out
    async function doCheckOut() {
    try {
        // Hiển thị hộp thoại xác nhận
        Notiflix.Confirm.show(
            'Xác nhận Check-out',
            'Bạn có chắc chắn muốn check-out ngay bây giờ?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                // Vô hiệu hóa nút khi đang xử lý
                const btn = document.getElementById('btn-attendance');
                btn.disabled = true;
                btn.innerHTML = '<i class="ki-solid ki-spinner ki-spin me-1"></i> Đang xử lý...';
                
                const res = await axiosTemplate('post', '/account/timekeeping/do-check-out', null, {});
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    // Tải lại trang sau 1.5 giây
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('warning', res.data.message);
                    // Khôi phục nút
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ki-filled ki-time me-1"></i> Check Out';
                }
            },
            function() {
                // Người dùng đã hủy bỏ
            }
        );
    } catch (error) {
        showAlert('error', 'Đã xảy ra lỗi khi check out');
        console.error(error);
    }
}
    
    function initCharts() {
        // Financial Chart
        const finCtx = document.getElementById('financialChart').getContext('2d');
        const financialChart = new Chart(finCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($financialTrends as $trend)
                        '{{ $trend['month'] }}',
                    @endforeach
                ],
                datasets: [
                    {
                        label: 'Thu',
                        data: [
                            @foreach($financialTrends as $trend)
                                {{ $trend['income'] }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: '#10b981',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Chi',
                        data: [
                            @foreach($financialTrends as $trend)
                                {{ $trend['expense'] }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(239, 68, 68, 0.2)',
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush