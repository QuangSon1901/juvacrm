@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Biên nhận thanh toán
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
    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách biên nhận
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex flex-wrap gap-2.5">
                        <select data-filter="payment_stage" class="select select-sm w-40">
                            <option value="" selected>Tất cả biên nhận</option>
                            <option value="0">Biên nhận cọc</option>
                            <option value="2">Thanh toán cuối</option>
                            <option value="1">Tiền thưởng</option>
                            <option value="3">Khấu trừ</option>
                        </select>
                        <select data-filter="status" class="select select-sm w-40">
                            <option value="" selected>Tất cả trạng thái</option>
                            <option value="0">Chưa thanh toán</option>
                            <option value="1">Đã thanh toán</option>
                        </select>
                        @if(isset($customers) && count($customers) > 0)
                        <select data-filter="customer_id" class="select select-sm w-40">
                            <option value="" selected>Tất cả khách hàng</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @endif
                        <button class="btn btn-primary btn-sm" data-modal-toggle="#add-receipt-modal">
                            Thêm biên nhận
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="receipts_table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[50px]">
                                        <span class="sort">
                                            <span class="sort-label">STT</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[180px]">
                                        <span class="sort">
                                            <span class="sort-label">Tên biên nhận</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[180px]">
                                        <span class="sort">
                                            <span class="sort-label">Hợp đồng</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">Khách hàng</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Loại</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Số tiền</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">Phương thức</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Hạn thanh toán</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/deposit-receipt/data'])
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

<!-- Modal thêm biên nhận -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-receipt-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm biên nhận thanh toán</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="add-receipt-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Hợp đồng <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="contract_id" name="contract_id" required>
                        <option value="">Chọn hợp đồng</option>
                        <!-- Dữ liệu hợp đồng sẽ được load bằng AJAX -->
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên biên nhận <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="name" name="name" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Loại thanh toán <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <select class="select" id="payment_stage" name="payment_stage" required>
                            <option value="0">Đặt cọc</option>
                            <option value="2">Thanh toán cuối</option>
                            <option value="1">Tiền thưởng</option>
                            <option value="3">Khấu trừ</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Số tiền <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="number" id="price" name="price" min="0" step="1000" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Loại tiền tệ <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <select class="select" id="currency_id" name="currency_id" required>
                            @if(isset($currencies) && count($currencies) > 0)
                                @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->currency_code }}</option>
                                @endforeach
                            @else
                                <option value="1">VND</option>
                            @endif
                        </select>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Phương thức thanh toán <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <select class="select" id="method_id" name="method_id" required>
                            @if(isset($methods) && count($methods) > 0)
                                @foreach($methods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            @else
                                <option value="1">Tiền mặt</option>
                                <option value="2">Chuyển khoản</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Hạn thanh toán <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="text" id="due_date" name="due_date" data-flatpickr="true" required>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Trạng thái
                            </span>
                        </div>
                        <select class="select" id="status" name="status">
                            <option value="0">Chưa thanh toán</option>
                            <option value="1">Đã thanh toán</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Thêm biên nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa biên nhận -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-receipt-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Sửa biên nhận thanh toán</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-receipt-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="edit-receipt-id">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên biên nhận
                        </span>
                    </div>
                    <input class="input" type="text" id="edit-name" name="name">
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Số tiền
                        </span>
                    </div>
                    <input class="input" type="number" id="edit-price" name="price" min="0" step="1000">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Loại tiền tệ
                            </span>
                        </div>
                        <select class="select" id="edit-currency-id" name="currency_id">
                            @if(isset($currencies) && count($currencies) > 0)
                                @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->currency_code }}</option>
                                @endforeach
                            @else
                                <option value="1">VND</option>
                            @endif
                        </select>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Phương thức thanh toán
                            </span>
                        </div>
                        <select class="select" id="edit-method-id" name="method_id">
                            @if(isset($methods) && count($methods) > 0)
                                @foreach($methods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            @else
                                <option value="1">Tiền mặt</option>
                                <option value="2">Chuyển khoản</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Hạn thanh toán
                            </span>
                        </div>
                        <input class="input" type="text" id="edit-due-date" name="due_date" data-flatpickr="true">
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Trạng thái
                            </span>
                        </div>
                        <select class="select" id="edit-status" name="status">
                            <option value="0">Chưa thanh toán</option>
                            <option value="1">Đã thanh toán</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Cập nhật biên nhận
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
        // Khởi tạo flatpickr
        flatpickrMake($("[data-flatpickr]"), 'datetime');
        
        // Load hợp đồng khi mở trang
        loadContracts();
        
        // Xử lý form thêm biên nhận
        $('#add-receipt-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/deposit-receipt/create', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#add-receipt-modal')).hide();
                    $('#add-receipt-form')[0].reset();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi thêm biên nhận.');
                console.error(error);
            }
        });
        
        // Xử lý form sửa biên nhận
        $('#edit-receipt-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/deposit-receipt/update', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#edit-receipt-modal')).hide();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi cập nhật biên nhận.');
                console.error(error);
            }
        });
    });
    
    // Hàm load danh sách hợp đồng
    async function loadContracts() {
        try {
            let res = await axiosTemplate('get', '/contract-data', {filter: {status: 1}, json: 1}, null);
            if (res.data.status === 200) {
                let $select = $('#contract_id');
                $select.empty().append('<option value="">Chọn hợp đồng</option>');
                
                if (res.data.content && res.data.content.length > 0) {
                    res.data.content.forEach(function(contract) {
                        $select.append(`<option value="${contract.id}">${contract.contract_number} - ${contract.name}</option>`);
                    });
                }
            }
        } catch (error) {
            console.error('Lỗi khi tải danh sách hợp đồng:', error);
        }
    }
    
    // Mở modal sửa biên nhận
    function openEditReceiptModal(id, name, price, currency_id, method_id, due_date, status) {
        $('#edit-receipt-id').val(id);
        $('#edit-name').val(name);
        $('#edit-price').val(price);
        $('#edit-currency-id').val(currency_id);
        $('#edit-method-id').val(method_id);
        $('#edit-due-date').val(due_date);
        $('#edit-status').val(status);
        
        // Cập nhật lại flatpickr cho trường ngày tháng
        flatpickrMake($('#edit-due-date'), 'datetime');
        
        KTModal.getInstance(document.querySelector('#edit-receipt-modal')).show();
    }
    
    // Hàm xác nhận thanh toán
    async function confirmPayment(id) {
        try {
            Notiflix.Confirm.show(
                'Xác nhận thanh toán',
                'Bạn có chắc chắn muốn xác nhận thanh toán cho biên nhận này?',
                'Xác nhận',
                'Hủy',
                async function() {
                    let res = await axiosTemplate('post', '/deposit-receipt/update', null, {id: id, status: 1});
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi xác nhận thanh toán.');
            console.error(error);
        }
    }
    
    // Hàm hủy biên nhận
    async function cancelReceipt(id) {
        try {
            Notiflix.Confirm.show(
                'Hủy biên nhận',
                'Bạn có chắc chắn muốn hủy biên nhận này? Hành động này không thể hoàn tác!',
                'Hủy biên nhận',
                'Không',
                async function() {
                    let res = await axiosTemplate('post', '/deposit-receipt/cancel', null, {id: id});
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi hủy biên nhận.');
            console.error(error);
        }
    }
</script>
@endpush