{{-- resources/views/dashboard/contracts/partials/contract-description.blade.php --}}
<div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
    <div class="card-header bg-white border-b border-gray-100">
        <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
            <i class="ki-filled ki-document text-indigo-500"></i>
            Mô tả hợp đồng
        </h3>
    </div>
    <div class="card-body p-5">
        <div class="w-full">
            <label class="form-label mb-1.5 text-gray-700 font-medium">Số hợp đồng</label>
            <input name="contract_number" class="input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Tự động tạo" readonly>
        </div>

        <div class="mt-5">
            <label class="form-label mb-1.5 text-gray-700 font-medium">Tên hợp đồng</label>
            <input name="name" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full" type="text" placeholder="Tên hợp đồng">
        </div>

        <div class="mt-5 hidden">
            <label class="form-label mb-1.5 text-gray-700 font-medium">Loại hình dịch vụ</label>
            <select name="category_id" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full">
                <option class="disabled" disabled>Vui lòng chọn</option>
                @foreach ($details['categories'] as $category)
                <option value="{{$category['id']}}" selected>{{$category['name']}}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
            <div class="w-full">
                <label class="form-label mb-1.5 text-gray-700 font-medium">Thời gian dự kiến hoàn thành</label>
                <div class="relative">
                    <input name="expiry_date" class="input border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="DD/MM/YYYY">
                    <i class="ki-filled ki-calendar-8 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <label class="form-label mb-1.5 text-gray-700 font-medium">Tổng giá trị</label>
            <div class="relative">
                <input name="total_value_format" class="contract-total-value input bg-gray-50 border-gray-200 focus:border-blue-500 rounded-lg w-full !pl-10" type="text" placeholder="Sẽ tự tính dựa vào dịch vụ" readonly>
                <input type="hidden" name="total_value" value="0">
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