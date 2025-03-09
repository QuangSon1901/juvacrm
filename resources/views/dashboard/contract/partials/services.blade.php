<div class="card shadow-sm border border-gray-100 overflow-hidden mb-4">
    <div class="card-header bg-white border-b border-gray-100">
        <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
            <i class="ki-filled ki-dollar text-green-500"></i>
            Tổng hợp giá trị hợp đồng
        </h3>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:!grid-cols-4 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-600 mb-1">Tổng giá trị dịch vụ</div>
                <div class="services-total-value text-lg font-medium text-gray-800">0 ₫</div>
            </div>
            <div class="p-4 bg-red-50 rounded-lg">
                <div class="text-sm text-red-600 mb-1">Tổng giảm giá</div>
                <div class="discount-value text-lg font-medium text-red-600">-0 ₫</div>
            </div>
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-sm text-blue-600 mb-1">Tổng giá trị hợp đồng</div>
                <div class="contract-total-value text-lg font-medium text-blue-800">0 ₫</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border border-gray-100 overflow-hidden">
    <div class="card-header bg-white border-b border-gray-100 flex-wrap gap-4">
        <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
            <i class="ki-filled ki-package text-purple-500"></i>
            Thông tin sản phẩm và dịch vụ
        </h3>
        <div class="flex gap-2">
            <button type="button" class="btn btn-light btn-sm" onclick="addProductItem()">
                <i class="ki-filled ki-plus"></i>
                Thêm sản phẩm
            </button>
            <button type="button" class="btn btn-light btn-sm" onclick="addOtherItem('custom', 'Mục khác')">
                <i class="ki-filled ki-plus"></i>
                Thêm mục khác
            </button>
        </div>
    </div>
    <div class="card-body p-0 !px-[4px]">
        <div id="items-container">
            <!-- Products and other items will be dynamically added here -->
        </div>
    </div>
</div>

<!-- Product Item Template -->
<template id="product-item-template">
    <div class="item-container border-b border-gray-100 p-3" data-item-id="__ITEM_ID__" data-item-type="product">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div class="col-span-1">
                <div class="grid grid-cols-1 gap-3 mb-3">
                    <div>
                        <label class="block text-gray-700 text-sm mb-1">Tên sản phẩm</label>
                        <select name="item_product_id[]" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full">
                            <option disabled selected>Chọn sản phẩm</option>
                            __PRODUCTS_OPTIONS__
                        </select>
                        <input type="hidden" name="item_type[]" value="product">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-1">Hình ảnh</label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'item')">
                                <i class="ki-filled ki-file-up"></i>
                                Tải ảnh
                            </button>
                            <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden hidden">
                                <img class="item-image-preview h-full w-full object-cover" src="" alt="">
                            </div>
                            <input type="file" name="item_image[]" class="item-image-input hidden" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-light" onclick="addService(this)">
                        <i class="ki-filled ki-plus text-green-500 mr-1"></i>
                        Thêm dịch vụ
                    </button>
                    <button type="button" class="btn btn-sm btn-light" onclick="addCustomService(this)">
                        <i class="ki-filled ki-plus text-orange-500 mr-1"></i>
                        Thêm dịch vụ khác
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeItem(this)">
                        <i class="ki-filled ki-trash !text-red-500"></i>
                    </button>
                </div>
            </div>
            <div class="services-container col-span-1 lg:col-span-2 border-gray-100">
                <!-- Services will be added here -->
            </div>
        </div>
    </div>
</template>

<!-- Other Item Template (for discount, fees, etc.) -->
<template id="other-item-template">
    <div class="item-container border-b border-gray-100 p-3" data-item-id="__ITEM_ID__" data-item-type="__TYPE__">
        <div class="flex justify-between items-start gap-2 mb-3">
            <div class="flex items-center gap-2 text-indigo-500">
                <i class="ki-filled ki-note-2"></i>
                <h4 class="font-medium text-gray-800">__ITEM_TITLE__</h4>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeItem(this)">
                    <i class="ki-filled ki-trash !text-red-500"></i>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div>
                <label class="block text-gray-700 text-sm mb-1">Tên mục</label>
                <input name="item_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ví dụ: Giảm giá, Phí vận chuyển...">
                <input type="hidden" name="item_type[]" value="__TYPE__">
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Giá trị</label>
                <div class="relative">
                    <input name="item_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8 w-full" type="text" placeholder="Giá trị" oninput="calculateTotalValue()">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                </div>
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Ghi chú</label>
                <input name="item_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú">
            </div>
        </div>
    </div>
</template>

