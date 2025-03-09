{{-- resources/views/dashboard/contracts/create.blade.php --}}
@extends('dashboard.layouts.layout')

@section('dashboard_content')
@php
use Carbon\Carbon;
$now = Carbon::now();
$canEdit = ($details['status'] == 0);
@endphp
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5">
                    <h1 class="font-semibold text-base text-gray-900">
                        Thông tin hợp đồng #{{ $details['contract_number'] }}
                    </h1>
                    @switch($details['status'])
                    @case(1)
                    <span class="badge badge-sm badge-outline badge-primary">
                        Đang triển khai
                    </span>
                    @break
                    @case(2)
                    <span class="badge badge-sm badge-outline badge-success">
                        Đã hoàn thành
                    </span>
                    @break
                    @case(3)
                    <span class="badge badge-sm badge-outline badge-danger">
                        Đã huỷ
                    </span>
                    @break
                    @default
                    <span class="badge badge-sm badge-outline badge-warning">
                        Đang chờ
                    </span>
                    @endswitch
                </div>
                <div class="form-info text-gray-800 font-normal">
                    Được thêm bởi <b>{{$details['creator']['name']}}</b> khoảng <b>{{timeAgo(strtotime($details['created_at']))}}</b> trước.
                    @if ($details['updated_at'] != $details['created_at'])
                    Đã cập nhật <b>{{timeAgo(strtotime($details['updated_at']))}}</b> trước.
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
            @push("actions")
            <button type="button" class="btn btn-primary px-5 py-2 flex items-center gap-2" onclick="saveCreateTaskContract({{$details['id']}})">
                <i class="ki-filled ki-check text-white"></i>
                <span>Tạo công việc</span>
            </button>
            @endpush
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="card shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="card-header bg-white border-b border-gray-100">
            <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                <i class="ki-filled ki-dollar text-green-500"></i>
                Tổng hợp giá trị hợp đồng
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:!grid-cols-5 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Tổng giá trị</div>
                    <div class="text-lg font-medium text-gray-800">{{ number_format($details['payment_summary']['total_value']) }} ₫</div>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="text-sm text-green-600 mb-1">Đã thanh toán</div>
                    <div class="text-lg font-medium text-green-600">{{ number_format($details['payment_summary']['total_paid']) }} ₫</div>
                </div>
                <div class="p-4 bg-orange-50 rounded-lg">
                    <div class="text-sm text-orange-600 mb-1">Còn phải thanh toán</div>
                    <div class="text-lg font-medium text-orange-600">{{ number_format($details['payment_summary']['total_remaining']) }} ₫</div>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <div class="text-sm text-red-600 mb-1">Đã hoàn tiền</div>
                    <div class="text-lg font-medium text-red-600">{{ number_format($details['payment_summary']['total_deduction']) }} ₫</div>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="text-sm text-blue-600 mb-1">Thanh toán thừa</div>
                    <div class="text-lg font-medium text-blue-800">{{ number_format($details['payment_summary']['total_excess']) }} ₫</div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid gap-5">
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
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Tên công ty:</span>
                                    <span class="checkbox-label text-gray-800">{{NAME_COMPANY}}</span>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Mã số thuế:</span>
                                    <span class="checkbox-label text-gray-800"></span>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Địa chỉ:</span>
                                    <span class="checkbox-label text-gray-800"></span>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Nhân viên phụ trách:</span>
                                    <a class="checkbox-label text-gray-800 hover:text-primary" href="/member/{{$details['user']['id']}}">{{$details['user']['name']}}</a>

                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="user_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
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
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Khách hàng:</span>
                                    <a class="checkbox-label text-gray-800 hover:text-primary" href="/customer/{{$details['provider']['id']}}">{{$details['provider']['name']}}</a>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="provider_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Người đại diện:</span>
                                    <span class="checkbox-label text-gray-800">{{$details['customer_representative']}}</span>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="customer_representative">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Mã số thuế:</span>
                                    <span class="checkbox-label text-gray-800">{{$details['customer_tax_code']}}</span>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="customer_tax_code">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Địa chỉ:</span>
                                    <span class="checkbox-label text-gray-800">{{$details['address']}}</span>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="address">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Số điện thoại:</span>
                                    <span class="checkbox-label text-gray-800">{{$details['phone']}}</span>
                                    @if ($canEdit)
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="phone">
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                    @endif
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
                            <div class="grid gap-4">
                                <div class="flex flex-wrap gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">Số hợp đồng:</span>
                                        <span class="checkbox-label text-gray-800">{{$details['contract_number']}}</span>
                                    </div>
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">Tên hợp đồng:</span>
                                        <span class="checkbox-label text-gray-800">{{$details['name']}}</span>
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="name">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">Loại hình dịch vụ:</span>
                                    <span class="checkbox-label text-gray-800">Chụp ảnh sản phẩm</span>
                                </div>

                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">Ngày ký:</span>
                                        <span class="checkbox-label text-gray-800">{{formatDateTime($details['sign_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="sign_date">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">Ngày hiệu lực:</span>
                                        <span class="checkbox-label text-gray-800">{{formatDateTime($details['effective_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="effective_date">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">Ngày hết hạn:</span>
                                        <span class="checkbox-label text-gray-800">{{formatDateTime($details['expiry_date'], 'd-m-Y', 'Y-m-d')}}</span>
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="expiry_date">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">Ghi chú</span>
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="note">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                        <div class="ql-editor" style="white-space: normal;">
                                            {!! nl2br(e($details['note'] ?? '---')) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">Điều khoản chung</span>
                                        @if ($canEdit)
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="terms_and_conditions">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                        <div class="ql-editor" style="white-space: normal;">
                                            {!! nl2br(e($details['terms_and_conditions'] ?? '---')) !!}
                                        </div>
                                    </div>
                                </div>
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
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 text-sm mb-1">Hình ảnh</label>
                                                    <div class="flex items-center gap-2">
                                                        @if($canEdit)
                                                        <button type="button" class="btn btn-light btn-sm image-upload-btn" onclick="triggerImageUpload(this, 'item')">
                                                            <i class="ki-filled ki-file-up"></i>
                                                            Tải ảnh
                                                        </button>
                                                        @endif
                                                        @if(isset($item['image_url']) && $item['image_url'])
                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden">
                                                            <img class="item-image-preview h-full w-full object-cover" src="{{ $item['image_url'] }}" alt="">
                                                        </div>
                                                        @else
                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded-lg overflow-hidden hidden">
                                                            <img class="item-image-preview h-full w-full object-cover" src="" alt="">
                                                        </div>
                                                        @endif
                                                        <input type="file" name="item_image[]" class="item-image-input hidden" accept="image/*" onchange="previewImage(this)" {{ !$canEdit ? 'disabled' : '' }}>
                                                    </div>
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
                                                                <option value="{{ $serviceOption['id'] }}" {{ $service['id'] == $serviceOption['id'] ? 'selected' : '' }}>
                                                                    {{ $serviceOption['name'] }}
                                                                </option>
                                                                @endforeach
                                                            </select>
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
                                                    @endif
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
                                                                <div class="lg:!col-span-6">
                                                                    <label class="block text-gray-700 text-sm mb-1">Hình ảnh</label>
                                                                    <div class="flex items-center gap-1">
                                                                        @if($canEdit)
                                                                        <button type="button" class="btn btn-light btn-sm p-1 h-8" onclick="triggerImageUpload(this, 'sub_service')">
                                                                            <i class="ki-filled ki-file-up"></i>
                                                                            Tải ảnh
                                                                        </button>
                                                                        @endif
                                                                        @if(isset($subService['image_url']) && $subService['image_url'])
                                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden">
                                                                            <img class="sub-service-image-preview h-full w-full object-cover" src="{{ $subService['image_url'] }}" alt="">
                                                                        </div>
                                                                        @else
                                                                        <div class="preview-image-container h-8 w-8 border border-gray-200 rounded overflow-hidden hidden">
                                                                            <img class="sub-service-image-preview h-full w-full object-cover" src="" alt="">
                                                                        </div>
                                                                        @endif
                                                                        <input type="file" name="sub_service_image[]" class="sub-service-image-input hidden" accept="image/*" onchange="previewImage(this)" {{ !$canEdit ? 'disabled' : '' }}>
                                                                        @if($canEdit)
                                                                        <button type="button" class="btn btn-sm btn-icon btn-light hover:!bg-red-100 h-8 w-8" onclick="removeSubService(this)">
                                                                            <i class="ki-filled ki-trash !text-red-500"></i>
                                                                        </button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
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
                        <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                            <div class="card-header bg-white border-b border-gray-100">
                                <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                                    <i class="ki-filled ki-credit-cart text-green-500"></i>
                                    Biên nhận thanh toán
                                </h3>
                                <button class="btn btn-light btn-xs" data-modal-toggle="#add-payment-modal">
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
                                                <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Loại phiếu</th>
                                                <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Giá tiền</th>
                                                <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Phương thức</th>
                                                <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Ngày</th>
                                                <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Trạng thái</th>
                                                <th class="min-w-16 !px-1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details['payments'] as $payment)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="!px-1 min-w-20">
                                                    <span class="!text-xs text-gray-800">{{ $payment['name'] }}</span>
                                                </td>
                                                <td class="!px-1 min-w-20">
                                                    <span class="!text-xs text-gray-800">{{ $payment['payment_stage_text'] }}</span>
                                                </td>
                                                <td class="!px-1 min-w-20">
                                                    <span class="!text-xs text-gray-800">{{number_format($payment['price'], 0, ',', '.')}} {{ $payment['currency']['code'] }}</span>
                                                </td>
                                                <td class="!px-1 min-w-20">
                                                    <span class="!text-xs text-gray-800">{{ $payment['method']['name'] }}</span>
                                                </td>
                                                <td class="!px-1 min-w-20">
                                                    <span class="!text-xs text-gray-800">{{ $payment['due_date_formatted'] }}</span>
                                                </td>
                                                <td class="!px-1 min-w-20">
                                                    @if($payment['status'] == 1)
                                                    <span class="badge badge-sm badge-outline badge-success">{{ $payment['status_text'] }}</span>
                                                    @else
                                                    <span class="badge badge-sm badge-outline badge-warning">{{ $payment['status_text'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="!px-1 ">
                                                    <div class="menu" data-menu="true">
                                                        @if ($payment['status'] == 0)
                                                        <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                                <i class="ki-filled ki-dots-vertical"></i>
                                                            </button>
                                                            <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                                <div class="menu-item">
                                                                    <button class="menu-link" data-modal-toggle="#edit-payment-modal" onclick="loadPaymentData({{$payment['id']}})">
                                                                        <span class="menu-icon">
                                                                            <i class="ki-filled ki-pencil"></i>
                                                                        </span>
                                                                        <span class="menu-title">Chỉnh sửa</span>
                                                                    </button>
                                                                </div>
                                                                <div class="menu-item">
                                                                    <button class="menu-link" onclick="cancelPayment({{$payment['id']}})">
                                                                        <span class="menu-icon">
                                                                            <i class="ki-filled ki-trash"></i>
                                                                        </span>
                                                                        <span class="menu-title">Hủy</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal cập nhật thông tin hợp đồng -->
    <div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-contract-modal" style="z-index: 90;">
        <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
            <div class="modal-header pr-2.5">
                <h3 class="modal-title">Cập nhật thông tin</h3>
                <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>
            <div class="modal-body">
                <form class="grid gap-5 px-0 py-5">
                    <div class="flex flex-col gap-2.5">
                        <input class="input hidden" name="name" type="text" placeholder="Tên hợp đồng mới">
                        <select name="status" class="select hidden">
                            <option value="" selected>Chọn trạng thái</option>
                            <option value="0">Chờ duyệt</option>
                            <option value="1">Đang triển khai</option>
                            <option value="2">Đã hoàn thành</option>
                        </select>
                        <select name="user_id" class="select hidden">
                            <option value="" selected>Chọn nhân viên</option>
                            @foreach ($data_init['users'] as $user)
                            <option value="{{$user['id']}}">{{$user['name']}}</option>
                            @endforeach
                        </select>
                        <select name="provider_id" class="select hidden">
                            <option value="" selected>Chọn khách hàng</option>
                            @foreach ($data_init['customers'] as $customer)
                            <option value="{{$customer['id']}}">{{$customer['name']}}</option>
                            @endforeach
                        </select>
                        <input class="input hidden" name="sign_date" type="text" placeholder="Ngày ký (DD/MM/YYYY)">
                        <input class="input hidden" name="effective_date" type="text" placeholder="Ngày hiệu lực (DD/MM/YYYY)">
                        <input class="input hidden" name="expiry_date" type="text" placeholder="Ngày hết hạn (DD/MM/YYYY)">
                        <textarea class="textarea hidden" name="note" rows="5" placeholder="Ghi chú"></textarea>
                        <textarea class="textarea hidden" name="terms_and_conditions" rows="5" placeholder="Điều khoản chung"></textarea>
                    </div>
                    <div class="flex flex-col">
                        <button type="submit" class="btn btn-primary justify-center">Xong</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-payment-modal" style="z-index: 90;">
        <div class="modal-content max-w-[600px] top-5 lg:top-[15%]">
            <div class="modal-header pr-2.5">
                <h3 class="modal-title">Thêm biên nhận mới</h3>
                <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-payment-form" class="grid gap-5 px-0 py-5">
                    <div class="flex flex-col gap-2.5">
                        <input type="hidden" name="contract_id" value="{{$details['id']}}">

                        <span class="checkbox-label text-gray-800 !font-bold">Tên biên nhận</span>
                        <div class="checkbox-group">
                            <input name="name" class="input" type="text" placeholder="Tên biên nhận">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Phần trăm tổng giá trị (%)</span>
                                <div class="checkbox-group">
                                    <div class="relative w-full">
                                        <input name="percentage" class="input pl-8" type="number" min="0" max="100" step="0.1" placeholder="% giá trị" oninput="calculatePaymentAmount()">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Số tiền</span>
                                <div class="checkbox-group">
                                    <div class="relative w-full">
                                        <input name="price" class="input pl-8" type="number" placeholder="Giá tiền">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Tiền tệ</span>
                                <div class="checkbox-group">
                                    <select name="currency_id" class="select">
                                        <option value="" selected>Chọn tiền tệ</option>
                                        @foreach ($data_init['currencies'] as $currency)
                                        <option value="{{$currency['id']}}">{{$currency['currency_code']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Phương thức</span>
                                <div class="checkbox-group">
                                    <select name="method_id" class="select">
                                        <option value="" selected>Chọn phương thức</option>
                                        @foreach ($data_init['payment_methods'] as $method)
                                        <option value="{{$method['id']}}">{{$method['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Ngày</span>
                                <div class="checkbox-group">
                                    <div class="relative w-full">
                                        <input name="due_date" class="input pl-8" type="text" placeholder="DD/MM/YYYY">
                                        <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Loại phiếu</span>
                                <div class="checkbox-group">
                                    <select name="payment_stage" class="select">
                                        <option value="0">Biên nhận cọc</option>
                                        <option value="1">Tiền thưởng thêm</option>
                                        <option value="2">Biên nhận cuối</option>
                                        <option value="3">Tiền khấu trừ</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <span class="checkbox-label text-gray-800 !font-bold mt-2">Trạng thái</span>
                        <div class="checkbox-group">
                            <label class="checkbox flex items-center gap-2">
                                <input name="status" type="checkbox" value="1">
                                <span class="checkbox-label">Đã thanh toán</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <button type="submit" class="btn btn-primary justify-center">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-payment-modal" style="z-index: 90;">
        <div class="modal-content max-w-[600px] top-5 lg:top-[15%]">
            <div class="modal-header pr-2.5">
                <h3 class="modal-title">Chỉnh sửa biên nhận</h3>
                <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-payment-form" class="grid gap-5 px-0 py-5">
                    <div class="flex flex-col gap-2.5">
                        <input type="hidden" name="id" id="edit-payment-id">

                        <span class="checkbox-label text-gray-800 !font-bold">Tên biên nhận</span>
                        <div class="checkbox-group">
                            <input name="name" class="input" type="text" placeholder="Tên biên nhận">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Phần trăm tổng giá trị (%)</span>
                                <div class="checkbox-group">
                                    <div class="relative w-full">
                                        <input name="percentage" class="input pl-8" type="number" min="0" max="100" step="0.1" placeholder="% giá trị" oninput="calculatePaymentAmount()">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Số tiền</span>
                                <div class="checkbox-group">
                                    <div class="relative w-full">
                                        <input name="price" class="input pl-8" type="number" placeholder="Giá tiền">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Tiền tệ</span>
                                <div class="checkbox-group">
                                    <select name="currency_id" class="select">
                                        <option value="" selected>Chọn tiền tệ</option>
                                        @foreach ($data_init['currencies'] as $currency)
                                        <option value="{{$currency['id']}}">{{$currency['currency_code']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Phương thức</span>
                                <div class="checkbox-group">
                                    <select name="method_id" class="select">
                                        <option value="" selected>Chọn phương thức</option>
                                        @foreach ($data_init['payment_methods'] as $method)
                                        <option value="{{$method['id']}}">{{$method['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Ngày</span>
                                <div class="checkbox-group">
                                    <div class="relative w-full">
                                        <input name="due_date" class="input pl-8" type="text" placeholder="DD/MM/YYYY">
                                        <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <span class="checkbox-label text-gray-800 !font-bold mt-2">Trạng thái</span>
                                <div class="checkbox-group">
                                    <label class="checkbox flex items-center gap-2">
                                        <input name="status" type="checkbox" value="1">
                                        <span class="checkbox-label">Đã thanh toán</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <button type="submit" class="btn btn-primary justify-center">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @push('scripts')
    <script>
        const details = @json($data_init);

        $(function() {
            $('button[data-modal-toggle="#update-contract-modal"][data-name]').on('click', function() {
                let _this = $(this);
                let _modal = $('#update-contract-modal');
                _modal.find('input[name], select[name], textarea[name]').val('').addClass('hidden');
                _modal.find(`input[name=${_this.attr('data-name')}], select[name=${_this.attr('data-name')}], textarea[name=${_this.attr('data-name')}]`).removeClass('hidden');
            });

            $('#update-contract-modal form').on('submit', function(e) {
                e.preventDefault();
                postUpdateContract();
            });

            flatpickrMake($("input[name=sign_date], input[name=effective_date], input[name=expiry_date]"), 'date');
        });

        async function postUpdateContract() {
            let field = $('#update-contract-modal form').find('input:not(.hidden),select:not(.hidden),textarea:not(.hidden)');
            let method = "post",
                url = "/contract/update",
                params = null,
                data = {
                    id: "{{$details['id']}}",
                    [field.attr('name')]: field.val()
                };
            let res = await axiosTemplate(method, url, params, data);
            switch (res.data.status) {
                case 200:
                    showAlert('success', res.data.message);
                    window.location.reload();
                    break;
                default:
                    showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                    break;
            }
        }
    </script>
    <script>
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

            $('.services-total-value').text(new Intl.NumberFormat('vi-VN').format(amount));
            $('.discount-value').text(new Intl.NumberFormat('vi-VN').format(discount));
            $('.contract-total-value').text(new Intl.NumberFormat('vi-VN').format(total)).val(new Intl.NumberFormat('vi-VN').format(total));
            $('input[name="total_value"]').val(total);
        }

        // Khởi tạo với một sản phẩm mặc định
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotalValue();
        });
    </script>
    <script>
        $(function() {
            // Thêm biên nhận
            $('#add-payment-form').on('submit', function(e) {
                e.preventDefault();
                postAddPayment();
            });

            // Chỉnh sửa biên nhận
            $('#edit-payment-form').on('submit', function(e) {
                e.preventDefault();
                postUpdatePayment();
            });

            // Thiết lập flatpickr cho các input ngày
            flatpickrMake($('input[name="due_date"]'), 'datetime');
        });

        // Load dữ liệu biên nhận vào modal chỉnh sửa
        function loadPaymentData(paymentId) {
            const payment = @json($details['payments']).find(p => p.id === paymentId);

            if (payment) {
                $('#edit-payment-id').val(payment.id);
                $('#edit-payment-form [name=name]').val(payment.name);
                $('#edit-payment-form [name=percentage]').val(payment.percentage);
                $('#edit-payment-form [name=price]').val(payment.price);
                $('#edit-payment-form [name=currency_id]').val(payment.currency.id);
                $('#edit-payment-form [name=method_id]').val(payment.method.id);
                $('#edit-payment-form [name=due_date]').val(payment.due_date_formatted);
                $('#edit-payment-form [name=status]').prop('checked', payment.status == 1);
            }
        }

        // Tính toán số tiền dựa trên phần trăm
        function calculatePaymentAmount() {
            const percentage = parseFloat($('#add-payment-form [name=percentage]').val()) || 0;
            const totalValue = parseFloat('{{$details["total_value"]}}') || 0;

            if (percentage > 0 && totalValue > 0) {
                const amount = (percentage / 100) * totalValue;
                $('#add-payment-form [name=price]').val(Math.round(amount));
            }

            // Cũng áp dụng cho form chỉnh sửa
            const editPercentage = parseFloat($('#edit-payment-form [name=percentage]').val()) || 0;
            if (editPercentage > 0 && totalValue > 0) {
                const editAmount = (editPercentage / 100) * totalValue;
                $('#edit-payment-form [name=price]').val(Math.round(editAmount));
            }
        }

        // Thêm biên nhận
        async function postAddPayment() {
            let method = "post",
                url = "/contract/add-payment",
                params = null,
                data = $('#add-payment-form').serialize();

            try {
                let res = await axiosTemplate(method, url, params, data);
                switch (res.data.status) {
                    case 200:
                        showAlert('success', res.data.message);
                        window.location.reload();
                        break;
                    default:
                        showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                        break;
                }
            } catch (error) {
                showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                console.error(error);
            }
        }

        // Cập nhật biên nhận
        async function postUpdatePayment() {
            let method = "post",
                url = "/contract/update-payment",
                params = null,
                data = $('#edit-payment-form').serialize();

            try {
                let res = await axiosTemplate(method, url, params, data);
                switch (res.data.status) {
                    case 200:
                        showAlert('success', res.data.message);
                        window.location.reload();
                        break;
                    default:
                        showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                        break;
                }
            } catch (error) {
                showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                console.error(error);
            }
        }

        // Hủy biên nhận
        async function cancelPayment(paymentId) {
            Notiflix.Confirm.show(
                'Hủy biên nhận',
                'Bạn có chắc chắn muốn hủy biên nhận này?',
                'Đúng',
                'Hủy',
                async () => {
                        let method = "post",
                            url = "/contract/cancel-payment",
                            params = null,
                            data = {
                                id: paymentId
                            };
                        try {
                            let res = await axiosTemplate(method, url, params, data);
                            switch (res.data.status) {
                                case 200:
                                    showAlert('success', res.data.message);
                                    window.location.reload();
                                    break;
                                default:
                                    showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
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
    </script>
    <script>
        async function saveCreateTaskContract(id) {
            Notiflix.Confirm.show(
                'Tạo công việc',
                'Bạn có chắc chắn muốn tạo công việc cho hợp đồng này? Sau khi tạo sẽ không thể sửa đổi',
                'Đúng',
                'Hủy',
                async () => {
                        let method = "post",
                            url = "/contract/create-task",
                            params = null,
                            data = {
                                id
                            };
                        try {
                            let res = await axiosTemplate(method, url, params, data);
                            switch (res.data.status) {
                                case 200:
                                    showAlert('success', res.data.message);
                                    window.location.reload();
                                    break;
                                default:
                                    showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
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
    </script>
    @endpush