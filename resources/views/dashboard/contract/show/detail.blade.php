{{-- resources/views/dashboard/contracts/show.blade.php --}}
@extends('dashboard.layouts.layout')

@section('dashboard_content')
@php
use Carbon\Carbon;
$now = Carbon::now();
$canEdit = ($details['status'] == 0 || $details['status'] == 1);
@endphp

<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5">
                    <h1 class="font-semibold text-base text-gray-900">
                        Thông tin hợp đồng #{{ $details['contract_number'] }}
                    </h1>
                    @switch($details['status'])
                    @case(1)
                    <span class="badge badge-sm badge-outline badge-primary">
                        Đang triển khai
                    </span>
                    @break
                    @case(2)
                    <span class="badge badge-sm badge-outline badge-success">
                        Đã hoàn thành
                    </span>
                    @break
                    @case(3)
                    <span class="badge badge-sm badge-outline badge-danger">
                        Đã huỷ
                    </span>
                    @break
                    @default
                    <span class="badge badge-sm badge-outline badge-warning">
                        Đang chờ
                    </span>
                    @endswitch
                </div>
                <div class="form-info text-gray-800 font-normal">
                    Được thêm bởi <b>{{$details['creator']['name']}}</b> khoảng <b>{{timeAgo(strtotime($details['created_at']))}}</b> trước.
                    @if ($details['updated_at'] != $details['created_at'])
                    Đã cập nhật <b>{{timeAgo(strtotime($details['updated_at']))}}</b> trước.
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
            @push("actions")
            <div class="flex flex-wrap items-center gap-3">
                @if ($details['status'] == 0)
                <button type="button" class="btn btn-outline btn-success px-5 py-2 flex items-center gap-2" onclick="saveCreateTaskContract({{$details['id']}})">
                    <i class="ki-filled ki-plus text-white"></i>
                    <span>Tạo công việc</span>
                </button>
                @endif

                @if ($details['status'] == 0 || $details['status'] == 1)
                <button type="button" class="btn btn-outline btn-primary px-5 py-2 flex items-center gap-2" onclick="saveCreateContract({{$details['id']}})">
                    <i class="ki-filled ki-check text-white"></i>
                    <span>Cập nhật dịch vụ</span>
                </button>
                @endif

                @if ($details['status'] != 3)
                <a href="{{ route('dashboard.contract.export-pdf', $details['id']) }}" class="btn btn-light px-5 py-2 flex items-center gap-2">
                    <i class="ki-filled ki-file-down text-gray-700"></i>
                    <span>Xuất PDF</span>
                </a>

                <button type="button" class="btn btn-outline btn-danger px-5 py-2 flex items-center gap-2" onclick="saveCancelContract({{$details['id']}})">
                    <i class="ki-filled ki-cross text-white"></i>
                    <span>Huỷ hợp đồng</span>
                </button>
                @endif
            </div>
            @endpush
        </div>
    </div>
</div>

<div class="container-fixed">
    {{-- Phần tổng hợp giá trị hợp đồng --}}
    <div class="card shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="card-header bg-white border-b border-gray-100">
            <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                <i class="ki-filled ki-dollar text-green-500"></i>
                Tổng hợp giá trị hợp đồng
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:!grid-cols-5 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Tổng giá trị</div>
                    <div class="text-lg font-medium text-gray-800">{{ number_format($details['payment_summary']['total_value']) }} ₫</div>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="text-sm text-green-600 mb-1">Đã thanh toán</div>
                    <div class="text-lg font-medium text-green-600">{{ number_format($details['payment_summary']['total_paid']) }} ₫</div>
                </div>
                <div class="p-4 bg-orange-50 rounded-lg">
                    <div class="text-sm text-orange-600 mb-1">Còn phải thanh toán</div>
                    <div class="text-lg font-medium text-orange-600">{{ number_format($details['payment_summary']['total_remaining']) }} ₫</div>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <div class="text-sm text-red-600 mb-1">Đã hoàn tiền</div>
                    <div class="text-lg font-medium text-red-600">{{ number_format($details['payment_summary']['total_deduction']) }} ₫</div>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="text-sm text-blue-600 mb-1">Thanh toán thừa</div>
                    <div class="text-lg font-medium text-blue-800">{{ number_format($details['payment_summary']['total_excess']) }} ₫</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-5">
        <div class="flex items-center flex-wrap md:flex-nowrap lg:items-end justify-between border-b border-b-gray-200 dark:border-b-coal-100 gap-3">
            <div class="grid">
                <div class="scrollable-x-auto">
                    <div class="tabs gap-6" data-tabs="true">
                        <div class="tab cursor-pointer active" data-tab-toggle="#tab-info">
                            <span class="text-nowrap text-sm">
                                Thông tin chung
                            </span>
                        </div>
                        <div class="tab cursor-pointer" data-tab-toggle="#tab-services">
                            <span class="text-nowrap text-sm">
                                Dịch vụ
                            </span>
                        </div>
                        <div class="tab cursor-pointer" data-tab-toggle="#tab-payments">
                            <span class="text-nowrap text-sm">
                                Thanh toán
                            </span>
                        </div>
                        <div class="tab cursor-pointer" data-tab-toggle="#tab-tasks">
                            <span class="text-nowrap text-sm">
                                Công việc
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div></div>
        </div>

        {{-- Tab Thông tin chung --}}
        @include('dashboard.contract.show.info', [
        'details' => $details,
        'canEdit' => $canEdit
        ])

        {{-- Tab Dịch vụ --}}
        @include('dashboard.contract.show.services', [
        'details' => $details,
        'data_init' => $data_init,
        'canEdit' => $canEdit
        ])

        {{-- Tab Thanh toán --}}
        @include('dashboard.contract.show.payments', [
        'details' => $details,
        'data_init' => $data_init
        ])

        {{-- Tab Công việc (thêm phần này) --}}
        @include('dashboard.contract.show.tasks', [
        'details' => $details
        ])
    </div>
