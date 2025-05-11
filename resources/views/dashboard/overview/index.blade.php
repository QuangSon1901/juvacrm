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
            <!-- Thông tin tổng quan tài chính - Giữ nguyên -->
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
            
            <!-- Phần mới: Grid hiển thị các hạng mục hợp đồng -->
            <div class="grid !grid-cols-1 md:!grid-cols-2 gap-5 mb-7">
                <!-- Hợp đồng đang triển khai -->
                <div class="card card-bordered shadow-sm">
                    <div class="card-header bg-opacity-10 flex justify-between items-center py-3">
                        <h3 class="card-title text-primary">
                            <i class="ki-filled ki-rocket fs-2 me-3"></i>Đang triển khai ({{ $contractStats['active'] }})
                        </h3>
                        <a href="{{ route('dashboard.contract.contract') }}?filter[status]=1" class="btn btn-xs btn-light">Xem</a>
                    </div>
                    <div class="card-body p-3">
                        @if(isset($activeContracts) && count($activeContracts) > 0)
                        <div class="overflow-auto max-h-48">
                            <table class="table table-sm m-0">
                                <thead>
                                    <tr>
                                        <th>Số HĐ</th>
                                        <th>Khách hàng</th>
                                        <th>Tiến độ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeContracts as $contract)
                                    <tr>
                                        <td><a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary">{{ $contract->contract_number }}</a></td>
                                        <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress h-6px w-100 bg-light-primary">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $contract->task_progress }}%" aria-valuenow="{{ $contract->task_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="ms-3 text-muted">{{ $contract->task_progress }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-gray-500">
                            <i class="ki-filled ki-information-5 fs-2x mb-2"></i>
                            <p>Không có hợp đồng đang triển khai</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Hợp đồng do tôi quản lý -->
                <div class="card card-bordered shadow-sm">
                    <div class="card-header bg-opacity-10 flex justify-between items-center py-3">
                        <h3 class="card-title text-info">
                            <i class="ki-filled ki-user fs-2 me-3"></i>Hợp đồng của tôi
                        </h3>
                        <a href="{{ route('dashboard.contract.contract') }}?filter[my_contract]=1" class="btn btn-xs btn-light">Xem</a>
                    </div>
                    <div class="card-body p-3">
                        @if(isset($myContracts) && count($myContracts) > 0)
                        <div class="overflow-auto max-h-48">
                            <table class="table table-sm m-0">
                                <thead>
                                    <tr>
                                        <th>Số HĐ</th>
                                        <th>Khách hàng</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($myContracts as $contract)
                                    <tr>
                                        <td><a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary">{{ $contract->contract_number }}</a></td>
                                        <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($contract->status == 0)
                                                <span class="badge badge-warning">Chờ duyệt</span>
                                            @elseif($contract->status == 1)
                                                <span class="badge badge-primary">Đang triển khai</span>
                                            @elseif($contract->status == 2)
                                                <span class="badge badge-success">Hoàn thành</span>
                                            @elseif($contract->status == 3)
                                                <span class="badge badge-danger">Đã hủy</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-gray-500">
                            <i class="ki-filled ki-information-5 fs-2x mb-2"></i>
                            <p>Bạn chưa được phân công quản lý hợp đồng nào</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Hợp đồng chờ duyệt -->
                <div class="card card-bordered shadow-sm">
                    <div class="card-header bg-opacity-10 flex justify-between items-center py-3">
                        <h3 class="card-title text-warning">
                            <i class="ki-filled ki-timer fs-2 me-3"></i>Đang chờ ({{ $contractStats['pending_approval'] }})
                        </h3>
                        <a href="{{ route('dashboard.contract.contract') }}?filter[status]=0" class="btn btn-xs btn-light">Xem</a>
                    </div>
                    <div class="card-body p-3">
                        @if(isset($pendingContracts) && count($pendingContracts) > 0)
                        <div class="overflow-auto max-h-48">
                            <table class="table table-sm m-0">
                                <thead>
                                    <tr>
                                        <th>Số HĐ</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingContracts as $contract)
                                    <tr>
                                        <td><a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary">{{ $contract->contract_number }}</a></td>
                                        <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($contract->created_at)->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-gray-500">
                            <i class="ki-filled ki-information-5 fs-2x mb-2"></i>
                            <p>Không có hợp đồng đang chờ duyệt</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Hợp đồng cần thanh toán -->
                <div class="card card-bordered shadow-sm">
                    <div class="card-header bg-opacity-10 flex justify-between items-center py-3">
                        <h3 class="card-title text-success">
                            <i class="ki-filled ki-dollar fs-2 me-3"></i>Cần thanh toán
                        </h3>
                        <a href="{{ route('dashboard.accounting.deposit-receipt.deposit-receipt') }}" class="btn btn-xs btn-light">Xem</a>
                    </div>
                    <div class="card-body p-3">
                        @if(isset($paymentDueContracts) && count($paymentDueContracts) > 0)
                            <div class="overflow-auto max-h-48">
                                <table class="table table-sm m-0">
                                    <thead>
                                        <tr>
                                            <th>Số HĐ</th>
                                            <th>Khách hàng</th>
                                            <th>Số tiền</th>
                                            <th>Hạn thanh toán</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paymentDueContracts as $payment)
                                        <tr>
                                            <td><a href="{{ route('dashboard.contract.contract.detail', $payment->contract_id) }}" class="text-primary">{{ $payment->contract->contract_number }}</a></td>
                                            <td>{{ $payment->contract->provider->name ?? 'N/A' }}</td>
                                            <td class="text-end">{{ number_format($payment->price, 0, ',', '.') }}đ</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5 text-gray-500">
                                <i class="ki-duotone ki-information-5 fs-2x mb-2"></i>
                                <p>Không có hợp đồng cần thanh toán trong 7 ngày tới</p>
                            </div>
                            @endif
                    </div>
                </div>
                
                <!-- Hợp đồng đã hoàn thành task, đang chờ xử lý -->
                <div class="card card-bordered shadow-sm">
                    <div class="card-header bg-opacity-10 flex justify-between items-center py-3">
                        <h3 class="card-title text-info">
                            <i class="ki-filled ki-check-squared fs-2 me-3"></i>Đã hoàn thành task, chờ xử lý
                        </h3>
                        <a href="{{ route('dashboard.contract.contract') }}?filter[completed_tasks]=1" class="btn btn-xs btn-light">Xem</a>
                    </div>
                    <div class="card-body p-3">
                        @if(isset($completedTaskContracts) && count($completedTaskContracts) > 0)
                        <div class="overflow-auto max-h-48">
                            <table class="table table-sm m-0">
                                <thead>
                                    <tr>
                                        <th>Số HĐ</th>
                                        <th>Khách hàng</th>
                                        <th>Số task hoàn thành</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedTaskContracts as $contract)
                                    <tr>
                                        <td><a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary">{{ $contract->contract_number }}</a></td>
                                        <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $contract->completed_tasks_count }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-gray-500">
                            <i class="ki-filled ki-information-5 fs-2x mb-2"></i>
                            <p>Không có hợp đồng với task hoàn thành đang chờ xử lý</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Hợp đồng sắp hết hạn -->
                <div class="card card-bordered shadow-sm">
                    <div class="card-header bg-opacity-10 flex justify-between items-center py-3">
                        <h3 class="card-title text-danger">
                            <i class="ki-filled ki-calendar-remove fs-2 me-3"></i>Sắp quá hạn
                        </h3>
                        <a href="{{ route('dashboard.contract.contract') }}?filter[expiring]=1" class="btn btn-xs btn-danger">Xem</a>
                    </div>
                    <div class="card-body p-3">
                        @if(isset($expiringContracts) && count($expiringContracts) > 0)
                        <div class="overflow-auto max-h-48">
                            <table class="table table-sm m-0">
                                <thead>
                                    <tr>
                                        <th>Số HĐ</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày quá hạn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringContracts as $contract)
                                    <tr>
                                        <td><a href="{{ route('dashboard.contract.contract.detail', $contract->id) }}" class="text-primary">{{ $contract->contract_number }}</a></td>
                                        <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($contract->expiry_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-gray-500">
                            <i class="ki-filled ki-information-5 fs-2x mb-2"></i>
                            <p>Không có hợp đồng sắp hết hạn trong 14 ngày tới</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Phần biểu đồ tóm tắt -->
            <div class="row mb-6">
                <div class="col-md-6 mb-6">
                    <h4 class="font-medium text-sm mb-3">Phân bố hợp đồng theo trạng thái</h4>
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
                
                <div class="col-md-6">
                    <h4 class="font-medium text-sm mb-3">Tỷ lệ thanh toán hợp đồng</h4>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="bg-green-500 h-2.5 rounded-sm" style="width: {{ ($contractStats['paid_value'] / max($contractStats['total_value'], 1)) * 100 }}%"></div>
                        <div class="bg-orange-500 h-2.5 rounded-sm" style="width: {{ (($contractStats['total_value'] - $contractStats['paid_value']) / max($contractStats['total_value'], 1)) * 100 }}%"></div>
                    </div>
                    <div class="flex items-center flex-wrap gap-4">
                        <div class="flex items-center gap-1.5">
                            <span class="badge badge-dot size-2 badge-success"></span>
                            <span class="text-xs">Đã thanh toán ({{ number_format($contractStats['paid_value'], 0, ',', '.') }}đ)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="badge badge-dot size-2 badge-warning"></span>
                            <span class="text-xs">Chưa thanh toán ({{ number_format($contractStats['total_value'] - $contractStats['paid_value'], 0, ',', '.') }}đ)</span>
                        </div>
                    </div>
                </div>
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