<form class="hidden transition-opacity duration-700" id="tab-services">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="col-span-1 xl:!col-span-3">
            {{-- Tổng hợp giá trị dịch vụ --}}
            <div class="card shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="card-header bg-white border-b border-gray-100">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-dollar text-green-500"></i>
                        Tổng hợp giá trị dịch vụ
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
            @php
            // Extract data from the contract details
            $contractItems = $details['contract_items'] ?? [];
            @endphp

            <div class="card shadow-sm border border-gray-100 overflow-hidden">
                <div class="card-header bg-white border-b border-gray-100 flex-wrap gap-4">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-package text-purple-500"></i>
                        Thông tin sản phẩm và dịch vụ
                    </h3>
                    <div class="flex gap-2">
                        @if($canEdit)
                        <button type="button" class="btn btn-light btn-sm" onclick="addProductItem()">
                            <i class="ki-filled ki-plus"></i>
                            Thêm sản phẩm
                        </button>
                        <button type="button" class="btn btn-light btn-sm" onclick="addOtherItem('custom', 'Mục khác')">
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
                        <div class="item-container border-b border-gray-100 p-3" data-item-id="{{ $itemIndex }}" data-item-type="product">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                                <div class="col-span-1">
                                    <div class="grid grid-cols-1 gap-3 mb-3">
                                        <div>
                                            <label class="block text-gray-700 text-sm mb-1">Tên sản phẩm</label>
                                            <select name="item_product_id[]" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full" {{ !$canEdit ? 'disabled' : '' }}>
                                                <option disabled>Chọn sản phẩm</option>
                                                @foreach($data_init['products'] ?? [] as $product)
                                                <option value="{{ $product['id'] }}" {{ $item['product_id'] == $product['id'] ? 'selected' : '' }}>
                                                    {{ $product['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="item_type[]" value="product">
                                            <!-- Add contract_item_id field -->
                                            <input type="hidden" name="contract_item_id[]" value="{{ $item['id'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="flex gap-2 mb-3">
                                        @if($canEdit)
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
                                        @endif
                                    </div>
                                </div>
                                <div class="services-container col-span-1 lg:col-span-2 border-gray-100">
                                    @foreach($item['services'] ?? [] as $serviceIndex => $service)
                                    @if(isset($service['custom_name']) && $service['custom_name'])
                                    {{-- Custom Service --}}
                                    <div class="service-item mb-3 rounded-lg border border-gray-400 p-3" data-service-id="service_{{ $itemIndex }}_{{ $serviceIndex }}">
                                        <div class="flex justify-between items-start gap-2 mb-3">
                                            <div class="flex items-center gap-2 text-orange-500">
                                                <i class="ki-filled ki-abstract-26"></i>
                                                <h5 class="font-medium text-gray-700">Dịch vụ khác</h5>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                @if($canEdit)
                                                <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeService(this)">
                                                    <i class="ki-filled ki-trash !text-red-500"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Tên dịch vụ</label>
                                                <input name="service_custom_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nhập tên dịch vụ" value="{{ $service['custom_name'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                <input type="hidden" name="service_ids[]" value="custom">
                                                <!-- Add contract_service_id field -->
                                                <input type="hidden" name="contract_service_id[]" value="{{ $service['id'] ?? '' }}">
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Đơn giá</label>
                                                <div class="relative">
                                                    <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8 w-full service-price" type="text" placeholder="Đơn giá" oninput="updateSubServicePrices(this)" value="{{ $service['price'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Ghi chú</label>
                                                <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú" value="{{ $service['note'] ?? '' }}" {{ !$canEdit ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Hình ảnh mẫu</label>
                                                <div class="flex items-center gap-2">
                                                <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'service-sample')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh mẫu
                                </button>
                                                    <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden relative group">
                                                        
                                                        <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $service['sample_image_id'] }}" alt="">
                                                    </div>
                                                    <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'service_sample')">
                                                        <i class="ki-filled ki-trash"></i>
                                                    </button>
                                                    <input type="file" name="service_sample_image[]" class="service-sample-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="service_sample_image_id[]" value="{{ $service['sample_image_id'] }}">
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Hình ảnh kết quả</label>
                                                <div class="flex items-center gap-2">
                                                <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'service-result')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh kết quả
                                </button>
                                                    <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden relative group">
                                                        
                                                        <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $service['result_image_id'] }}" alt="">
                                                    </div>
                                                    <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'service_result')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="service_result_image[]" class="service-result-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="service_result_image_id[]" value="{{ $service['result_image_id'] }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 mb-3">
                                            @if($canEdit)
                                            <button type="button" class="btn btn-sm btn-light" onclick="addSubService(this)">
                                                <i class="ki-filled ki-plus text-blue-500 mr-1"></i>
                                                Thêm dịch vụ con
                                            </button>
                                            @endif
                                        </div>
                                        <div class="sub-services-container border-gray-100">
                                            @foreach($service['sub_services'] ?? [] as $subServiceIndex => $subService)
                                            <div class="sub-service-item mb-2 rounded p-2 border-l-2 border-gray-400" data-sub-service-id="sub_service_{{ $itemIndex }}_{{ $serviceIndex }}_{{ $subServiceIndex }}">
                                                <div class="grid grid-cols-1 lg:!grid-cols-12 gap-2">
                                                    <div class="lg:!col-span-6">
                                                        <label class="block text-gray-700 text-sm mb-1">Góc máy</label>
                                                        <input name="sub_service_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Góc máy" value="{{ $subService['name'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                        <!-- Add contract_sub_service_id field -->
                                                        <input type="hidden" name="contract_sub_service_id[]" value="{{ $subService['id'] ?? '' }}">
                                                    </div>
                                                    <div class="lg:!col-span-2">
                                                        <label class="block text-gray-700 text-sm mb-1">Số lượng</label>
                                                        <input name="sub_service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full sub-service-quantity" type="text" placeholder="Số lượng" oninput="calculateSubServiceTotal(this)" value="{{ $subService['quantity'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                    </div>
                                                    <div class="lg:!col-span-4">
                                                        <label class="block text-gray-700 text-sm mb-1">Thành tiền</label>
                                                        <div class="relative">
                                                            <input name="sub_service_total[]" class="input border-gray-200 focus:border-blue-500 rounded-lg !pl-6 w-full sub-service-total" type="text" placeholder="Thành tiền" readonly value="{{$subService['total'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₫</span>
                                                        </div>
                                                    </div>
                                                    <div class="lg:!col-span-6">
                                                        <label class="block text-gray-700 text-sm mb-1">Nội dung</label>
                                                        <textarea name="sub_service_content[]" class="textarea border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nội dung" {{ !$canEdit ? 'disabled' : '' }}>{{ $subService['content'] }}</textarea>
                                                    </div>
                                                </div>
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-2">
                                                <div>
                                                    <label class="block text-gray-700 text-sm mb-1">Hình ảnh mẫu</label>
                                                    <div class="flex items-center gap-1">
                                                    <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-sample')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh mẫu
                                </button>
                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden relative group">
                                                            
                                                            <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $subService['sample_image_id'] }}" alt="">
                                                        </div>
                                                        <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'sub_service_sample')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="sub_service_sample_image[]" class="sub-service-sample-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="sub_service_sample_image_id[]" value="{{ $subService['sample_image_id'] }}">
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-gray-700 text-sm mb-1">Hình ảnh kết quả</label>
                                                    <div class="flex items-center gap-1">
                                                    <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-result')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh kết quả
                                </button>
                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden relative group">
                                                            
                                                            <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $subService['result_image_id'] }}" alt="">
                                                        </div>
                                                        <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'sub_service_result')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="sub_service_result_image[]" class="sub-service-result-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="sub_service_result_image_id[]" value="{{ $subService['result_image_id'] }}">
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @else
                                    {{-- Standard Service --}}
                                    <div class="service-item mb-3 rounded-lg border border-gray-400 p-3" data-service-id="service_{{ $itemIndex }}_{{ $serviceIndex }}">
                                        <div class="flex justify-between items-start gap-2 mb-3">
                                            <div class="flex items-center gap-2 text-green-500">
                                                <i class="ki-filled ki-abstract-26"></i>
                                                <h5 class="font-medium text-gray-700">Dịch vụ</h5>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                @if($canEdit)
                                                <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeService(this)">
                                                    <i class="ki-filled ki-trash !text-red-500"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Tên dịch vụ</label>
                                                <select name="service_ids[]" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full service-select" {{ !$canEdit ? 'disabled' : '' }}>
                                                    <option disabled>Chọn dịch vụ</option>
                                                    @foreach($data_init['services'] ?? [] as $serviceOption)
                                                    <option value="{{ $serviceOption['id'] }}" {{ $service['service_id'] == $serviceOption['id'] ? 'selected' : '' }}>
                                                        {{ $serviceOption['name'] }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <!-- Add contract_service_id field -->
                                                <input type="hidden" name="contract_service_id[]" value="{{ $service['id'] ?? '' }}">
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Đơn giá</label>
                                                <div class="relative">
                                                    <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg !pl-8 w-full service-price" type="text" placeholder="Đơn giá" oninput="updateSubServicePrices(this)" value="{{ $service['price'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Ghi chú</label>
                                                <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú" value="{{ $service['note'] ?? '' }}" {{ !$canEdit ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Hình ảnh mẫu</label>
                                                <div class="flex items-center gap-2">
                                                <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'service-sample')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh mẫu
                                </button>
                                                    <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden relative group">
                                                        
                                                        <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $service['sample_image_id'] }}" alt="">
                                                    </div>
                                                    <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'service_sample')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="service_sample_image[]" class="service-sample-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="service_sample_image_id[]" value="{{ $service['sample_image_id'] }}">
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-gray-700 text-sm mb-1">Hình ảnh kết quả</label>
                                                <div class="flex items-center gap-2">
                                                <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'service-result')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh kết quả
                                </button>
                                                    <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden relative group">
                                                        
                                                        <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $service['result_image_id'] }}" alt="">
                                                    </div>
                                                    <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'service_result')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="service_result_image[]" class="service-result-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="service_result_image_id[]" value="{{ $service['result_image_id'] }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 mb-3">
                                            @if($canEdit)
                                            <button type="button" class="btn btn-sm btn-light" onclick="addSubService(this)">
                                                <i class="ki-filled ki-plus text-blue-500 mr-1"></i>
                                                Thêm dịch vụ con
                                            </button>
                                            @endif
                                        </div>
                                        <div class="sub-services-container border-gray-100">
                                            @foreach($service['sub_services'] ?? [] as $subServiceIndex => $subService)
                                            <div class="sub-service-item mb-2 rounded p-2 border-l-2 border-gray-400" data-sub-service-id="sub_service_{{ $itemIndex }}_{{ $serviceIndex }}_{{ $subServiceIndex }}">
                                                <div class="grid grid-cols-1 lg:!grid-cols-12 gap-2">
                                                    <div class="lg:!col-span-6">
                                                        <label class="block text-gray-700 text-sm mb-1">Góc máy</label>
                                                        <input name="sub_service_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Góc máy" value="{{ $subService['name'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                        <!-- Add contract_sub_service_id field -->
                                                        <input type="hidden" name="contract_sub_service_id[]" value="{{ $subService['id'] ?? '' }}">
                                                    </div>
                                                    <div class="lg:!col-span-2">
                                                        <label class="block text-gray-700 text-sm mb-1">Số lượng</label>
                                                        <input name="sub_service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full sub-service-quantity" type="text" placeholder="Số lượng" oninput="calculateSubServiceTotal(this)" value="{{ $subService['quantity'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                    </div>
                                                    <div class="lg:!col-span-4">
                                                        <label class="block text-gray-700 text-sm mb-1">Thành tiền</label>
                                                        <div class="relative">
                                                            <input name="sub_service_total[]" class="input border-gray-200 focus:border-blue-500 rounded-lg !pl-6 w-full sub-service-total" type="text" placeholder="Thành tiền" readonly value="{{$subService['total'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₫</span>
                                                        </div>
                                                    </div>
                                                    <div class="lg:!col-span-6">
                                                        <label class="block text-gray-700 text-sm mb-1">Nội dung</label>
                                                        <textarea name="sub_service_content[]" class="textarea border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Nội dung" {{ !$canEdit ? 'disabled' : '' }}>{{ $subService['content'] }}</textarea>
                                                    </div>
                                                </div>
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-2">
                                                <div>
                                                    <label class="block text-gray-700 text-sm mb-1">Hình ảnh mẫu</label>
                                                    <div class="flex items-center gap-1">
                                                    <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-sample')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh mẫu
                                </button>
                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden relative group">
                                                            
                                                            <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $subService['sample_image_id'] }}" alt="">
                                                        </div>
                                                        <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'sub_service_sample')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="sub_service_sample_image[]" class="sub-service-sample-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="sub_service_sample_image_id[]" value="{{ $subService['sample_image_id'] }}">
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-gray-700 text-sm mb-1">Hình ảnh kết quả</label>
                                                    <div class="flex items-center gap-1">
                                                    <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-result')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh kết quả
                                </button>
                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden relative group">
                                                            
                                                            <img onerror="this.src='/assets/images/default.svg'" class="h-full w-full object-cover cursor-pointer" src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/{{ $subService['result_image_id'] }}" alt="" >
                                                        </div>
                                                        <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'sub_service_result')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="sub_service_result_image[]" class="sub-service-result-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="sub_service_result_image_id[]" value="{{ $subService['result_image_id'] }}">
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @elseif($item['type'] == 'custom')
                        {{-- Custom Item (like discount, fees, etc.) --}}
                        <div class="item-container border-b border-gray-100 p-3" data-item-id="{{ $itemIndex }}" data-item-type="custom">
                            <div class="flex justify-between items-start gap-2 mb-3">
                                <div class="flex items-center gap-2 text-indigo-500">
                                    <i class="ki-filled ki-note-2"></i>
                                    <h4 class="font-medium text-gray-800">Mục khác</h4>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if($canEdit)
                                    <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100" onclick="removeItem(this)">
                                        <i class="ki-filled ki-trash !text-red-500"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-gray-700 text-sm mb-1">Tên mục</label>
                                    <input name="item_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ví dụ: Giảm giá, Phí vận chuyển..." value="{{ $item['name'] ?? '' }}" {{ !$canEdit ? 'disabled' : '' }}>
                                    <input type="hidden" name="item_type[]" value="custom">
                                    <!-- Add contract_item_id field -->
                                    <input type="hidden" name="contract_item_id[]" value="{{ $item['id'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm mb-1">Giá trị</label>
                                    <div class="relative">
                                        <input name="item_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8 w-full" type="text" placeholder="Giá trị" oninput="calculateTotalValue()" value="{{ $item['price'] }}" {{ !$canEdit ? 'disabled' : '' }}>
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm mb-1">Ghi chú</label>
                                    <input name="item_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Ghi chú" value="{{ $item['note'] ?? '' }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
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
                                    <input type="hidden" name="contract_item_id[]" value="">
                                </div>
                                <!-- Đã loại bỏ phần upload ảnh sản phẩm -->
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
                            <input type="hidden" name="contract_item_id[]" value="">
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

            <!-- Thay đổi trong template dịch vụ -->
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
                                __SERVICES_OPTIONS__
                            </select>
                            <input type="hidden" name="contract_service_id[]" value="">
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
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-gray-700 text-sm mb-1">Hình ảnh mẫu</label>
                            <div class="flex items-center gap-2">
                                <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'service-sample')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh mẫu
                                </button>
                                <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden hidden relative group">
                                    <div class="image-loading-spinner h-full w-full bg-gray-100 flex items-center justify-center hidden">
                                        <i class="ki-filled ki-loading animate-spin text-blue-500"></i>
                                    </div>
                                    
                                    <img onerror="this.src='/assets/images/default.svg'" class="service-sample-image-preview h-full w-full object-cover cursor-pointer" src="" alt="">
                                </div>
                                <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'service_sample')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="service_sample_image[]" class="service-sample-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="service_sample_image_id[]" value="">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm mb-1">Hình ảnh kết quả</label>
                            <div class="flex items-center gap-2">
                                <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'service-result')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh kết quả
                                </button>
                                <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden hidden relative group">
                                    <div class="image-loading-spinner h-full w-full bg-gray-100 flex items-center justify-center hidden">
                                        <i class="ki-filled ki-loading animate-spin text-blue-500"></i>
                                    </div>
                                    
                                    <img onerror="this.src='/assets/images/default.svg'" class="service-result-image-preview h-full w-full object-cover cursor-pointer" src="" alt="">
                                </div>
                                        <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'service_result')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="service_result_image[]" class="service-result-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="service_result_image_id[]" value="">
                            </div>
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
                            <!-- Add contract_service_id field -->
                            <input type="hidden" name="contract_service_id[]" value="">
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

            <!-- Thay đổi trong template dịch vụ con -->
            <template id="sub-service-template">
                <div class="sub-service-item mb-2 rounded p-2 border-l-2 border-gray-400" data-sub-service-id="__SUB_SERVICE_ID__">
                    <div class="grid grid-cols-1 lg:!grid-cols-12 gap-2">
                        <div class="lg:!col-span-6">
                            <label class="block text-gray-700 text-sm mb-1">Góc máy</label>
                            <input name="sub_service_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Góc máy">
                            <input type="hidden" name="contract_sub_service_id[]" value="">
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
                    </div>
                    
                    <!-- Thay đổi phần upload hình ảnh thành hai loại ảnh: mẫu và kết quả -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-2">
                        <div>
                            <label class="block text-gray-700 text-sm mb-1">Hình ảnh mẫu</label>
                            <div class="flex items-center gap-1">
                                <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-sample')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh mẫu
                                </button>
                                <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden hidden relative group">
                                    <div class="image-loading-spinner h-full w-full bg-gray-100 flex items-center justify-center hidden">
                                        <i class="ki-filled ki-loading animate-spin text-blue-500"></i>
                                    </div>
                                    
                                    <img onerror="this.src='/assets/images/default.svg'" class="sub-service-sample-image-preview h-full w-full object-cover cursor-pointer" src="" alt="">
                                </div>
                                        <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'sub_service_sample')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="sub_service_sample_image[]" class="sub-service-sample-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="sub_service_sample_image_id[]" value="">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm mb-1">Hình ảnh kết quả</label>
                            <div class="flex items-center gap-1">
                                <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub-service-result')">
                                    <i class="ki-filled ki-file-up"></i>
                                    Tải ảnh kết quả
                                </button>
                                <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden hidden relative group">
                                    <div class="image-loading-spinner h-full w-full bg-gray-100 flex items-center justify-center hidden">
                                        <i class="ki-filled ki-loading animate-spin text-blue-500"></i>
                                    </div>
                                    
                                    <img onerror="this.src='/assets/images/default.svg'" class="sub-service-result-image-preview h-full w-full object-cover cursor-pointer" src="" alt="">
                                </div>
                                        <button type="button" class="p-1 text-red-500" title="Xóa ảnh" onclick="removeImage(this, 'sub_service_result')">
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                <input type="file" name="sub_service_result_image[]" class="sub-service-result-image-input hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="sub_service_result_image_id[]" value="">
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
</form>
@push('scripts')
<script>
    // resources/js/dashboard/contracts/service-items.js
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
        const fileInput = container.querySelector(`.${type}-image-input`);
        
        if (fileInput) {
            fileInput.click();
        }
    }


    // Xóa hình ảnh
    function removeImage(button, type) {
        const container = button.closest('div').querySelector('.preview-image-container').parentNode;
        const previewContainer = container.querySelector('.preview-image-container');
        const imageIdInput = container.querySelector(`input[name="${type}_image_id[]"]`);

        // Ẩn container ảnh preview
        previewContainer.classList.add('hidden');

        // Reset ảnh preview
        const previewImg = previewContainer.querySelector('img');
        previewImg.src = '';

        // Reset link xem ảnh
        const imageLink = previewContainer.querySelector('a');
        if (imageLink) {
            imageLink.href = '#';
        }

        // Reset giá trị image_id
        if (imageIdInput) {
            imageIdInput.value = '';
        }

        // Reset input file
        const fileInput = container.querySelector(`input[name="${type}_image[]"]`);
        if (fileInput) {
            fileInput.value = '';
        }
    }

    // Hiển thị preview hình ảnh và upload lên Google Drive
    async function previewImage(input) {
        if (input.files && input.files[0]) {
            const container = input.closest('div');
            const previewContainer = container.querySelector('.preview-image-container');
            const previewImg = previewContainer.querySelector('img');
            const imageLink = previewContainer.querySelector('a');
            const imageIdInputName = input.name.replace('image', 'image_id');
            const imageIdInput = container.querySelector(`input[name="${imageIdInputName}"]`);

            try {
                // Hiển thị container ảnh
                previewContainer.classList.remove('hidden');

                previewImg.classList.add('hidden');

                // Tạo preview tạm thời
                const reader = new FileReader();
                reader.readAsDataURL(input.files[0]);

                // Upload file
                let file = input.files[0];
                let res = await uploadFileTemplate(file);

                if (res.data.status == 200) {
                    const imageUrl = `https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/q_auto,f_auto/uploads/${res.data.data.driver_id}`;
                    const thumbnailUrl = `https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_56,h_56,q_auto,f_auto/uploads/${res.data.data.driver_id}`;

                    // Hiển thị ảnh và thay đổi source của ảnh preview thành ảnh từ Google Drive
                    previewImg.src = thumbnailUrl;
                    previewImg.classList.remove('hidden');

                    // Cập nhật link xem ảnh
                    if (imageLink) {
                        imageLink.href = imageUrl;
                    }

                    // Cập nhật giá trị image_id
                    if (imageIdInput) {
                        imageIdInput.value = res.data.data.driver_id;
                    }
                    
                } else {
                    previewContainer.classList.add('hidden');
                    showAlert('error', 'Upload failed!');
                }
            } catch (error) {
                // Ẩn loading spinner và preview container nếu có lỗi
                previewContainer.classList.add('hidden');
                showAlert('error', 'Error uploading file');
                console.error(error);
            }
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
        totalInput.value = total;

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

        $('.services-total-value').text(formatNumberLikePhp(amount));
        $('.discount-value').text(formatNumberLikePhp(discount));
        $('.contract-total-value').text(formatNumberLikePhp(total)).val(formatNumberLikePhp(total));
        $('input[name="total_value"]').val(total);
    }

    
    async function saveCreateContract($id) {
        Notiflix.Confirm.show(
            'Bạn có chắc chắn muốn cập nhật dịch vụ',
            'Hành động này sẽ ảnh hướng đến công việc nếu bạn đã tạo chúng. Hãy cân nhắc kỹ lưỡng',
            'Đúng',
            'Hủy',
            async () => {
                calculateTotalValue();

                // Tạo cấu trúc dữ liệu cho sản phẩm, dịch vụ và dịch vụ con
                let contractItemsData = [];

                // Lặp qua tất cả các item (sản phẩm và mục khác)
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
                        itemData.quantity = itemContainer.querySelector('input[name="item_quantity[]"]')?.value;
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
                        const contractServiceId = serviceItem.querySelector('input[name="contract_service_id[]"]')?.value || null;

                        let serviceData = {
                            service_id: isCustomService ? 'custom' : serviceItem.querySelector('select[name="service_ids[]"]')?.value,
                            id: contractServiceId,
                            custom_name: isCustomService ? serviceItem.querySelector('input[name="service_custom_name[]"]')?.value : null,
                            price: serviceItem.querySelector('input[name="service_price[]"]')?.value,
                            note: serviceItem.querySelector('input[name="service_note[]"]')?.value,
                            // Thêm các trường hình ảnh mẫu và kết quả
                            sample_image_id: serviceItem.querySelector('input[name="service_sample_image_id[]"]')?.value || null,
                            result_image_id: serviceItem.querySelector('input[name="service_result_image_id[]"]')?.value || null,
                            sub_services: []
                        };

                        // Thu thập dữ liệu dịch vụ con
                        const subServiceItems = serviceItem.querySelectorAll('.sub-service-item');
                        subServiceItems.forEach(subServiceItem => {
                            const contractSubServiceId = subServiceItem.querySelector('input[name="contract_sub_service_id[]"]')?.value || null;

                            const subServiceData = {
                                id: contractSubServiceId,
                                name: subServiceItem.querySelector('input[name="sub_service_name[]"]')?.value,
                                quantity: subServiceItem.querySelector('input[name="sub_service_quantity[]"]')?.value,
                                total: subServiceItem.querySelector('input[name="sub_service_total[]"]')?.value,
                                content: subServiceItem.querySelector('textarea[name="sub_service_content[]"]')?.value,
                                // Thêm các trường hình ảnh mẫu và kết quả
                                sample_image_id: subServiceItem.querySelector('input[name="sub_service_sample_image_id[]"]')?.value || null,
                                result_image_id: subServiceItem.querySelector('input[name="sub_service_result_image_id[]"]')?.value || null
                            };

                            serviceData.sub_services.push(subServiceData);
                        });

                        itemData.services.push(serviceData);
                    });

                    contractItemsData.push(itemData);
                });

                // Tạo FormData để gửi dữ liệu form
                const formData = new FormData(document.getElementById('tab-services'));

                // Thêm dữ liệu cấu trúc JSON
                formData.append('contract_items_data', JSON.stringify(contractItemsData));
                formData.append('contract_id', $id);

                let method = "post",
                    url = "/contract/update",
                    params = null,
                    data = formData;

                try {
                    // Sử dụng Axios với Content-Type là multipart/form-data
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
                            }, 500);
                            break;
                        default:
                            showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                            break;
                    }
                } catch (error) {
                    showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                    console.error(error);
                }
            },
            () => {}, {}
        );
    }

    // Khởi tạo với một sản phẩm mặc định
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotalValue();
    });
</script>
@endpush