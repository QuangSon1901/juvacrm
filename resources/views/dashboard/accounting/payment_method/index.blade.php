@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý phương thức thanh toán
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
    <!-- Card danh sách phương thức thanh toán -->
    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách phương thức thanh toán
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex flex-wrap gap-2.5">
                        <select data-filter="is_active" class="select select-sm w-40">
                            <option value="" selected>Tất cả trạng thái</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                        <button class="btn btn-sm btn-primary" data-modal-toggle="#create-payment-method-modal">
                            Thêm mới
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="payment_methods_table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[50px]">
                                        <span class="sort">
                                            <span class="sort-label">STT</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label">Tên phương thức</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[250px]">
                                        <span class="sort">
                                            <span class="sort-label">Mô tả</span>
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
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/payment-method/data'])
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

<!-- Modal thêm phương thức thanh toán -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-payment-method-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm phương thức thanh toán</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-payment-method-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên phương thức <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="payment-method-name" name="name" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Mô tả
                        </span>
                    </div>
                    <textarea class="textarea" id="payment-method-description" name="description" rows="3"></textarea>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Trạng thái
                        </span>
                    </div>
                    <select class="select" id="payment-method-status" name="is_active">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm phương thức thanh toán</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa phương thức thanh toán -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-payment-method-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Sửa phương thức thanh toán</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-payment-method-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="edit-payment-method-id">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên phương thức <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="edit-payment-method-name" name="name" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Mô tả
                        </span>
                    </div>
                    <textarea class="textarea" id="edit-payment-method-description" name="description" rows="3"></textarea>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Trạng thái
                        </span>
                    </div>
                    <select class="select" id="edit-payment-method-status" name="is_active">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật phương thức thanh toán</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        // Xử lý form thêm phương thức thanh toán
        $('#create-payment-method-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/payment-method/create', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-payment-method-modal')).hide();
                    $('#create-payment-method-form')[0].reset();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi thêm phương thức thanh toán.');
                console.error(error);
            }
        });
        
        // Xử lý form sửa phương thức thanh toán
        $('#edit-payment-method-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/payment-method/update', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#edit-payment-method-modal')).hide();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi cập nhật phương thức thanh toán.');
                console.error(error);
            }
        });
    });
    
    // Mở modal sửa phương thức thanh toán và đổ dữ liệu
    function openEditPaymentMethodModal(id, name, description, status) {
        $('#edit-payment-method-id').val(id);
        $('#edit-payment-method-name').val(name);
        $('#edit-payment-method-description').val(description);
        $('#edit-payment-method-status').val(status);
        
        KTModal.getInstance(document.querySelector('#edit-payment-method-modal')).show();
    }
</script>
@endpush