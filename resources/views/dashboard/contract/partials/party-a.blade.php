{{-- resources/views/dashboard/contracts/partials/party-a.blade.php --}}
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
                <option value="{{$user['id']}}" @if($user['id']==Session::get(ACCOUNT_CURRENT_SESSION)['id']) selected @endif>{{$user['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>