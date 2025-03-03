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
    <form id="contract-form" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Thông tin Bên A (Cung cấp) -->
        <div class="col-span-1">
            <div class="grid gap-6">
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

                <!-- Thông tin Bên B (Khách hàng) -->
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

        <!-- Thông tin mô tả hợp đồng -->
        <div class="col-span-1 xl:!col-span-2">
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
                            <input name="total_value" class="input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="Sẽ tự tính dựa vào dịch vụ" readonly>
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

        <!-- Thông tin dịch vụ -->
        <div class="col-span-1 xl:!col-span-3">
            <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                <div class="card-header bg-white border-b border-gray-100">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-package text-purple-500"></i>
                        Thông tin dịch vụ
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-sm bg-purple-50 text-purple-600 hover:bg-purple-100 border-none rounded-lg flex items-center gap-1.5" onclick="addService('service')">
                            <i class="ki-filled ki-plus-square"></i>
                            Thêm dịch vụ
                        </button>
                        <button type="button" class="btn btn-sm bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border-none rounded-lg flex items-center gap-1.5" onclick="addService('other')">
                            <i class="ki-filled ki-plus-square"></i>
                            Thêm mục khác
                        </button>
                    </div>
                </div>
                <div class="card-table scrollable-x-auto">
                    <div class="scrollable-auto">
                        <table id="services-table" class="table align-middle text-sm text-gray-600">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="text-start font-medium text-gray-700 min-w-40 !px-1">Dịch vụ</th>
                                    <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Số lượng</th>
                                    <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Giá</th>
                                    <th class="text-start font-medium text-gray-700 min-w-40 !px-1">Ghi chú</th>
                                    <th class="min-w-16 !px-1"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh toán -->
        <div class="col-span-1 xl:!col-span-3">
            <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                <div class="card-header bg-white border-b border-gray-100">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-credit-cart text-green-500"></i>
                        Thanh toán
                    </h3>
                    <button type="button" class="btn btn-sm bg-green-50 text-green-600 hover:bg-green-100 border-none rounded-lg flex items-center gap-1.5" onclick="addPayment()">
                        <i class="ki-filled ki-plus-square"></i>
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
    </form>
</div>
@endsection

