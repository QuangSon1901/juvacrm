{{-- resources/views/dashboard/contracts/create.blade.php --}}
@extends('dashboard.layouts.layout')

@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin hợp đồng
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
            @push("actions")
            <button type="button" class="btn btn-primary px-5 py-2 flex items-center gap-2" onclick="saveCreateContract()">
                <i class="ki-filled ki-check text-white"></i>
                <span>Tạo hợp đồng</span>
            </button>
            @endpush
        </div>
    </div>
</div>

<div class="container-fixed">
    <form id="contract-form" class="grid gap-5">
        <div class="flex items-center flex-wrap md:flex-nowrap lg:items-end justify-between border-b border-b-gray-200 dark:border-b-coal-100 gap-3">
            <div class="grid">
                <div class="scrollable-x-auto">
                    <div class="tabs gap-6" data-tabs="true">
                        <div class="tab cursor-pointer active" data-tab-toggle="#tab-info">
                            <span class="text-nowrap text-sm">
                                Thông tin chung
                            </span>
                        </div>
                        <div class="tab cursor-pointer" data-tab-toggle="#tab-services">
                            <span class="text-nowrap text-sm">
                                Dịch vụ
                            </span>
                        </div>
                        <div class="tab cursor-pointer" data-tab-toggle="#tab-payments">
                            <span class="text-nowrap text-sm">
                                Thanh toán
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div></div>
        </div>

        <div class="transition-opacity duration-700" id="tab-info">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="col-span-1">
                    <div class="grid gap-6">
                        {{-- resources/views/dashboard/contracts/partials/party-a.blade.php --}}
                        <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                            <div class="card-header bg-white border-b border-gray-100">
                                <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                                    <i class="ki-filled ki-crown text-blue-500"></i>
                                    Bên A (bên cung cấp)
                                </h3>
                            </div>
                            <div class="card-body p-5 grid gap-4">
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Tên công ty</label>
                                    <input name="company_name" class="input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg" type="text" value="Juva Media" readonly>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Mã số thuế</label>
                                    <input name="tax_code" class="input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg" type="text" value="" readonly>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Địa chỉ</label>
                                    <input name="company_address" class="input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg" type="text" value="" readonly>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Nhân viên phụ trách</label>
                                    <select name="user_id" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full">
                                        <option class="disabled" disabled selected>Vui lòng chọn</option>
                                        @foreach ($details['users'] as $user)
                                        <option value="{{$user['id']}}">{{$user['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- resources/views/dashboard/contracts/partials/party-b.blade.php --}}
                        <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                            <div class="card-header bg-white border-b border-gray-100">
                                <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                                    <i class="ki-filled ki-user-tick text-green-500"></i>
                                    Bên B (khách hàng)
                                </h3>
                            </div>
                            <div class="card-body p-5 grid gap-4">
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Khách hàng</label>
                                    <select name="provider_id" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full">
                                        <option class="disabled" disabled selected>Vui lòng chọn</option>
                                        @foreach ($details['customers'] as $customer)
                                        <option value="{{$customer['id']}}" @if (isset($details['customer']) && $customer['id']==$details['customer']['id']) selected @endif>{{$customer['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Người đại diện</label>
                                    <input name="customer_representative" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Tên người đại diện" value="@if (isset($details['customer'])) {{$details['customer']['representative'] ?? ''}} @endif">
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Mã số thuế</label>
                                    <input name="customer_tax_code" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Mã số thuế khách hàng" value="@if (isset($details['customer'])) {{$details['customer']['tax_code'] ?? ''}} @endif">
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Địa chỉ</label>
                                    <input name="address" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Địa chỉ khách hàng" value="@if (isset($details['customer'])) {{$details['customer']['address']}} @endif">
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Điện thoại</label>
                                    <input name="phone" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Số điện thoại" value="@if (isset($details['customer'])) {{$details['customer']['phone']}} @endif">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-1 xl:!col-span-2">
                    {{-- resources/views/dashboard/contracts/partials/contract-description.blade.php --}}
                    <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                        <div class="card-header bg-white border-b border-gray-100">
                            <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                                <i class="ki-filled ki-document text-indigo-500"></i>
                                Mô tả hợp đồng
                            </h3>
                        </div>
                        <div class="card-body p-5">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Trạng thái</label>
                                    <select name="status" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full">
                                        <option value="0" selected>Đang chờ</option>
                                        <option value="1">Đang triển khai</option>
                                    </select>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Số hợp đồng</label>
                                    <input name="contract_number" class="input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Tự động tạo" readonly>
                                </div>
                            </div>

                            <div class="mt-5">
                                <label class="form-label mb-1.5 text-gray-700 font-medium">Tên hợp đồng</label>
                                <input name="name" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Tên hợp đồng">
                            </div>

                            <div class="mt-5">
                                <label class="form-label mb-1.5 text-gray-700 font-medium">Loại hình dịch vụ</label>
                                <select name="category_id" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full">
                                    <option class="disabled" disabled selected>Vui lòng chọn</option>
                                    @foreach ($details['categories'] as $category)
                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Ngày ký</label>
                                    <div class="relative">
                                        <input name="sign_date" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="DD/MM/YYYY">
                                        <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Ngày hiệu lực</label>
                                    <div class="relative">
                                        <input name="effective_date" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="DD/MM/YYYY">
                                        <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Ngày hết hạn</label>
                                    <div class="relative">
                                        <input name="expiry_date" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="DD/MM/YYYY">
                                        <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <label class="form-label mb-1.5 text-gray-700 font-medium">Thời gian dự kiến</label>
                                    <div class="relative">
                                        <input name="estimate_date" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="DD/MM/YYYY">
                                        <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <label class="form-label mb-1.5 text-gray-700 font-medium">Tổng giá trị</label>
                                <div class="relative">
                                    <input name="total_value_format" class="contract-total-value input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="Sẽ tự tính dựa vào dịch vụ" readonly>
                                    <input type="hidden" name="total_value" value="0">
                                    <i class="ki-filled ki-dollar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>

                            <div class="mt-5">
                                <label class="form-label mb-1.5 text-gray-700 font-medium">Ghi chú</label>
                                <textarea class="textarea border-gray-200 focus:border-blue-500 rounded-lg w-full min-h-24" name="note" placeholder="Ghi chú chi tiết về hợp đồng"></textarea>
                            </div>

                            <div class="mt-5">
                                <label class="form-label mb-1.5 text-gray-700 font-medium">Điều khoản chung</label>
                                <textarea class="textarea border-gray-200 focus:border-blue-500 rounded-lg w-full min-h-32" name="terms_and_conditions" placeholder="Các điều khoản và điều kiện hợp đồng"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden transition-opacity duration-700" id="tab-services">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="col-span-1 xl:!col-span-3">
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
                </div>
            </div>
        </div>
        <div class="hidden transition-opacity duration-700" id="tab-payments">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="col-span-1 xl:!col-span-3">
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
                    <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                        <div class="card-header bg-white border-b border-gray-100">
                            <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                                <i class="ki-filled ki-credit-cart text-green-500"></i>
                                Thanh toán
                            </h3>
                            <button type="button" class="btn btn-light btn-sm" onclick="addPayment()">
                                <i class="ki-filled ki-plus"></i>
                                Thêm biên nhận
                            </button>
                        </div>
                        <div class="card-table scrollable-x-auto">
                            <div class="scrollable-auto">
                                <table id="payments-table" class="table align-middle text-sm text-gray-600">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Biên nhận</th>
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Phần trăm (%)</th>
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Giá tiền</th>
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Tiền tệ</th>
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Phương thức</th>
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Ngày</th>
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Loại phiếu</th>
                                            <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Trạng thái</th>
                                            <th class="min-w-16 !px-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Initialize with the available data
    const details = @json($details);


    let itemCounter = 0;
    let serviceCounter = 0;
    let subServiceCounter = 0;

    // Thêm sản phẩm
    function addProductItem() {
        const itemId = itemCounter++;
        let template = document.getElementById('product-item-template').innerHTML;

        // Thay thế các placeholder
        let productsOptions = '';
        if (details && details.products) {
            productsOptions = details.products.map(item =>
                `<option value="${item.id}">${item.name}</option>`
            ).join('');
        }

        template = template.replace(/__ITEM_ID__/g, itemId);
        template = template.replace(/__PRODUCTS_OPTIONS__/g, productsOptions);

        addItemToContainer(template);
    }

    // Thêm mục khác (giảm giá, phí khác)
    function addOtherItem(type, title) {
        const itemId = itemCounter++;
        let template = document.getElementById('other-item-template').innerHTML;

        template = template.replace(/__ITEM_ID__/g, itemId);
        template = template.replace(/__TYPE__/g, type);
        template = template.replace(/__ITEM_TITLE__/g, title);

        addItemToContainer(template);
    }

    // Thêm item vào container
    function addItemToContainer(templateHtml) {
        const container = document.getElementById('items-container');
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = templateHtml;
        container.appendChild(tempDiv.firstElementChild);
    }

    // Thêm dịch vụ từ danh sách
    function addService(button) {
        const itemContainer = button.closest('.item-container');
        const servicesContainer = itemContainer.querySelector('.services-container');
        const serviceId = serviceCounter++;

        let template = document.getElementById('service-template').innerHTML;

        // Thay thế options
        let servicesOptions = '';
        if (details && details.services) {
            servicesOptions = details.services.map(item =>
                `<option value="${item.id}">${item.name}</option>`
            ).join('');
        }

        template = template.replace(/__SERVICE_ID__/g, serviceId);
        template = template.replace(/__SERVICES_OPTIONS__/g, servicesOptions);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = template;
        servicesContainer.appendChild(tempDiv.firstElementChild);
    }

    // Thêm dịch vụ tùy chỉnh
    function addCustomService(button) {
        const itemContainer = button.closest('.item-container');
        const servicesContainer = itemContainer.querySelector('.services-container');
        const serviceId = serviceCounter++;

        let template = document.getElementById('custom-service-template').innerHTML;
        template = template.replace(/__SERVICE_ID__/g, serviceId);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = template;
        servicesContainer.appendChild(tempDiv.firstElementChild);
    }

    // Thêm dịch vụ con
    function addSubService(button) {
        const serviceItem = button.closest('.service-item');
        const subServicesContainer = serviceItem.querySelector('.sub-services-container');
        const subServiceId = subServiceCounter++;

        let template = document.getElementById('sub-service-template').innerHTML;
        template = template.replace(/__SUB_SERVICE_ID__/g, subServiceId);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = template;
        subServicesContainer.appendChild(tempDiv.firstElementChild);
    }

    // Xóa item (sản phẩm hoặc mục khác)
    function removeItem(button) {
        const itemContainer = button.closest('.item-container');
        itemContainer.remove();
        calculateTotalValue();
    }

    // Xóa dịch vụ
    function removeService(button) {
        const serviceItem = button.closest('.service-item');
        serviceItem.remove();
        calculateTotalValue();
    }

    // Xóa dịch vụ con
    function removeSubService(button) {
        const subServiceItem = button.closest('.sub-service-item');
        subServiceItem.remove();
        calculateTotalValue();
    }

    // Kích hoạt dialog upload ảnh
    function triggerImageUpload(button, type) {
        const container = button.closest('div');
        const fileInput = type === 'item' ?
            container.querySelector('.item-image-input') :
            container.querySelector('.sub-service-image-input');

        fileInput.click();
    }

    // Xem trước ảnh đã tải lên
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const container = input.closest('div');
            const previewContainer = container.querySelector('.preview-image-container');
            const previewImg = container.querySelector('img');

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Cập nhật giá dịch vụ con dựa trên đơn giá dịch vụ
    function updateSubServicePrices(input) {
        const serviceItem = input.closest('.service-item');
        const subServices = serviceItem.querySelectorAll('.sub-service-item');

        subServices.forEach(subService => {
            calculateSubServiceTotal(subService.querySelector('.sub-service-quantity'));
        });

        calculateTotalValue();
    }

    // Tính toán thành tiền cho dịch vụ con
    function calculateSubServiceTotal(input) {
        const subServiceItem = input.closest('.sub-service-item');
        const serviceItem = subServiceItem.closest('.service-item');
        const servicePrice = parseFloat(serviceItem.querySelector('.service-price').value.replace(/[.,]/g, '')) || 0;
        const quantityInput = subServiceItem.querySelector('.sub-service-quantity');
        const totalInput = subServiceItem.querySelector('.sub-service-total');

        const quantity = parseFloat(quantityInput.value.replace(/[.,]/g, '')) || 0;
        const total = quantity * servicePrice;

        // Định dạng số tiền Việt Nam
        totalInput.value = new Intl.NumberFormat('vi-VN').format(total);

        calculateTotalValue();
    }

    // Tính tổng giá trị hợp đồng
    function calculateTotalValue() {
        // Tính tổng giá trị từ tất cả dịch vụ con
        let total = 0;
        let amount = 0;
        let discount = 0;

        // Cộng thành tiền từ tất cả dịch vụ con
        document.querySelectorAll('.sub-service-total').forEach(input => {
            const value = parseFloat(input.value.replace(/[.,]/g, '')) || 0;
            total += value;
            amount += value;
        });

        // Cộng/trừ giá trị từ các mục khác (giảm giá, phí...)
        document.querySelectorAll('.item-container[data-item-type="custom"]').forEach(item => {
            const priceInput = item.querySelector('input[name="item_price[]"]');
            if (priceInput) {
                const value = parseFloat(priceInput.value.replace(/[.,]/g, '')) || 0;
                total += value; // Có thể cần điều chỉnh dấu +/- tùy loại mục
                if (value < 0)
                    discount += value;
                else
                    amount += value;
            }
        });

        $('.services-total-value').text(new Intl.NumberFormat('vi-VN').format(amount));
        $('.discount-value').text(new Intl.NumberFormat('vi-VN').format(discount));
        $('.contract-total-value').text(new Intl.NumberFormat('vi-VN').format(total)).val(new Intl.NumberFormat('vi-VN').format(total));
        $('input[name="total_value"]').val(total);
    }

    // Khởi tạo với một sản phẩm mặc định
    document.addEventListener('DOMContentLoaded', function() {
        addProductItem();
    });

    function addPayment() {
        let paymentMethods = details.payments.map(item => `<option value="${item.id}">${item.name}</option>`);
        let currencies = details.currencies.map(item => `<option value="${item.id}">${item.currency_code}</option>`);
        $('#payments-table tbody').append(`
    <tr>
        <td class="!px-1 min-w-20">
            <input name="payment_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Biên nhận">
        </td>
        <td class="!px-1 min-w-20">
            <div class="relative">
                <input name="payment_percentage[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="" oninput="calculatePaymentFromPercentage(this)">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
            </div>
        </td>
        <td class="!px-1 min-w-20">
            <div class="relative">
                <input name="payment_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="Giá tiền" id="payment_price_${$('#payments-table tbody tr').length}">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
            </div>
        </td>
        <td class="!px-1">
            <select name="payment_currencies[]" class="select border-gray-200 focus:border-blue-500 rounded-lg !w-full">${currencies}</select>
        </td>
        <td class="!px-1 min-w-20">
            <select name="payment_methods[]" class="select border-gray-200 focus:border-blue-500 rounded-lg">${paymentMethods}</select>
        </td>
        <td class="!px-1 min-w-20">
            <div class="relative">
                <input name="payment_due_dates[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="DD/MM/YYYY">
                <i class="ki-filled ki-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </td>
        <td class="!px-1 min-w-20">
            <select name="payment_stage[]" class="select border-gray-200 focus:border-blue-500 rounded-lg">
                <option value="0">Biên nhận cọc</option>
                <option value="1">Tiền thưởng thêm</option>
                <option value="2">Biên nhận cuối</option>
                <option value="3">Tiền khấu trừ</option>
            </select>
        </td>
        <td class="!px-1 min-w-20">
            <label class="checkbox flex items-center gap-2">
                <input name="payment_status[]" type="checkbox" value="1" class="h-5 w-5 text-blue-500 rounded border-gray-300 focus:ring-blue-500">
                <span class="checkbox-label text-gray-700">Đã thanh toán</span>
            </label>
        </td>
        <td class="text-center !px-1">
            <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-red-100" onclick="$(this).closest('tr').remove();">
                <i class="ki-filled ki-trash text-red-500"></i>
            </button>
        </td>
    </tr>
`);

        flatpickrMake($('input[name="payment_due_dates[]"]').last(), 'datetime');
    }

    // Function to calculate payment amount from percentage
    function calculatePaymentFromPercentage(input) {
        const percentage = parseFloat($(input).val()) || 0;
        const totalValue = parseFloat($('input[name="total_value"]').val()) || 0;

        if (percentage > 0 && totalValue > 0) {
            const amount = (percentage / 100) * totalValue;
            const priceInput = $(input).closest('tr').find('input[name="payment_price[]"]');
            priceInput.val(Math.round(amount));
        }
    }

    async function saveCreateContract() {
        calculateTotalValue();

        // Tạo cấu trúc dữ liệu cho sản phẩm, dịch vụ và dịch vụ con
        let contractItemsData = [];

        // Lặp qua tất cả các item (sản phẩm và mục khác)
        document.querySelectorAll('.item-container').forEach(itemContainer => {
            const itemId = itemContainer.dataset.itemId;
            const itemType = itemContainer.dataset.itemType;

            let itemData = {
                type: itemType,
                id: null,
                name: null,
                quantity: null,
                price: null,
                note: null,
                image: null,
                services: []
            };

            // Xử lý dựa vào loại item
            if (itemType === 'product') {
                // Sản phẩm
                itemData.id = itemContainer.querySelector('select[name="item_product_id[]"]')?.value;
                itemData.quantity = itemContainer.querySelector('input[name="item_quantity[]"]')?.value;

                // Lấy file ảnh nếu có
                const imageInput = itemContainer.querySelector('input[name="item_image[]"]');
                if (imageInput && imageInput.files.length > 0) {
                    itemData.image = imageInput.files[0];
                }
            } else {
                // Mục khác (giảm giá, phí,...)
                itemData.name = itemContainer.querySelector('input[name="item_name[]"]')?.value;
                itemData.price = itemContainer.querySelector('input[name="item_price[]"]')?.value;
                itemData.note = itemContainer.querySelector('input[name="item_note[]"]')?.value;
            }

            // Thu thập dữ liệu dịch vụ cho mỗi item
            const serviceItems = itemContainer.querySelectorAll('.service-item');
            serviceItems.forEach(serviceItem => {
                const serviceId = serviceItem.dataset.serviceId;
                const isCustomService = serviceItem.querySelector('input[name="service_ids[]"][value="custom"]') !== null;

                let serviceData = {
                    id: isCustomService ? 'custom' : serviceItem.querySelector('select[name="service_ids[]"]')?.value,
                    custom_name: isCustomService ? serviceItem.querySelector('input[name="service_custom_name[]"]')?.value : null,
                    price: serviceItem.querySelector('input[name="service_price[]"]')?.value,
                    note: serviceItem.querySelector('input[name="service_note[]"]')?.value,
                    sub_services: []
                };

                // Thu thập dữ liệu dịch vụ con
                const subServiceItems = serviceItem.querySelectorAll('.sub-service-item');
                subServiceItems.forEach(subServiceItem => {
                    const subServiceData = {
                        name: subServiceItem.querySelector('input[name="sub_service_name[]"]')?.value,
                        quantity: subServiceItem.querySelector('input[name="sub_service_quantity[]"]')?.value,
                        total: subServiceItem.querySelector('input[name="sub_service_total[]"]')?.value,
                        content: subServiceItem.querySelector('input[name="sub_service_content[]"]')?.value,
                        image: null
                    };

                    // Lấy file ảnh dịch vụ con nếu có
                    const imageInput = subServiceItem.querySelector('input[name="sub_service_image[]"]');
                    if (imageInput && imageInput.files.length > 0) {
                        subServiceData.image = imageInput.files[0];
                    }

                    serviceData.sub_services.push(subServiceData);
                });

                itemData.services.push(serviceData);
            });

            contractItemsData.push(itemData);
        });

        // Tạo FormData để gửi dữ liệu form kèm file
        const formData = new FormData(document.getElementById('contract-form'));

        // Xóa các file input để tránh trùng lặp
        formData.delete('item_image[]');
        formData.delete('sub_service_image[]');

        // Thêm dữ liệu cấu trúc JSON
        formData.append('contract_items_data', JSON.stringify(contractItemsData));

        // Thêm lại các file với key phù hợp
        contractItemsData.forEach((item, itemIndex) => {
            if (item.image instanceof File) {
                formData.append(`items[${itemIndex}][image]`, item.image);
            }

            item.services.forEach((service, serviceIndex) => {
                service.sub_services.forEach((subService, subServiceIndex) => {
                    if (subService.image instanceof File) {
                        formData.append(`items[${itemIndex}][services][${serviceIndex}][sub_services][${subServiceIndex}][image]`, subService.image);
                    }
                });
            });
        });

        let method = "post",
            url = "/contract/create",
            params = null,
            data = formData;

        try {
            // Sử dụng Axios với Content-Type là multipart/form-data để gửi cả file và dữ liệu
            const axiosConfig = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            };

            let res = await axios[method](url, data, axiosConfig);

            switch (res.data.status) {
                case 200:
                    showAlert('success', res.data.message);
                    // Hiệu ứng thành công trước khi tải lại trang
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    break;
                default:
                    showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                    break;
            }
        } catch (error) {
            showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
            console.error(error);
        }
    }

    // Document ready
    $(document).ready(function() {
        flatpickrMake($('input[name="sign_date"], input[name="effective_date"], input[name="expiry_date"], input[name="estimate_date"]'), 'date');
    });
</script>
@endpush