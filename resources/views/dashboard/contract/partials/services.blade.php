{{-- resources/views/dashboard/contracts/partials/services.blade.php --}}
<div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
    <div class="card-header bg-white border-b border-gray-100">
        <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
            <i class="ki-filled ki-package text-purple-500"></i>
            Thông tin dịch vụ
        </h3>
        <div class="flex gap-2">
            <button type="button" class="btn btn-light btn-sm" onclick="addService('service')">
                <i class="ki-filled ki-plus"></i>
                Thêm dịch vụ
            </button>
            <button type="button" class="btn btn-light btn-sm" onclick="addService('other')">
                <i class="ki-filled ki-plus"></i>
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