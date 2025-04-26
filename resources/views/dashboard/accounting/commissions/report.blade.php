@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Báo cáo hoa hồng
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
        <div class="grid !grid-cols-1 lg:!grid-cols-2 gap-5">
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-dollar size-6 shrink-0 text-primary"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Tổng hoa hồng
                        </div>
                        <div class="text-2sm text-gray-700" id="total-commission">
                            0 VNĐ
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                <div class="flex items-center flex-wrap gap-3.5">
                    <i class="ki-outline ki-sand-clock size-6 shrink-0 text-warning"></i>
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900 mb-px">
                            Hoa hồng chờ thanh toán
                        </div>
                        <div class="text-2sm text-gray-700" id="pending-commission">
                            0 VNĐ
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bộ lọc -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Bộ lọc
                </h3>
            </div>
            <div class="card-body !p-5">
                <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-5">
                    <div class="relative">
                        <select class="select" id="user-filter" data-filter="user_id">
                            <option value="">Tất cả nhân viên</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="relative">
                        <select class="select" id="status-filter" data-filter="is_paid">
                            <option value="">Tất cả trạng thái</option>
                            <option value="0">Chưa thanh toán</option>
                            <option value="1">Đã thanh toán</option>
                        </select>
                    </div>
                    
                    <div class="relative">
                        <input class="input" type="text" id="date-from-filter" data-filter="date_from" data-flatpickr="true" placeholder="Từ ngày">
                    </div>
                    
                    <div class="relative">
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
        
        <!-- Danh sách hoa hồng -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Danh sách hoa hồng
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative" id="bulk-payment-container" style="display: none;">
                        <button class="btn btn-success btn-sm" id="btn-bulk-pay">
                            <i class="ki-filled ki-dollar me-1"></i>
                            Thanh toán tất cả
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="commission-table" class="datatable-initialized">
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
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Hợp đồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Tỷ lệ hoa hồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Giá trị hợp đồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Hoa hồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Ngày thanh toán</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/accounting/commissions/report-data'])
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

<!-- Modal chi tiết giao dịch -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="transaction-detail-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chi tiết giao dịch
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="transaction-detail-content" class="grid gap-5 px-0 py-5">
                <!-- Nội dung chi tiết sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Khởi tạo flatpickr cho các trường ngày
        flatpickrMake($("#date-from-filter"), 'date');
        flatpickrMake($("#date-to-filter"), 'date');
        
        // Xử lý sự kiện khi thay đổi bộ lọc
        $("#btn-apply-filter").on('click', function() {
            callAjaxDataTable($('.updater'));
        });
        
        // Xử lý hiển thị nút thanh toán hàng loạt
        $("#user-filter").on('change', function() {
            const userId = $(this).val();
            if (userId && $("#status-filter").val() !== '1') {
                $("#bulk-payment-container").show();
            } else {
                $("#bulk-payment-container").hide();
            }
        });
        
        $("#status-filter").on('change', function() {
            const status = $(this).val();
            const userId = $("#user-filter").val();
            if (userId && status !== '1') {
                $("#bulk-payment-container").show();
            } else {
                $("#bulk-payment-container").hide();
            }
        });
        
        // Xử lý thanh toán hàng loạt
        $("#btn-bulk-pay").on('click', function() {
            const userId = $("#user-filter").val();
            if (!userId) {
                showAlert('warning', 'Vui lòng chọn nhân viên để thanh toán hàng loạt');
                return;
            }
            
            Notiflix.Confirm.show(
                'Thanh toán hoa hồng hàng loạt',
                'Bạn có chắc chắn muốn thanh toán tất cả hoa hồng chưa chi cho nhân viên này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    try {
                        const res = await axiosTemplate('post', '/accounting/commissions/bulk-pay', null, {
                            user_id: userId
                        });
                        
                        if (res.data.status === 200) {
                            showAlert('success', res.data.message);
                            callAjaxDataTable($('.updater'));
                        } else {
                            showAlert('warning', res.data.message);
                        }
                    } catch (error) {
                        showAlert('error', 'Đã xảy ra lỗi khi thanh toán hoa hồng');
                        console.error(error);
                    }
                }
            );
        });
        
        // Cập nhật thống kê sau khi tải dữ liệu
        $(document).on('tableDataLoaded', function(e, response) {
            if (response.summary) {
                $("#total-commission").text(response.summary.total_commission + ' VNĐ');
                $("#pending-commission").text(response.summary.pending_commission + ' VNĐ');
            }
        });
    });
    
    // Hàm thanh toán hoa hồng
    async function payCommission(id) {
        try {
            Notiflix.Confirm.show(
                'Thanh toán hoa hồng',
                'Bạn có chắc chắn muốn thanh toán hoa hồng này?',
                'Đồng ý',
                'Hủy bỏ',
                async function() {
                    const res = await axiosTemplate('post', '/accounting/commissions/pay', null, {
                        id: id
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
            showAlert('error', 'Đã xảy ra lỗi khi thanh toán hoa hồng');
            console.error(error);
        }
    }
    
    // Hàm xem chi tiết giao dịch
    function viewTransaction(id) {
        try {
            // Hiển thị modal
            KTModal.getInstance(document.querySelector('#transaction-detail-modal')).show();
            
            // Hiển thị loading
            $('#transaction-detail-content').html(`
                <div class="flex justify-center items-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);
            
            // Gọi API lấy chi tiết
            axiosTemplate('get', `/accounting/transactions/${id}/detail`, null, null)
                .then(res => {
                    if (res.data.status === 200) {
                        $('#transaction-detail-content').html(res.data.content);
                    } else {
                        $('#transaction-detail-content').html(`
                            <div class="text-center text-danger">
                                ${res.data.message || 'Không thể tải thông tin chi tiết giao dịch'}
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    $('#transaction-detail-content').html(`
                        <div class="text-center text-danger">
                            Đã xảy ra lỗi khi tải thông tin
                        </div>
                    `);
                    console.error(error);
                });
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi xem chi tiết giao dịch');
            console.error(error);
        }
    }
</script>
@endpush