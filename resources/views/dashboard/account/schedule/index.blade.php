@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý lịch làm việc
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
        <!-- Bảng điều khiển thống kê -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
            <!-- Thống kê -->
            <div class="lg:col-span-8">
                <div class="grid grid-cols-4 gap-4">
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-primary rounded-full p-2 mr-3">
                            <i class="ki-outline ki-calendar-tick text-primary text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Tổng lịch</div>
                            <div class="text-xl font-bold" id="total-count">{{ $stats['totalSchedules'] }}</div>
                        </div>
                    </div>
                    
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-success rounded-full p-2 mr-3">
                            <i class="ki-outline ki-check text-success text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Đã duyệt</div>
                            <div class="text-xl font-bold" id="approved-count">{{ $stats['approvedSchedules'] }}</div>
                        </div>
                    </div>
                    
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-warning rounded-full p-2 mr-3">
                            <i class="ki-outline ki-timer text-warning text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Chờ duyệt</div>
                            <div class="text-xl font-bold" id="pending-count">{{ $stats['pendingSchedules'] }}</div>
                        </div>
                    </div>
                    
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-danger rounded-full p-2 mr-3">
                            <i class="ki-outline ki-cross-square text-danger text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Từ chối</div>
                            <div class="text-xl font-bold" id="rejected-count">{{ $stats['rejectedSchedules'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danh sách lịch đăng ký -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách lịch đăng ký
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
                        <select class="select select-sm" id="status-filter" data-filter="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ duyệt</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="rejected">Từ chối</option>
                            <option value="cancel_requested">Yêu cầu hủy</option>
                        </select>
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-from-filter" data-filter="date_from" data-flatpickr="true" placeholder="Từ ngày">
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-to-filter" data-filter="date_to" data-flatpickr="true" placeholder="Đến ngày">
                    </div>

                    <div class="relative">
                        <button class="btn btn-success btn-sm w-max" id="batch-approve-btn">
                            <i class="ki-outline ki-check me-1"></i> Duyệt đã chọn
                        </button>
                    </div>

                    <div class="relative">
                        <button class="btn btn-primary btn-sm w-max" data-modal-toggle="#create-schedule-modal">
                            <i class="ki-outline ki-plus me-1"></i> Tạo lịch
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="schedule-table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th width="30px">
                                        <input type="checkbox" id="select-all-schedules" class="checkbox">
                                    </th>
                                    <th class="w-[150px]">Nhân viên</th>
                                    <th class="w-[100px]">Ngày</th>
                                    <th class="w-[90px]">Bắt đầu</th>
                                    <th class="w-[90px]">Kết thúc</th>
                                    <th class="w-[80px]">Tổng giờ</th>
                                    <th class="w-[120px]">Trạng thái</th>
                                    <th class="w-[120px]">Thao tác</th>
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

<!-- Modal tạo lịch làm việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-schedule-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Tạo lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-schedule-form" class="grid gap-4 py-4">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Nhân viên <span class="text-red-500">*</span>
                    </label>
                    <select class="select" name="user_id" required>
                        <option value="">Chọn nhân viên</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm mb-1">
                            Ngày làm việc <span class="text-red-500">*</span>
                        </label>
                        <input class="input" type="text" name="schedule_date" data-flatpickr="true" placeholder="Chọn ngày" required>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm mb-1">
                            Ghi chú
                        </label>
                        <textarea class="textarea resize-none" name="note" rows="1" placeholder="Ghi chú (nếu có)"></textarea>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm mb-1">
                            Giờ bắt đầu <span class="text-red-500">*</span>
                        </label>
                        <input class="input" type="text" name="start_time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Giờ bắt đầu" required>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm mb-1">
                            Giờ kết thúc <span class="text-red-500">*</span>
                        </label>
                        <input class="input" type="text" name="end_time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Giờ kết thúc" required>
                    </div>
                </div>
                
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-primary justify-center">
                        Tạo lịch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal chi tiết lịch làm việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="schedule-detail-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chi tiết lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="schedule-detail-content" class="grid gap-5 px-0 py-5">
                <!-- Nội dung chi tiết sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận từ chối -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="reject-modal" style="z-index: 90;">
    <div class="modal-content max-w-[400px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Từ chối lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="reject-form" class="grid gap-4 py-4">
                <input type="hidden" name="id" id="reject-schedule-id">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Lý do từ chối <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="note" rows="3" placeholder="Nhập lý do từ chối" required></textarea>
                </div>
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-danger justify-center">
                        Từ chối
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal từ chối yêu cầu hủy -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="reject-cancel-modal" style="z-index: 90;">
    <div class="modal-content max-w-[400px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Từ chối yêu cầu hủy lịch
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="reject-cancel-form" class="grid gap-4 py-4">
                <input type="hidden" name="id" id="reject-cancel-schedule-id">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Lý do từ chối <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="reason" rows="3" placeholder="Nhập lý do từ chối hủy lịch" required></textarea>
                </div>
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-danger justify-center">
                        Từ chối yêu cầu hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Khởi tạo flatpickr cho các trường ngày và giờ
        flatpickrMake($("input[name='schedule_date']"), 'date');
        flatpickrMake($("input[name='start_time']"), 'time', {enableSeconds: false, noCalendar: true, enableTime: true, dateFormat: 'H:i'});
        flatpickrMake($("input[name='end_time']"), 'time', {enableSeconds: false, noCalendar: true, enableTime: true, dateFormat: 'H:i'});
        flatpickrMake($("#date-from-filter"), 'date');
        flatpickrMake($("#date-to-filter"), 'date');
        
        // Xử lý sự kiện khi thay đổi bộ lọc
        $('[data-filter]').on('change', function() {
            callAjaxDataTable($('.updater'));
        });
        
        // Xử lý form tạo lịch làm việc
        $('#create-schedule-form').on('submit', async function(e) {
            e.preventDefault();
            
            const userId = $(this).find('select[name="user_id"]').val();
            const scheduleDate = $(this).find('input[name="schedule_date"]').val();
            const startTime = $(this).find('input[name="start_time"]').val();
            const endTime = $(this).find('input[name="end_time"]').val();
            const note = $(this).find('textarea[name="note"]').val();
            
            // Kiểm tra form
            if (!userId || !scheduleDate || !startTime || !endTime) {
                showAlert('warning', 'Vui lòng điền đầy đủ thông tin bắt buộc');
                return;
            }
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/schedule/create', null, {
                    user_id: userId,
                    schedule_date: scheduleDate,
                    start_time: startTime,
                    end_time: endTime,
                    note: note
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-schedule-modal')).hide();
                    
                    // Cập nhật thống kê
                    updateCounters(res.data.stats);
                    
                    // Làm mới bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                    
                    // Reset form
                    $(this).trigger('reset');
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi tạo lịch làm việc');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Tạo lịch');
            }
        });

        // Xử lý checkbox chọn tất cả
        $(document).on('change', '#select-all-schedules', function() {
            $('.schedule-checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Xử lý nút phê duyệt hàng loạt
        $('#batch-approve-btn').on('click', async function() {
            const selectedIds = $('.schedule-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedIds.length === 0) {
                showAlert('warning', 'Vui lòng chọn ít nhất một lịch làm việc');
                return;
            }
            
            try {
                $(this).prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/schedule/batch-approve', null, {
                    ids: selectedIds
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    
                    // Cập nhật thống kê
                    updateCounters(res.data.stats);
                    
                    // Làm mới bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi duyệt lịch hàng loạt');
                console.error(error);
            } finally {
                $(this).prop('disabled', false).html('<i class="ki-outline ki-check me-1"></i> Duyệt đã chọn');
            }
        });
        
        // Xử lý form từ chối lịch
        $('#reject-form').on('submit', async function(e) {
            e.preventDefault();
            
            const id = $('#reject-schedule-id').val();
            const note = $(this).find('textarea[name="note"]').val();
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/schedule/update-status', null, {
                    id: id,
                    status: 'rejected',
                    note: note
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#reject-modal')).hide();
                    
                    // Cập nhật thống kê
                    updateCounters(res.data.stats);
                    
                    // Làm mới bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                    
                    // Reset form
                    $(this).trigger('reset');
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi từ chối lịch');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Từ chối');
            }
        });
        
        // Xử lý form từ chối yêu cầu hủy
        $('#reject-cancel-form').on('submit', async function(e) {
            e.preventDefault();
            
            const id = $('#reject-cancel-schedule-id').val();
            const reason = $(this).find('textarea[name="reason"]').val();
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', '/account/schedule/reject-cancel', null, {
                    id: id,
                    reason: reason
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#reject-cancel-modal')).hide();
                    
                    // Cập nhật thống kê
                    updateCounters(res.data.stats);
                    
                    // Làm mới bảng dữ liệu
                    callAjaxDataTable($('.updater'));
                    
                    // Reset form
                    $(this).trigger('reset');
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi từ chối yêu cầu hủy');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Từ chối yêu cầu hủy');
            }
        });
    });
    
    // Hàm xem chi tiết lịch
    function showScheduleDetails(id) {
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
        axiosTemplate('get', `/account/schedule/${id}/detail`, null, null)
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
    }
    
    // Hàm duyệt nhanh lịch
    function quickApprove(id) {
        Notiflix.Confirm.show(
            'Duyệt lịch làm việc',
            'Bạn có chắc chắn muốn duyệt lịch làm việc này?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                try {
                    const res = await axiosTemplate('post', '/account/schedule/update-status', null, {
                        id: id,
                        status: 'approved'
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        
                        // Cập nhật thống kê
                        updateCounters(res.data.stats);
                        
                        // Cập nhật dòng thay vì làm mới toàn bộ bảng
                        $(`tr[data-id="${id}"]`).replaceWith(res.data.row_html);
                    } else {
                        showAlert('warning', res.data.message);
                    }
                } catch (error) {
                    showAlert('error', 'Đã xảy ra lỗi khi duyệt lịch');
                    console.error(error);
                }
            }
        );
    }
    
    // Hàm từ chối lịch
    function rejectSchedule(id) {
        $('#reject-schedule-id').val(id);
        KTModal.getInstance(document.querySelector('#reject-modal')).show();
    }
    
    // Hàm xóa lịch
    function deleteSchedule(id) {
        Notiflix.Confirm.show(
            'Xóa lịch làm việc',
            'Bạn có chắc chắn muốn xóa lịch làm việc này?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                try {
                    const res = await axiosTemplate('post', '/account/schedule/delete', null, {
                        id: id
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        
                        // Cập nhật thống kê
                        updateCounters(res.data.stats);
                        
                        // Làm mới bảng dữ liệu
                        callAjaxDataTable($('.updater'));
                    } else {
                        showAlert('warning', res.data.message);
                    }
                } catch (error) {
                    showAlert('error', 'Đã xảy ra lỗi khi xóa lịch');
                    console.error(error);
                }
            }
        );
    }

    // Hàm phê duyệt yêu cầu hủy lịch
    function approveCancelRequest(id) {
        Notiflix.Confirm.show(
            'Phê duyệt hủy lịch',
            'Bạn có chắc chắn muốn phê duyệt yêu cầu hủy lịch này?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                try {
                    const res = await axiosTemplate('post', '/account/schedule/approve-cancel', null, {
                        id: id
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        
                        // Cập nhật thống kê
                        updateCounters(res.data.stats);
                        
                        // Làm mới bảng dữ liệu
                        callAjaxDataTable($('.updater'));
                    } else {
                        showAlert('warning', res.data.message);
                    }
                } catch (error) {
                    showAlert('error', 'Đã xảy ra lỗi khi phê duyệt yêu cầu hủy lịch');
                    console.error(error);
                }
            }
        );
    }
    
    // Hàm từ chối yêu cầu hủy lịch
    function rejectCancelRequest(id) {
        $('#reject-cancel-schedule-id').val(id);
        KTModal.getInstance(document.querySelector('#reject-cancel-modal')).show();
    }
    
    // Hàm cập nhật bộ đếm
    function updateCounters(stats) {
        $('#total-count').text(stats.totalSchedules);
        $('#approved-count').text(stats.approvedSchedules);
        $('#pending-count').text(stats.pendingSchedules);
        $('#rejected-count').text(stats.rejectedSchedules);
    }
</script>
@endpush