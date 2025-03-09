<div class="card shadow-sm border border-gray-100 overflow-hidden mb-4">
    <div class="card-header bg-white border-b border-gray-100">
        <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
            <i class="ki-filled ki-dollar text-green-500"></i>
            Tổng hợp giá trị hợp đồng
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