@extends('dashboard.layouts.layout')
@section('dashboard_content')
<style>
    /* Thêm CSS này vào file hoặc thẻ style */
.contract-list-content {
    display: none;
}

.contract-list-content.active {
    display: block;
}

.contract-filter-btn.active {
    position: relative;
    font-weight: 600;
    z-index: 1;
}

.contract-filter-btn.active::after {
    content: "";
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: currentColor;
}
</style>
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

@if(hasPermission('view-dashboard'))
<div class="container-fixed">
    <!-- Summary Cards -->
    <div class="grid !grid-cols-2 md:!grid-cols-4 gap-5 mb-7">
        @if(hasPermission('view-contract'))
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
        @endif

        @if(hasPermission('view-schedule'))
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
        @endif
        
        @if(hasPermission('view-task'))
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
        @endif
        
        @if(hasPermission('view-customer'))
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
        @endif
        
        @if(hasPermission('view-transaction'))
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
        @endif
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
    
    @if(hasPermission('view-contract'))
    <div class="card mb-7">
    <div class="card-header justify-between">
        <h3 class="card-title">
            <i class="ki-filled ki-cheque text-primary text-2xl"></i>&nbsp;Tổng quan hợp đồng
        </h3>
        <div class="flex gap-2">
            <a href="{{ route('dashboard.contract.contract') }}?filter[expiring]=1" class="btn btn-sm btn-danger">
                <i class="ki-filled ki-timer"></i> Sắp quá hạn ({{ count($expiringContracts) }})
            </a>
            <a href="{{ route('dashboard.contract.contract') }}" class="btn btn-sm btn-light">
                <i class="ki-filled ki-document"></i> Xem tất cả
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- KPI chính - Thống kê chính về hợp đồng -->
        <div class="grid !grid-cols-2 md:!grid-cols-5 gap-3 mb-6">
            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600">Tổng hợp đồng</p>
                <p class="text-xl font-bold text-primary">{{ $contractStats['total'] }}</p>
                <div class="flex justify-between text-xs mt-1">
                    <span>Đang triển khai:</span>
                    <span class="font-medium">{{ $contractStats['active'] }}</span>
                </div>
            </div>
            
            <div class="bg-success-50 rounded-lg p-3 border border-success-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600">Tổng giá trị</p>
                <p class="text-xl font-bold text-success">{{ number_format($contractStats['total_value']/1000000, 1) }}M</p>
                <div class="flex justify-between text-xs mt-1">
                    <span>Đã thanh toán:</span>
                    <span class="font-medium">{{ $contractStats['total_value'] == 0 ? 0 : number_format(($contractStats['paid_value']/$contractStats['total_value'])*100, 0) }}%</span>
                </div>
            </div>
            
            <div class="bg-warning-50 rounded-lg p-3 border border-warning-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600">Cần thanh toán</p>
                <p class="text-xl font-bold text-warning">{{ $financialStats['pending_payments'] }}</p>
                <div class="flex justify-between text-xs mt-1">
                    <span>7 ngày tới:</span>
                    <span class="font-medium">{{ number_format($contractStats['total_value'] - $contractStats['paid_value'], 0, ',', '.') }}đ</span>
                </div>
            </div>
            
            <div class="bg-danger-50 rounded-lg p-3 border border-danger-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600">Sắp quá hạn</p>
                <p class="text-xl font-bold text-danger">{{ count($expiringContracts) }}</p>
                <a href="{{ route('dashboard.contract.contract') }}?filter[expiring]=1" class="text-xs text-danger mt-1">
                    <i class="ki-filled ki-arrow-right"></i> Xem ngay
                </a>
            </div>
            
            <div class="bg-info-50 rounded-lg p-3 border border-info-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600">Task hoàn thành</p>
                <p class="text-xl font-bold text-info">{{ count($completedTaskContracts) }}</p>
                <a href="{{ route('dashboard.contract.contract') }}?filter[completed_tasks]=1" class="text-xs text-info mt-1">
                    <i class="ki-filled ki-arrow-right"></i> Xác nhận ngay
                </a>
            </div>
        </div>

        <!-- Biểu đồ tiến độ và phân loại hợp đồng -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Biểu đồ phân loại hợp đồng -->
            <div class="card card-bordered shadow-sm">
                <div class="card-header bg-opacity-10 py-3">
                    <h3 class="card-title text-gray-700 text-sm font-medium">
                        Phân loại hợp đồng
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs flex items-center"><span class="w-2 h-2 bg-primary rounded-full mr-1"></span> Đang triển khai</span>
                                <div class="flex items-center">
                                    <span class="text-xs font-medium">{{ $contractStats['active'] }}</span>
                                    <a href="{{ route('dashboard.contract.contract') }}?filter[status]=1" class="ml-2 text-primary">
                                        <i class="ki-filled ki-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-primary h-1.5 rounded-full" style="width: {{ ($contractStats['active'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs flex items-center"><span class="w-2 h-2 bg-warning rounded-full mr-1"></span> Chờ duyệt</span>
                                <div class="flex items-center">
                                    <span class="text-xs font-medium">{{ $contractStats['pending_approval'] }}</span>
                                    <a href="{{ route('dashboard.contract.contract') }}?filter[status]=0" class="ml-2 text-warning">
                                        <i class="ki-filled ki-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-warning h-1.5 rounded-full" style="width: {{ ($contractStats['pending_approval'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs flex items-center"><span class="w-2 h-2 bg-success rounded-full mr-1"></span> Đã hoàn thành</span>
                                <div class="flex items-center">
                                    <span class="text-xs font-medium">{{ $contractStats['completed'] }}</span>
                                    <a href="{{ route('dashboard.contract.contract') }}?filter[status]=2" class="ml-2 text-success">
                                        <i class="ki-filled ki-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-success h-1.5 rounded-full" style="width: {{ ($contractStats['completed'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs flex items-center"><span class="w-2 h-2 bg-danger rounded-full mr-1"></span> Đã hủy</span>
                                <div class="flex items-center">
                                    <span class="text-xs font-medium">{{ $contractStats['canceled'] }}</span>
                                    <a href="{{ route('dashboard.contract.contract') }}?filter[status]=3" class="ml-2 text-danger">
                                        <i class="ki-filled ki-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-danger h-1.5 rounded-full" style="width: {{ ($contractStats['canceled'] / max($contractStats['total'], 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tiến độ thanh toán hợp đồng -->
            <div class="card card-bordered shadow-sm">
                <div class="card-header bg-opacity-10 py-3">
                    <h3 class="card-title text-gray-700 text-sm font-medium">
                        Tiến độ thanh toán
                    </h3>
                </div>
                <div class="card-body p-4">
                    <!-- Visualize payment progress -->
                    <div class="mb-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-xs">Tiến độ tổng thanh toán</span>
                            <span class="text-xs font-medium">{{ number_format(($contractStats['paid_value'] / max($contractStats['total_value'], 1)) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full h-6 bg-gray-200 rounded-full relative overflow-hidden">
                            <div class="bg-success h-full rounded-full absolute left-0" style="width: {{ ($contractStats['paid_value'] / max($contractStats['total_value'], 1)) * 100 }}%"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-xs font-medium">
                                {{ number_format($contractStats['paid_value'], 0, ',', '.') }}đ / {{ number_format($contractStats['total_value'], 0, ',', '.') }}đ
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payments due soon -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Thanh toán sắp đến hạn</h4>
                        <div class="space-y-2 max-h-[100px] overflow-y-auto pr-1">
                            @if(isset($paymentDueContracts) && count($paymentDueContracts) > 0)
                                @foreach($paymentDueContracts->take(3) as $payment)
                                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-md border border-gray-200">
                                        <div>
                                            <span class="text-xs font-medium">{{ $payment->contract->contract_number }}</span>
                                            <p class="text-xs text-gray-500">{{ number_format($payment->price, 0, ',', '.') }}đ</p>
                                        </div>
                                        <div class="text-xs text-right">
                                            <p class="{{ \Carbon\Carbon::parse($payment->due_date)->isPast() ? 'text-danger' : 'text-warning' }}">
                                                {{ \Carbon\Carbon::parse($payment->due_date)->diffForHumans() }}
                                            </p>
                                            <p class="text-gray-500">{{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                @if(count($paymentDueContracts) > 3)
                                    <a href="{{ route('dashboard.accounting.deposit-receipt.deposit-receipt') }}" class="text-center block text-xs text-primary font-medium">
                                        + {{ count($paymentDueContracts) - 3 }} thanh toán khác
                                    </a>
                                @endif
                            @else
                                <div class="text-center py-3 text-gray-500 text-xs">
                                    <i class="ki-filled ki-information-5 fs-1x mb-1"></i>
                                    <p>Không có thanh toán nào sắp đến hạn</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Custom tabs for contract lists -->
        <div class="card card-bordered shadow-sm">
            <div class="card-header flex flex-wrap gap-2">
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-light-danger contract-filter-btn active" data-filter="expiring">
                        <i class="ki-filled ki-timer me-1"></i>Sắp quá hạn
                        <span class="badge badge-circle badge-danger ms-1">{{ count($expiringContracts) }}</span>
                    </button>
                    <button class="btn btn-sm btn-light-success contract-filter-btn" data-filter="completed">
                        <i class="ki-filled ki-check-squared me-1"></i>Task hoàn thành
                        <span class="badge badge-circle badge-success ms-1">{{ count($completedTaskContracts) }}</span>
                    </button>
                    <button class="btn btn-sm btn-light-primary contract-filter-btn" data-filter="my">
                        <i class="ki-filled ki-user me-1"></i>Hợp đồng của tôi
                    </button>
                    <button class="btn btn-sm btn-light-warning contract-filter-btn" data-filter="pending">
                        <i class="ki-filled ki-calendar me-1"></i>Chờ duyệt
                        <span class="badge badge-circle badge-warning ms-1">{{ count($pendingContracts) }}</span>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Contract List Content -->
                <div id="contract-list-container">
                    <!-- Tab Sắp quá hạn -->
                    <div class="contract-list-content active" id="contracts-expiring">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3 m-0">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="min-w-120px">Hợp đồng</th>
                                        <th>Khách hàng</th>
                                        <th>Nhân viên phụ trách</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Tiến độ</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($expiringContracts) && count($expiringContracts) > 0)
                                        @foreach($expiringContracts as $contract)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary fw-bold">
                                                        {{ $contract->contract_number }}
                                                    </a>
                                                    <div class="text-muted fs-7">{{ \Illuminate\Support\Str::limit($contract->name, 30) }}</div>
                                                </td>
                                                <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                                <td>{{ $contract->user->name ?? 'Chưa phân công' }}</td>
                                                <td>
                                                    <span class="badge badge-danger">
                                                        {{ \Carbon\Carbon::parse($contract->expiry_date)->format('d/m/Y') }}
                                                    </span>
                                                    <div class="text-muted fs-7">
                                                        {{ \Carbon\Carbon::parse($contract->expiry_date)->diffForHumans() }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $tasks = $contract->tasks->where('is_active', 1);
                                                        $totalTasks = $tasks->count();
                                                        $completedTasks = $tasks->where('status_id', '>=', 4)->count();
                                                        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress h-6px w-100 bg-light-primary">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $taskProgress }}%" aria-valuenow="{{ $taskProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="ms-3 text-muted">{{ $taskProgress }}%</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="btn btn-sm btn-icon btn-light-primary">
                                                        <i class="ki-filled ki-eye fs-2"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Không có hợp đồng nào sắp hết hạn</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Tab Task hoàn thành -->
                    <div class="contract-list-content hidden" id="contracts-completed">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3 m-0">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="min-w-120px">Hợp đồng</th>
                                        <th>Khách hàng</th>
                                        <th>Nhân viên phụ trách</th>
                                        <th>Task hoàn thành</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($completedTaskContracts) && count($completedTaskContracts) > 0)
                                        @foreach($completedTaskContracts as $contract)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary fw-bold">
                                                        {{ $contract->contract_number }}
                                                    </a>
                                                    <div class="text-muted fs-7">{{ \Illuminate\Support\Str::limit($contract->name, 30) }}</div>
                                                </td>
                                                <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                                <td>{{ $contract->user->name ?? 'Chưa phân công' }}</td>
                                                <td>
                                                    <span class="badge badge-success">{{ $contract->completed_tasks_count }} task</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">Đang triển khai</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="btn btn-sm btn-icon btn-light-primary me-1">
                                                        <i class="ki-filled ki-eye fs-2"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-light-success" onclick="completeContract({{ $contract->id }})">
                                                        <i class="ki-filled ki-check fs-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Không có hợp đồng nào với task hoàn thành</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Tab Hợp đồng của tôi -->
                    <div class="contract-list-content hidden" id="contracts-my">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3 m-0">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="min-w-120px">Hợp đồng</th>
                                        <th>Khách hàng</th>
                                        <th>Giá trị</th>
                                        <th>Ngày hiệu lực</th>
                                        <th>Tiến độ</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($myContracts) && count($myContracts) > 0)
                                        @foreach($myContracts as $contract)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary fw-bold">
                                                        {{ $contract->contract_number }}
                                                    </a>
                                                    <div class="text-muted fs-7">{{ \Illuminate\Support\Str::limit($contract->name, 30) }}</div>
                                                </td>
                                                <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($contract->total_value, 0, ',', '.') }}đ</td>
                                                <td>
                                                    @if($contract->effective_date)
                                                        {{ \Carbon\Carbon::parse($contract->effective_date)->format('d/m/Y') }}
                                                    @else
                                                        <span class="text-muted">Chưa có</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $tasks = $contract->tasks->where('is_active', 1);
                                                        $totalTasks = $tasks->count();
                                                        $completedTasks = $tasks->where('status_id', '>=', 4)->count();
                                                        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress h-6px w-100 bg-light-primary">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $taskProgress }}%" aria-valuenow="{{ $taskProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="ms-3 text-muted">{{ $taskProgress }}%</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="btn btn-sm btn-icon btn-light-primary">
                                                        <i class="ki-filled ki-eye fs-2"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Bạn chưa được phân công quản lý hợp đồng nào</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Tab Chờ duyệt -->
                    <div class="contract-list-content hidden" id="contracts-pending">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3 m-0">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="min-w-120px">Hợp đồng</th>
                                        <th>Khách hàng</th>
                                        <th>Người tạo</th>
                                        <th>Giá trị</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($pendingContracts) && count($pendingContracts) > 0)
                                        @foreach($pendingContracts as $contract)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary fw-bold">
                                                        {{ $contract->contract_number }}
                                                    </a>
                                                    <div class="text-muted fs-7">{{ \Illuminate\Support\Str::limit($contract->name, 30) }}</div>
                                                </td>
                                                <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                                <td>{{ $contract->creator->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($contract->total_value, 0, ',', '.') }}đ</td>
                                                <td>{{ \Carbon\Carbon::parse($contract->created_at)->format('d/m/Y') }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="btn btn-sm btn-icon btn-light-primary me-1">
                                                        <i class="ki-filled ki-eye fs-2"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-icon btn-light-success" onclick="createContractTasks({{ $contract->id }})">
                                                        <i class="ki-filled ki-check fs-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Không có hợp đồng nào đang chờ duyệt</td>
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
</div>
    @endif
    
    @if(hasPermission('view-task'))
    <!-- Task Overview Section -->
    <!-- Task Overview Section - Enhanced UI with Filter Links -->
<div class="card mb-7">
    <div class="card-header justify-between">
        <h3 class="card-title">
            <i class="ki-filled ki-kanban text-green-900 text-2xl"></i>&nbsp;<span>Tổng quan công việc</span>
        </h3>
        <div class="flex gap-2">
            <a class="btn btn-sm btn-light" href="{{ route('dashboard.account.task.task') }}">
                <i class="ki-filled ki-eye"></i> Xem tất cả
            </a>
            <a class="btn btn-sm btn-primary" href="{{ route('dashboard.account.task.task') }}?filter[status_task]=7">
                <i class="ki-filled ki-flag"></i> Cần chỉnh sửa
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Task stats overview -->
        <div class="grid !grid-cols-2 md:!grid-cols-4 gap-4 mb-6">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-red-50 gap-2 p-3.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <div class="h-10 w-10 flex items-center justify-center bg-red-100 rounded-full">
                        <i class="ki-filled ki-calendar-tick text-red-600 text-lg"></i>
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
                        @if($taskStats['overdue'] > 0)
                        <a href="{{ route('dashboard.account.task.task') }}?filter[status_task]=6" class="text-xs text-red-600 hover:underline mt-1">
                            <i class="ki-filled ki-arrow-right-circle"></i> Xem ngay
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-orange-50 gap-2 p-3.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <div class="h-10 w-10 flex items-center justify-center bg-orange-100 rounded-full">
                        <i class="ki-filled ki-timer text-orange-600 text-lg"></i>
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
                        @if($taskStats['due_today'] > 0)
                        <a href="{{ route('dashboard.account.task.task') }}?filter[due_today]=1" class="text-xs text-orange-600 hover:underline mt-1">
                            <i class="ki-filled ki-arrow-right-circle"></i> Xem danh sách
                        </a>
                        @else
                        <span class="text-xs text-gray-500 mt-1">Tuần này: {{ $taskStats['due_this_week'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-green-50 gap-2 p-3.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <div class="h-10 w-10 flex items-center justify-center bg-green-100 rounded-full">
                        <i class="ki-filled ki-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-2sm font-medium text-gray-600">
                                Tiến độ hoàn thành
                            </span>
                        </div>
                        <span class="text-2sm font-bold text-gray-900">
                            {{ $taskStats['completed'] }}/{{ $taskStats['total'] }}
                        </span>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                            @php 
                                $completedPercent = $taskStats['total'] > 0 ? ($taskStats['completed'] / $taskStats['total']) * 100 : 0;
                            @endphp
                            <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $completedPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl bg-purple-50 gap-2 p-3.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <div class="h-10 w-10 flex items-center justify-center bg-purple-100 rounded-full">
                        <i class="ki-filled ki-arrow-up-down text-purple-600 text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-2sm font-medium text-gray-600">
                                Cần chỉnh sửa
                            </span>
                        </div>
                        <span class="text-2sm font-bold text-gray-900">
                            {{ $taskStats['need_revision'] }}
                        </span>
                        @if($taskStats['need_revision'] > 0)
                        <a href="{{ route('dashboard.account.task.task') }}?filter[status_task]=7" class="text-xs text-purple-600 hover:underline mt-1">
                            <i class="ki-filled ki-arrow-right-circle"></i> Xem ngay
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Task by status and progress -->
        <div class="grid !grid-cols-1 md:!grid-cols-2 gap-5 mb-6">
            <!-- Task distribution by status -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium mb-3">Phân bố theo trạng thái</h4>
                
                <!-- Status distribution bars -->
                <div class="space-y-3">
                    @php
                        $statuses = [
                            ['id' => 1, 'name' => 'Chưa bắt đầu', 'color' => 'warning'],
                            ['id' => 2, 'name' => 'Đang chờ', 'color' => 'warning'],
                            ['id' => 3, 'name' => 'Đang thực hiện', 'color' => 'info'],
                            ['id' => 4, 'name' => 'Hoàn thành', 'color' => 'success'],
                            ['id' => 7, 'name' => 'Cần chỉnh sửa', 'color' => 'danger'],
                        ];
                        
                        // Giả lập số lượng công việc cho mỗi trạng thái
                        // Trong thực tế, bạn sẽ cần dữ liệu thực từ controller
                        $statusCounts = [
                            1 => $taskStats['total'] - $taskStats['in_progress'] - $taskStats['completed'] - $taskStats['need_revision'],
                            2 => round($taskStats['in_progress'] * 0.3),
                            3 => round($taskStats['in_progress'] * 0.7),
                            4 => $taskStats['completed'],
                            7 => $taskStats['need_revision'],
                        ];
                        
                        $maxCount = max($statusCounts);
                    @endphp
                    
                    @foreach($statuses as $status)
                        @php
                            $count = $statusCounts[$status['id']] ?? 0;
                            $percent = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-{{ $status['color'] }} mr-2"></span>
                                    <span class="text-xs">{{ $status['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium">{{ $count }}</span>
                                    @if($count > 0)
                                    <a href="{{ route('dashboard.account.task.task') }}?filter[status_task]={{ $status['id'] }}" class="btn btn-icon btn-xs btn-light">
                                        <i class="ki-filled ki-arrow-right text-xs"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-{{ $status['color'] }} h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <a href="{{ route('dashboard.account.task.task') }}" class="btn btn-sm btn-light w-full">
                        <i class="ki-filled ki-filter"></i> Lọc theo trạng thái
                    </a>
                </div>
            </div>
            
            <!-- Task priority distribution -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium mb-3">Công việc ưu tiên (Đang bảo trì)</h4>
                
                <!-- Recent activity -->
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <h5 class="text-xs font-medium mb-2">Hoạt động gần đây</h5>
                    <div class="space-y-2">
                        <div class="text-xs text-gray-600">
                            <i class="ki-filled ki-check-circle text-success mr-1"></i>
                            <span>{{ $taskStats['completed'] > 0 ? $taskStats['completed'] : 0 }} công việc đã hoàn thành trong tuần này</span>
                        </div>
                        <div class="text-xs text-gray-600">
                            <i class="ki-filled ki-flag text-warning mr-1"></i>
                            <span>{{ $taskStats['due_today'] > 0 ? $taskStats['due_today'] : 0 }} công việc cần hoàn thành hôm nay</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tasks that need attention -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-medium text-sm">Công việc cần chú ý</h4>
                <div class="flex gap-2">
                    <button class="btn btn-xs btn-light active task-filter-btn" data-filter="all">Tất cả</button>
                    <button class="btn btn-xs btn-light task-filter-btn" data-filter="overdue">Quá hạn</button>
                    <button class="btn btn-xs btn-light task-filter-btn" data-filter="today">Hôm nay</button>
                    <button class="btn btn-xs btn-light task-filter-btn" data-filter="revision">Cần sửa</button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Tên công việc</th>
                            <th>Người theo dõi</th>
                            <th>Trạng thái</th>
                            <th>Tiến độ</th>
                            <th>Hạn xử lý</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taskStats['recent_tasks'] as $task)
                        <tr class="task-row {{ $task->status_id == 7 ? 'task-revision' : '' }} {{ (strtotime($task->due_date) < time() && $task->status_id < 4) ? 'task-overdue' : '' }} {{ (date('Y-m-d') == date('Y-m-d', strtotime($task->due_date))) ? 'task-today' : '' }}">
                            <td>
                                <a class="text-primary hover:text-primary-hover font-medium">
                                    {{ \Illuminate\Support\Str::limit($task->name, 40) }}
                                </a>
                                <div class="text-xs text-gray-500">
                                    <span class="badge badge-sm badge-light">#{{ $task->id }}</span>
                                    <span class="badge badge-sm badge-outline badge-primary">{{ $task->type }}</span>
                                </div>
                            </td>
                            <td>
                                @if($task->assign->id ?? 0)
                                <div class="flex items-center gap-2">
                                    <span>{{ $task->assign->name ?? 'Chưa gán' }}</span>
                                </div>
                                @else
                                <span class="badge badge-sm badge-outline badge-warning">Chưa gán</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $task->status->color ?? 'gray' }}">
                                    {{ $task->status->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-sm h-1.5">
                                        <div class="{{ $task->progress >= 100 ? 'bg-success' : 'bg-blue-800' }} h-1.5 rounded-sm" style="width: {{ $task->progress ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-xs">{{ $task->progress ?? 0 }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($task->due_date)
                                    @if(strtotime($task->due_date) < time() && $task->status_id < 4)
                                        <span class="text-danger flex items-center gap-1">
                                            <i class="ki-solid ki-timer text-xs"></i>
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                        </span>
                                    @elseif(date('Y-m-d') == date('Y-m-d', strtotime($task->due_date)))
                                        <span class="text-warning flex items-center gap-1">
                                            <i class="ki-solid ki-calendar-tick text-xs"></i>
                                            Hôm nay
                                        </span>
                                    @else
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                    @endif
                                @else
                                    <span class="text-gray-500">Không có</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    @if(strtotime($task->due_date) < time() && $task->status_id < 4)
                                        <a href="{{ route('dashboard.account.task.task') }}?filter[status_task]=6" class="btn btn-icon btn-sm btn-danger">
                                            <i class="ki-filled ki-timer"></i>
                                        </a>
                                    @elseif(date('Y-m-d') == date('Y-m-d', strtotime($task->due_date)))
                                        <a href="{{ route('dashboard.account.task.task') }}?filter[due_today]=1" class="btn btn-icon btn-sm btn-warning">
                                            <i class="ki-filled ki-calendar-tick"></i>
                                        </a>
                                    @elseif($task->status_id == 7)
                                        <a href="{{ route('dashboard.account.task.task') }}?filter[status_task]=7" class="btn btn-icon btn-sm btn-purple">
                                            <i class="ki-filled ki-arrow-up-down"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Filter action buttons -->
            <div class="flex justify-end mt-3 gap-2">
                <a href="{{ route('dashboard.account.task.task') }}?filter[status_task]=6" id="view-overdue-tasks-btn" class="btn btn-sm btn-danger hidden">
                    <i class="ki-filled ki-timer"></i> Xem tất cả task quá hạn
                </a>
                <a href="{{ route('dashboard.account.task.task') }}?filter[due_today]=1" id="view-today-tasks-btn" class="btn btn-sm btn-warning hidden">
                    <i class="ki-filled ki-calendar-tick"></i> Xem tất cả task hôm nay
                </a>
                <a href="{{ route('dashboard.account.task.task') }}?filter[status_task]=7" id="view-revision-tasks-btn" class="btn btn-sm btn-purple hidden">
                    <i class="ki-filled ki-arrow-up-down"></i> Xem tất cả task cần sửa
                </a>
            </div>
        </div>
        
        <!-- Task completion trend -->
        <div>
            <h4 class="font-medium text-sm mb-3">Xu hướng hoàn thành công việc</h4>
            <div class="h-48">
                <canvas id="taskTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>
    @endif
    
    @if(hasPermission('view-customer'))
    <!-- Customer Support Dashboard - Enhanced Version -->
<div class="card mb-7">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ki-filled ki-user text-blue-600 text-2xl"></i>&nbsp;Chăm sóc khách hàng
        </h3>
        <div class="flex gap-2">
            <a href="{{ route('dashboard.customer.support.customer-support') }}" class="btn btn-sm btn-light">
                <i class="ki-filled ki-people"></i> Danh sách KH
            </a>
            <a href="{{ route('dashboard.customer.client.customer-leads') }}" class="btn btn-sm btn-primary">
                <i class="ki-filled ki-abstract-26"></i> Leads
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Chỉ số KPI chính - Tổng quan hiệu suất -->
        <div class="grid !grid-cols-2 md:!grid-cols-4 gap-3 mb-6">
            <div class="bg-primary-50 rounded-lg p-3 border border-primary-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Tổng khách hàng</p>
                        <p class="text-xl font-bold text-primary">{{ $customerStats['total_customers'] }}</p>
                    </div>
                    <div>
                        <span class="badge badge-sm badge-primary">+{{ $customerStats['new_today'] }} hôm nay</span>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs">Leads: {{ $customerStats['leads'] }}</span>
                    <span class="text-xs">KH: {{ $customerStats['customers'] }}</span>
                </div>
            </div>
            
            <div class="bg-success-50 rounded-lg p-3 border border-success-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Tỷ lệ chuyển đổi</p>
                        <p class="text-xl font-bold text-success">{{ number_format($conversion_stats['lead_to_customer'] ?? 0, 1) }}%</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-success-100 flex items-center justify-center">
                        <i class="ki-filled ki-abstract-26 text-success"></i>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="bg-success h-1.5 rounded-full" style="width: {{ min($conversion_stats['lead_to_customer'] ?? 0, 100) }}%"></div>
                </div>
            </div>
            
            <div class="bg-warning-50 rounded-lg p-3 border border-warning-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Đang tư vấn</p>
                        <p class="text-xl font-bold text-warning">{{ $customerStats['active_consultations'] }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-warning-100 flex items-center justify-center">
                        <i class="ki-filled ki-message-text-2 text-warning"></i>
                    </div>
                </div>
                <p class="text-xs mt-2">{{ $customerStats['active_consultations'] > 0 ? 'Cần hoàn thành tư vấn' : 'Không có tư vấn đang thực hiện' }}</p>
            </div>
            
            <div class="bg-danger-50 rounded-lg p-3 border border-danger-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Cần chăm sóc</p>
                        <p class="text-xl font-bold text-danger">{{ $customerStats['need_follow_up'] }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-danger-100 flex items-center justify-center">
                        <i class="ki-filled ki-timer text-danger"></i>
                    </div>
                </div>
                <a href="{{ route('dashboard.customer.support.customer-support') }}?filter[interaction]=old" class="text-danger text-xs font-medium mt-2 inline-block">
                    <i class="ki-filled ki-arrow-right"></i> Xem danh sách
                </a>
            </div>
        </div>
        
        <!-- Main content area - 3 column layout -->
        <div class="grid !grid-cols-1 md:!grid-cols-3 gap-5 mb-5">
            <!-- Column 1: Todo list và công việc ưu tiên -->
            <div>
                <h4 class="font-medium text-sm mb-3">Cần ưu tiên xử lý</h4>
                <!-- Cuộc hẹn hôm nay -->
                <div class="bg-primary-50 p-3 rounded-lg mb-3 border border-primary-100">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-sm font-medium">Cuộc hẹn hôm nay</h5>
                        <span class="badge badge-primary">{{ $statistics['today'] ?? 0 }}</span>
                    </div>
                    @if(isset($statistics['today']) && $statistics['today'] > 0)
                        <a href="/appointment/detail?date={{ now()->format('Y-m-d') }}" class="text-primary text-xs font-medium">
                            <i class="ki-filled ki-calendar-tick"></i> Xem lịch hẹn hôm nay
                        </a>
                    @else
                        <p class="text-xs text-gray-500">Không có cuộc hẹn hôm nay</p>
                    @endif
                </div>
                
                <!-- Khách hàng không tương tác >14 ngày -->
                <div class="bg-warning-50 p-3 rounded-lg mb-3 border border-warning-100">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-sm font-medium">KH không tương tác >14 ngày</h5>
                        <span class="badge badge-warning">{{ $customerStats['need_follow_up'] }}</span>
                    </div>
                    @if($customerStats['need_follow_up'] > 0)
                        <a href="{{ route('dashboard.customer.support.customer-support') }}?filter[interaction]=old" class="text-warning text-xs font-medium">
                            <i class="ki-filled ki-call"></i> Cần liên hệ lại ngay
                        </a>
                    @else
                        <p class="text-xs text-gray-500">Tất cả khách hàng đều đã được tương tác gần đây</p>
                    @endif
                </div>
                
                <!-- Tư vấn đang thực hiện -->
                {{--<div class="bg-success-50 p-3 rounded-lg border border-success-100">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-sm font-medium">Tư vấn đang thực hiện</h5>
                        <span class="badge badge-success">{{ $customerStats['active_consultations'] }}</span>
                    </div>
                    <div class="text-xs space-y-1">
                        @if(isset($recent_consultations) && count($recent_consultations) > 0)
                            @foreach($recent_consultations->take(3) as $consultation)
                                <p class="flex justify-between">
                                    <span class="truncate max-w-[180px]">{{ $consultation->customer->name ?? 'Không có tên' }}</span>
                                    <span>{{ \Carbon\Carbon::parse($consultation->created_at)->format('d/m') }}</span>
                                </p>
                            @endforeach
                        @else
                            <p class="text-gray-500">Không có tư vấn đang thực hiện</p>
                        @endif
                    </div>
                </div>--}}
            </div>
            
            <!-- Column 2: Cuộc hẹn sắp tới và lịch -->
            <div>
                <h4 class="font-medium text-sm mb-3">Cuộc hẹn sắp tới</h4>
                <div class="space-y-2 overflow-auto max-h-[250px] pr-1">
                    @if(isset($customerStats['upcoming_appointments']) && count($customerStats['upcoming_appointments']) > 0)
                        @foreach($customerStats['upcoming_appointments'] as $appointment)
                            <div class="flex items-start gap-3 p-3 rounded-lg border border-{{ $appointment->color }}-200 bg-{{ $appointment->color }}-50">
                                <div class="rounded-full bg-{{ $appointment->color }}-100 p-2 shrink-0">
                                    <i class="ki-filled ki-calendar text-{{ $appointment->color }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <p class="font-medium text-sm truncate">{{ $appointment->name }}</p>
                                        <span class="badge badge-{{ $appointment->color }} whitespace-nowrap ml-1">
                                            {{ \Carbon\Carbon::parse($appointment->start_time)->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 mt-1">
                                        <i class="ki-filled ki-profile-user mr-1"></i>
                                        <span class="truncate">{{ $appointment->customer->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 mt-0.5">
                                        <i class="ki-filled ki-calendar-8 mr-1"></i>
                                        <span>{{ \Carbon\Carbon::parse($appointment->start_time)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5 text-gray-500">
                            <i class="ki-filled ki-calendar-8 text-3xl mb-2"></i>
                            <p>Không có cuộc hẹn sắp tới</p>
                            <a href="{{ route('dashboard.customer.support.appointment-detail') }}" class="btn btn-sm btn-light mt-2">
                                <i class="ki-filled ki-plus"></i> Tạo lịch hẹn
                            </a>
                        </div>
                    @endif
                </div>
                <div class="flex justify-end mt-3">
                    <a href="{{ route('dashboard.customer.support.appointment-detail') }}" class="btn btn-sm btn-light">
                        <i class="ki-filled ki-calendar"></i> Xem tất cả lịch hẹn
                    </a>
                </div>
            </div>
            
            <!-- Column 3: Chỉ số và phân tích hiệu quả -->
            <div>
                <h4 class="font-medium text-sm mb-3">Phân tích hiệu quả</h4>
                
                <!-- Tỷ lệ chuyển đổi -->
                <div class="bg-white rounded-lg border border-gray-200 p-3 mb-3">
                    <h5 class="text-sm font-medium mb-2">Tỷ lệ chuyển đổi</h5>
                    <div class="space-y-2">
                        <div>
                            <div class="flex justify-between text-xs">
                                <span>Lead → KH tiềm năng</span>
                                <span class="font-medium">{{ number_format($conversion_stats['lead_to_prospect'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="bg-primary h-1.5 rounded-full" style="width: {{ min($conversion_stats['lead_to_prospect'] ?? 0, 100) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs">
                                <span>KH tiềm năng → KH</span>
                                <span class="font-medium">{{ number_format($conversion_stats['prospect_to_customer'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="bg-success h-1.5 rounded-full" style="width: {{ min($conversion_stats['prospect_to_customer'] ?? 0, 100) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs">
                                <span>Tỷ lệ phản hồi</span>
                                <span class="font-medium">{{ number_format($conversion_stats['response_rate'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                <div class="bg-warning h-1.5 rounded-full" style="width: {{ min($conversion_stats['response_rate'] ?? 0, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Phân bố khách hàng theo trạng thái -->
                <div class="bg-white rounded-lg border border-gray-200 p-3">
                    <h5 class="text-sm font-medium mb-2">Phân bố theo trạng thái</h5>
                    <div class="space-y-2">
                        @if(isset($customerStats['by_status']))
                            @foreach($customerStats['by_status'] as $status)
                                <div class="flex justify-between items-center">
                                    <span class="text-xs">{{ $status->name }}</span>
                                    <span class="text-xs font-medium">{{ $status->customers_count }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-gray-500">Không có dữ liệu</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Khách hàng mới và khách hàng tiềm năng -->
        <div class="mt-5">
            <h4 class="font-medium text-sm mb-3">Khách hàng tiềm năng mới (5 gần đây nhất)</h4>
            <div class="overflow-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th>Thông tin liên hệ</th>
                            <th>Điểm tiềm năng</th>
                            <th>Nguồn</th>
                            <th>Ngày thêm</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($recent_leads) && count($recent_leads) > 0)
                            @foreach($recent_leads as $lead)
                                <tr>
                                    <td>
                                        <a href="/customer/{{ $lead->id }}" class="text-primary font-medium">{{ $lead->name }}</a>
                                    </td>
                                    <td>
                                        <div class="text-xs">{{ $lead->phone ?: 'Không có SĐT' }}</div>
                                        <div class="text-xs text-gray-500">{{ $lead->email ?: 'Không có email' }}</div>
                                    </td>
                                    <td>
                                        @if($lead->lead_score > 60)
                                            <span class="badge badge-success">{{ $lead->lead_score }}</span>
                                        @elseif($lead->lead_score > 30)
                                            <span class="badge badge-warning">{{ $lead->lead_score }}</span>
                                        @else
                                            <span class="badge badge-gray">{{ $lead->lead_score }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $lead->source->name ?? 'Không xác định' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($lead->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="/customer-consultation/{{ $lead->id }}" class="btn btn-sm btn-light">
                                            <i class="ki-filled ki-message-text"></i> Tư vấn
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500">Không có khách hàng tiềm năng mới</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-3">
                <a href="{{ route('dashboard.customer.client.customer-leads') }}" class="btn btn-sm btn-primary">
                    <i class="ki-filled ki-external-drive"></i> Quản lý leads
                </a>
            </div>
        </div>
    </div>
</div>
    @endif
    
    @if(hasPermission('view-transaction') || hasPermission('view-report'))
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
    @endif
    
    @if(hasPermission('view-member') || hasPermission('view-team'))
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
    @endif
    
    @if(hasPermission('view-schedule') && ($pendingSchedulesCount > 0 || $cancelRequestsCount > 0))
    <!-- Lịch làm việc chờ duyệt Section -->
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
@endif

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
<script>
    // Thêm vào phần document ready
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo tab tùy chỉnh cho hợp đồng
    initContractTabs();
    
    // Khởi tạo biểu đồ nếu cần
    // initContractCharts();
});

function initContractTabs() {
    // Lấy tham chiếu đến container cha của phần contract để tránh xung đột
    const contractCard = document.querySelector('.card-header .contract-filter-btn')?.closest('.card');
    
    if (!contractCard) return;
    
    // Tất cả các nút tab trong card này
    const filterBtns = contractCard.querySelectorAll('.contract-filter-btn');
    // Tất cả các nội dung tab trong card này
    const contentPanels = contractCard.querySelectorAll('.contract-list-content');
    
    // Gắn sự kiện click cho mỗi nút
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Loại bỏ active khỏi tất cả các nút và thêm cho nút hiện tại
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Ẩn tất cả nội dung và hiển thị nội dung tương ứng
            contentPanels.forEach(panel => {
                panel.classList.add('hidden');
                if (panel.id === `contracts-${filter}`) {
                    panel.classList.remove('hidden');
                    panel.classList.add('active');
                } else {
                    panel.classList.remove('active');
                }
            });
        });
    });
}

function createContractTasks(id) {
    Notiflix.Confirm.show(
        'Tạo công việc',
        'Bạn có chắc chắn muốn tạo công việc cho hợp đồng này? Sau khi tạo sẽ không thể sửa đổi',
        'Đúng',
        'Hủy',
        async () => {
            let method = "post",
                url = "/contract/create-task",
                params = null,
                data = { id };
            try {
                Notiflix.Loading.pulse('Đang tạo công việc...');
                let res = await axiosTemplate(method, url, params, data);
                Notiflix.Loading.remove();
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('warning', res.data.message || "Đã có lỗi xảy ra!");
                }
            } catch (error) {
                Notiflix.Loading.remove();
                showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                console.error(error);
            }
        }
    );
}

function completeContract(id) {
    Notiflix.Confirm.show(
        'Hoàn thành hợp đồng',
        'Bạn có chắc chắn muốn đánh dấu hợp đồng này là đã hoàn thành? Hành động này sẽ tính hoa hồng cho nhân viên và không thể hoàn tác.',
        'Đúng',
        'Hủy',
        async () => {
            let method = "post",
                url = "/contract/complete",
                params = null,
                data = { id };
            try {
                Notiflix.Loading.pulse('Đang xử lý...');
                let res = await axiosTemplate(method, url, params, data);
                Notiflix.Loading.remove();
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('warning', res.data.message || "Đã có lỗi xảy ra!");
                }
            } catch (error) {
                Notiflix.Loading.remove();
                showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                console.error(error);
            }
        }
    );
}
</script>
@endpush