@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý đăng ký Part-time
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <div class="text-sm text-gray-500">
                <span id="current-month" class="font-medium">{{ date('m/Y') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Bảng điều khiển thống kê -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="card bg-light-warning">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Lịch chờ duyệt
                                </div>
                                <div class="text-2sm text-gray-700" id="stat-pending-schedules">
                                    Đang tải...
                                </div>
                            </div>
                        </div>
                        <a href="#pending-list" class="btn btn-sm btn-warning">
                            <i class="ki-filled ki-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light-primary">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-people size-6 shrink-0 text-primary"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Top nhân viên
                                </div>
                                <div class="text-2sm text-gray-700" id="stat-top-user">
                                    Đang tải...
                                </div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="ki-filled ki-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light-success">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-wrap gap-2 px-3.5 py-2.5">
                        <div class="flex items-center flex-wrap gap-3.5">
                            <i class="ki-outline ki-time size-6 shrink-0 text-success"></i>
                            <div class="flex flex-col">
                                <div class="text-sm font-medium text-gray-900 mb-px">
                                    Tổng giờ Part-time tháng này
                                </div>
                                <div class="text-2sm text-gray-700" id="stat-total-hours">
                                    Đang tải...
                                </div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-success">
                            <i class="ki-filled ki-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danh sách lịch chờ duyệt -->
        <div class="card card-grid min-w-full" id="pending-list">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    <i class="ki-filled ki-timer text-warning me-2"></i>
                    Lịch Part-time chờ duyệt
                </h3>
                <div class="flex flex-wrap gap-2">
                    <button class="btn btn-success btn-sm" id="btn-approve-all">
                        <i class="ki-filled ki-check me-1"></i>
                        Duyệt tất cả
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border">
                            <thead>
                                <tr>
                                    <th class="w-[40px]">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" id="check-all">
                                        </div>
                                    </th>
                                    <th class="w-[60px] text-center">STT</th>
                                    <th class="w-[200px]">Nhân viên</th>
                                    <th class="w-[150px]">Ngày</th>
                                    <th class="w-[150px]">Thời gian</th>
                                    <th class="w-[100px]">Tổng giờ</th>
                                    <th class="w-[200px]">Ghi chú</th>
                                    <th class="w-[150px]">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="pending-schedules-list">
                                <!-- Dữ liệu sẽ được tải bằng AJAX -->
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Đang tải...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Biểu đồ thống kê -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Biểu đồ giờ làm part-time theo ngày -->
            <div class="card card-grid">
                <div class="card-header flex-wrap py-5">
                    <h3 class="card-title">
                        <i class="ki-filled ki-chart-line-up text-primary me-2"></i>
                        Giờ Part-time theo ngày
                    </h3>
                </div>
                <div class="card-body">
                    <div id="hours-by-day-chart" style="height: 300px;"></div>
                </div>
            </div>
            
            <!-- Biểu đồ top nhân viên có nhiều giờ part-time nhất -->
            <div class="card card-grid">
                <div class="card-header flex-wrap py-5">
                    <h3 class="card-title">
                        <i class="ki-filled ki-chart-simple-horizontal text-success me-2"></i>
                        Top nhân viên làm Part-time
                    </h3>
                </div>
                <div class="card-body">
                    <div id="top-employees-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Bộ lọc tìm kiếm -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    <i class="ki-filled ki-magnifier text-primary me-2"></i>
                    Tìm kiếm nâng cao
                </h3>
            </div>
            <div class="card-body !p-5">
                <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
                    <div class="relative">
                        <label class="text-gray-600 text-2sm mb-1 block">Nhân viên</label>
                        <select class="select" id="user-filter" data-filter="user_id">
                            <option value="">Tất cả nhân viên</option>
                            <!-- Danh sách nhân viên sẽ được tải bằng AJAX -->
                        </select>
                    </div>
                    
                    <div class="relative">
                        <label class="text-gray-600 text-2sm mb-1 block">Trạng thái</label>
                        <select class="select" id="status-filter" data-filter="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="rejected">Từ chối</option>
                        </select>
                    </div>
                    
                    <div class="relative">
                        <label class="text-gray-600 text-2sm mb-1 block">Từ ngày</label>
                        <input class="input" type="text" id="date-from-filter" data-filter="date_from" data-flatpickr="true" placeholder="Từ ngày">
                    </div>
                    
                    <div class="relative">
                        <label class="text-gray-600 text-2sm mb-1 block">Đến ngày</label>
                        <input class="input" type="text" id="date-to-filter" data-filter="date_to" data-flatpickr="true" placeholder="Đến ngày">
                    </div>
                </div>
                
                <div class="flex justify-end mt-5">
                    <button class="btn btn-primary" id="btn-apply-filter">
                        <i class="ki-filled ki-filter me-1"></i>
                        Lọc
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Lịch làm việc -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    <i class="ki-filled ki-calendar text-primary me-2"></i>
                    Lịch Part-time
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <input class="input input-sm" type="text" id="month-filter" data-flatpickr="true" data-flatpickr-type="month" placeholder="Chọn tháng">
                    </div>
                </div>
            </div>
            <div class="card-body !p-5">
                <div id="calendar"></div>
            </div>
        </div>
        
        <!-- Kết quả tìm kiếm -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    <i class="ki-filled ki-search text-primary me-2"></i>
                    Kết quả tìm kiếm
                </h3>
            </div>
            <div class="card-body">
                <div id="schedule-table" class="datatable-initialized">
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
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ngày</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Bắt đầu</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Kết thúc</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Tổng giờ</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/account/schedule/data'])
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
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/vi.js"></script>
<script>
    $(function() {
        // Khởi tạo flatpickr cho các trường ngày
        $("#month-filter").flatpickr({
            dateFormat: 'm-Y',
            enableTime: false,
            noCalendar: false,
            time_24hr: true,
            prevArrow: '<i class="ki-outline ki-left"></i>',
            nextArrow: '<i class="ki-outline ki-right"></i>',
        });
        flatpickrMake($("#date-from-filter"), 'date');
        flatpickrMake($("#date-to-filter"), 'date');
        
        // Tải thống kê và thiết lập lịch
        loadStatistics();
        loadUsersList();
        
        // Khởi tạo lịch fullcalendar
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'vi',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                // Lấy tháng hiện tại của lịch
                const startDate = fetchInfo.start;
                const year = startDate.getFullYear();
                const month = startDate.getMonth() + 2;
                const currentMonth = `${year}-${month}`;
                
                // Cập nhật giá trị bộ lọc tháng
                $("#month-filter").val(currentMonth);
                $("#current-month").text(`${month}/${year}`);
                
                // Gọi API lấy dữ liệu lịch
                axiosTemplate('get', '/account/schedule/calendar-data', {
                    month: currentMonth
                }, null)
                .then(res => {
                    if (res.data.status === 200) {
                        successCallback(res.data.events);
                    } else {
                        failureCallback(res.data.message);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải dữ liệu lịch:', error);
                    failureCallback('Không thể tải dữ liệu lịch');
                });
            },
            eventClick: function(info) {
                // Xử lý khi click vào sự kiện
                const eventId = info.event.id;
                showScheduleDetails(eventId);
            }
        });
        calendar.render();
        
        // Xử lý sự kiện khi thay đổi tháng trên lịch
        calendar.on('datesSet', function(info) {
            // Cập nhật tháng trong bộ lọc
            const currentMonth = info.view.currentStart;
            const monthValue = currentMonth.getFullYear() + '-' + 
                              String(currentMonth.getMonth() + 1).padStart(2, '0');
            
            $("#month-filter").val(monthValue);
        });
        
        // Xử lý khi thay đổi tháng trong bộ lọc
        $("#month-filter").on('change', function() {
            const selectedMonth = $(this).val();
            console.log(selectedMonth);
            
            if (selectedMonth) {
                const [month, year] = selectedMonth.split('-');
                calendar.gotoDate(new Date(year, month - 1, 1));
                $("#current-month").text(`${month}/${year}`);
            }
        });
        
        // Xử lý khi nhấn nút lọc
        $("#btn-apply-filter").on('click', function() {
            callAjaxDataTable($('.updater'));
        });
    });
    
    // Hàm tải thống kê
    function loadStatistics() {
        axiosTemplate('get', '/account/schedule/statistics', null, null)
            .then(res => {
                if (res.data.status === 200) {
                    const stats = res.data.statistics;
                    
                    // Cập nhật thông tin thống kê
                    $("#stat-total-users").text(stats.totalUsers + ' nhân viên');
                    $("#stat-total-schedules").text(stats.totalSchedules + ' lịch');
                    $("#stat-pending-schedules").text(stats.pendingSchedules + ' lịch');
                    $("#stat-total-hours").text(stats.totalHours + ' giờ');
                }
            })
            .catch(error => {
                console.error('Lỗi khi tải thống kê:', error);
            });
    }
    
    // Hàm tải danh sách nhân viên
    function loadUsersList() {
        axiosTemplate('get', '/account/schedule/users-list', null, null)
            .then(res => {
                if (res.data.status === 200) {
                    const users = res.data.users;
                    let options = '<option value="">Tất cả nhân viên</option>';
                    
                    users.forEach(user => {
                        options += `<option value="${user.id}">${user.name}</option>`;
                    });
                    
                    $("#user-filter").html(options);
                }
            })
            .catch(error => {
                console.error('Lỗi khi tải danh sách nhân viên:', error);
            });
    }
    
    // Hàm xem chi tiết lịch
    function showScheduleDetails(scheduleId) {
        try {
            // Hiển thị modal
            KTModal.getInstance(document.querySelector('#schedule-detail-modal')).show();
            
            // Hiển thị loading
            $('#schedule-detail-content').html(`
                <div class="flex justify-center items-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);
            
            // Gọi API lấy chi tiết
            axiosTemplate('get', `/account/schedule/${scheduleId}/detail`, null, null)
                .then(res => {
                    if (res.data.status === 200) {
                        $('#schedule-detail-content').html(res.data.content);
                    } else {
                        $('#schedule-detail-content').html(`
                            <div class="text-center text-danger">
                                ${res.data.message || 'Không thể tải thông tin chi tiết'}
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    $('#schedule-detail-content').html(`
                        <div class="text-center text-danger">
                            Đã xảy ra lỗi khi tải thông tin
                        </div>
                    `);
                    console.error(error);
                });
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi xem chi tiết lịch');
            console.error(error);
        }
    }
    
    // Hàm duyệt lịch
    async function approveSchedule(id) {
        try {
            Notiflix.Confirm.show(
                'Duyệt lịch làm việc',
                'Bạn có chắc chắn muốn duyệt lịch làm việc này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/schedule/update-status', null, {
                        id: id,
                        status: 'approved',
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                        loadStatistics();
                        
                        // Đóng modal nếu đang mở
                        if ($("#schedule-detail-modal").hasClass("shown")) {
                            KTModal.getInstance(document.querySelector('#schedule-detail-modal')).hide();
                        }
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi duyệt lịch');
            console.error(error);
        }
    }
    
    // Hàm từ chối lịch
    async function rejectSchedule(id) {
        try {
            Notiflix.Confirm.show(
                'Từ chối lịch làm việc',
                'Bạn có chắc chắn muốn từ chối lịch làm việc này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/account/schedule/update-status', null, {
                        id: id,
                        status: 'rejected',
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                        loadStatistics();
                        
                        // Đóng modal nếu đang mở
                        if ($("#schedule-detail-modal").hasClass("shown")) {
                            KTModal.getInstance(document.querySelector('#schedule-detail-modal')).hide();
                        }
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi từ chối lịch');
            console.error(error);
        }
    }
</script>
@endpush