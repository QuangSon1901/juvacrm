@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Bảng lương cá nhân
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
        <!-- Thông tin lương hiện tại -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="col-span-2">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-filled ki-dollar text-success text-2xl"></i>&nbsp;Thông tin lương
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        Loại lương
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        {{ isset($salaryConfig) && $salaryConfig->type == 'fulltime' ? 'Lương cố định' : 'Lương theo giờ' }}
                                    </td>
                                </tr>
                                @if(isset($salaryConfig) && $salaryConfig->type == 'fulltime')
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        Lương cơ bản
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        {{ number_format($salaryConfig->monthly_salary, 0, ',', '.') }} VNĐ
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        Lương theo giờ
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        {{ number_format($salaryConfig->hourly_rate ?? 0, 0, ',', '.') }} VNĐ/giờ
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        Hệ số làm thêm giờ
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        {{ $salaryConfig->overtime_rate ?? 1.5 }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        Thuế thu nhập
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        {{ ($salaryConfig->tax_rate ?? 0) }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        Bảo hiểm
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        {{ ($salaryConfig->insurance_rate ?? 0) }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-span-1">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-filled ki-arrow-down-square text-primary text-2xl"></i>&nbsp;Tạm ứng
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-2.5">
                            @if(isset($currentAdvance))
                            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                                <div class="flex items-center flex-wrap gap-3.5">
                                    <i class="ki-outline ki-dollar size-6 shrink-0 text-warning"></i>
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900 mb-px">
                                            Tạm ứng hiện tại
                                        </div>
                                        <div class="text-2sm text-gray-700">
                                            {{ number_format($currentAdvance->amount, 0, ',', '.') }} VNĐ
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ formatDateTime($currentAdvance->request_date, 'd/m/Y') }}
                                        </div>
                                        <div class="text-xs badge badge-sm badge-outline mt-1
                                        @if($currentAdvance->status == 'pending') badge-warning
                                        @elseif($currentAdvance->status == 'approved') badge-primary
                                        @elseif($currentAdvance->status == 'rejected') badge-danger
                                        @elseif($currentAdvance->status == 'paid') badge-success
                                        @endif">
                                            @if($currentAdvance->status == 'pending') Chờ duyệt
                                            @elseif($currentAdvance->status == 'approved') Đã duyệt
                                            @elseif($currentAdvance->status == 'rejected') Từ chối
                                            @elseif($currentAdvance->status == 'paid') Đã chi
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center gap-3.5 py-4">
                                <i class="ki-outline ki-dollar size-6 text-gray-400"></i>
                                <div class="text-sm text-gray-500">Chưa có yêu cầu tạm ứng trong tháng này</div>
                                <button class="btn btn-sm btn-primary" data-modal-toggle="#request-advance-modal">
                                    Yêu cầu tạm ứng
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bảng lương -->
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap py-5">
                <h3 class="card-title">
                    Lịch sử bảng lương
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <input class="input input-sm" type="text" id="year-filter" data-flatpickr="true" data-flatpickr-type="year" placeholder="Chọn năm">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-fixed table-border">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">STT</th>
                                    <th class="w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Kỳ lương</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Lương cơ bản</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Hoa hồng</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Thêm giờ</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Khấu trừ</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Thực nhận</span>
                                        </span>
                                    </th>
                                    <th class="w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salaryRecords as $index => $record)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $record->period_month }}/{{ $record->period_year }}</td>
                                    <td>{{ number_format($record->base_salary, 0, ',', '.') }}</td>
                                    <td>{{ number_format($record->commission_amount, 0, ',', '.') }}</td>
                                    <td>{{ number_format($record->overtime_amount, 0, ',', '.') }}</td>
                                    <td>{{ number_format($record->deductions + $record->tax_amount + $record->insurance_amount + $record->advance_payments, 0, ',', '.') }}</td>
                                    <td class="font-semibold text-success">{{ number_format($record->final_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            switch($record->status) {
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
                                                    $statusText = $record->status;
                                            }
                                        @endphp
                                        
                                        <span class="badge badge-sm badge-outline badge-{{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="menu" data-menu="true">
                                            <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical"></i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <button class="menu-link" onclick="viewSalaryDetail({{ $record->id }})">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-search-list"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Xem chi tiết
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $salaryRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Yêu cầu tạm ứng -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="request-advance-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Yêu cầu tạm ứng lương
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="advance-request-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Số tiền cần ứng <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="number" name="amount" placeholder="Nhập số tiền cần ứng" min="100000" required>
                </div>
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Lý do <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="reason" rows="3" placeholder="Nhập lý do tạm ứng" required></textarea>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chi tiết lương -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="salary-detail-modal" style="z-index: 90;">
    <div class="modal-content max-w-[700px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chi tiết bảng lương
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="salary-detail-content" class="grid gap-5 px-0 py-5">
                <!-- Nội dung chi tiết lương sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Khởi tạo flatpickr cho bộ lọc năm
        flatpickrMake($("#year-filter"), 'year');
        
        // Xử lý khi thay đổi năm
        $("#year-filter").on('change', function() {
            const selectedYear = $(this).val();
            if (selectedYear) {
                window.location.href = "{{ route('dashboard.profile.my-salary') }}?year=" + selectedYear;
            }
        });
        
        // Xử lý form yêu cầu tạm ứng
        $("#advance-request-form").on('submit', async function(e) {
            e.preventDefault();
            
            const amount = $(this).find('input[name="amount"]').val();
            const reason = $(this).find('textarea[name="reason"]').val();
            
            try {
                const res = await axiosTemplate('post', '/account/salary/create-advance', null, {
                    amount: amount,
                    reason: reason,
                    request_date: '{{ date("d-m-Y") }}'
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#request-advance-modal')).hide();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi gửi yêu cầu tạm ứng');
                console.error(error);
            }
        });
    });
    
    // Hàm xem chi tiết lương
    async function viewSalaryDetail(id) {
        try {
            // Hiển thị modal
            KTModal.getInstance(document.querySelector('#salary-detail-modal')).show();
            
            // Load nội dung chi tiết
            const content = `
                <div class="flex justify-center items-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `;
            $('#salary-detail-content').html(content);
            
            // Gọi API để lấy thông tin chi tiết
            const res = await axiosTemplate('get', `/profile/my-salary/${id}/detail`, null, null);
            
            if (res.data.status === 200) {
                $('#salary-detail-content').html(res.data.content);
            } else {
                $('#salary-detail-content').html('<div class="text-center text-red-500">Không thể tải thông tin chi tiết</div>');
            }
        } catch (error) {
            $('#salary-detail-content').html('<div class="text-center text-red-500">Đã xảy ra lỗi khi tải thông tin</div>');
            console.error(error);
        }
    }
</script>
@endpush