<!-- Service Template -->
<template id="service-template">
    <div class="service-item mb-3 rounded-lg border border-gray-400 p-3" data-service-id="__SERVICE_ID__">
        <div class="flex justify-between items-start gap-2 mb-3">
            <div class="flex items-center gap-2 text-green-500">
                <i class="ki-filled ki-abstract-26"></i>
                <h5 class="font-medium text-gray-700">Dịch vụ</h5>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeService(this)">
                    <i class="ki-filled ki-trash !text-red-500"></i>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
            <div>
                <label class="block text-gray-700 text-sm mb-1">Tên dịch vụ</label>
                <select name="service_ids[]" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full service-select">
                    <option disabled selected>Chọn dịch vụ</option>
                    <!-- Services options will be added dynamically -->
                    __SERVICES_OPTIONS__
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Đơn giá</label>
                <div class="relative">
                    <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg !pl-8 w-full service-price" type="text" placeholder="Đơn giá" oninput="updateSubServicePrices(this)">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                </div>
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Ghi chú</label>
                <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú">
            </div>
        </div>
        <div class="flex gap-2 mb-3">
            <button type="button" class="btn btn-sm btn-light" onclick="addSubService(this)">
                <i class="ki-filled ki-plus text-blue-500 mr-1"></i>
                Thêm dịch vụ con
            </button>
        </div>
        <div class="sub-services-container border-gray-100">
            <!-- Sub services will be added here -->
        </div>
    </div>
</template>

<!-- Custom Service Template -->
<template id="custom-service-template">
    <div class="service-item mb-3 rounded-lg border border-gray-400 p-3" data-service-id="__SERVICE_ID__">
        <div class="flex justify-between items-start gap-2 mb-3">
            <div class="flex items-center gap-2 text-orange-500">
                <i class="ki-filled ki-abstract-26"></i>
                <h5 class="font-medium text-gray-700">Dịch vụ khác</h5>
            </div>
            <div class="flex items-center gap-1">
                <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeService(this)">
                    <i class="ki-filled ki-trash !text-red-500"></i>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
            <div>
                <label class="block text-gray-700 text-sm mb-1">Tên dịch vụ</label>
                <input name="service_custom_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nhập tên dịch vụ">
                <input type="hidden" name="service_ids[]" value="custom">
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Đơn giá</label>
                <div class="relative">
                    <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8 w-full service-price" type="text" placeholder="Đơn giá" oninput="updateSubServicePrices(this)">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                </div>
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Ghi chú</label>
                <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú">
            </div>
        </div>
        <div class="flex gap-2 mb-3">
            <button type="button" class="btn btn-sm btn-light" onclick="addSubService(this)">
                <i class="ki-filled ki-plus text-blue-500 mr-1"></i>
                Thêm dịch vụ con
            </button>
        </div>
        <div class="sub-services-container border-gray-100">
            <!-- Sub services will be added here -->
        </div>
    </div>
</template>

<!-- Sub Service Template -->
<template id="sub-service-template">
    <div class="sub-service-item mb-2 rounded p-2 border-l-2 border-gray-400" data-sub-service-id="__SUB_SERVICE_ID__">
        <div class="grid grid-cols-1 lg:!grid-cols-12 gap-2">
            <div class="lg:!col-span-6">
                <label class="block text-gray-700 text-sm mb-1">Góc máy</label>
                <input name="sub_service_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Góc máy">
            </div>
            <div class="lg:!col-span-2">
                <label class="block text-gray-700 text-sm mb-1">Số lượng</label>
                <input name="sub_service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full sub-service-quantity" type="text" placeholder="Số lượng" oninput="calculateSubServiceTotal(this)">
            </div>
            <div class="lg:!col-span-4">
                <label class="block text-gray-700 text-sm mb-1">Thành tiền</label>
                <div class="relative">
                    <input name="sub_service_total[]" class="input border-gray-200 focus:border-blue-500 rounded-lg !pl-6 w-full sub-service-total" type="text" placeholder="Thành tiền" readonly>
                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₫</span>
                </div>
            </div>
            <div class="lg:!col-span-6">
                <label class="block text-gray-700 text-sm mb-1">Nội dung</label>
                <textarea name="sub_service_content[]" class="textarea border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nội dung"></textarea>
            </div>
            <div class="lg:!col-span-6">
                <label class="block text-gray-700 text-sm mb-1">Hình ảnh</label>
                <div class="flex items-center gap-1">
                    <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub_service')">
                        <i class="ki-filled ki-file-up"></i>
                        Tải ảnh
                    </button>
                    <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden hidden">
                        <img class="sub-service-image-preview h-full w-full object-cover" src="" alt="">
                    </div>
                    <input type="file" name="sub_service_image[]" class="sub-service-image-input hidden" accept="image/*" onchange="previewImage(this)">
                    <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100 h-8 w-8" onclick="removeSubService(this)">
                        <i class="ki-filled ki-trash !text-red-500"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>