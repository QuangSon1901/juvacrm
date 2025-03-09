{{-- resources/views/dashboard/contracts/forms/payment-form.blade.php --}}
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
            @if($formId == 'add-payment-form')
            <span class="checkbox-label text-gray-800 !font-bold mt-2">Loại phiếu</span>
            <div class="checkbox-group">
                <select name="payment_stage" class="select">
                    <option value="0">Biên nhận cọc</option>
                    <option value="1">Tiền thưởng thêm</option>
                    <option value="2">Biên nhận cuối</option>
                    <option value="3">Tiền khấu trừ</option>
                </select>
            </div>
            @else
            <span class="checkbox-label text-gray-800 !font-bold mt-2">Trạng thái</span>
            <div class="checkbox-group">
                <label class="checkbox flex items-center gap-2">
                    <input name="status" type="checkbox" value="1">
                    <span class="checkbox-label">Đã thanh toán</span>
                </label>
            </div>
            @endif
        </div>
    </div>
</div>