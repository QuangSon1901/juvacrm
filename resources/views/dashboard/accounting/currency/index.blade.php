@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý đơn vị tiền tệ
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
    <!-- Card danh sách đơn vị tiền tệ -->
    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách đơn vị tiền tệ
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex flex-wrap gap-2.5">
                        <select data-filter="is_active" class="select select-sm w-40">
                            <option value="" selected>Tất cả trạng thái</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                        <button class="btn btn-sm btn-primary" data-modal-toggle="#create-currency-modal">
                            Thêm mới
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="currencies_table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[50px]">
                                        <span class="sort">
                                            <span class="sort-label">STT</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Mã tiền tệ</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label">Tên tiền tệ</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label">Ký hiệu</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Ngày tạo</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/currency/data'])
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

<!-- Modal thêm đơn vị tiền tệ -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-currency-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm đơn vị tiền tệ</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-currency-form" class="grid gap-5 px-0 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Mã tiền tệ <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="text" id="currency-code" name="currency_code" required>
                        <div class="text-xs text-gray-500">Ví dụ: USD, EUR, VND</div>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ký hiệu
                            </span>
                        </div>
                        <input class="input" type="text" id="currency-symbol" name="symbol">
                        <div class="text-xs text-gray-500">Ví dụ: $, €, ₫</div>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên tiền tệ <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="currency-name" name="currency_name" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Trạng thái
                        </span>
                    </div>
                    <select class="select" id="currency-status" name="is_active">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm đơn vị tiền tệ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa đơn vị tiền tệ -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-currency-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Sửa đơn vị tiền tệ</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-currency-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="edit-currency-id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Mã tiền tệ <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="text" id="edit-currency-code" name="currency_code" required>
                        <div class="text-xs text-gray-500">Ví dụ: USD, EUR, VND</div>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ký hiệu
                            </span>
                        </div>
                        <input class="input" type="text" id="edit-currency-symbol" name="symbol">
                        <div class="text-xs text-gray-500">Ví dụ: $, €, ₫</div>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên tiền tệ <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="edit-currency-name" name="currency_name" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Trạng thái
                        </span>
                    </div>
                    <select class="select" id="edit-currency-status" name="is_active">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật đơn vị tiền tệ</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        // Xử lý form thêm đơn vị tiền tệ
        $('#create-currency-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/currency/create', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-currency-modal')).hide();
                    $('#create-currency-form')[0].reset();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi thêm đơn vị tiền tệ.');
                console.error(error);
            }
        });
        
        // Xử lý form sửa đơn vị tiền tệ
        $('#edit-currency-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/currency/update', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#edit-currency-modal')).hide();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi cập nhật đơn vị tiền tệ.');
                console.error(error);
            }
        });
    });
    
    // Mở modal sửa đơn vị tiền tệ và đổ dữ liệu
    function openEditCurrencyModal(id, currencyCode, currencyName, symbol, status) {
        $('#edit-currency-id').val(id);
        $('#edit-currency-code').val(currencyCode);
        $('#edit-currency-name').val(currencyName);
        $('#edit-currency-symbol').val(symbol);
        $('#edit-currency-status').val(status);
        
        KTModal.getInstance(document.querySelector('#edit-currency-modal')).show();
    }
</script>
@endpush