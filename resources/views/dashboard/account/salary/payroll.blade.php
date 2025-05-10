@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Bảng lương nhân viên
            </h1>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Thống kê -->
        <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-dollar size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng chi lương tháng trước
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
                            Tổng lương cơ bản
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ number_format($stats['totalBaseSalary'] ?? 0, 0, ',', '.') }} VNĐ
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng hoa hồng
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ number_format($stats['totalCommission'] ?? 0, 0, ',', '.') }} VNĐ
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-abstract-26 size-6 shrink-0 text-danger"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng nhiệm vụ
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ number_format($stats['totalMission'] ?? 0, 0, ',', '.') }} VNĐ
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Số lượng theo trạng thái -->
        <div class="grid !grid-cols-1 lg:!grid-cols-3 gap-5">
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
                    <i class="ki-outline ki-abstract-26 size-6 shrink-0 text-primary"></i>
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
                        <select class="select select-sm" id="month-filter" data-filter="period_month">
                            <option value="">Tất cả tháng</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == $previousMonth ? 'selected' : '' }}>Tháng {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="relative">
                        <select class="select select-sm" id="year-filter" data-filter="period_year">
                            <option value="">Tất cả năm</option>
                            @for($i = 2020; $i <= date('Y'); $i++)
                                <option value="{{ $i }}" {{ $i == $previousYear ? 'selected' : '' }}>Năm {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="relative">
                        <select class="select select-sm" id="status-filter" data-filter="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processed">Đã duyệt</option>
                            <option value="paid">Đã thanh toán</option>
                        </select>
                    </div>

                    <button class="btn btn-primary btn-sm" id="calculate-salary-btn">
                        <i class="ki-filled ki-calculator me-1"></i>
                        Tính lương tháng trước
                    </button>

                    <div class="dropdown" data-dropdown="true" data-dropdown-trigger="click">
                        <button class="dropdown-toggle btn btn-success btn-sm">
                            <i class="ki-filled ki-check me-1"></i>
                            Thao tác hàng loạt
                        </button>
                        <div class="dropdown-content w-full max-w-56 py-2">
                            <div class="menu menu-default flex flex-col w-full">
                                <div class="menu-item">
                                    <button class="menu-link" id="bulk-approve-btn">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-check text-success"></i>
                                        </span>
                                        <span class="menu-title !text-left">
                                        Duyệt đã chọn
                                        </span>
                                    </button>
                                </div>
                                <div class="menu-item">
                                    <button class="menu-link" id="bulk-pay-btn">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-dollar text-primary"></i>
                                        </span>
                                        <span class="menu-title !text-left">
                                        Thanh toán đã chọn
                                        </span>
                                    </button>
                                </div>
                                <div class="menu-separator" bis_skin_checked="1"></div>
                                <div class="menu-item">
                                    <button class="menu-link" id="bulk-approve-all-btn">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-check-squared text-success"></i>
                                        </span>
                                        <span class="menu-title !text-left">
                                        Duyệt tất cả
                                        </span>
                                    </button>
                                </div>
                                <div class="menu-item">
                                    <button class="menu-link" id="bulk-pay-all-btn">
                                        <span class="menu-icon">
                                            <i class="ki-filled ki-dollar text-primary"></i>
                                        </span>
                                        <span class="menu-title !text-left">
                                        Thanh toán tất cả
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="payroll-table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[40px]">
                                        <div class="checkbox">
                                            <input class="form-checkbox checkbox" type="checkbox" id="checkbox-all">
                                            <label for="checkbox-all"></label>
                                        </div>
                                    </th>
                                    <th class="w-[40px] text-center">STT</th>
                                    <th class="w-[180px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Nhân viên</span>
                                        </span>
                                    </th>
                                    <th class="w-[80px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Kỳ lương</span>
                                        </span>
                                    </th>
                                    <th class="w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Lương cơ bản</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Hoa hồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Tiền công nhiệm vụ</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Khấu trừ</span>
                                        </span>
                                    </th>
                                    <th class="w-[120px]">
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

<!-- Modal: Kết quả tính lương -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="salary-result-modal" style="z-index: 90;">
    <div class="modal-content max-w-[800px] top-5 lg:top-[5%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Kết quả tính lương
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="salary-calculation-result" class="grid gap-5 px-0 py-2">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-lg">Kết quả tính lương tháng <span id="result-period"></span></h4>
                    <div class="flex gap-2">
                        <span class="text-success font-medium">Thành công: <span id="result-success-count">0</span></span>
                        <span class="text-danger font-medium">Lỗi: <span id="result-error-count">0</span></span>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg mb-4">
                    <span class="font-medium">Tổng tiền lương:</span>
                    <span class="text-lg font-semibold text-success" id="result-total-salary">0 VNĐ</span>
                </div>

                <div class="card-table scrollable-y-auto max-h-[400px]">
                    <table class="table align-middle text-sm text-gray-500">
                        <thead>
                            <tr>
                                <th class="py-2">Nhân viên</th>
                                <th class="py-2">Lương</th>
                                <th class="py-2">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="result-employees">
                            <!-- Kết quả sẽ được thêm ở đây bằng JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Xử lý hàng loạt -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="bulk-process-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title" id="bulk-process-title">
                Xử lý lương hàng loạt
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="grid gap-5 px-0 py-5">
                <p id="bulk-process-message">Bạn đang chuẩn bị xử lý <span class="font-semibold" id="bulk-process-count">0</span> bảng lương.</p>

                <div class="flex justify-center">
                    <button id="bulk-process-confirm" class="btn btn-primary justify-center">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Chi tiết bảng lương -->
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
    // Biến toàn cục
    let selectedSalaryIds = [];

    $(function() {
        // Xử lý khi thay đổi bộ lọc
        $('[data-filter]').on('change', function() {
            callAjaxDataTable($('.updater'));
        });

        // Xử lý nút tính lương
        $('#calculate-salary-btn').on('click', async function() {
            try {
                // Lấy tháng trước
                const now = new Date();
                let previousMonth = now.getMonth(); // getMonth() trả về 0-11
                if (previousMonth === 0) {
                    previousMonth = 12;
                }

                let previousYear = now.getFullYear();
                if (previousMonth === 12) {
                    previousYear--;
                }

                const confirmed = await new Promise(resolve => {
                    Notiflix.Confirm.show(
                        'Tính lương hàng loạt',
                        `Bạn đang chuẩn bị tính lương tháng ${previousMonth}/${previousYear} cho tất cả nhân viên. Tiếp tục?`,
                        'Tiếp tục',
                        'Hủy bỏ',
                        () => resolve(true),
                        () => resolve(false)
                    );
                });

                if (!confirmed) {
                    return;
                }

                // Hiển thị loading
                Notiflix.Loading.circle('Đang tính lương...');

                const res = await axiosTemplate('get', '/account/salary/calculate', {
                    period_month: previousMonth,
                    period_year: previousYear
                }, null);

                Notiflix.Loading.remove();

                if (res.data.status === 200) {
                    // Hiển thị kết quả
                    $('#result-period').text(res.data.data.period);
                    $('#result-success-count').text(res.data.data.success_count);
                    $('#result-error-count').text(res.data.data.error_count);
                    $('#result-total-salary').text(res.data.data.total_salary + ' VNĐ');

                    // Render danh sách nhân viên
                    let html = '';
                    res.data.data.results.forEach(result => {
                        html += `<tr>
                            <td class="py-2 text-gray-800">${result.user_name}</td>
                            <td class="py-2 text-gray-800">${result.status === 'success' ? result.salary + ' VNĐ' : '-'}</td>
                            <td class="py-2">
                                <span class="badge badge-sm badge-outline badge-${result.status === 'success' ? 'success' : 'danger'}">
                                    ${result.status === 'success' ? 'Thành công' : 'Lỗi: ' + result.message}
                                </span>
                            </td>
                        </tr>`;
                    });

                    $('#result-employees').html(html);

                    // Hiển thị modal kết quả
                    KTModal.getInstance(document.querySelector('#salary-result-modal')).show();

                    // Cập nhật lại bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                Notiflix.Loading.remove();
                showAlert('error', 'Đã xảy ra lỗi khi tính lương');
                console.error(error);
            }
        });

        // Xử lý checkbox chọn tất cả
        $('#checkbox-all').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.checkbox-row').prop('checked', isChecked);
            updateSelectedIds();
        });

        // Xử lý khi chọn từng checkbox
        $(document).on('change', '.checkbox-row', function() {
            updateSelectedIds();
            // Kiểm tra nếu tất cả đều được chọn
            const totalRows = $('.checkbox-row').length;
            const checkedRows = $('.checkbox-row:checked').length;
            $('#checkbox-all').prop('checked', totalRows === checkedRows && totalRows > 0);
        });

        // Xử lý nút duyệt lương hàng loạt
        $('#bulk-approve-btn').on('click', function() {
            if (selectedSalaryIds.length === 0) {
                showAlert('warning', 'Vui lòng chọn ít nhất một bảng lương để duyệt');
                return;
            }

            $('#bulk-process-title').text('Duyệt lương hàng loạt');
            $('#bulk-process-message').html(`Bạn đang chuẩn bị <span class="font-semibold text-success">duyệt</span> <span class="font-semibold">${selectedSalaryIds.length}</span> bảng lương đã chọn.`);
            $('#bulk-process-confirm').data('action', 'process');
            KTModal.getInstance(document.querySelector('#bulk-process-modal')).show();
        });

        // Xử lý nút chi lương hàng loạt
        $('#bulk-pay-btn').on('click', function() {
            if (selectedSalaryIds.length === 0) {
                showAlert('warning', 'Vui lòng chọn ít nhất một bảng lương để thanh toán');
                return;
            }

            $('#bulk-process-title').text('Thanh toán lương hàng loạt');
            $('#bulk-process-message').html(`Bạn đang chuẩn bị <span class="font-semibold text-primary">thanh toán</span> <span class="font-semibold">${selectedSalaryIds.length}</span> bảng lương đã chọn.`);
            $('#bulk-process-confirm').data('action', 'pay');
            KTModal.getInstance(document.querySelector('#bulk-process-modal')).show();
        });

        // Xử lý nút duyệt tất cả lương chờ xử lý
        $('#bulk-approve-all-btn').on('click', async function() {
            try {
                const res = await axiosTemplate('get', '/account/salary/get-pending-ids', null, null);

                if (res.data.status === 200) {
                    const pendingIds = res.data.data.ids;

                    if (pendingIds.length === 0) {
                        showAlert('warning', 'Không có bảng lương nào đang chờ duyệt');
                        return;
                    }

                    $('#bulk-process-title').text('Duyệt tất cả lương chờ xử lý');
                    $('#bulk-process-message').html(`Bạn đang chuẩn bị <span class="font-semibold text-success">duyệt</span> <span class="font-semibold">${pendingIds.length}</span> bảng lương đang chờ xử lý.`);
                    $('#bulk-process-confirm').data('action', 'process-all');
                    KTModal.getInstance(document.querySelector('#bulk-process-modal')).show();
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi lấy danh sách bảng lương chờ duyệt');
                console.error(error);
            }
        });

        // Xử lý nút chi tất cả lương đã duyệt
        $('#bulk-pay-all-btn').on('click', async function() {
            try {
                const res = await axiosTemplate('get', '/account/salary/get-processed-ids', null, null);

                if (res.data.status === 200) {
                    const processedIds = res.data.data.ids;

                    if (processedIds.length === 0) {
                        showAlert('warning', 'Không có bảng lương nào đã duyệt');
                        return;
                    }

                    $('#bulk-process-title').text('Thanh toán tất cả lương đã duyệt');
                    $('#bulk-process-message').html(`Bạn đang chuẩn bị <span class="font-semibold text-primary">thanh toán</span> <span class="font-semibold">${processedIds.length}</span> bảng lương đã duyệt.`);
                    $('#bulk-process-confirm').data('action', 'pay-all');
                    KTModal.getInstance(document.querySelector('#bulk-process-modal')).show();
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi lấy danh sách bảng lương đã duyệt');
                console.error(error);
            }
        });

        // Xử lý nút xác nhận xử lý hàng loạt
        $('#bulk-process-confirm').on('click', async function() {
            const action = $(this).data('action');

            try {
                Notiflix.Loading.circle('Đang xử lý...');

                let res;

                if (action === 'process' || action === 'pay') {
                    res = await axiosTemplate('post', '/account/salary/bulk-process-salary', null, {
                        salary_ids: selectedSalaryIds,
                        action: action
                    });
                } else if (action === 'process-all') {
                    res = await axiosTemplate('post', '/account/salary/bulk-process-all-pending', null, null);
                } else if (action === 'pay-all') {
                    res = await axiosTemplate('post', '/account/salary/bulk-pay-all-processed', null, null);
                }

                Notiflix.Loading.remove();

                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#bulk-process-modal')).hide();

                    // Cập nhật lại bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                Notiflix.Loading.remove();
                showAlert('error', 'Đã xảy ra lỗi khi xử lý hàng loạt');
                console.error(error);
            }
        });
    });

    // Cập nhật danh sách ID đã chọn
    function updateSelectedIds() {
        selectedSalaryIds = [];
        $('.checkbox-row:checked').each(function() {
            selectedSalaryIds.push($(this).val());
        });
    }

    // Xem chi tiết bảng lương
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

    // Xử lý lương (duyệt/chi trả)
    async function processSalary(id, status) {
        const statusText = status === 'processed' ? 'duyệt' : 'thanh toán';

        try {
            Notiflix.Confirm.show(
                `${status === 'processed' ? 'Duyệt' : 'Thanh toán'} bảng lương`,
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