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