{{-- resources/views/dashboard/contracts/partials/payments.blade.php --}}
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