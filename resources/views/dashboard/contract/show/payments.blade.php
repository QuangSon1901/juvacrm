{{-- resources/views/dashboard/contracts/tabs/payments.blade.php --}}
<div class="hidden transition-opacity duration-700" id="tab-payments">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="col-span-1 xl:!col-span-3">
            <div class="card shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="card-header bg-white border-b border-gray-100">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-dollar text-green-500"></i>
                        Tổng hợp giá trị dịch vụ
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
                        Biên nhận thanh toán
                    </h3>
                    <button class="btn btn-light btn-xs" data-modal-toggle="#add-payment-modal">
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
                                    <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Loại phiếu</th>
                                    <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Giá tiền</th>
                                    <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Phương thức</th>
                                    <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Ngày</th>
                                    <th class="text-start font-medium text-gray-700 min-w-20 !px-1">Trạng thái</th>
                                    <th class="min-w-16 !px-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details['payments'] as $payment)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="!px-1 min-w-20">
                                        <span class="!text-xs text-gray-800">{{ $payment['name'] }}</span>
                                    </td>
                                    <td class="!px-1 min-w-20">
                                        <span class="!text-xs text-gray-800">{{ $payment['payment_stage_text'] }}</span>
                                    </td>
                                    <td class="!px-1 min-w-20">
                                        <span class="!text-xs text-gray-800">{{number_format($payment['price'], 0, ',', '.')}} {{ $payment['currency']['code'] }}</span>
                                    </td>
                                    <td class="!px-1 min-w-20">
                                        <span class="!text-xs text-gray-800">{{ $payment['method']['name'] }}</span>
                                    </td>
                                    <td class="!px-1 min-w-20">
                                        <span class="!text-xs text-gray-800">{{ $payment['due_date_formatted'] }}</span>
                                    </td>
                                    <td class="!px-1 min-w-20">
                                        @if($payment['status'] == 1)
                                        <span class="badge badge-sm badge-outline badge-success">{{ $payment['status_text'] }}</span>
                                        @else
                                        <span class="badge badge-sm badge-outline badge-warning">{{ $payment['status_text'] }}</span>
                                        @endif
                                    </td>
                                    <td class="!px-1 ">
                                        <div class="menu" data-menu="true">
                                            @if ($payment['status'] == 0)
                                            <button class="btn btn-sm btn-icon btn-light btn-clear" onclick="paymentAccept({{$payment['id']}})" data-tooltip="#tooltip_payment" data-tooltip-trigger="hover"> 
                                                <i class="ki-filled ki-dollar"></i>
                                            </button>
                                            <div class="tooltip" id="tooltip_payment">
                                                Xác nhận đã thanh toán
                                            </div>
                                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical"></i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <button class="menu-link" data-modal-toggle="#edit-payment-modal" onclick="loadPaymentData({{$payment['id']}})">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-pencil"></i>
                                                            </span>
                                                            <span class="menu-title">Chỉnh sửa</span>
                                                        </button>
                                                    </div>
                                                    <div class="menu-separator"></div>
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="{{ route('dashboard.accounting.deposit-receipt.export-pdf', $details['id']) }}">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-file-down"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Xuất PDF
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="menu-item">
                                                        <button class="menu-link" onclick="cancelPayment({{$payment['id']}})">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-trash"></i>
                                                            </span>
                                                            <span class="menu-title">Hủy</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                    <i class="ki-filled ki-dots-vertical"></i>
                                                </button>
                                                <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="{{ route('dashboard.accounting.deposit-receipt.export-pdf', $payment['id']) }}">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-file-down"></i>
                                                            </span>
                                                            <span class="menu-title">
                                                                Xuất PDF
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // resources/js/dashboard/contracts/payments.js
    $(function() {
        // Thêm biên nhận
        $('#add-payment-form').on('submit', function(e) {
            e.preventDefault();
            postAddPayment();
        });

        // Chỉnh sửa biên nhận
        $('#edit-payment-form').on('submit', function(e) {
            e.preventDefault();
            postUpdatePayment();
        });

        // Thiết lập flatpickr cho các input ngày
        flatpickrMake($('input[name="due_date"]'), 'datetime');
    });

    // Load dữ liệu biên nhận vào modal chỉnh sửa
    function loadPaymentData(paymentId) {
        const payment = @json($details['payments']).find(p => p.id === paymentId);

        if (payment) {
            $('#edit-payment-id').val(payment.id);
            $('#edit-payment-form [name=name]').val(payment.name);
            $('#edit-payment-form [name=percentage]').val(payment.percentage);
            $('#edit-payment-form [name=price]').val(payment.price);
            $('#edit-payment-form [name=currency_id]').val(payment.currency.id);
            $('#edit-payment-form [name=method_id]').val(payment.method.id);
            $('#edit-payment-form [name=due_date]').val(payment.due_date_formatted);
            $('#edit-payment-form [name=status]').prop('checked', payment.status == 1);
        }
    }

    // Tính toán số tiền dựa trên phần trăm
    function calculatePaymentAmount() {
        const percentage = parseFloat($('#add-payment-form [name=percentage]').val()) || 0;
        const totalValue = parseFloat({{$details["total_value"]}}) || 0;

        if (percentage > 0 && totalValue > 0) {
            const amount = (percentage / 100) * totalValue;
            $('#add-payment-form [name=price]').val(Math.round(amount));
        }

        // Cũng áp dụng cho form chỉnh sửa
        const editPercentage = parseFloat($('#edit-payment-form [name=percentage]').val()) || 0;
        if (editPercentage > 0 && totalValue > 0) {
            const editAmount = (editPercentage / 100) * totalValue;
            $('#edit-payment-form [name=price]').val(Math.round(editAmount));
        }
    }

    // Thêm biên nhận
    async function postAddPayment() {
        let method = "post",
            url = "/contract/add-payment",
            params = null,
            data = $('#add-payment-form').serialize();

        try {
            let res = await axiosTemplate(method, url, params, data);
            switch (res.data.status) {
                case 200:
                    showAlert('success', res.data.message);
                    window.location.reload();
                    break;
                default:
                    showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                    break;
            }
        } catch (error) {
            showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
            console.error(error);
        }
    }

    // Cập nhật biên nhận
    async function postUpdatePayment() {
        let method = "post",
            url = "/contract/update-payment",
            params = null,
            data = $('#edit-payment-form').serialize();

        try {
            let res = await axiosTemplate(method, url, params, data);
            switch (res.data.status) {
                case 200:
                    showAlert('success', res.data.message);
                    window.location.reload();
                    break;
                default:
                    showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                    break;
            }
        } catch (error) {
            showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
            console.error(error);
        }
    }

    async function paymentAccept(paymentId) {
        Notiflix.Confirm.show(
            'Xác nhận thanh toán',
            'Bạn có chắc chắn muốn xác nhận đã thanh toán biên nhận này?',
            'Đúng',
            'Hủy',
            async () => {
                let method = "post",
                    url = "/contract/update-payment",
                    params = null,
                    data = {
                        id: paymentId, 
                        status: 1
                    };

                try {
                    let res = await axiosTemplate(method, url, params, data);
                    switch (res.data.status) {
                        case 200:
                            showAlert('success', 'Xác nhận thanh toán thành công');
                            window.location.reload();
                            break;
                        default:
                            showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                            break;
                    }
                } catch (error) {
                    showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                    console.error(error);
                }
                },
                () => {}, {}
        );
    }

    // Hủy biên nhận
    async function cancelPayment(paymentId) {
        Notiflix.Confirm.show(
            'Hủy biên nhận',
            'Bạn có chắc chắn muốn hủy biên nhận này?',
            'Đúng',
            'Hủy',
            async () => {
                    let method = "post",
                        url = "/contract/cancel-payment",
                        params = null,
                        data = {
                            id: paymentId
                        };
                    try {
                        let res = await axiosTemplate(method, url, params, data);
                        switch (res.data.status) {
                            case 200:
                                showAlert('success', res.data.message);
                                window.location.reload();
                                break;
                            default:
                                showAlert('warning', res?.data?.message || "Đã có lỗi xảy ra!");
                                break;
                        }
                    } catch (error) {
                        showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
                        console.error(error);
                    }
                },
                () => {}, {}
        );
    }
</script>
@endpush