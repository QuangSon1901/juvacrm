@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Hoa hồng cá nhân
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
        <!-- Thống kê hoa hồng -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-dollar size-6 shrink-0 text-success"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Tổng hoa hồng đã nhận
                                </div>
                                <div class="text-2sm text-gray-700">
                                    {{ number_format($commissionStats['totalPaid'] ?? 0, 0, ',', '.') }} VNĐ
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
                                    Hoa hồng chờ thanh toán
                                </div>
                                <div class="text-2sm text-gray-700">
                                    {{ number_format($commissionStats['totalPending'] ?? 0, 0, ',', '.') }} VNĐ
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
                            <i class="ki-outline ki-check-square size-6 shrink-0 text-primary"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Tổng số hợp đồng
                                </div>
                                <div class="text-2sm text-gray-700">
                                    {{ $commissionStats['totalContracts'] ?? 0 }} hợp đồng
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danh sách hoa hồng -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách hoa hồng
                </h3>
                <div class="flex flex-wrap gap-2">
                    <label class="switch switch-sm">
                        <input class="order-2" id="filter-paid" data-filter='is_paid' type="checkbox" value="1">
                        <span class="switch-label order-1">
                            Đã thanh toán
                        </span>
                    </label>
                    <div class="relative">
                        <input class="input input-sm" type="text" id="daterange-filter" data-flatpickr="true" data-flatpickr-type="range" placeholder="Chọn khoảng thời gian">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">STT</th>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Mã hợp đồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[250px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Tên hợp đồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Giá trị hợp đồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">% Hoa hồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Số tiền hoa hồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ngày tạo</span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commissions as $index => $commission)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.contract.contract.detail', $commission->contract->id) }}" class="text-primary hover:text-primary-active">
                                            {{ $commission->contract->contract_number }}
                                        </a>
                                    </td>
                                    <td>{{ $commission->contract->name }}</td>
                                    <td>{{ number_format($commission->contract_value, 0, ',', '.') }}</td>
                                    <td>{{ $commission->commission_percentage }}%</td>
                                    <td class="font-semibold text-success">{{ number_format($commission->commission_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-sm badge-outline badge-{{ $commission->is_paid ? 'success' : 'warning' }}">
                                            {{ $commission->is_paid ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                        </span>
                                    </td>
                                    <td>{{ formatDateTime($commission->created_at, 'd/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $commissions->links() }}
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
        // Khởi tạo flatpickr cho bộ lọc khoảng thời gian
        flatpickrMake($("#daterange-filter"), 'range');
        
        // Xử lý khi thay đổi bộ lọc đã thanh toán
        $("#filter-paid").on('change', function() {
            applyFilters();
        });
        
        // Xử lý khi thay đổi khoảng thời gian
        $("#daterange-filter").on('change', function() {
            applyFilters();
        });
    });
    
    // Hàm áp dụng các bộ lọc
    function applyFilters() {
        const isPaid = $("#filter-paid").is(':checked') ? 1 : '';
        const dateRange = $("#daterange-filter").val();
        
        let url = "{{ route('dashboard.profile.my-commission') }}?";
        
        if (isPaid !== '') {
            url += `is_paid=${isPaid}&`;
        }
        
        if (dateRange) {
            url += `date_range=${encodeURIComponent(dateRange)}`;
        }
        
        window.location.href = url;
    }
</script>
@endpush