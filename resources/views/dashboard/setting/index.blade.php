@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thiết lập hệ thống
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
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <!-- Cột 1: Thiết lập thời gian -->
        <div class="col-span-2">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thiết lập thời gian làm việc
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                @foreach ($configs as $config)
                                    @if (in_array($config->config_key, ['checkin_time', 'checkout_time', 'work_days', 'break_time', 'min_working_hours', 'annual_leave_max']))
                                    <tr>
                                        <td class="py-2 min-w-28 text-gray-600 font-normal">
                                            {{ $config->description }}
                                        </td>
                                        <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                            {{ $config->config_value }}
                                        </td>
                                        <td class="py-2 text-center">
                                            <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-config-modal" data-key="{{ $config->config_key }}" data-value="{{ $config->config_value }}" data-description="{{ $config->description }}">
                                                <i class="ki-filled ki-notepad-edit">
                                                </i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Thiết lập hoa hồng -->
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thiết lập hoa hồng
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                @php
                                    $commissionConfig = $configs->firstWhere('config_key', 'contract_commission_percentage');
                                @endphp
                                @if ($commissionConfig)
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        {{ $commissionConfig->description }}
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        {{ $commissionConfig->config_value }}%
                                    </td>
                                    <td class="py-2 text-center">
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-config-modal" data-key="{{ $commissionConfig->config_key }}" data-value="{{ $commissionConfig->config_value }}" data-description="{{ $commissionConfig->description }}">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="3" class="py-2 text-center text-gray-600">
                                        Chưa có thiết lập hoa hồng
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cột 2: Thống kê hoa hồng -->
        <div class="col-span-1">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Thống kê hoa hồng
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid gap-2.5">
                        <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                            <div class="flex items-center flex-wrap gap-3.5">
                                <i class="ki-outline ki-dollar size-6 shrink-0 text-success"></i>
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900 mb-px">
                                        Tổng hoa hồng chờ chi
                                    </div>
                                    <div class="text-2sm text-gray-700">
                                        {{ number_format($pendingCommissions, 0, ',', '.') }} VNĐ
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                            <div class="flex items-center flex-wrap gap-3.5">
                                <i class="ki-outline ki-abstract-26 size-6 shrink-0 text-primary"></i>
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900 mb-px">
                                        Tổng số hoa hồng
                                    </div>
                                    <div class="text-2sm text-gray-700">
                                        {{ $totalCommissions }} (Đã chi: {{ $paidCommissions }})
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-center mt-5">
                        <a href="{{ route('dashboard.setting.commissions') }}" class="btn btn-sm btn-primary">
                            <i class="ki-outline ki-eye me-1"></i>
                            Xem danh sách hoa hồng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-config-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật thiết lập
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5" id="update-config-form">
                <div class="flex flex-col gap-2.5">
                    <input type="hidden" name="config_key">
                    <label id="config-description" class="text-sm font-medium text-gray-700"></label>
                    <input class="input" name="config_value" type="text" placeholder="Nhập giá trị">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(function() {
        $('button[data-modal-toggle="#update-config-modal"]').on('click', function() {
            let _this = $(this);
            let _modal = $('#update-config-modal');
            let key = _this.data('key');
            let value = _this.data('value');
            let description = _this.data('description');
            
            _modal.find('input[name="config_key"]').val(key);
            _modal.find('input[name="config_value"]').val(value);
            _modal.find('#config-description').text(description);
        });

        $('#update-config-form').on('submit', function(e) {
            e.preventDefault();
            updateConfig();
        });
    });

    async function updateConfig() {
        let form = $('#update-config-form');
        let method = "post",
            url = "/setting/update",
            params = null,
            data = {
                config_key: form.find('input[name="config_key"]').val(),
                config_value: form.find('input[name="config_value"]').val()
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }
</script>
@endpush