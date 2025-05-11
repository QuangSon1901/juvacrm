<style>
    .input, .select, .textarea {
            border-color: #cbd5e1;
            background-color: #ffffff;
            color: #1e293b;
        }

        .input:focus, .select:focus, .textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        /* Style cho dịch vụ combo */
        .service-item.combo-pricing {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
        }

        /* Style cho dịch vụ lẻ */
        .service-item.individual-pricing {
            background-color: #f8fafc;
            border-left: 4px solid #64748b;
        }

        /* Hiệu ứng hover rõ ràng hơn */
        .btn:hover {
            filter: brightness(0.95);
        }

        /* Tăng độ rõ ràng cho badge status */
        .badge {
            font-weight: 600;
            padding: 0.25rem 0.5rem;
        }
        
        /* Hiển thị nút xóa ảnh khi hover */
        .image-preview-container .relative:hover button {
            display: block;
        }
</style>
<form class="hidden transition-opacity duration-700" id="tab-services">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="col-span-1 xl:!col-span-3">
            {{-- Tổng hợp giá trị dịch vụ --}}
            <div class="card shadow-sm border border-gray-300 overflow-hidden mb-4">
                <div class="card-header bg-white border-b border-gray-300">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-dollar text-green-600"></i>
                        Tổng hợp giá trị dịch vụ
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:!grid-cols-4 gap-4">
                        <div class="p-4 bg-gray-100 rounded-lg">
                            <div class="text-sm font-medium text-gray-700 mb-1">Tổng giá trị dịch vụ</div>
                            <div class="services-total-value text-lg font-semibold text-gray-900">0 ₫</div>
                        </div>
                        <div class="p-4 bg-red-50 rounded-lg">
                            <div class="text-sm font-medium text-red-700 mb-1">Tổng giảm giá</div>
                            <div class="discount-value text-lg font-semibold text-red-700">-0 ₫</div>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-sm font-medium text-blue-700 mb-1">Tổng giá trị hợp đồng</div>
                            <div class="contract-total-value text-lg font-semibold text-blue-900">0 ₫</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm border border-gray-300 overflow-hidden">
                <div class="card-header bg-white border-b border-gray-300 flex-wrap gap-4">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-package text-purple-600"></i>
                        Thông tin sản phẩm và dịch vụ
                    </h3>
                    <div class="flex gap-2">
                        @if($canEdit)
                        <button type="button" class="btn btn-light btn-sm hover:bg-gray-200" onclick="addProductItem()">
                            <i class="ki-filled ki-plus"></i>
                            Thêm sản phẩm
                        </button>
                        <button type="button" class="btn btn-light btn-sm hover:bg-gray-200" onclick="addOtherItem('custom', 'Mục khác')">
                            <i class="ki-filled ki-plus"></i>
                            Thêm mục khác
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0 !px-[4px]">
                    <div id="items-container">
                        @foreach($details['contract_items'] as $itemIndex => $item)
                        @if($item['type'] == 'product')
                        <div class="item-container border-b border-gray-300 p-3" data-item-id="{{ $itemIndex }}" data-item-type="product">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                                <div class="col-span-1">
                                    <div class="grid grid-cols-1 gap-3 mb-3">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-1">Tên sản phẩm</label>
                                            <select name="item_product_id[]" class="select border-gray-300 focus:border-blue-500 rounded-lg w-full" {{ !$canEdit ? 'disabled' : '' }}>
                                                <option disabled>Chọn sản phẩm</option>
                                                @foreach($data_init['products'] ?? [] as $product)
                                                <option value="{{ $product['id'] }}" {{ $item['product_id'] == $product['id'] ? 'selected' : '' }}>
                                                    {{ $product['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="item_type[]" value="product">
                                            <input type="hidden" name="contract_item_id[]" value="{{ $item['id'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="flex gap-2 mb-3">
                                        @if($canEdit)
                                        <button type="button" class="btn btn-sm btn-light" onclick="addService(this)">
                                            <i class="ki-filled ki-plus text-green-600 mr-1"></i>
                                            Thêm dịch vụ
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light" onclick="addCustomService(this)">
                                            <i class="ki-filled ki-plus text-orange-600 mr-1"></i>
                                            Thêm dịch vụ khác
                                        </button>
                                        <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeItem(this)">
                                            <i class="ki-filled ki-trash !text-red-600"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="services-container col-span-1 lg:col-span-2 border-gray-300">
                                    @foreach($item['services'] ?? [] as $serviceIndex => $service)
                                    @php
                                    $serviceType = isset($service['service_type']) ? $service['service_type'] : 'individual';
                                    $serviceClass = $serviceType == 'combo' ? 'combo-pricing' : 'individual-pricing';
                                    @endphp
                                    
                                    <div class="service-item mb-3 rounded-lg border border-gray-400 p-3 {{ $serviceClass }}" data-service-id="service_{{ $itemIndex }}_{{ $serviceIndex }}">
                                        <div class="flex justify-between items-start gap-2 mb-3">
                                            <div class="flex items-center gap-2 {{ $serviceType == 'combo' ? 'text-blue-600' : 'text-green-600' }}">
                                                <i class="ki-filled ki-abstract-26"></i>
                                                <h5 class="font-medium text-gray-800">
                                                    {{ isset($service['custom_name']) && $service['custom_name'] ? 'Dịch vụ khác' : 'Dịch vụ' }}
                                                    <span class="badge badge-sm {{ $serviceType == 'combo' ? 'badge-info' : 'badge-success' }} ml-2">
                                                        {{ $serviceType == 'combo' ? 'Combo' : 'Lẻ' }}
                                                    </span>
                                                </h5>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                @if($canEdit)
                                                <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeService(this)">
                                                    <i class="ki-filled ki-trash !text-red-600"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                                            @if(isset($service['custom_name']) && $service['custom_name'])
                                            <div>
                                                <label class="block text-gray-700 text-sm font-medium mb-1">Tên dịch vụ</label>
                                                <input name="service_custom_name[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nhập tên dịch vụ" value="{{ $service['custom_name'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                <input type="hidden" name="service_ids[]" value="custom">
                                                <input type="hidden" name="contract_service_id[]" value="{{ $service['id'] ?? '' }}">
                                            </div>
                                            @else
                                            <div>
                                                <label class="block text-gray-700 text-sm font-medium mb-1">Tên dịch vụ</label>
                                                <select name="service_ids[]" class="select border-gray-300 focus:border-blue-500 rounded-lg w-full service-select" {{ !$canEdit ? 'disabled' : '' }}>
                                                    <option disabled>Chọn dịch vụ</option>
                                                    @foreach($data_init['services'] ?? [] as $serviceOption)
                                                    <option value="{{ $serviceOption['id'] }}" {{ $service['service_id'] == $serviceOption['id'] ? 'selected' : '' }}>
                                                        {{ $serviceOption['name'] }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="contract_service_id[]" value="{{ $service['id'] ?? '' }}">
                                            </div>
                                            @endif
                                            <div>
                                                <label class="block text-gray-700 text-sm font-medium mb-1">Đơn giá</label>
                                                <div class="relative">
                                                    <input name="service_price[]" class="input border-gray-300 focus:border-blue-500 rounded-lg !pl-8 w-full service-price" type="text" placeholder="Đơn giá" oninput="updateSubServicePrices(this)" value="{{ $service['price'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 text-sm font-medium mb-1">Loại tính giá</label>
                                                <select name="service_type[]" class="select border-gray-300 focus:border-blue-500 rounded-lg w-full service-type-select" onchange="updatePricingModel(this)" {{ !$canEdit ? 'disabled' : '' }}>
                                                    <option value="individual" {{ $serviceType == 'individual' ? 'selected' : '' }}>Tính giá lẻ</option>
                                                    <option value="combo" {{ $serviceType == 'combo' ? 'selected' : '' }}>Tính giá combo</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="flex gap-2 mb-3">
                                            @if($canEdit)
                                            <button type="button" class="btn btn-sm btn-light" onclick="addSubService(this)">
                                                <i class="ki-filled ki-plus text-blue-600 mr-1"></i>
                                                Thêm góc máy
                                            </button>
                                            @endif
                                        </div>
                                        
                                        <div class="sub-services-container border-gray-300">
                                            @foreach($service['sub_services'] ?? [] as $subServiceIndex => $subService)
                                            <div class="sub-service-item mb-2 rounded p-2 border-l-2 border-gray-400" data-sub-service-id="sub_service_{{ $itemIndex }}_{{ $serviceIndex }}_{{ $subServiceIndex }}">
                                                <div class="grid grid-cols-1 lg:!grid-cols-12 gap-2">
                                                    <div class="lg:!col-span-6">
                                                        <label class="block text-gray-700 text-sm font-medium mb-1">Góc máy</label>
                                                        <input name="sub_service_name[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Góc máy" value="{{ $subService['name'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                        <input type="hidden" name="contract_sub_service_id[]" value="{{ $subService['id'] ?? '' }}">
                                                    </div>
                                                    <div class="lg:!col-span-2">
                                                        <label class="block text-gray-700 text-sm font-medium mb-1">Số lượng</label>
                                                        <input name="sub_service_quantity[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full sub-service-quantity" type="text" placeholder="Số lượng" oninput="calculateSubServiceTotal(this)" value="{{ $subService['quantity'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                    </div>
                                                    <div class="lg:!col-span-4">
                                                        <label class="block text-gray-700 text-sm font-medium mb-1">Thành tiền</label>
                                                        <div class="relative">
                                                            <input name="sub_service_total[]" class="input border-gray-300 focus:border-blue-500 rounded-lg !pl-6 w-full sub-service-total bg-gray-100" type="text" placeholder="Thành tiền" readonly value="{{$subService['total'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₫</span>
                                                        </div>
                                                    </div>
                                                    <div class="lg:!col-span-6">
                                                        <label class="block text-gray-700 text-sm font-medium mb-1">Nội dung</label>
                                                        <textarea name="sub_service_content[]" class="textarea border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nội dung" {{ !$canEdit ? 'disabled' : '' }}>{{ $subService['content'] }}</textarea>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-2">
                                                    <div>
                                                        <label class="block text-gray-700 text-sm font-medium mb-1">Hình ảnh mẫu</label>
                                                        <div class="flex flex-wrap items-center gap-2 blocks-preview">
                                                            <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-sample')">
                                                                <i class="ki-filled ki-file-up"></i>
                                                                Tải ảnh mẫu
                                                            </button>
                                                            <div class="image-preview-container flex flex-wrap gap-2 mt-2">
                                                                @php
                                                                $sampleImages = isset($subService['sample_image_id']) ? explode('|', $subService['sample_image_id']) : [];
                                                                @endphp
                                                                
                                                                @foreach($sampleImages as $imgId)
                                                                @if(!empty($imgId))
                                                                <div class="relative group h-16 w-16 border border-gray-200 rounded overflow-hidden  block-preview">
                                                                    <img src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_80,h_80,c_fill,q_auto,f_auto/uploads/{{ $imgId }}" 
                                                                         class="h-full w-full object-cover cursor-pointer" 
                                                                         alt="Sample image"
                                                                         onerror="this.src='/assets/images/default.svg'">
                                                                    @if($canEdit)
                                                                    <button type="button" class="absolute top-0 right-0 p-1 bg-red-500 text-white rounded-bl hidden group-hover:block" 
                                                                            onclick="removeImageFromGallery(this, '{{ $imgId }}')">
                                                                        <i class="ki-filled ki-trash text-xs"></i>
                                                                    </button>
                                                                    @endif
                                                                </div>
                                                                @endif
                                                                @endforeach
                                                            </div>
                                                            <input type="file" name="sub_service_sample_images[]" class="sub-service-sample-image-input hidden" accept="image/*" onchange="previewMultipleImages(this, 'sample')" multiple {{ !$canEdit ? 'disabled' : '' }}>
                                                            <input type="hidden" name="sub_service_sample_image_ids[]" value="{{ $subService['sample_image_id'] }}">
                                                        </div>
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-gray-700 text-sm font-medium mb-1">Hình ảnh kết quả</label>
                                                        <div class="flex flex-wrap items-center gap-2 blocks-preview">
                                                            <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-result')">
                                                                <i class="ki-filled ki-file-up"></i>
                                                                Tải ảnh kết quả
                                                            </button>
                                                            <div class="image-preview-container flex flex-wrap gap-2 mt-2">
                                                                @php
                                                                $resultImages = isset($subService['result_image_id']) ? explode('|', $subService['result_image_id']) : [];
                                                                @endphp
                                                                
                                                                @foreach($resultImages as $imgId)
                                                                @if(!empty($imgId))
                                                                <div class="relative group h-16 w-16 border border-gray-200 rounded overflow-hidden">
                                                                    <img src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_80,h_80,c_fill,q_auto,f_auto/uploads/{{ $imgId }}" 
                                                                         class="h-full w-full object-cover cursor-pointer" 
                                                                         alt="Result image"
                                                                         onerror="this.src='/assets/images/default.svg'">
                                                                    @if($canEdit)
                                                                    <button type="button" class="absolute top-0 right-0 p-1 bg-red-500 text-white rounded-bl hidden group-hover:block" 
                                                                            onclick="removeImageFromGallery(this, '{{ $imgId }}')">
                                                                        <i class="ki-filled ki-trash text-xs"></i>
                                                                    </button>
                                                                    @endif
                                                                </div>
                                                                @endif
                                                                @endforeach
                                                            </div>
                                                            <input type="file" name="sub_service_result_images[]" class="sub-service-result-image-input hidden" accept="image/*" onchange="previewMultipleImages(this, 'result')" multiple {{ !$canEdit ? 'disabled' : '' }}>
                                                            <input type="hidden" name="sub_service_result_image_ids[]" value="{{ $subService['result_image_id'] }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @if($canEdit)
                                                <div class="flex justify-end mt-2">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeSubService(this)">
                                                        <i class="ki-filled ki-trash !text-red-600"></i>
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @elseif($item['type'] == 'custom')
                        {{-- Custom Item (like discount, fees, etc.) --}}
                        <div class="item-container border-b border-gray-300 p-3" data-item-id="{{ $itemIndex }}" data-item-type="custom">
                            <div class="flex justify-between items-start gap-2 mb-3">
                                <div class="flex items-center gap-2 text-indigo-600">
                                    <i class="ki-filled ki-note-2"></i>
                                    <h4 class="font-medium text-gray-800">Mục khác</h4>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if($canEdit)
                                    <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeItem(this)">
                                        <i class="ki-filled ki-trash !text-red-600"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Tên mục</label>
                                    <input name="item_name[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ví dụ: Giảm giá, Phí vận chuyển..." value="{{ $item['name'] ?? '' }}" {{ !$canEdit ? 'disabled' : '' }}>
                                    <input type="hidden" name="item_type[]" value="custom">
                                    <input type="hidden" name="contract_item_id[]" value="{{ $item['id'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Giá trị</label>
                                    <div class="relative">
                                        <input name="item_price[]" class="input border-gray-300 focus:border-blue-500 rounded-lg pl-8 w-full" type="text" placeholder="Giá trị" oninput="calculateTotalValue()" value="{{ $item['price'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Ghi chú</label>
                                    <input name="item_note[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú" value="{{ $item['note'] ?? '' }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TEMPLATES FOR JAVASCRIPT USE -->
            <!-- Product Item Template -->
            <template id="product-item-template">
                <div class="item-container border-b border-gray-300 p-3" data-item-id="__ITEM_ID__" data-item-type="product">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <div class="grid grid-cols-1 gap-3 mb-3">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Tên sản phẩm</label>
                                    <select name="item_product_id[]" class="select border-gray-300 focus:border-blue-500 rounded-lg w-full">
                                        <option disabled selected>Chọn sản phẩm</option>
                                        __PRODUCTS_OPTIONS__
                                    </select>
                                    <input type="hidden" name="item_type[]" value="product">
                                    <input type="hidden" name="contract_item_id[]" value="">
                                </div>
                            </div>
                            <div class="flex gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-light" onclick="addService(this)">
                                    <i class="ki-filled ki-plus text-green-600 mr-1"></i>
                                    Thêm dịch vụ
                                </button>
                                <button type="button" class="btn btn-sm btn-light" onclick="addCustomService(this)">
                                    <i class="ki-filled ki-plus text-orange-600 mr-1"></i>
                                    Thêm dịch vụ khác
                                </button>
                                <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeItem(this)">
                                    <i class="ki-filled ki-trash !text-red-600"></i>
                                </button>
                            </div>
                        </div>
                        <div class="services-container col-span-1 lg:col-span-2 border-gray-300">
                            <!-- Services will be added here -->
                        </div>
                    </div>
                </div>
            </template>

            <!-- Other Item Template (for discount, fees, etc.) -->
            <template id="other-item-template">
                <div class="item-container border-b border-gray-300 p-3" data-item-id="__ITEM_ID__" data-item-type="__TYPE__">
                    <div class="flex justify-between items-start gap-2 mb-3">
                        <div class="flex items-center gap-2 text-indigo-600">
                            <i class="ki-filled ki-note-2"></i>
                            <h4 class="font-medium text-gray-800">__ITEM_TITLE__</h4>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeItem(this)">
                                <i class="ki-filled ki-trash !text-red-600"></i>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Tên mục</label>
                            <input name="item_name[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ví dụ: Giảm giá, Phí vận chuyển...">
                            <input type="hidden" name="item_type[]" value="__TYPE__">
                            <input type="hidden" name="contract_item_id[]" value="">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Giá trị</label>
                            <div class="relative">
                                <input name="item_price[]" class="input border-gray-300 focus:border-blue-500 rounded-lg pl-8 w-full" type="text" placeholder="Giá trị" oninput="calculateTotalValue()">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Ghi chú</label>
                            <input name="item_note[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú">
                        </div>
                    </div>
                </div>
            </template>

            <!-- Service Template -->
            <template id="service-template">
                <div class="service-item mb-3 rounded-lg border border-gray-400 p-3 individual-pricing" data-service-id="__SERVICE_ID__">
                    <div class="flex justify-between items-start gap-2 mb-3">
                        <div class="flex items-center gap-2 text-green-600">
                            <i class="ki-filled ki-abstract-26"></i>
                            <h5 class="font-medium text-gray-800">
                                Dịch vụ
                                <span class="badge badge-sm badge-success ml-2">Lẻ</span>
                            </h5>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeService(this)">
                                <i class="ki-filled ki-trash !text-red-600"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Tên dịch vụ</label>
                            <select name="service_ids[]" class="select border-gray-300 focus:border-blue-500 rounded-lg w-full service-select">
                                <option disabled selected>Chọn dịch vụ</option>
                                __SERVICES_OPTIONS__
                            </select>
                            <input type="hidden" name="contract_service_id[]" value="">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Đơn giá</label>
                            <div class="relative">
                                <input name="service_price[]" class="input border-gray-300 focus:border-blue-500 rounded-lg !pl-8 w-full service-price" type="text" placeholder="Đơn giá" oninput="updateSubServicePrices(this)">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Loại tính giá</label>
                            <select name="service_type[]" class="select border-gray-300 focus:border-blue-500 rounded-lg w-full service-type-select" onchange="updatePricingModel(this)">
                                <option value="individual" selected>Tính giá lẻ</option>
                                <option value="combo">Tính giá combo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-light" onclick="addSubService(this)">
                            <i class="ki-filled ki-plus text-blue-600 mr-1"></i>
                            Thêm góc máy
                        </button>
                    </div>
                    
                    <div class="sub-services-container border-gray-300">
                        <!-- Sub services will be added here -->
                    </div>
                </div>
            </template>

            <!-- Custom Service Template -->
            <template id="custom-service-template">
                <div class="service-item mb-3 rounded-lg border border-gray-400 p-3 individual-pricing" data-service-id="__SERVICE_ID__">
                    <div class="flex justify-between items-start gap-2 mb-3">
                        <div class="flex items-center gap-2 text-orange-600">
                            <i class="ki-filled ki-abstract-26"></i>
                            <h5 class="font-medium text-gray-800">
                                Dịch vụ khác
                                <span class="badge badge-sm badge-success ml-2">Lẻ</span>
                            </h5>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeService(this)">
                                <i class="ki-filled ki-trash !text-red-600"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Tên dịch vụ</label>
                            <input name="service_custom_name[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nhập tên dịch vụ">
                            <input type="hidden" name="service_ids[]" value="custom">
                            <input type="hidden" name="contract_service_id[]" value="">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Đơn giá</label>
                            <div class="relative">
                                <input name="service_price[]" class="input border-gray-300 focus:border-blue-500 rounded-lg pl-8 w-full service-price" type="text" placeholder="Đơn giá" oninput="updateSubServicePrices(this)">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Loại tính giá</label>
                            <select name="service_type[]" class="select border-gray-300 focus:border-blue-500 rounded-lg w-full service-type-select" onchange="updatePricingModel(this)">
                                <option value="individual" selected>Tính giá lẻ</option>
                                <option value="combo">Tính giá combo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-light" onclick="addSubService(this)">
                            <i class="ki-filled ki-plus text-blue-600 mr-1"></i>
                            Thêm góc máy
                        </button>
                    </div>
                    
                    <div class="sub-services-container border-gray-300">
                        <!-- Sub services will be added here -->
                    </div>
                </div>
            </template>

            <!-- Sub Service Template -->
            <template id="sub-service-template">
                <div class="sub-service-item mb-2 rounded p-2 border-l-2 border-gray-400" data-sub-service-id="__SUB_SERVICE_ID__">
                    <div class="grid grid-cols-1 lg:!grid-cols-12 gap-2">
                        <div class="lg:!col-span-6">
                            <label class="block text-gray-700 text-sm font-medium mb-1">Góc máy</label>
                            <input name="sub_service_name[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Góc máy">
                            <input type="hidden" name="contract_sub_service_id[]" value="">
                        </div>
                        <div class="lg:!col-span-2">
                            <label class="block text-gray-700 text-sm font-medium mb-1">Số lượng</label>
                            <input name="sub_service_quantity[]" class="input border-gray-300 focus:border-blue-500 rounded-lg w-full sub-service-quantity" type="text" placeholder="Số lượng" oninput="calculateSubServiceTotal(this)">
                        </div>
                        <div class="lg:!col-span-4">
                            <label class="block text-gray-700 text-sm font-medium mb-1">Thành tiền</label>
                            <div class="relative">
                                <input name="sub_service_total[]" class="input border-gray-300 focus:border-blue-500 rounded-lg !pl-6 w-full sub-service-total bg-gray-100" type="text" placeholder="Thành tiền" readonly>
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₫</span>
                            </div>
                        </div>
                        <div class="lg:!col-span-6">
                            <label class="block text-gray-700 text-sm font-medium mb-1">Nội dung</label>
                            <textarea name="sub_service_content[]" class="textarea border-gray-300 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nội dung"></textarea>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-2">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Hình ảnh mẫu</label>
                            <div class="flex flex-wrap items-center gap-2 blocks-preview">
                                <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-sample')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh mẫu
                                </button>
                                <div class="image-preview-container flex flex-wrap gap-2 mt-2">
                                    <!-- Images will be added here -->
                                </div>
                                <input type="file" name="sub_service_sample_images[]" class="sub-service-sample-image-input hidden" accept="image/*" onchange="previewMultipleImages(this, 'sample')" multiple>
                                <input type="hidden" name="sub_service_sample_image_ids[]" value="">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Hình ảnh kết quả</label>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-result')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh kết quả
                                </button>
                                <div class="image-preview-container flex flex-wrap gap-2 mt-2">
                                    <!-- Images will be added here -->
                                </div>
                                <input type="file" name="sub_service_result_images[]" class="sub-service-result-image-input hidden" accept="image/*" onchange="previewMultipleImages(this, 'result')" multiple>
                                <input type="hidden" name="sub_service_result_image_ids[]" value="">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-2">
                        <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeSubService(this)">
                            <i class="ki-filled ki-trash !text-red-600"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Thêm vào file scripts
document.addEventListener('DOMContentLoaded', function() {
    // Thêm CSS cho các styles mới
    addStyles();
    
    // Tính giá trị ban đầu
    calculateTotalValue();
});

function addStyles() {
    const styleElement = document.createElement('style');
    styleElement.textContent = `
        /* Tăng độ tương phản cho form fields */
        .input, .select, .textarea {
            border-color: #cbd5e1;
            background-color: #ffffff;
            color: #1e293b;
        }

        .input:focus, .select:focus, .textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        /* Style cho dịch vụ combo */
        .service-item.combo-pricing {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
        }

        /* Style cho dịch vụ lẻ */
        .service-item.individual-pricing {
            background-color: #f8fafc;
            border-left: 4px solid #64748b;
        }

        /* Hiệu ứng hover rõ ràng hơn */
        .btn:hover {
            filter: brightness(0.95);
        }

        /* Tăng độ rõ ràng cho badge status */
        .badge {
            font-weight: 600;
            padding: 0.25rem 0.5rem;
        }
        
        /* Hiển thị nút xóa ảnh khi hover */
        .image-preview-container .relative:hover button {
            display: block;
        }
    `;
    document.head.appendChild(styleElement);
}

let itemCounter = 0;
let serviceCounter = 0;
let subServiceCounter = 0;
const cloudinaryName = '{{env("CLOUDINARY_CLOUD_NAME")}}';

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

// Thêm dịch vụ con (góc máy)
function addSubService(button) {
    const serviceItem = button.closest('.service-item');
    const subServicesContainer = serviceItem.querySelector('.sub-services-container');
    const subServiceId = subServiceCounter++;

    let template = document.getElementById('sub-service-template').innerHTML;
    template = template.replace(/__SUB_SERVICE_ID__/g, subServiceId);

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = template;
    subServicesContainer.appendChild(tempDiv.firstElementChild);
    
    // Cập nhật mô hình tính giá cho sub-service mới
    updatePricingModel(serviceItem.querySelector('.service-type-select'));
}

// Xóa item (sản phẩm hoặc mục khác)
function removeItem(button) {
    if (confirm("Bạn có chắc chắn muốn xóa mục này?")) {
        const itemContainer = button.closest('.item-container');
        itemContainer.remove();
        calculateTotalValue();
    }
}

// Xóa dịch vụ
function removeService(button) {
    if (confirm("Bạn có chắc chắn muốn xóa dịch vụ này?")) {
        const serviceItem = button.closest('.service-item');
        serviceItem.remove();
        calculateTotalValue();
    }
}

// Xóa dịch vụ con (góc máy)
function removeSubService(button) {
    if (confirm("Bạn có chắc chắn muốn xóa góc máy này?")) {
        const subServiceItem = button.closest('.sub-service-item');
        const serviceItem = subServiceItem.closest('.service-item');
        subServiceItem.remove();
        
        // Cập nhật lại tổng giá trị và mô hình tính giá
        updatePricingModel(serviceItem.querySelector('.service-type-select'));
        calculateTotalValue();
    }
}

// Kích hoạt dialog upload ảnh
function triggerImageUpload(button, type) {
    const container = button.closest('div');
    const fileInput = container.querySelector(`.${type}-image-input`);
    
    if (fileInput) {
        fileInput.click();
    }
}

// Hàm xử lý upload và hiển thị nhiều ảnh
function previewMultipleImages(input, type) {
    if (!input.files || input.files.length === 0) return;
    
    const container = input.closest('div');
    const previewContainer = container.querySelector('.image-preview-container');
    const imageIdsInput = container.querySelector(`input[name="sub_service_${type}_image_ids[]"]`);
    let imageIds = imageIdsInput.value ? imageIdsInput.value.split('|') : [];
    
    // Upload từng file và thêm vào gallery
    Array.from(input.files).forEach(async (file) => {
        // Tạo preview tạm thời
        const previewDiv = document.createElement('div');
        previewDiv.className = 'relative group h-16 w-16 border border-gray-200 rounded overflow-hidden';
        
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'absolute inset-0 flex items-center justify-center bg-gray-100';
        loadingIndicator.innerHTML = '<i class="ki-filled ki-loading animate-spin text-blue-500"></i>';
        
        previewDiv.appendChild(loadingIndicator);
        previewContainer.appendChild(previewDiv);
        
        try {
            // Upload file
            let res = await uploadFileTemplate(file);
            
            if (res.data.status == 200) {
                const imageId = res.data.data.driver_id;
                imageIds.push(imageId);
                
                // Cập nhật preview với ảnh thực tế
                loadingIndicator.remove();
                
                const img = document.createElement('img');
                img.src = `https://res.cloudinary.com/${cloudinaryName}/image/upload/w_80,h_80,c_fill,q_auto,f_auto/uploads/${imageId}`;
                img.className = 'h-full w-full object-cover';
                img.onerror = () => img.src = '/assets/images/default.svg';
                
                const removeBtn = document.createElement('button');
                removeBtn.className = 'absolute top-0 right-0 p-1 bg-red-500 text-white rounded-bl hidden group-hover:block';
                removeBtn.innerHTML = '<i class="ki-filled ki-trash text-xs"></i>';
                removeBtn.onclick = (e) => {
                    e.stopPropagation();
                    removeImageFromGallery(removeBtn, imageId);
                };
                
                previewDiv.appendChild(img);
                previewDiv.appendChild(removeBtn);
                
                // Cập nhật input hidden với danh sách ID ảnh
                imageIdsInput.value = imageIds.join('|');
            } else {
                previewDiv.remove();
                showAlert('error', 'Upload failed!');
            }
        } catch (error) {
            previewDiv.remove();
            showAlert('error', 'Error uploading file');
            console.error(error);
        }
    });
    
    // Reset input file để có thể chọn cùng file nhiều lần
    input.value = '';
}

// Xóa ảnh khỏi gallery
function removeImageFromGallery(button, imageId) {
    const container = button.closest('.blocks-preview');
    const previewDiv = button.closest('.relative');
    const imageIdsInput = container.querySelector('input[type="hidden"]');
    
    let imageIds = imageIdsInput.value.split('|');
    const index = imageIds.indexOf(imageId);
    
    if (index > -1) {
        imageIds.splice(index, 1);
        imageIdsInput.value = imageIds.join('|');
        previewDiv.remove();
    }
}

// Cập nhật mô hình tính giá
function updatePricingModel(select) {
    const serviceItem = select.closest('.service-item');
    const subServices = serviceItem.querySelectorAll('.sub-service-item');
    const isPricingCombo = select.value === 'combo';
    
    // Cập nhật class và badge
    const badgeSpan = serviceItem.querySelector('h5 .badge');
    
    if (isPricingCombo) {
        serviceItem.classList.add('combo-pricing');
        serviceItem.classList.remove('individual-pricing');
        
        if (badgeSpan) {
            badgeSpan.textContent = 'Combo';
            badgeSpan.classList.remove('badge-success');
            badgeSpan.classList.add('badge-info');
        }
        
        // Cập nhật icon
        const icon = serviceItem.querySelector('.flex.items-center.gap-2 i');
        if (icon) {
            icon.classList.remove('text-green-600', 'text-orange-600');
            icon.classList.add('text-blue-600');
        }
    } else {
        serviceItem.classList.add('individual-pricing');
        serviceItem.classList.remove('combo-pricing');
        
        if (badgeSpan) {
            badgeSpan.textContent = 'Lẻ';
            badgeSpan.classList.remove('badge-info');
            badgeSpan.classList.add('badge-success');
        }
        
        // Giữ màu icon mặc định (xanh lá hoặc cam)
        const icon = serviceItem.querySelector('.flex.items-center.gap-2 i');
        if (icon && icon.classList.contains('text-blue-600')) {
            const isCustomService = serviceItem.querySelector('input[name="service_ids[]"][value="custom"]') !== null;
            icon.classList.remove('text-blue-600');
            icon.classList.add(isCustomService ? 'text-orange-600' : 'text-green-600');
        }
    }
    
    // Cập nhật tính toán giá cho tất cả sub-services
    subServices.forEach(subService => {
        calculateSubServiceTotal(subService.querySelector('.sub-service-quantity'));
    });
    
    // Cập nhật tổng
    calculateTotalValue();
}

// Cập nhật đơn giá cho các dịch vụ con
function updateSubServicePrices(input) {
    const serviceItem = input.closest('.service-item');
    const subServices = serviceItem.querySelectorAll('.sub-service-item');

    subServices.forEach(subService => {
        calculateSubServiceTotal(subService.querySelector('.sub-service-quantity'));
    });

    calculateTotalValue();
}

// Tính toán thành tiền cho dịch vụ con (góc máy)
function calculateSubServiceTotal(input) {
    if (!input) return;
    
    const subServiceItem = input.closest('.sub-service-item');
    const serviceItem = subServiceItem.closest('.service-item');
    const servicePrice = parseFloat(serviceItem.querySelector('.service-price').value.replace(/[.,]/g, '')) || 0;
    const serviceTypeSelect = serviceItem.querySelector('.service-type-select');
    const isPricingCombo = serviceTypeSelect && serviceTypeSelect.value === 'combo';
    
    const quantityInput = subServiceItem.querySelector('.sub-service-quantity');
    const totalInput = subServiceItem.querySelector('.sub-service-total');
    
    if (!quantityInput || !totalInput) return;
    
    const quantity = parseFloat(quantityInput.value.replace(/[.,]/g, '')) || 0;
    
    // Tính giá dựa trên mô hình tính giá
    if (isPricingCombo) {
        // Tìm tất cả các góc máy trong service này
        const allSubServices = serviceItem.querySelectorAll('.sub-service-item');
        const totalSubServices = allSubServices.length;
        
        if (totalSubServices > 0) {
            // Đối với combo, mỗi góc máy có giá = tổng giá / số góc máy
            const pricePerAngle = Math.round(servicePrice / totalSubServices);
            totalInput.value = formatNumberLikePhp(pricePerAngle);
        } else {
            totalInput.value = '0';
        }
    } else {
        // Đối với tính giá lẻ, mỗi góc máy = số lượng × đơn giá dịch vụ
        const total = quantity * servicePrice;
        totalInput.value = formatNumberLikePhp(total);
    }
    
    calculateTotalValue();
}

// Tính tổng giá trị hợp đồng
function calculateTotalValue() {
    // Tính tổng giá trị từ tất cả dịch vụ con
    let total = 0;
    let amount = 0;
    let discount = 0;

    // Cộng thành tiền từ tất cả các góc máy
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
            total += value; 
            if (value < 0) {
                discount += Math.abs(value);
            } else {
                amount += value;
            }
        }
    });

    // Cập nhật hiển thị trên UI
    document.querySelectorAll('.services-total-value').forEach(el => {
        el.textContent = formatNumberLikePhp(amount) + ' ₫';
    });
    
    document.querySelectorAll('.discount-value').forEach(el => {
        el.textContent = '-' + formatNumberLikePhp(discount) + ' ₫';
    });
    
    document.querySelectorAll('.contract-total-value').forEach(el => {
        el.textContent = formatNumberLikePhp(total) + ' ₫';
    });
    
    // Cập nhật input hidden
    const totalValueInput = document.querySelector('input[name="total_value"]');
    if (totalValueInput) {
        totalValueInput.value = total;
    }
}

// Lưu thay đổi dịch vụ
async function saveCreateContract(contractId) {
    Notiflix.Confirm.show(
        'Xác nhận cập nhật dịch vụ',
        'Bạn có chắc chắn muốn cập nhật dịch vụ cho hợp đồng này? Hành động này sẽ ảnh hưởng đến các công việc đã tạo.',
        'Xác nhận',
        'Hủy',
        async () => {
            // Cập nhật lại tổng giá trị trước khi gửi
            calculateTotalValue();

            // Tạo cấu trúc dữ liệu để gửi
            let contractItemsData = [];

            // Duyệt qua tất cả các item (sản phẩm và mục khác)
            document.querySelectorAll('.item-container').forEach(itemContainer => {
                const itemId = itemContainer.dataset.itemId;
                const itemType = itemContainer.dataset.itemType;
                const contractItemId = itemContainer.querySelector('input[name="contract_item_id[]"]')?.value || null;

                let itemData = {
                    type: itemType,
                    id: contractItemId,
                    product_id: null,
                    name: null,
                    quantity: null,
                    price: null,
                    note: null,
                    services: []
                };

                // Xử lý dựa vào loại item
                if (itemType === 'product') {
                    // Sản phẩm
                    itemData.product_id = itemContainer.querySelector('select[name="item_product_id[]"]')?.value;
                    
                    // Thu thập dữ liệu dịch vụ cho mỗi item
                    const serviceItems = itemContainer.querySelectorAll('.service-item');
                    serviceItems.forEach(serviceItem => {
                        const serviceId = serviceItem.dataset.serviceId;
                        const isCustomService = serviceItem.querySelector('input[name="service_ids[]"][value="custom"]') !== null;
                        const contractServiceId = serviceItem.querySelector('input[name="contract_service_id[]"]')?.value || null;
                        const serviceType = serviceItem.querySelector('select[name="service_type[]"]')?.value || 'individual';

                        let serviceData = {
                            service_id: isCustomService ? 'custom' : serviceItem.querySelector('select[name="service_ids[]"]')?.value,
                            id: contractServiceId,
                            custom_name: isCustomService ? serviceItem.querySelector('input[name="service_custom_name[]"]')?.value : null,
                            price: serviceItem.querySelector('input[name="service_price[]"]')?.value,
                            note: serviceItem.querySelector('input[name="service_note[]"]')?.value,
                            service_type: serviceType,
                            sub_services: []
                        };

                        // Thu thập dữ liệu dịch vụ con (góc máy)
                        const subServiceItems = serviceItem.querySelectorAll('.sub-service-item');
                        subServiceItems.forEach(subServiceItem => {
                            const contractSubServiceId = subServiceItem.querySelector('input[name="contract_sub_service_id[]"]')?.value || null;
                            const sampleImageIds = subServiceItem.querySelector('input[name="sub_service_sample_image_ids[]"]')?.value || '';
                            const resultImageIds = subServiceItem.querySelector('input[name="sub_service_result_image_ids[]"]')?.value || '';

                            const subServiceData = {
                                id: contractSubServiceId,
                                name: subServiceItem.querySelector('input[name="sub_service_name[]"]')?.value,
                                quantity: subServiceItem.querySelector('input[name="sub_service_quantity[]"]')?.value,
                                total: subServiceItem.querySelector('input[name="sub_service_total[]"]')?.value,
                                content: subServiceItem.querySelector('textarea[name="sub_service_content[]"]')?.value,
                                sample_image_id: sampleImageIds,
                                result_image_id: resultImageIds
                            };

                            serviceData.sub_services.push(subServiceData);
                        });

                        itemData.services.push(serviceData);
                    });
                } else {
                    // Mục khác (giảm giá, phí,...)
                    itemData.name = itemContainer.querySelector('input[name="item_name[]"]')?.value;
                    itemData.price = itemContainer.querySelector('input[name="item_price[]"]')?.value;
                    itemData.note = itemContainer.querySelector('input[name="item_note[]"]')?.value;
                }

                contractItemsData.push(itemData);
            });

            // Tạo FormData để gửi dữ liệu form
            const formData = new FormData(document.getElementById('tab-services'));
            formData.append('contract_items_data', JSON.stringify(contractItemsData));
            formData.append('contract_id', contractId);

            try {
                Notiflix.Loading.circle('Đang cập nhật dịch vụ...');
                
                const axiosConfig = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                };

                let res = await axios.post("/contract/update", formData, axiosConfig);

                Notiflix.Loading.remove();
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    
                    // Hiệu ứng thành công và chuyển đến tab dịch vụ
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    showAlert('warning', res.data.message || "Đã có lỗi xảy ra khi lưu dịch vụ");
                }
            } catch (error) {
                Notiflix.Loading.remove();
                showAlert('error', "Đã có lỗi xảy ra: " + (error.response?.data?.message || error.message));
                console.error(error);
            }
        },
        () => {}, {}
    );
}
</script>
@endpush