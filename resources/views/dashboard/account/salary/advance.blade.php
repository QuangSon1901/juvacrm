@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Tạm ứng lương
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
        <!-- Thống kê tạm ứng -->
        <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-dollar size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng tiền đã tạm ứng
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
                            Tạm ứng đã duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['approvedCount'] ?? 0 }} yêu cầu
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-timer size-6 shrink-0 text-warning"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tạm ứng chờ duyệt
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['pendingCount'] ?? 0 }} yêu cầu
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-cross-square size-6 shrink-0 text-danger"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tạm ứng bị từ chối
                        </div>
                        <div class="text-2sm text-gray-700">
                            {{ $stats['rejectedCount'] ?? 0 }} yêu cầu
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danh sách tạm ứng -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách yêu cầu tạm ứng
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
                            <option value="paid">Đã chi</option>
                        </select>
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-from-filter" data-filter="date_from" data-flatpickr="true" placeholder="Từ ngày">
                    </div>
                    
                    <div class="relative">
                        <input class="input input-sm" type="text" id="date-to-filter" data-filter="date_to" data-flatpickr="true" placeholder="Đến ngày">
                    </div>
                    
                    <button class="btn btn-primary btn-sm" data-modal-toggle="#create-advance-modal">
                        <i class="ki-filled ki-plus me-1"></i>
                        Tạo yêu cầu
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="advance-table" class="datatable-initialized">
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
                                            <span class="sort-label text-gray-700 font-normal">Ngày yêu cầu</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Số tiền</span>
                                        </span>
                                    </th>
                                    <th class="w-[250px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Lý do</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Người duyệt</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ngày duyệt</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/account/salary/advance-data'])
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

<!-- Modal Tạo yêu cầu tạm ứng -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-advance-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Tạo yêu cầu tạm ứng lương
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-advance-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Nhân viên <span class="text-red-500">*</span>
                    </label>
                    <select class="select" name="user_id" required>
                        <option value="">Chọn nhân viên</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Số tiền cần ứng <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="number" name="amount" placeholder="Nhập số tiền cần ứng" min="100000" required>
                </div>
                
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Ngày yêu cầu <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="text" name="request_date" data-flatpickr="true" placeholder="Chọn ngày yêu cầu" required>
                </div>
                
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Lý do <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="reason" rows="3" placeholder="Nhập lý do tạm ứng" required></textarea>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Tạo yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xử lý yêu cầu tạm ứng -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="process-advance-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Xử lý yêu cầu tạm ứng
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="process-advance-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="process-advance-id">
                <input type="hidden" name="status" id="process-advance-status">
                
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Ghi chú
                    </label>
                    <textarea class="textarea" name="note" rows="3" placeholder="Nhập ghi chú (nếu có)"></textarea>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" id="process-advance-btn" class="btn btn-primary justify-center">
                        Xác nhận
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
        // Khởi tạo flatpickr cho các trường ngày
        flatpickrMake($("input[name='request_date']"), 'date');
        flatpickrMake($("#date-from-filter"), 'date');
        flatpickrMake($("#date-to-filter"), 'date');
        
        // Xử lý khi thay đổi bộ lọc
        $('[data-filter]').on('change', function() {
            callAjaxDataTable($('.updater'));
        });
        
        // Xử lý form tạo yêu cầu tạm ứng
        $('#create-advance-form').on('submit', async function(e) {
            e.preventDefault();
            
            const userId = $(this).find('select[name="user_id"]').val();
            const amount = $(this).find('input[name="amount"]').val();
            const requestDate = $(this).find('input[name="request_date"]').val();
            const reason = $(this).find('textarea[name="reason"]').val();
            
            try {
                const res = await axiosTemplate('post', '/account/salary/create-advance', null, {
                    user_id: userId,
                    amount: amount,
                    request_date: requestDate,
                    reason: reason
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-advance-modal')).hide();
                    
                    // Reset form
                    $(this).trigger('reset');
                    
                    // Cập nhật bảng
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi tạo yêu cầu tạm ứng');
                console.error(error);
            }
        });
        
        // Xử lý form xử lý yêu cầu tạm ứng
        $('#process-advance-form').on('submit', async function(e) {
            e.preventDefault();
            
            const id = $('#process-advance-id').val();
            const status = $('#process-advance-status').val();
            const note = $(this).find('textarea[name="note"]').val();
            
            try {
                const res = await axiosTemplate('post', '/account/salary/update-advance-status', null, {
                    id: id,
                    status: status,
                    note: note
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#process-advance-modal')).hide();
                    
                    // Reset form
                    $(this).trigger('reset');
                    
                    // Cập nhật bảng
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi xử lý yêu cầu tạm ứng');
                console.error(error);
            }
        });
    });
    
    // Hàm mở modal xử lý yêu cầu tạm ứng
    function openProcessModal(id, status) {
        const statusText = status === 'approved' ? 'duyệt' : (status === 'rejected' ? 'từ chối' : 'chi trả');
        const buttonClass = status === 'approved' ? 'btn-success' : (status === 'rejected' ? 'btn-danger' : 'btn-primary');
        
        $('#process-advance-id').val(id);
        $('#process-advance-status').val(status);
        $('#process-advance-btn').text(`Xác nhận ${statusText}`).removeClass('btn-success btn-danger btn-primary').addClass(buttonClass);
        $('.modal-title').text(`${status === 'approved' ? 'Duyệt' : (status === 'rejected' ? 'Từ chối' : 'Chi trả')} yêu cầu tạm ứng`);
        
        KTModal.getInstance(document.querySelector('#process-advance-modal')).show();
    }
</script>
@endpush