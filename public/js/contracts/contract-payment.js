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