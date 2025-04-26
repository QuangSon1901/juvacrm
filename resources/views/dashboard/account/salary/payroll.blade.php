@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Bảng lương nhân viên
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
        <!-- Bảng thống kê -->
        <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-dollar size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng chi lương tháng này
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ number_format($stats['totalPaid'] ?? 0, 0, ',', '.') }} VNĐ
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-notification-status size-6 shrink-0 text-success"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Bảng lương đã duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['processedCount'] ?? 0 }} bảng lương
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Bảng lương chờ duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['pendingCount'] ?? 0 }} bảng lương
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-abstract-26 size-6 shrink-0 text-danger"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng nhân viên
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['employeeCount'] ?? 0 }} nhân viên
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danh sách bảng lương -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách bảng lương
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <select class="select select-sm" id="user-filter" data-filter="user_id">
                            <option value="">Tất cả nhân viên</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="relative">
                        <select class="select select-sm" id="month-filter" data-filter="period_month">
                            <option value="">Tất cả tháng</option>
                            @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>Tháng {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="relative">
                        <select class="select select-sm" id="year-filter" data-filter="period_year">
                            <option value="">Tất cả năm</option>
                            @for($i = 2020; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>Năm {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="relative">
                        <select class="select select-sm" id="status-filter" data-filter="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processed">Đã duyệt</option>
                            <option value="paid">Đã chi</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-primary btn-sm" data-modal-toggle="#calculate-salary-modal">
                        <i class="ki-filled ki-calculator me-1"></i>
                        Tính lương
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="payroll-table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">STT</th>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Nhân viên</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Kỳ lương</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Lương cơ bản</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Thêm giờ</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Hoa hồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Tiền công nhiệm vụ</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Khấu trừ</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Thực nhận</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/account/salary/payroll-data'])
                        </table>
                    </div>
                    <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                        <div class="flex items-center gap-2 order-2 md:order-1">
                            Hiển thị {{TABLE_PERPAGE_NUM}} mỗi trang
                        </div>
                        <div class="flex items-center gap-4 order-1 md:order-2">
                            <p><span class="sorterlow"></span> - <span class="sorterhigh"></span> trong <span class="sorterrecords"></span></p>
                            <div class="pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tính lương -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="calculate-salary-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Tính lương nhân viên
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="calculate-salary-form" class="grid gap-5 px-0 py-5">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Nhân viên <span class="text-red-500">*</span>
                        </label>
                        <select class="select" name="user_id" id="calc-user-id" required>
                            <option value="">Chọn nhân viên</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Kỳ lương <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <select class="select" name="period_month" id="calc-period-month" required>
                                <option value="">Tháng</option>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>Tháng {{ $i }}</option>
                                @endfor
                            </select>
                            <select class="select" name="period_year" id="calc-period-year" required>
                                <option value="">Năm</option>
                                @for($i = 2020; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Tính lương
                    </button>
                </div>
            </form>
            
            <div id="salary-calculation-result" class="hidden mt-5 border-t pt-5">
                <h4 class="font-semibold text-lg mb-4">Kết quả tính lương</h4>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="font-medium">Nhân viên: <span id="result-user-name" class="font-normal"></span></p>
                        <p class="font-medium">Kỳ lương: <span id="result-period" class="font-normal"></span></p>
                    </div>
                    <div>
                        <p class="font-medium">Số ngày làm việc: <span id="result-working-days" class="font-normal"></span></p>
                        <p class="font-medium">Giờ làm thêm: <span id="result-overtime-hours" class="font-normal"></span></p>
                        <p class="font-medium">Giờ làm part-time: <span id="result-parttime-hours" class="font-normal"></span></p>
                    </div>
                </div>
                
                <div class="card-table scrollable-x-auto pb-3">
                    <table class="table align-middle text-sm text-gray-500">
                        <tbody>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Lương cơ bản
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-base-salary"></span> VNĐ
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Lương part-time
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-parttime-salary"></span> VNĐ
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Lương làm thêm giờ
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-overtime-amount"></span> VNĐ
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Hoa hồng hợp đồng
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-commission-amount"></span> VNĐ
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Tiền công hoàn thành công việc
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-task-mission-amount"></span> VNĐ
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Tạm ứng
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-advance-amount"></span> VNĐ
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Thuế thu nhập
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-tax-amount"></span> VNĐ
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 min-w-28 text-gray-600 font-medium">
                                    Bảo hiểm
                                </td>
                                <td class="py-2 text-gray-700 font-medium min-w-32 text-right">
                                    <span id="result-insurance-amount"></span> VNĐ
                                </td>
                            </tr>
                            <tr class="border-t border-gray-200">
                                <td class="py-2 min-w-28 text-gray-800 font-semibold">
                                    Thực nhận
                                </td>
                                <td class="py-2 text-success font-semibold min-w-32 text-right">
                                    <span id="result-final-amount"></span> VNĐ
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="flex justify-end mt-4">
                    <button id="save-salary-btn" class="btn btn-primary">
                        <i class="ki-filled ki-save me-1"></i>
                        Lưu bảng lương
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chi tiết bảng lương -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="salary-detail-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chi tiết bảng lương
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="salary-detail-content" class="grid gap-5 px-0 py-5">
                <!-- Nội dung chi tiết lương sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let calculatedSalaryData = null;
    
    $(function() {
        // Xử lý khi thay đổi bộ lọc
        $('[data-filter]').on('change', function() {
            callAjaxDataTable($('.updater'));
        });
        
        // Xử lý form tính lương
        $('#calculate-salary-form').on('submit', async function(e) {
            e.preventDefault();
            
            const userId = $('#calc-user-id').val();
            const periodMonth = $('#calc-period-month').val();
            const periodYear = $('#calc-period-year').val();
            
            if (!userId || !periodMonth || !periodYear) {
                showAlert('warning', 'Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            try {
                const res = await axiosTemplate('get', '/account/salary/calculate', {
                    user_id: userId,
                    period_month: periodMonth,
                    period_year: periodYear
                }, null);
                
                if (res.data.status === 200) {
                    // Lưu dữ liệu tính toán
                    calculatedSalaryData = res.data.data;
                    
                    // Hiển thị kết quả
                    $('#salary-calculation-result').removeClass('hidden');
                    
                    // Cập nhật thông tin hiển thị
                    $('#result-user-name').text(calculatedSalaryData.user.name);
                    $('#result-period').text(calculatedSalaryData.period.month + '/' + calculatedSalaryData.period.year);
                    $('#result-working-days').text(calculatedSalaryData.workingDays);
                    $('#result-overtime-hours').text(calculatedSalaryData.overtimeHours);
                    $('#result-parttime-hours').text(calculatedSalaryData.partTimeHours);
                    
                    // Định dạng số tiền
                    $('#result-base-salary').text(formatNumber(calculatedSalaryData.baseSalary));
                    $('#result-parttime-salary').text(formatNumber(calculatedSalaryData.partTimeSalary));
                    $('#result-overtime-amount').text(formatNumber(calculatedSalaryData.overtimeAmount));
                    $('#result-commission-amount').text(formatNumber(calculatedSalaryData.commissionAmount));
                    $('#result-task-mission-amount').text(formatNumber(calculatedSalaryData.taskMissionAmount)); // Thêm dòng này
                    $('#result-advance-amount').text(formatNumber(calculatedSalaryData.advanceAmount));
                    $('#result-tax-amount').text(formatNumber(calculatedSalaryData.taxAmount));
                    $('#result-insurance-amount').text(formatNumber(calculatedSalaryData.insuranceAmount));
                    $('#result-final-amount').text(formatNumber(calculatedSalaryData.finalAmount));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi tính lương');
                console.error(error);
            }
        });
        
        // Xử lý nút lưu bảng lương
        $('#save-salary-btn').on('click', async function() {
            if (!calculatedSalaryData) {
                showAlert('warning', 'Không có dữ liệu lương để lưu');
                return;
            }
            
            try {
                const res = await axiosTemplate('post', '/account/salary/save-salary', null, {
                    user_id: calculatedSalaryData.user.id,
                    period_month: calculatedSalaryData.period.month,
                    period_year: calculatedSalaryData.period.year,
                    base_salary: calculatedSalaryData.baseSalary + calculatedSalaryData.partTimeSalary,
                    attendance_bonus: 0,
                    overtime_hours: calculatedSalaryData.overtimeHours,
                    overtime_amount: calculatedSalaryData.overtimeAmount,
                    commission_amount: calculatedSalaryData.commissionAmount,
                    task_mission_amount: calculatedSalaryData.taskMissionAmount, // Thêm dòng này
                    deductions: 0,
                    tax_amount: calculatedSalaryData.taxAmount,
                    insurance_amount: calculatedSalaryData.insuranceAmount,
                    advance_payments: calculatedSalaryData.advanceAmount,
                    final_amount: calculatedSalaryData.finalAmount,
                    commission_ids: calculatedSalaryData.commissionIds || []
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#calculate-salary-modal')).hide();
                    calculatedSalaryData = null;
                    
                    // Cập nhật lại bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi lưu bảng lương');
                console.error(error);
            }
        });
    });
    
    // Hàm định dạng số
    function formatNumber(number) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(number));
    }
    
    // Hàm xử lý khi xem chi tiết bảng lương
    async function viewSalaryDetail(id) {
        try {
            // Hiển thị modal
            KTModal.getInstance(document.querySelector('#salary-detail-modal')).show();
            
            // Hiển thị loading
            $('#salary-detail-content').html(`
                <div class="flex justify-center items-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);
            
            // Gọi API lấy chi tiết
            const res = await axiosTemplate('get', `/account/salary/payroll/${id}/detail`, null, null);
            
            if (res.data.status === 200) {
                $('#salary-detail-content').html(res.data.content);
            } else {
                $('#salary-detail-content').html('<div class="text-center text-danger">Không thể tải thông tin.</div>');
            }
        } catch (error) {
            $('#salary-detail-content').html('<div class="text-center text-danger">Đã xảy ra lỗi khi tải thông tin.</div>');
            console.error(error);
        }
    }
    
    // Hàm xử lý khi xử lý bảng lương
    async function processSalary(id, status) {
        const statusText = status === 'processed' ? 'duyệt' : 'chi trả';
        
        try {
            Notiflix.Confirm.show(
                `${status === 'processed' ? 'Duyệt' : 'Chi trả'} bảng lương`,
                `Bạn có chắc chắn muốn ${statusText} bảng lương này?`,
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/salary/process-salary', null, {
                        id: id,
                        status: status,
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', `Đã xảy ra lỗi khi ${statusText} bảng lương`);
            console.error(error);
        }
    }
</script>
@endpush