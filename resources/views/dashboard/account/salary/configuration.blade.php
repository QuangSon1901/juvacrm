@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Cấu hình tính lương
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
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Cấu hình toàn cục -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ki-filled ki-setting text-primary text-2xl"></i>&nbsp;Cấu hình lương toàn cục
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <!-- Cấu hình lương cố định -->
                    <div class="card border border-gray-200">
                        <div class="card-header">
                            <h3 class="card-title">Lương cố định (Fulltime)</h3>
                        </div>
                        <div class="card-body">
                            <form id="global-fulltime-form">
                                <input type="hidden" name="type" value="fulltime">
                                <div class="grid gap-5">
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Lương cơ bản (VNĐ/tháng) <span class="text-red-500">*</span>
                                        </label>
                                        <input class="input" type="number" name="monthly_salary" min="0" value="{{ $globalFulltimeConfig->monthly_salary ?? 0 }}" required>
                                    </div>
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Hệ số làm thêm giờ
                                        </label>
                                        <input class="input" type="number" name="overtime_rate" min="0" step="0.1" value="{{ $globalFulltimeConfig->overtime_rate ?? 1.5 }}">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Thuế thu nhập (%)
                                            </label>
                                            <input class="input" type="number" name="tax_rate" min="0" max="100" step="0.1" value="{{ $globalFulltimeConfig->tax_rate ?? 0 }}">
                                        </div>
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Bảo hiểm (%)
                                            </label>
                                            <input class="input" type="number" name="insurance_rate" min="0" max="100" step="0.1" value="{{ $globalFulltimeConfig->insurance_rate ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Thưởng chuyên cần (%)
                                        </label>
                                        <input class="input" type="number" name="attendance_bonus_rate" min="0" step="0.1" value="{{ $globalFulltimeConfig->attendance_bonus_rate ?? 0 }}">
                                    </div>
                                    <div class="flex flex-col">
                                        <button type="submit" class="btn btn-primary justify-center">
                                            Lưu cấu hình
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Cấu hình lương theo giờ -->
                    <div class="card border border-gray-200">
                        <div class="card-header">
                            <h3 class="card-title">Lương theo giờ (Part-time)</h3>
                        </div>
                        <div class="card-body">
                            <form id="global-parttime-form">
                                <input type="hidden" name="type" value="part-time">
                                <div class="grid gap-5">
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Lương theo giờ (VNĐ/giờ) <span class="text-red-500">*</span>
                                        </label>
                                        <input class="input" type="number" name="hourly_rate" min="0" value="{{ $globalPartTimeConfig->hourly_rate ?? 0 }}" required>
                                    </div>
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Hệ số làm thêm giờ
                                        </label>
                                        <input class="input" type="number" name="overtime_rate" min="0" step="0.1" value="{{ $globalPartTimeConfig->overtime_rate ?? 1.5 }}">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Thuế thu nhập (%)
                                            </label>
                                            <input class="input" type="number" name="tax_rate" min="0" max="100" step="0.1" value="{{ $globalPartTimeConfig->tax_rate ?? 0 }}">
                                        </div>
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Bảo hiểm (%)
                                            </label>
                                            <input class="input" type="number" name="insurance_rate" min="0" max="100" step="0.1" value="{{ $globalPartTimeConfig->insurance_rate ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <button type="submit" class="btn btn-primary justify-center">
                                            Lưu cấu hình
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cấu hình theo nhân viên -->
        <div class="card">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    <i class="ki-filled ki-user-tick text-primary text-2xl"></i>&nbsp;Cấu hình lương theo nhân viên
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <select class="select select-sm" id="user-selector">
                            <option value="">Chọn nhân viên</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="user-config-container" class="grid grid-cols-1 lg:grid-cols-2 gap-5 hidden">
                    <!-- Cấu hình lương cố định nhân viên -->
                    <div class="card border border-gray-200">
                        <div class="card-header">
                            <h3 class="card-title">Lương cố định (Fulltime)</h3>
                        </div>
                        <div class="card-body">
                            <form id="user-fulltime-form">
                                <input type="hidden" name="user_id" id="fulltime-user-id">
                                <input type="hidden" name="type" value="fulltime">
                                <div class="grid gap-5">
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Lương cơ bản (VNĐ/tháng) <span class="text-red-500">*</span>
                                        </label>
                                        <input class="input" type="number" name="monthly_salary" id="user-monthly-salary" min="0" required>
                                    </div>
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Hệ số làm thêm giờ
                                        </label>
                                        <input class="input" type="number" name="overtime_rate" id="user-overtime-rate" min="0" step="0.1">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Thuế thu nhập (%)
                                            </label>
                                            <input class="input" type="number" name="tax_rate" id="user-tax-rate" min="0" max="100" step="0.1">
                                        </div>
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Bảo hiểm (%)
                                            </label>
                                            <input class="input" type="number" name="insurance_rate" id="user-insurance-rate" min="0" max="100" step="0.1">
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Thưởng chuyên cần (%)
                                        </label>
                                        <input class="input" type="number" name="attendance_bonus_rate" id="user-attendance-bonus-rate" min="0" step="0.1">
                                    </div>
                                    <div class="flex flex-col">
                                        <button type="submit" class="btn btn-primary justify-center">
                                            Lưu cấu hình
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Cấu hình lương theo giờ nhân viên -->
                    <div class="card border border-gray-200">
                        <div class="card-header">
                            <h3 class="card-title">Lương theo giờ (Part-time)</h3>
                        </div>
                        <div class="card-body">
                            <form id="user-parttime-form">
                                <input type="hidden" name="user_id" id="parttime-user-id">
                                <input type="hidden" name="type" value="part-time">
                                <div class="grid gap-5">
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Lương theo giờ (VNĐ/giờ) <span class="text-red-500">*</span>
                                        </label>
                                        <input class="input" type="number" name="hourly_rate" id="user-hourly-rate" min="0" required>
                                    </div>
                                    <div class="flex flex-col gap-2.5">
                                        <label class="text-gray-900 font-semibold text-2sm">
                                            Hệ số làm thêm giờ
                                        </label>
                                        <input class="input" type="number" name="overtime_rate" id="user-pt-overtime-rate" min="0" step="0.1">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Thuế thu nhập (%)
                                            </label>
                                            <input class="input" type="number" name="tax_rate" id="user-pt-tax-rate" min="0" max="100" step="0.1">
                                        </div>
                                        <div class="flex flex-col gap-2.5">
                                            <label class="text-gray-900 font-semibold text-2sm">
                                                Bảo hiểm (%)
                                            </label>
                                            <input class="input" type="number" name="insurance_rate" id="user-pt-insurance-rate" min="0" max="100" step="0.1">
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <button type="submit" class="btn btn-primary justify-center">
                                            Lưu cấu hình
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="user-select-message" class="flex items-center justify-center py-10">
                    <p class="text-gray-500">Vui lòng chọn nhân viên để cấu hình lương</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Xử lý form cấu hình toàn cục - Fulltime
        $('#global-fulltime-form').on('submit', async function(e) {
            e.preventDefault();
            
            try {
                const res = await axiosTemplate('post', '/account/salary/save-config', null, {
                    monthly_salary: $(this).find('input[name="monthly_salary"]').val(),
                    overtime_rate: $(this).find('input[name="overtime_rate"]').val(),
                    tax_rate: $(this).find('input[name="tax_rate"]').val(),
                    insurance_rate: $(this).find('input[name="insurance_rate"]').val(),
                    attendance_bonus_rate: $(this).find('input[name="attendance_bonus_rate"]').val(),
                    type: 'fulltime'
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi lưu cấu hình');
                console.error(error);
            }
        });
        
        // Xử lý form cấu hình toàn cục - Part-time
        $('#global-parttime-form').on('submit', async function(e) {
            e.preventDefault();
            
            try {
                const res = await axiosTemplate('post', '/account/salary/save-config', null, {
                    hourly_rate: $(this).find('input[name="hourly_rate"]').val(),
                    overtime_rate: $(this).find('input[name="overtime_rate"]').val(),
                    tax_rate: $(this).find('input[name="tax_rate"]').val(),
                    insurance_rate: $(this).find('input[name="insurance_rate"]').val(),
                    type: 'part-time'
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi lưu cấu hình');
                console.error(error);
            }
        });
        
        // Xử lý khi chọn nhân viên
        $('#user-selector').on('change', async function() {
            const userId = $(this).val();
            
            if (userId) {
                try {
                    const res = await axiosTemplate('get', '/account/user-config', {
                        user_id: userId
                    }, null);
                    
                    if (res.data.status === 200) {
                        const data = res.data.data;
                        
                        // Hiển thị form cấu hình nhân viên
                        $('#user-config-container').removeClass('hidden');
                        $('#user-select-message').addClass('hidden');
                        
                        // Cập nhật giá trị cho form fulltime
                        $('#fulltime-user-id').val(userId);
                        $('#user-monthly-salary').val(data.fulltime ? data.fulltime.monthly_salary : '');
                        $('#user-overtime-rate').val(data.fulltime ? data.fulltime.overtime_rate : '');
                        $('#user-tax-rate').val(data.fulltime ? data.fulltime.tax_rate : '');
                        $('#user-insurance-rate').val(data.fulltime ? data.fulltime.insurance_rate : '');
                        $('#user-attendance-bonus-rate').val(data.fulltime ? data.fulltime.attendance_bonus_rate : '');
                        
                        // Cập nhật giá trị cho form part-time
                        $('#parttime-user-id').val(userId);
                        $('#user-hourly-rate').val(data['part-time'] ? data['part-time'].hourly_rate : '');
                        $('#user-pt-overtime-rate').val(data['part-time'] ? data['part-time'].overtime_rate : '');
                        $('#user-pt-tax-rate').val(data['part-time'] ? data['part-time'].tax_rate : '');
                        $('#user-pt-insurance-rate').val(data['part-time'] ? data['part-time'].insurance_rate : '');
                    } else {
                        showAlert('warning', res.data.message);
                    }
                } catch (error) {
                    showAlert('error', 'Đã xảy ra lỗi khi tải cấu hình');
                    console.error(error);
                }
            } else {
                // Ẩn form cấu hình nhân viên
                $('#user-config-container').addClass('hidden');
                $('#user-select-message').removeClass('hidden');
            }
        });
        
        // Xử lý form cấu hình nhân viên - Fulltime
        $('#user-fulltime-form').on('submit', async function(e) {
            e.preventDefault();
            
            try {
                const res = await axiosTemplate('post', '/account/salary/save-config', null, {
                    user_id: $('#fulltime-user-id').val(),
                    monthly_salary: $('#user-monthly-salary').val(),
                    overtime_rate: $('#user-overtime-rate').val(),
                    tax_rate: $('#user-tax-rate').val(),
                    insurance_rate: $('#user-insurance-rate').val(),
                    attendance_bonus_rate: $('#user-attendance-bonus-rate').val(),
                    type: 'fulltime'
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi lưu cấu hình');
                console.error(error);
            }
        });
        
        // Xử lý form cấu hình nhân viên - Part-time
        $('#user-parttime-form').on('submit', async function(e) {
            e.preventDefault();
            
            try {
                const res = await axiosTemplate('post', '/account/salary/save-config', null, {
                    user_id: $('#parttime-user-id').val(),
                    hourly_rate: $('#user-hourly-rate').val(),
                    overtime_rate: $('#user-pt-overtime-rate').val(),
                    tax_rate: $('#user-pt-tax-rate').val(),
                    insurance_rate: $('#user-pt-insurance-rate').val(),
                    type: 'part-time'
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi lưu cấu hình');
                console.error(error);
            }
        });
    });
</script>
@endpush