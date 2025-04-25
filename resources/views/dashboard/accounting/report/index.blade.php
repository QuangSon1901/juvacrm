@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Báo cáo tài chính
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <a href="{{ route('dashboard.accounting.report.financial-export') }}" target="_blank" id="export-excel-btn" class="btn btn-primary btn-sm">
                <i class="ki-filled ki-file-down me-1"></i>
                Xuất Excel
            </a>
        </div>
    </div>
</div>
<div class="container-fixed">
    <!-- Filter panel -->
    <div class="card card-grid mb-5">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc báo cáo</h3>
        </div>
        <div class="card-body !p-5">
            <form id="report-filter-form" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khoảng thời gian
                        </span>
                    </div>
                    <select class="select" id="date-range" name="date_range">
                        <option value="this_month">Tháng này</option>
                        <option value="last_month">Tháng trước</option>
                        <option value="this_quarter">Quý này</option>
                        <option value="last_quarter">Quý trước</option>
                        <option value="this_year">Năm nay</option>
                        <option value="last_year">Năm trước</option>
                        <option value="custom">Tùy chọn</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 date-range-custom hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Từ ngày
                        </span>
                    </div>
                    <input type="text" class="input" id="date-from" name="date_from" data-flatpickr="true" data-flatpickr-date="true">
                </div>
                <div class="flex flex-col gap-2.5 date-range-custom hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đến ngày
                        </span>
                    </div>
                    <input type="text" class="input" id="date-to" name="date_to" data-flatpickr="true" data-flatpickr-date="true">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nhóm theo
                        </span>
                    </div>
                    <select class="select" id="group-by" name="group_by">
                        <option value="day">Ngày</option>
                        <option value="week">Tuần</option>
                        <option value="month" selected>Tháng</option>
                        <option value="quarter">Quý</option>
                        <option value="year">Năm</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Danh mục
                        </span>
                    </div>
                    <select class="select" id="category-id" name="category_id">
                        <option value="">Tất cả danh mục</option>
                        @if(isset($categories) && count($categories) > 0)
                            <optgroup label="Danh mục thu">
                            @foreach($categories as $category)
                                @if($category->type == 0)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                            </optgroup>
                            <optgroup label="Danh mục chi">
                            @foreach($categories as $category)
                                @if($category->type == 1)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đối tượng
                        </span>
                    </div>
                    <select class="select" id="target-type" name="target_type">
                        <option value="">Tất cả đối tượng</option>
                        <option value="client">Khách hàng</option>
                        <option value="employee">Nhân viên</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 target-client hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khách hàng
                        </span>
                    </div>
                    <select class="select" id="target-client-id" name="target_client_id">
                        <option value="">Tất cả khách hàng</option>
                        @if(isset($customers) && count($customers) > 0)
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 target-employee hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nhân viên
                        </span>
                    </div>
                    <select class="select" id="target-employee-id" name="target_employee_id">
                        <option value="">Tất cả nhân viên</option>
                        @if(isset($employees) && count($employees) > 0)
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="md:col-span-3 lg:col-span-4 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-filled ki-filter me-1"></i>
                        Lọc báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="grid !grid-cols-1 md:!grid-cols-3 gap-5 mb-5">
        <div class="card shadow-sm border border-gray-200">
            <div class="card-body p-5">
                <div class="flex items-center mb-5">
                    <div class="size-12 flex items-center justify-center rounded-md bg-success-dark bg-opacity-10 me-3">
                        <i class="ki-filled ki-arrow-down text-2xl text-success"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 font-medium">Tổng thu</div>
                        <div class="text-xl font-semibold text-gray-900" id="summary-income">0₫</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-xs text-gray-600 mb-1">Số giao dịch</div>
                        <div class="font-medium" id="summary-income-count">0</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 mb-1">Danh mục nhiều nhất</div>
                        <div class="font-medium" id="summary-income-top-category">-</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border border-gray-200">
            <div class="card-body p-5">
                <div class="flex items-center mb-5">
                    <div class="size-12 flex items-center justify-center rounded-md bg-danger-dark bg-opacity-10 me-3">
                        <i class="ki-filled ki-arrow-up text-2xl text-danger"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 font-medium">Tổng chi</div>
                        <div class="text-xl font-semibold text-gray-900" id="summary-expense">0₫</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-xs text-gray-600 mb-1">Số giao dịch</div>
                        <div class="font-medium" id="summary-expense-count">0</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 mb-1">Danh mục nhiều nhất</div>
                        <div class="font-medium" id="summary-expense-top-category">-</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border border-gray-200">
            <div class="card-body p-5">
                <div class="flex items-center mb-5">
                    <div class="size-12 flex items-center justify-center rounded-md bg-primary-dark bg-opacity-10 me-3">
                        <i class="ki-filled ki-dollar text-2xl text-primary"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 font-medium">Số dư</div>
                        <div class="text-xl font-semibold" id="summary-balance">0₫</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-xs text-gray-600 mb-1">Giai đoạn</div>
                        <div class="font-medium" id="summary-date-range">Tháng hiện tại</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 mb-1">Tỷ lệ thu/chi</div>
                        <div class="font-medium" id="summary-ratio">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time series chart -->
    <div class="card shadow-sm border border-gray-200 mb-5">
        <div class="card-header">
            <h3 class="card-title">Thu chi theo thời gian</h3>
        </div>
        <div class="card-body">
            <div style="height: 350px;">
                <canvas id="time-series-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Category charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header">
                <h3 class="card-title">Phân tích thu theo danh mục</h3>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="income-pie-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header">
                <h3 class="card-title">Phân tích chi theo danh mục</h3>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="expense-pie-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Biến lưu trữ các biểu đồ
    let timeSeriesChart = null;
    let incomePieChart = null;
    let expensePieChart = null;

    $(function() {
        // Khởi tạo flatpickr
        flatpickrMake($("[data-flatpickr]"));

        // Xử lý hiển thị/ẩn trường ngày tùy chọn
        $('#date-range').on('change', function() {
            if ($(this).val() === 'custom') {
                $('.date-range-custom').removeClass('hidden');
            } else {
                $('.date-range-custom').addClass('hidden');
            }
        });

        // Xử lý hiển thị trường đối tượng
        $('#target-type').on('change', function() {
            let targetType = $(this).val();
            
            $('.target-client, .target-employee').addClass('hidden');
            
            if (targetType === 'client') {
                $('.target-client').removeClass('hidden');
            } else if (targetType === 'employee') {
                $('.target-employee').removeClass('hidden');
            }
            
            // Reset các trường không hiển thị
            if (targetType !== 'client') $('#target-client-id').val('');
            if (targetType !== 'employee') $('#target-employee-id').val('');
        });

        // Xử lý form lọc báo cáo
        $('#report-filter-form').on('submit', async function(e) {
            e.preventDefault();
            await loadReportData();
        });

        // Khởi tạo biểu đồ
        initCharts();

        // Tải dữ liệu báo cáo mặc định
        loadReportData();
    });

    // Khởi tạo các biểu đồ
    function initCharts() {
        // Biểu đồ thu chi theo thời gian
        const timeSeriesCtx = document.getElementById('time-series-chart').getContext('2d');
        timeSeriesChart = new Chart(timeSeriesCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Thu',
                        data: [],
                        backgroundColor: 'rgba(23, 198, 83, 0.5)',
                        borderColor: 'rgba(23, 198, 83, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Chi',
                        data: [],
                        backgroundColor: 'rgba(255, 82, 82, 0.5)',
                        borderColor: 'rgba(255, 82, 82, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Số dư',
                        data: [],
                        type: 'line',
                        borderColor: 'rgba(27, 132, 255, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Biểu đồ tròn thu theo danh mục
        const incomePieCtx = document.getElementById('income-pie-chart').getContext('2d');
        incomePieChart = new Chart(incomePieCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    label: 'Thu',
                    data: [],
                    backgroundColor: [
                        'rgba(23, 198, 83, 0.7)',
                        'rgba(0, 150, 136, 0.7)',
                        'rgba(0, 188, 212, 0.7)',
                        'rgba(3, 169, 244, 0.7)',
                        'rgba(33, 150, 243, 0.7)',
                        'rgba(63, 81, 181, 0.7)',
                        'rgba(156, 39, 176, 0.7)'
                    ],
                    borderColor: [
                        'rgba(23, 198, 83, 1)',
                        'rgba(0, 150, 136, 1)',
                        'rgba(0, 188, 212, 1)',
                        'rgba(3, 169, 244, 1)',
                        'rgba(33, 150, 243, 1)',
                        'rgba(63, 81, 181, 1)',
                        'rgba(156, 39, 176, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Biểu đồ tròn chi theo danh mục
        const expensePieCtx = document.getElementById('expense-pie-chart').getContext('2d');
        expensePieChart = new Chart(expensePieCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    label: 'Chi',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 82, 82, 0.7)',
                        'rgba(244, 67, 54, 0.7)',
                        'rgba(233, 30, 99, 0.7)',
                        'rgba(156, 39, 176, 0.7)',
                        'rgba(103, 58, 183, 0.7)',
                        'rgba(63, 81, 181, 0.7)',
                        'rgba(33, 150, 243, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 82, 82, 1)',
                        'rgba(244, 67, 54, 1)',
                        'rgba(233, 30, 99, 1)',
                        'rgba(156, 39, 176, 1)',
                        'rgba(103, 58, 183, 1)',
                        'rgba(63, 81, 181, 1)',
                        'rgba(33, 150, 243, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    }

    // Tải dữ liệu báo cáo
    async function loadReportData() {
        // Hiển thị trạng thái đang tải
        $('#recent-transactions-body').html('<tr><td colspan="6" class="text-center py-5">Đang tải dữ liệu...</td></tr>');
        
        try {
            // Chuyển đổi form data sang đối tượng params
            const $form = $('#report-filter-form');
            const formArray = $form.serializeArray();
            const params = {};
            
            // Chuyển đổi mảng thành đối tượng
            $.each(formArray, function(i, field) {
                if (field.value) {
                    params[field.name] = field.value;
                }
            });
            
            // Gọi API
            const response = await axiosTemplate('get', '/financial-report/data', params, null);
            
            if (response && response.data && response.data.status === 200) {
                // Cập nhật thông tin tổng quan
                updateSummary(response.data.data || {});
                
                // Cập nhật biểu đồ
                updateCharts(response.data.data || {});
                
            } else {
                showAlert('warning', 'Không thể tải dữ liệu báo cáo');
                $('#recent-transactions-body').html('<tr><td colspan="6" class="text-center py-5">Không có dữ liệu</td></tr>');
            }
        } catch (error) {
            console.error('Lỗi khi tải dữ liệu báo cáo:', error);
            showAlert('error', 'Đã xảy ra lỗi khi tải dữ liệu báo cáo');
            $('#recent-transactions-body').html('<tr><td colspan="6" class="text-center py-5">Không thể tải dữ liệu</td></tr>');
        }
    }

    // Cập nhật thông tin tổng quan
    function updateSummary(data) {
        const summary = data.summary || {};
        const dateRange = data.date_range || {};
        
        // Cập nhật tổng thu
        $('#summary-income').text(formatNumberLikePhp(summary.total_income || 0) + '₫');
        $('#summary-income-count').text(summary.income_count || 0);
        
        // Cập nhật tổng chi
        $('#summary-expense').text(formatNumberLikePhp(summary.total_expense || 0) + '₫');
        $('#summary-expense-count').text(summary.expense_count || 0);
        
        // Cập nhật số dư
        $('#summary-balance').text(formatNumberLikePhp(summary.balance || 0) + '₫');
        
        // Đổi màu số dư nếu âm
        if ((summary.balance || 0) < 0) {
            $('#summary-balance').addClass('text-danger').removeClass('text-gray-900');
        } else {
            $('#summary-balance').removeClass('text-danger').addClass('text-gray-900');
        }
        
        // Cập nhật khoảng thời gian
        $('#summary-date-range').text(`${dateRange.from || '--'} - ${dateRange.to || '--'}`);
        
        // Cập nhật tỷ lệ thu/chi
        if (summary.total_expense && summary.total_expense > 0) {
            const ratio = (summary.total_income / summary.total_expense * 100).toFixed(1);
            $('#summary-ratio').text(`${ratio}%`);
        } else {
            $('#summary-ratio').text('--');
        }
        
        // Danh mục thu nhiều nhất
        const incomeCategories = data.categories?.income || [];
        if (incomeCategories.length > 0) {
            incomeCategories.sort((a, b) => b.amount - a.amount);
            $('#summary-income-top-category').text(incomeCategories[0].category);
        } else {
            $('#summary-income-top-category').text('--');
        }
        
        // Danh mục chi nhiều nhất
        const expenseCategories = data.categories?.expense || [];
        if (expenseCategories.length > 0) {
            expenseCategories.sort((a, b) => b.amount - a.amount);
            $('#summary-expense-top-category').text(expenseCategories[0].category);
        } else {
            $('#summary-expense-top-category').text('--');
        }
    }

    // Cập nhật biểu đồ
    function updateCharts(data) {
        // Biểu đồ theo thời gian
        const timeSeries = data.time_series || [];
        const labels = timeSeries.map(item => item.date);
        const incomeData = timeSeries.map(item => item.income);
        const expenseData = timeSeries.map(item => item.expense);
        const balanceData = timeSeries.map(item => item.balance);
        
        timeSeriesChart.data.labels = labels;
        timeSeriesChart.data.datasets[0].data = incomeData;
        timeSeriesChart.data.datasets[1].data = expenseData;
        timeSeriesChart.data.datasets[2].data = balanceData;
        timeSeriesChart.update();
        
        // Biểu đồ danh mục thu
        const incomeCategories = data.categories?.income || [];
        incomePieChart.data.labels = incomeCategories.map(item => item.category);
        incomePieChart.data.datasets[0].data = incomeCategories.map(item => item.amount);
        incomePieChart.update();
        
        // Biểu đồ danh mục chi
        const expenseCategories = data.categories?.expense || [];
        expensePieChart.data.labels = expenseCategories.map(item => item.category);
        expensePieChart.data.datasets[0].data = expenseCategories.map(item => item.amount);
        expensePieChart.update();
    }
</script>
@endpush