@push('scripts')
<script>
    const details = @json($details);

    let serviceRowCounter = 0;

    function addService(type) {
        const currentIndex = serviceRowCounter++;
        let serviceRow = '';

        if (type === 'service') {
            let servicesOption = details.services.map(item => `<option value="${item.id}">${item.name}</option>`);
            serviceRow = `
                <tr class="service-row" data-type="service" data-service-id="${currentIndex}">
                    <td class="!px-1">
                        <select name="service_ids[]" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full service-select">
                            <option class="disabled" disabled selected>Chọn dịch vụ</option>
                            ${servicesOption}
                        </select>
                    </td>
                    <td class="!px-1">
                        <input name="service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Số lượng" oninput="calculateTotalValue()">
                    </td>
                    <td class="!px-1">
                        <div class="relative">
                            <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="Giá tiền" oninput="calculateTotalValue()">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                        </div>
                    </td>
                    <td class="!px-1">
                        <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ghi chú">
                    </td>
                    <td class="text-center !px-1">
                        <div class="flex items-center">
                            <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-blue-100 mr-1" onclick="addSubService(this)">
                                <i class="ki-filled ki-plus-circle text-blue-500"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-red-100" onclick="removeServiceWithChildren(this)">
                                <i class="ki-filled ki-trash text-red-500"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            serviceRow = `
                <tr class="service-row" data-type="other" data-service-id="${currentIndex}">
                    <td class="!px-1">
                        <input name="service_custom_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ví dụ: Giảm giá">
                        <input type="hidden" name="service_ids[]" value="custom">
                    </td>
                    <td class="!px-1">
                        <input name="service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Số lượng" oninput="calculateTotalValue()">
                    </td>
                    <td class="!px-1">
                        <div class="relative">
                            <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="Giá tiền" oninput="calculateTotalValue()">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                        </div>
                    </td>
                    <td class="!px-1">
                        <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ghi chú">
                    </td>
                    <td class="text-center !px-1">
                        <div class="flex items-center">
                            <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-blue-100 mr-1" onclick="addSubService(this)">
                                <i class="ki-filled ki-plus-circle text-blue-500"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-red-100" onclick="removeServiceWithChildren(this)">
                                <i class="ki-filled ki-trash text-red-500"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        $('#services-table tbody').append(serviceRow);
    }

    function addSubService(button) {
        const parentRow = $(button).closest('tr');
        const parentId = parentRow.data('service-id');

        const subServiceRow = `
            <tr class="sub-service-row bg-gray-50" data-parent-id="${parentId}">
                <td class="!px-1">
                    <div class="flex items-center">
                        <span class="text-gray-400 mr-2">└</span>
                        <input name="sub_service_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Tên dịch vụ con">
                    </div>
                </td>
                <td class="!px-1">
                    <input name="sub_service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Số lượng">
                </td>
                <td class="!px-1">
                    <div class="relative">
                        <input name="sub_service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="Giá tiền" disabled>
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                    </div>
                </td>
                <td class="!px-1">
                    <input name="sub_service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ghi chú">
                </td>
                <td class="text-center !px-1">
                    <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-red-100" onclick="$(this).closest('tr').remove();">
                        <i class="ki-filled ki-trash text-red-500"></i>
                    </button>
                </td>
            </tr>
        `;

        // Find the last sub-service of this parent, or the parent itself if no sub-services
        let insertAfterRow = parentRow;
        $('#services-table tbody tr.sub-service-row').each(function() {
            if ($(this).data('parent-id') === parentId) {
                insertAfterRow = $(this);
            }
        });

        // Insert after the last related row
        insertAfterRow.after(subServiceRow);
    }

    function removeServiceWithChildren(button) {
        const parentRow = $(button).closest('tr');
        const parentId = parentRow.data('service-id');

        // Remove all child sub-services first
        $('#services-table tbody tr.sub-service-row').each(function() {
            if ($(this).data('parent-id') === parentId) {
                $(this).remove();
            }
        });

        // Then remove the parent row
        parentRow.remove();

        // Recalculate total after removing rows
        calculateTotalValue();
    }

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

    async function saveCreateContract() {
        calculateTotalValue();
        
        // Create structured data for services and sub-services
        let servicesData = [];
        
        $('#services-table tbody tr.service-row').each(function() {
            const row = $(this);
            const type = row.data('type');
            const serviceId = row.data('service-id');
            
            let serviceData = {
                type: type,
                id: type === 'service' ? row.find('select[name="service_ids[]"]').val() : 'custom',
                custom_name: type === 'other' ? row.find('input[name="service_custom_name[]"]').val() : null,
                quantity: row.find('input[name="service_quantity[]"]').val(),
                price: row.find('input[name="service_price[]"]').val(),
                note: row.find('input[name="service_note[]"]').val(),
                sub_services: []
            };
            
            // Find child sub-services
            $('#services-table tbody tr.sub-service-row').each(function() {
                const subRow = $(this);
                if (subRow.data('parent-id') === serviceId) {
                    serviceData.sub_services.push({
                        name: subRow.find('input[name="sub_service_name[]"]').val(),
                        quantity: subRow.find('input[name="sub_service_quantity[]"]').val(),
                        note: subRow.find('input[name="sub_service_note[]"]').val()
                    });
                }
            });
            
            servicesData.push(serviceData);
        });
        
        // Add services data to form data
        let formData = $('#contract-form').serialize();
        formData += '&services_data=' + encodeURIComponent(JSON.stringify(servicesData));

        let method = "post",
            url = "/contract/create",
            params = null,
            data = formData;
        
        try {
            let res = await axiosTemplate(method, url, params, data);
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

    function calculateTotalValue() {
        let total = 0;
        $('#services-table tbody tr.service-row').each(function() {
            const quantity = parseFloat($(this).find('input[name="service_quantity[]"]').val()) || 0;
            const price = parseFloat($(this).find('input[name="service_price[]"]').val()) || 0;
            total += quantity * price;
        });
        $('input[name="total_value"]').val(total);
    }

    // Thêm focus vào các input khi trang được tải
    $(document).ready(function() {
        // Tự động focus vào trường tên hợp đồng khi trang được tải
        $('input[name="name"]').focus();

        flatpickrMake($('input[name="sign_date"], input[name="effective_date"], input[name="expiry_date"], input[name="estimate_date"]'), 'datetime');
    });
</script>
@endpush