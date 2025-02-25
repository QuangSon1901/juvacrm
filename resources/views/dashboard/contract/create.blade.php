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
        </div>
    </div>
</div>
<div class="container-fixed">
    <form id="contract-form" class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">Bên A (bên cung cấp)</h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Tên công ty</label>
                                <input name="company_name" class="input" type="text" value="Juva Media" readonly>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Mã số thuế</label>
                                <input name="tax_code" class="input" type="text" value="" readonly>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Địa chỉ</label>
                                <input name="company_address" class="input" type="text" value="" readonly>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Nhân viên phụ trách</label>
                                <select name="user_id" class="select">
                                    <option class="disabled" disabled selected>Vui lòng chọn</option>
                                    @foreach ($details['users'] as $user)
                                    <option value="{{$user['id']}}">{{$user['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Chức danh</label>
                                <input class="input disabled" type="text" placeholder="Chức vụ nhân viên" disabled>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">Bên B (khách hàng)</h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Khách hàng</label>
                                <select name="provider_id" class="select">
                                    <option class="disabled" disabled selected>Vui lòng chọn</option>
                                    @foreach ($details['customers'] as $customer)
                                    <option value="{{$customer['id']}}" @if (isset($details['customer']) && $customer['id']==$details['customer']['id']) selected @endif>{{$customer['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Người đại diện</label>
                                <input name="customer_representative" class="input" type="text" placeholder="Tên người đại diện" value="@if (isset($details['customer'])) {{$details['customer']['representative'] ?? ''}} @endif">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Mã số thuế</label>
                                <input name="customer_tax_code" class="input" type="text" placeholder="Mã số thuế khách hàng" value="@if (isset($details['customer'])) {{$details['customer']['tax_code'] ?? ''}} @endif">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Địa chỉ</label>
                                <input name="address" class="input" type="text" placeholder="Địa chỉ khách hàng" value="@if (isset($details['customer'])) {{$details['customer']['address']}} @endif">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">Điện thoại</label>
                                <input name="phone" class="input" type="text" placeholder="Số điện thoại" value="@if (isset($details['customer'])) {{$details['customer']['phone']}} @endif">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1 xl:!col-span-2">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">Mô tả hợp đồng</h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Trạng thái</label>
                                <select name="status" class="select">
                                    <option value="0" selected>Đang chờ</option>
                                    <option value="1">Đang triển khai</option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Số hợp đồng</label>
                                <input name="contract_number" class="input" type="text" placeholder="Tự động tạo" readonly>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Tên hợp đồng</label>
                                <input name="name" class="input" type="text" placeholder="Tên hợp đồng">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Loại hình dịch vụ</label>
                                <select name="category_id" class="select">
                                    <option class="disabled" disabled selected>Vui lòng chọn</option>
                                    @foreach ($details['categories'] as $category)
                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Ngày ký</label>
                                <input name="sign_date" class="input" type="text" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Ngày hiệu lực</label>
                                <input name="effective_date" class="input" type="text" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Ngày hết hạn</label>
                                <input name="expiry_date" class="input" type="text" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Thời gian dự kiến</label>
                                <input name="estimate_date" class="input" type="text" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Tổng giá trị</label>
                                <input name="total_value" class="input" type="text" placeholder="Sẽ tự tính dựa vào dịch vụ" readonly>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Ghi chú</label>
                                <textarea class="textarea" name="note" placeholder="Ghi chú"></textarea>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">Điều khoản chung</label>
                                <textarea class="textarea" name="terms_and_conditions" placeholder="Điều khoản chung"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1 xl:!col-span-3">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin dịch vụ</h3>
                        <button type="button" class="btn btn-light btn-sm" onclick="addService()">Thêm dịch vụ</button>
                    </div>
                    <div class="card-table scrollable-x-auto">
                        <div class="scrollable-auto">
                            <table id="services-table" class="table align-middle text-2sm text-gray-600">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="text-start font-normal min-w-40 !px-1">Dịch vụ</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Số lượng</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Giá</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Ghi chú</th>
                                        <th class="min-w-16 !px-1"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">Thanh toán</h3>
                        <button type="button" class="btn btn-light btn-sm" onclick="addPayment()">Thêm biên nhận</button>
                    </div>
                    <div class="card-table scrollable-x-auto">
                        <div class="scrollable-auto">
                            <table id="payments-table" class="table align-middle text-2sm text-gray-600">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="text-start font-normal min-w-20 !px-1">Biên nhận</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Giá tiền</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Tiền tệ</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Phương thức</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Ngày</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Loại phiếu</th>
                                        <th class="text-start font-normal min-w-20 !px-1">Trạng thái</th>
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
    </form>
</div>
@endsection
@push("actions")
<button type="button" class="btn btn-success" onclick="saveCreateContract()">Tạo hợp đồng</button>
@endpush
@push('scripts')
<script>
    const details = @json($details);

    function addService() {
        let servicesOption = details.services.map(item => `<option value="${item.id}">${item.name}</option>`);
        $('#services-table tbody').append(`
            <tr>
                <td class="!px-1">
                    <select name="service_ids[]" class="select">
                        <option class="disabled" disabled selected>Chọn dịch vụ</option>
                        ${servicesOption}
                    </select>
                </td>
                <td class="!px-1">
                    <input name="service_quantity[]" class="input" type="text" placeholder="Số lượng" oninput="calculateTotalValue()">
                </td>
                <td class="!px-1">
                    <input name="service_price[]" class="input" type="text" placeholder="Giá tiền" oninput="calculateTotalValue()">
                </td>
                <td class="!px-1">
                    <input name="service_note[]" class="input" type="text" placeholder="Ghi chú">
                </td>
                <td class="text-center !px-1">
                    <button type="button" class="btn btn-sm btn-icon btn-light btn-clear" onclick="$(this).closest('tr').remove();">
                        <i class="ki-filled ki-trash !text-red-600"></i>
                    </button>
                </td>
            </tr>
        `);
    }

    function addPayment() {
        let paymentMethods = details.payments.map(item => `<option value="${item.id}">${item.name}</option>`);
        let currencies = details.currencies.map(item => `<option value="${item.id}">${item.currency_code}</option>`);
        $('#payments-table tbody').append(`
            <tr>
                <td class="!px-1 min-w-20">
                    <input name="payment_name[]" class="input" type="text" placeholder="Biên nhận">
                </td>
                <td class="!px-1 min-w-20">
                    <input name="payment_price[]" class="input" type="text" placeholder="Giá tiền">
                </td>
                <td class="!px-1">
                    <select name="payment_currencies[]" class="select !w-max">${currencies}</select>
                </td>
                <td class="!px-1 min-w-20">
                    <select name="payment_methods[]" class="select">${paymentMethods}</select>
                </td>
                <td class="!px-1 min-w-20">
                    <input name="payment_due_dates[]" class="input" type="text" placeholder="DD/MM/YYYY">
                </td>
                <td class="!px-1 min-w-20">
                    <select name="payment_stage[]" class="select">
                        <option value="0">Biên nhận cọc</option>
                        <option value="1">Tiền thưởng thêm</option>
                        <option value="2">Biên nhận cuối</option>
                        <option value="3">Tiền khấu trừ</option>
                    </select>
                </td>
                <td class="!px-1 min-w-20">
                    <label class="checkbox">
                        <input name="payment_status[]" type="checkbox" value="1">
                        <span class="checkbox-label">Đã thanh toán</span>
                    </label>
                </td>
                <td class="text-center !px-1">
                    <button type="button" class="btn btn-sm btn-icon btn-light btn-clear" onclick="$(this).closest('tr').remove();">
                        <i class="ki-filled ki-trash !text-red-600"></i>
                    </button>
                </td>
            </tr>
        `);
    }

    async function saveCreateContract() {
        calculateTotalValue();

        let method = "post",
            url = "/contract/create",
            params = null,
            data = $('#contract-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }

    function calculateTotalValue() {
        let total = 0;
        $('#services-table tbody tr').each(function() {
            const quantity = parseFloat($(this).find('input[name="service_quantity[]"]').val()) || 0;
            const price = parseFloat($(this).find('input[name="service_price[]"]').val()) || 0;
            total += quantity * price;
        });
        // $('input[name="total_value"]').val(total.toLocaleString('vi-VN')); // Định dạng số theo kiểu Việt Nam
        $('input[name="total_value"]').val(total);
    }
</script>
@endpush