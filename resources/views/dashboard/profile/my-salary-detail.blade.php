@php
    use App\Models\SystemConfig;
@endphp
<div class="card-table">
    <table class="table align-middle text-sm text-gray-500 mb-0">
        <tbody>
            <tr>
                <td class="py-2 min-w-28 text-gray-600 font-medium" colspan="2">
                    <div class="flex items-center">
                        <i class="ki-outline ki-calendar me-2 text-primary"></i>
                        Thông tin kỳ lương
                    </div>
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Kỳ lương
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    Tháng {{ $salaryRecord->period_month }}/{{ $salaryRecord->period_year }}
                </td>
            </tr>
            
            <tr>
                <td class="py-2 min-w-28 text-gray-600 font-medium" colspan="2">
                    <div class="flex items-center mt-2">
                        <i class="ki-outline ki-dollar me-2 text-success"></i>
                        Thông tin lương
                    </div>
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Lương cơ bản
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    {{ number_format($salaryRecord->base_salary, 0, ',', '.') }} VNĐ
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Hoa hồng
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    {{ number_format($salaryRecord->commission_amount, 0, ',', '.') }} VNĐ
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Tiền công nhiệm vụ
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    {{ number_format($salaryRecord->task_mission_amount, 0, ',', '.') }} VNĐ
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600 font-medium" colspan="2">
                    <div class="flex items-center mt-2">
                        <i class="ki-outline ki-minus-square me-2 text-danger"></i>
                        Các khoản khấu trừ
                    </div>
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Thuế ({{ SystemConfig::getValue('tax_rate', 0) }}%)
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    {{ number_format($salaryRecord->tax_amount, 0, ',', '.') }} VNĐ
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Bảo hiểm ({{ SystemConfig::getValue('insurance_rate', 0) }}%)
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    {{ number_format($salaryRecord->insurance_amount, 0, ',', '.') }} VNĐ
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Tổng khấu trừ
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    {{ number_format($salaryRecord->deductions, 0, ',', '.') }} VNĐ
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600 font-medium" colspan="2">
                    <div class="flex items-center mt-2">
                        <i class="ki-outline ki-abstract-26 me-2 text-primary"></i>
                        Tổng kết
                    </div>
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600 font-medium">
                    Tổng thu nhập
                </td>
                <td class="py-2 text-success font-medium min-w-32">
                    {{ number_format($salaryRecord->final_amount, 0, ',', '.') }} VNĐ
                </td>
            </tr>
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Trạng thái
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    @php
                        $statusClass = '';
                        $statusText = '';
                        
                        switch($salaryRecord->status) {
                            case 'pending':
                                $statusClass = 'warning';
                                $statusText = 'Chờ xử lý';
                                break;
                            case 'processed':
                                $statusClass = 'primary';
                                $statusText = 'Đã duyệt';
                                break;
                            case 'paid':
                                $statusClass = 'success';
                                $statusText = 'Đã thanh toán';
                                break;
                            default:
                                $statusClass = 'gray';
                                $statusText = $salaryRecord->status;
                        }
                    @endphp
                    
                    <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </td>
            </tr>
            @if($salaryRecord->transaction)
            <tr>
                <td class="py-2 min-w-28 text-gray-600">
                    Ngày thanh toán
                </td>
                <td class="py-2 text-gray700 min-w-32">
                    {{ formatDateTime($salaryRecord->transaction->paid_date, 'd/m/Y') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>