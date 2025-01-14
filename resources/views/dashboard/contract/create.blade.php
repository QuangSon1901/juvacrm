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
                <i class="ki-filled ki-magnifier !text-base">
                </i>
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
                        <h3 class="card-title">
                            Bên A (bên cung cấp)
                        </h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Nhân viên phụ trách
                                </label>
                                <select name="user_id" class="select">
                                    <option class="disabled" disabled selected>
                                        Vui lòng chọn
                                    </option>
                                    @foreach ($details['users'] as $user)
                                    <option value="{{$user['id']}}">
                                        {{$user['name']}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Chức danh
                                </label>
                                <input class="input disabled" type="text" placeholder="Chức vụ nhân viên" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Bên B (khách hàng)
                        </h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Khách hàng
                                </label>
                                <select name="provider_id" class="select">
                                    <option class="disabled" disabled selected>
                                        Vui lòng chọn
                                    </option>
                                    @foreach ($details['customers'] as $customer)
                                    <option value="{{$customer['id']}}" @if (isset($details['customer']) && $customer['id']==$details['customer']['id']) selected @endif>
                                        {{$customer['name']}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Địa chỉ
                                </label>
                                <input name="address" class="input" type="text" placeholder="Địa chỉ khách hàng" value="@if (isset($details['customer'])) {{$details['customer']['address']}} @endif">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Điện thoại
                                </label>
                                <input name="phone"  class="input" type="text" placeholder="Số điện thoại" value="@if (isset($details['customer'])) {{$details['customer']['phone']}} @endif">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Mô tả hợp đồng
                        </h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Tên hợp đồng
                                </label>
                                <input name="name"  class="input" type="text" placeholder="Tên hợp đồng">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Loại hình dịch vụ
                                </label>
                                <select name="category_id" class="select">
                                    <option class="disabled" disabled selected>
                                        Vui lòng chọn
                                    </option>
                                    @foreach ($details['categories'] as $category)
                                    <option value="{{$category['id']}}">
                                        {{$category['name']}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Thời gian dự kiến
                                </label>
                                <input name="estimate_day" class="input max-w-20" type="text" placeholder="Số ngày">
                                <input name="estimate_date" class="input" type="text" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Ghi chú
                                </label>
                                <textarea class="textarea" name="note" id="" placeholder="Ghi chú"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin dịch vụ
                        </h3>
                        <button type="button" class="btn btn-light btn-sm" onclick="addService()">
                            Thêm dịch vụ
                        </button>
                    </div>
                    <div class="card-table scrollable-x-auto">
                        <div class="scrollable-auto">
                            <table id="services-table" class="table align-middle text-2sm text-gray-600">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="text-start font-normal min-w-40 !px-1">
                                            Dịch vụ
                                        </th>
                                        <th class="text-start font-normal min-w-20 !px-1">
                                            Số lượng
                                        </th>
                                        <th class="text-start font-normal min-w-20 !px-1">
                                            Giá
                                        </th>
                                        <th class="text-start font-normal min-w-20 !px-1">
                                            Ghi chú
                                        </th>
                                        <th class="min-w-16 !px-1">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td class="!px-1">
                                            <select name="service_ids[]" class="select">
                                                <option class="disabled" disabled selected>
                                                    Chọn dịch vụ
                                                </option>
                                                @foreach ($details['services'] as $service)
                                                <option value="{{$service['id']}}">
                                                    {{$service['name']}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="!px-1">
                                            <input name="service_quantity[]" class="input" type="text" placeholder="Số lượng">
                                        </td>
                                        <td class="!px-1">
                                            <input name="service_price[]" class="input" type="text" placeholder="Giá tiền">
                                        </td>
                                        <td class="!px-1">
                                            <input name="service_note[]" class="input" type="text" placeholder="Ghi chú">
                                        </td>
                                        <td class="text-center !px-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-light btn-clear" onclick="$(this).closest('tr').remove();">
                                                <i class="ki-filled ki-trash !text-red-600">
                                                </i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thanh toán
                        </h3>
                        <button type="button" class="btn btn-light btn-sm" onclick="addPayment()">
                            Thêm biên nhận
                        </button>
                    </div>
                    <div class="card-table scrollable-x-auto">
                        <div class="scrollable-auto">
                            <table id="payments-table" class="table align-middle text-2sm text-gray-600">
                                <tbody>
                                    <tr>
                                        <td class="!px-1 min-w-20">
                                            <input name="payment_name[]" class="input" type="text" placeholder="Biên nhận">
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <input name="payment_price[]" class="input" type="text" placeholder="Giá tiền">
                                        </td>
                                        <td class="!px-1">
                                            <select name="payment_currencies[]" class="select !w-max">
                                                @foreach ($details['currencies'] as $currency)
                                                <option value="{{$currency['id']}}">
                                                    {{$currency['currency_code']}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <select name="payment_methods[]" class="select">
                                                @foreach ($details['payments'] as $payment)
                                                <option value="{{$payment['id']}}">
                                                    {{$payment['name']}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <input name="payment_dates[]" class="input" type="text" placeholder="DD/MM/YYYY">
                                        </td>
                                        <td class="text-center !px-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-light btn-clear" onclick="$(this).closest('tr').remove();">
                                                <i class="ki-filled ki-trash !text-red-600">
                                                </i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
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
<button type="button" class="btn btn-success" onclick="saveCreateContract()">
    Tạo hợp đồng
</button>
@endpush
@push('scripts')
<script>
    const details = @json($details);

    function addService() {
        let servicesOption = details.services.map(item=>`<option value="${item.id}">${item.name}</option>`)
        $('#services-table tbody').append(`<tr>
                                        <td class="!px-1">
                                            <select name="service_ids[]" class="select">
                                                <option class="disabled" disabled selected>
                                                    Chọn dịch vụ
                                                </option>
                                                ${servicesOption}
                                            </select>
                                        </td>
                                        <td class="!px-1">
                                            <input name="service_quantity[]" class="input" type="text" placeholder="Số lượng">
                                        </td>
                                        <td class="!px-1">
                                            <input name="service_price[]" class="input" type="text" placeholder="Giá tiền">
                                        </td>
                                        <td class="!px-1">
                                            <input name="service_note[]" class="input" type="text" placeholder="Ghi chú">
                                        </td>
                                        <td class="text-center !px-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-light btn-clear" onclick="$(this).closest('tr').remove();">
                                                <i class="ki-filled ki-trash !text-red-600">
                                                </i>
                                            </button>
                                        </td>
                                    </tr>
        `)
    }

    function addPayment() {
        let paymentMethods = details.payments.map(item=>`<option value="${item.id}">${item.name}</option>`)
        let currencies = details.currencies.map(item=>`<option value="${item.id}">${item.currency_code}</option>`)
        $('#payments-table tbody').append(`
        <tr>
                                        <td class="!px-1 min-w-20">
                                            <input name="payment_name[]" class="input" type="text" placeholder="Biên nhận">
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <input name="payment_price[]" class="input" type="text" placeholder="Giá tiền">
                                        </td>
                                        <td class="!px-1">
                                            <select name="payment_currencies[]" class="select !w-max">
    ${currencies}
                                            </select>
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <select name="payment_methods[]" class="select">
                                                    ${paymentMethods}

                                            </select>
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <input name="payment_dates[]" class="input" type="text" placeholder="DD/MM/YYYY">
                                        </td>
                                        <td class="text-center !px-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-light btn-clear" onclick="$(this).closest('tr').remove();">
                                                <i class="ki-filled ki-trash !text-red-600">
                                                </i>
                                            </button>
                                        </td>
                                    </tr>
        `)
    }

    async function saveCreateContract() {
        let method = "post",
            url = "/contract/create",
            params = null,
            data = $('#contract-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                alert(res.data.message)
                window.location.reload();
                break;
            default:
                alert(res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
        }
    }
</script>
@endpush