</div>

{{-- Modals --}}
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-contract-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Cập nhật thông tin</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <input class="input hidden" name="name" type="text" placeholder="Tên hợp đồng mới">
                    <select name="status" class="select hidden">
                        <option value="" selected>Chọn trạng thái</option>
                        <option value="0">Chờ duyệt</option>
                        <option value="1">Đang triển khai</option>
                        <option value="2">Đã hoàn thành</option>
                    </select>
                    <select name="user_id" class="select hidden">
                        <option value="" selected>Chọn nhân viên</option>
                        @foreach ($data_init['users'] as $user)
                        <option value="{{$user['id']}}">{{$user['name']}}</option>
                        @endforeach
                    </select>
                    <select name="provider_id" class="select hidden">
                        <option value="" selected>Chọn khách hàng</option>
                        @foreach ($data_init['customers'] as $customer)
                        <option value="{{$customer['id']}}">{{$customer['name']}}</option>
                        @endforeach
                    </select>
                    <input class="input hidden" name="sign_date" type="text" placeholder="Ngày ký (DD/MM/YYYY)">
                    <input class="input hidden" name="effective_date" type="text" placeholder="Ngày hiệu lực (DD/MM/YYYY)">
                    <input class="input hidden" name="expiry_date" type="text" placeholder="Ngày hết hạn (DD/MM/YYYY)">
                    <textarea class="textarea hidden" name="note" rows="5" placeholder="Ghi chú"></textarea>
                    <textarea class="textarea hidden" name="terms_and_conditions" rows="5" placeholder="Điều khoản chung"></textarea>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Xong</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-payment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm biên nhận mới</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="add-payment-form" class="grid gap-5 px-0 py-5">
                <!-- Nội dung form thêm biên nhận -->
                @include('dashboard.contract.show.payment-form', [
                'details' => $details,
                'data_init' => $data_init,
                'formId' => 'add-payment-form'
                ])
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-payment-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Chỉnh sửa biên nhận</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-payment-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="edit-payment-id">
                <!-- Nội dung form sửa biên nhận -->
                @include('dashboard.contract.show.payment-form', [
                'details' => $details,
                'data_init' => $data_init,
                'formId' => 'edit-payment-form'
                ])
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const details = @json($data_init);

    $(function() {
        $('button[data-modal-toggle="#update-contract-modal"][data-name]').on('click', function() {
            let _this = $(this);
            let _modal = $('#update-contract-modal');
            _modal.find('input[name], select[name], textarea[name]').val('').addClass('hidden');
            _modal.find(`input[name=${_this.attr('data-name')}], select[name=${_this.attr('data-name')}], textarea[name=${_this.attr('data-name')}]`).removeClass('hidden');
        });

        $('#update-contract-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateContract();
        });

        flatpickrMake($("input[name=effective_date], input[name=expiry_date]"), 'datetime');
    });

    async function postUpdateContract() {
        let field = $('#update-contract-modal form').find('input:not(.hidden),select:not(.hidden),textarea:not(.hidden)');
        let method = "post",
            url = "/contract/update-info",
            params = null,
            data = {
                id: "{{$details['id']}}",
                [field.attr('name')]: field.val()
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function saveCreateTaskContract(id) {
        Notiflix.Confirm.show(
            'Tạo công việc',
            'Bạn có chắc chắn muốn tạo công việc cho hợp đồng này? Sau khi tạo sẽ không thể sửa đổi',
            'Đúng',
            'Hủy',
            async () => {
                    let method = "post",
                        url = "/contract/create-task",
                        params = null,
                        data = {
                            id
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

    async function saveCancelContract(id) {
        Notiflix.Confirm.show(
            'Huỷ hợp đồng',
            'Bạn có chắc chắn muốn huỷ hợp đồng? Sau khi huỷ không thể hoàn tác.',
            'Đúng',
            'Hủy',
            async () => {
                    let method = "post",
                        url = "/contract/cancel",
                        params = null,
                        data = {
                            id
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