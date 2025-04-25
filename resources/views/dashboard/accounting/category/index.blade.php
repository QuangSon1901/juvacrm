@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Danh mục thu chi
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
                    Danh sách danh mục thu chi
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex flex-wrap gap-2.5">
                        <select data-filter="type" class="select select-sm w-40">
                            <option value="" selected>
                                Tất cả loại 
                            </option>
                            <option value="0">Danh mục thu</option>
                            <option value="1">Danh mục chi</option>
                        </select>
                        <button class="btn btn-primary btn-sm" data-modal-toggle="#create-category-modal">
                            Thêm danh mục
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="categories_table" class="datatable-initialized">
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
                                            <span class="sort-label">Loại</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[250px]">
                                        <span class="sort">
                                            <span class="sort-label">Tên danh mục</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[300px]">
                                        <span class="sort">
                                            <span class="sort-label">Ghi chú</span>
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
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/transaction-category/data'])
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

<!-- Modal thêm danh mục -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-category-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm danh mục thu chi</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-category-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên danh mục <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="name" name="name" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Loại danh mục <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="type" name="type" required>
                        <option value="0">Thu</option>
                        <option value="1">Chi</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Ghi chú
                        </span>
                    </div>
                    <textarea class="textarea" id="note" name="note" rows="3"></textarea>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa danh mục -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-category-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Sửa danh mục thu chi</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-category-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="edit-category-id">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên danh mục <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="edit-name" name="name" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Loại danh mục <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="edit-type" name="type" required>
                        <option value="0">Thu</option>
                        <option value="1">Chi</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Ghi chú
                        </span>
                    </div>
                    <textarea class="textarea" id="edit-note" name="note" rows="3"></textarea>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Trạng thái
                        </span>
                    </div>
                    <select class="select" id="edit-is-active" name="is_active">
                        <option value="1">Kích hoạt</option>
                        <option value="0">Vô hiệu</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        // Xử lý form tạo danh mục
        $('#create-category-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/transaction-category/create', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-category-modal')).hide();
                    $('#create-category-form')[0].reset();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi thêm danh mục.');
                console.error(error);
            }
        });
        
        // Xử lý form sửa danh mục
        $('#edit-category-form').on('submit', async function(e) {
            e.preventDefault();
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/transaction-category/update', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#edit-category-modal')).hide();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi cập nhật danh mục.');
                console.error(error);
            }
        });
    });
    
    // Mở modal sửa danh mục và đổ dữ liệu
    function openEditCategoryModal(id, name, type, note, is_active) {
        $('#edit-category-id').val(id);
        $('#edit-name').val(name);
        $('#edit-type').val(type);
        $('#edit-note').val(note);
        $('#edit-is-active').val(is_active);
        
        KTModal.getInstance(document.querySelector('#edit-category-modal')).show();
    }
</script>
@endpush