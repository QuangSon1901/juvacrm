@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý sản phẩm
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
        </div>
    </div>
</div>
<div class="container-fixed">
    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách sản phẩm
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex flex-wrap gap-2.5">
                        <select class="select select-sm w-40" id="filter-status">
                            <option value="" selected>Tất cả trạng thái</option>
                            <option value="1">Đang hoạt động</option>
                            <option value="0">Đã ẩn</option>
                        </select>
                    </div>
                    @if(hasPermission('manage-assets'))
                    <button class="btn btn-sm btn-primary" data-modal-toggle="#add_product_modal">
                        <i class="ki-filled ki-plus fs-2"></i>
                        Thêm sản phẩm
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div id="products-container" data-datatable="true" data-datatable-page-size="10" class="datatable-initialized">
                    <!-- Data will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add_product_modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thêm sản phẩm mới
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form id="add-product-form" class="grid gap-5 px-0 py-5">
                <input name="id" class="input hidden" type="text" value="0">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên sản phẩm
                        </span>
                    </div>
                    <input name="name" class="input" type="text" placeholder="Vui lòng nhập tên sản phẩm">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit_product_modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật sản phẩm
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-product-form" class="grid gap-5 px-0 py-5">
                <input name="id" class="input hidden" type="text">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Tên sản phẩm
                        </span>
                    </div>
                    <input name="name" class="input" type="text" placeholder="Vui lòng nhập tên sản phẩm">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    $(function() {
        loadData();
        
        // Status filter change
        $('#filter-status').on('change', function() {
            loadData();
        });

        // Add product form submission
        $('#add-product-form').on('submit', function(e) {
            e.preventDefault();
            saveProduct($(this), 'create');
        });

        // Edit product form submission
        $('#edit-product-form').on('submit', function(e) {
            e.preventDefault();
            saveProduct($(this), 'update');
        });

        // Edit button click handler (delegated)
        $(document).on('click', '.btn-edit-product', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            
            $('#edit_product_modal form input[name=id]').val(id);
            $('#edit_product_modal form input[name=name]').val(name);
            
            // Show the modal
            const editModal = document.querySelector('#edit_product_modal');
            if (editModal) {
                const modalInstance = KTModal.getInstance(editModal);
                if (modalInstance) {
                    modalInstance.show();
                }
            }
        });

        // Status toggle handler (delegated)
        $(document).on('change', '.product-status-toggle', function() {
            const id = $(this).data('id');
            const isActive = $(this).is(':checked') ? 1 : 0;
            
            updateProductStatus(id, isActive);
        });
    });

    async function loadData() {
        const statusFilter = $('#filter-status').val();
        
        let method = "get",
            url = "/product/data",
            params = {
                filter: {
                    status: statusFilter
                }
            },
            data = null;
        
        try {
            let res = await axiosTemplate(method, url, params, data);
            if (res.data.status === 200) {
                $('#products-container').html(res.data.content);
                
                // Initialize datatable again if needed
                if (typeof initDatatable === 'function') {
                    initDatatable();
                }
            } else {
                showAlert('warning', 'Không thể tải dữ liệu sản phẩm.');
            }
        } catch (error) {
            console.error('Error loading data:', error);
            showAlert('error', 'Đã xảy ra lỗi khi tải dữ liệu.');
        }
    }

    async function saveProduct(form, action) {
        const formData = form.serialize();
        let method = "post",
            url = `/product/${action}`,
            params = null,
            data = formData;
        
        try {
            let res = await axiosTemplate(method, url, params, data);
            if (res.data.status === 200) {
                showAlert('success', res.data.message);
                
                // Close modal
                const modalId = action === 'create' ? '#add_product_modal' : '#edit_product_modal';
                const modal = document.querySelector(modalId);
                if (modal) {
                    const modalInstance = KTModal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
                
                // Reset form if adding new product
                if (action === 'create') {
                    form[0].reset();
                }
                
                // Reload data
                loadData();
            } else {
                showAlert('warning', res.data.message || 'Không thể lưu sản phẩm.');
            }
        } catch (error) {
            console.error('Error saving product:', error);
            showAlert('error', 'Đã xảy ra lỗi khi lưu sản phẩm.');
        }
    }

    async function updateProductStatus(id, isActive) {
        let method = "post",
            url = "/product/change-status",
            params = null,
            data = {
                id: id,
                is_active: isActive
            };
        
        try {
            let res = await axiosTemplate(method, url, params, data);
            if (res.data.status === 200) {
                showAlert('success', res.data.message);
            } else {
                showAlert('warning', res.data.message || 'Không thể cập nhật trạng thái sản phẩm.');
                // Revert the toggle if update failed
                $('.product-status-toggle[data-id="' + id + '"]').prop('checked', !isActive);
            }
        } catch (error) {
            console.error('Error updating status:', error);
            showAlert('error', 'Đã xảy ra lỗi khi cập nhật trạng thái.');
            // Revert the toggle if update failed
            $('.product-status-toggle[data-id="' + id + '"]').prop('checked', !isActive);
        }
    }
</script>
@endpush