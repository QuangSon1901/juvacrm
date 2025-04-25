@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Khách hàng tiềm năng
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
    <!-- Thêm thống kê tổng quan -->
    <div class="grid !grid-cols-1 lg:!grid-cols-4 gap-4 mb-5">
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-primary-100 p-3 mr-4">
                        <i class="ki-filled ki-profile-user text-primary text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Tổng số KH tiềm năng</p>
                        <h3 class="text-2xl font-bold">{{ $statistics['total'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-success-100 p-3 mr-4">
                        <i class="ki-filled ki-calendar-add text-success text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">KH mới hôm nay</p>
                        <h3 class="text-2xl font-bold">{{ $statistics['new_today'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-warning-100 p-3 mr-4">
                        <i class="ki-filled ki-star text-warning text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">KH tiềm năng cao</p>
                        <h3 class="text-2xl font-bold">{{ $statistics['high_potential'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card bg-white shadow-sm">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-danger-100 p-3 mr-4">
                        <i class="ki-filled ki-time text-danger text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Chưa tương tác</p>
                        <h3 class="text-2xl font-bold">{{ $statistics['no_interaction'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-5">
        {{--<div class="card bg-white shadow-sm mb-5">
            <div class="card-header">
                <h3 class="card-title">Phân tích nguồn khách hàng tiềm năng</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-lg font-medium mb-2">Theo nguồn</h4>
                        <div class="h-48" id="leads-by-source-chart">
                            <!-- Đây sẽ là biểu đồ hiển thị lead theo nguồn -->
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium mb-2">Tỷ lệ chuyển đổi</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col items-center p-4 border rounded-lg">
                                <span class="text-sm text-gray-500">Lead → Khách hàng</span>
                                <span class="text-2xl font-bold text-primary">{{ number_format($conversion_stats['lead_to_customer'], 1) }}%</span>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                    <div class="bg-primary h-2.5 rounded-full" style="width: {{ min($conversion_stats['lead_to_customer'], 100) }}%"></div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center p-4 border rounded-lg">
                                <span class="text-sm text-gray-500">Tỷ lệ phản hồi</span>
                                <span class="text-2xl font-bold text-success">{{ number_format($conversion_stats['response_rate'], 1) }}%</span>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                    <div class="bg-success h-2.5 rounded-full" style="width: {{ min($conversion_stats['response_rate'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>--}}
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách khách hàng tiềm năng
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap lg:justify-end gap-2">
                            <!-- Thêm bộ lọc mới -->
                            <select data-filter="source_id" class="select select-sm w-40">
                                <option value="" selected>
                                    Nguồn
                                </option>
                                @foreach ($sources as $source)
                                <option value="{{$source['id']}}">
                                    {{$source['name']}}
                                </option>
                                @endforeach
                            </select>
                            
                            <select data-filter="status_id" class="select select-sm w-40">
                                <option value="" selected>
                                    Trạng thái
                                </option>
                                @foreach ($statuses as $status)
                                <option value="{{$status['id']}}">
                                    {{$status['name']}}
                                </option>
                                @endforeach
                            </select>
                            
                            <select data-filter="lead_score" class="select select-sm w-40">
                                <option value="" selected>
                                    Điểm tiềm năng
                                </option>
                                <option value="1">Thấp (1-30)</option>
                                <option value="2">Trung bình (31-60)</option>
                                <option value="3">Cao (>60)</option>
                            </select>
                            
                            <select data-filter="sort_by" class="select select-sm w-40">
                                <option value="" selected>
                                    Sắp xếp theo
                                </option>
                                <option value="created_at">Mới nhất</option>
                                <option value="lead_score">Điểm tiềm năng</option>
                                <option value="last_interaction">Tương tác gần đây</option>
                            </select>
                        </div>
                        
                        <div class="flex flex-wrap lg:justify-end gap-2">
                            <div class="hidden">
                                <label class="switch switch-sm">
                                    <span class="switch-label">
                                        Khách hàng của tôi
                                    </span>
                                    <input data-filter="my_customer" type="checkbox" value="1">
                                </label>
                            </div>
                            <div class="hidden">
                                <label class="switch switch-sm">
                                    <span class="switch-label">
                                        Danh sách đen
                                    </span>
                                    <input data-filter="black_list" type="checkbox" value="1">
                                </label>
                            </div>
                            <input data-filter="lead" type="checkbox" value="0" class="hidden" checked>
                            <div class="relative">
                                <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                                </i>
                                <input class="input input-sm pl-8" id="search-input" data-filter="search" placeholder="Tìm kiếm" type="text">
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="/customer/create-view" class="btn btn-primary btn-sm">
                            <i class="ki-filled ki-plus"></i> Thêm khách hàng tiềm năng
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="true" data-datatable-page-size="10" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true" id="clients_table">
                            <thead>
                                <tr>
                                    <th class="text-gray-700 font-normal w-[70px]">
                                        #
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[220px]">
                                        Họ tên
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[200px]">
                                        Liên hệ
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[180px]">
                                        Dịch vụ quan tâm
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[150px]">
                                        Nhân viên CSKH
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[100px]">
                                        Công ty
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[100px]">
                                        Điểm tiềm năng
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[120px]">
                                        Tương tác gần nhất
                                    </th>
                                    <th class="w-[60px]">
                                    </th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/customer-leads-data'])
                        </table>
                    </div>
                    <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                        <div class="flex items-center gap-2 order-2 md:order-1">
                            Hiển thị {{TABLE_PERPAGE_NUM}} mỗi trang
                        </div>
                        <div class="flex items-center gap-4 order-1 md:order-2">
                            <p><span class="sorterlow"></span> - <span class="sorterhigh"></span> trong <span class="sorterrecords"></span></p>
                            <div class="pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="convert-lead-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
            Chuyển đổi khách hàng tiềm năng
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body scrollable-y max-h-[95%]">
            <p class="mb-4">Bạn đang chuyển đổi khách hàng tiềm năng thành khách hàng chính thức. Thao tác này sẽ thay đổi trạng thái khách hàng và cho phép thực hiện các tương tác như tạo hợp đồng.</p>
            
            <div class="form-group mb-4">
                <label class="form-label mb-2">Trạng thái khách hàng</label>
                <select class="select w-full" id="lead-status-select">
                    @foreach ($statuses as $status)
                    <option value="{{$status['id']}}">{{$status['name']}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mb-4">
                <label class="form-label mb-2">Ghi chú</label>
                <textarea class="textarea w-full" id="lead-convert-note" placeholder="Ghi chú về lý do chuyển đổi"></textarea>
            </div>
            
            <input type="hidden" id="lead-id-to-convert">
            <div class="flex gap-4">
                <button class="btn btn-light" data-modal-dismiss="true">Huỷ</button>
                <button class="btn btn-primary" id="convert-lead-confirm-btn">Xác nhận chuyển đổi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $(document).on('click', '.convert-lead-btn', function() {
            let customerId = $(this).data('id');
            $('#lead-id-to-convert').val(customerId);
            KTModal.getInstance(document.querySelector('#convert-lead-modal')).show();
        });
        
        $('#convert-lead-confirm-btn').on('click', function() {
            convertLeadToProspect();
        });
        
        $(document).on('click', '.black-list-customer-btn', function() {
            let _this = $(this);
            Notiflix.Confirm.show(
                _this.attr('data-active') == 1 ? 'Cho vào danh sách đen' : 'Gỡ khỏi danh sách đen',
                _this.attr('data-active') == 1 ? 'Bạn muốn chuyển khách hàng này vào danh sách đen?' : 'Bạn muốn gỡ khách hàng này khỏi danh sách đen?',
                'Đúng',
                'Huỷ',
                () => {
                    postBlackListCustomer($(this).attr('data-id'), _this);
                },
                () => {}, {},
            );
        });
    });
    
    async function convertLeadToProspect() {
        const customerId = $('#lead-id-to-convert').val();
        const statusId = $('#lead-status-select').val();
        const note = $('#lead-convert-note').val();
        
        let method = "post",
            url = "/customer-leads/convert-to-prospect",
            params = null,
            data = {
                id: customerId,
                status_id: statusId,
                note: note
            };
            
        let res = await axiosTemplate(method, url, params, data);
        
        if (res.data.status === 200) {
            showAlert('success', res.data.message);
            KTModal.getInstance(document.querySelector('#convert-lead-modal')).hide();
            // Reload the table
            callAjaxDataTable($('.updater'));
        } else {
            showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
        }
    }

    async function postBlackListCustomer(id, _this) {
        let method = "post",
            url = "/customer/black-list",
            params = null,
            data = {
                id
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message)
                callAjaxDataTable(_this.closest('.card').find('.updater'));
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!")
                break;
        }
    }
</script>
<script>
    $(function() {
    // Tạo biểu đồ nguồn lead nếu có
    if (document.getElementById('leads-by-source-chart')) {
        // Code để tạo biểu đồ với Chart.js hoặc thư viện khác
        // Có thể sử dụng ApexCharts hoặc Chart.js
        
        // Ví dụ giả định nếu dùng ApexCharts
        var options = {
            series: [{
                name: 'Số lượng lead',
                data: [31, 40, 28, 51, 42, 15]
            }],
            chart: {
                height: 250,
                type: 'bar',
            },
            colors: ['#3b82f6'],
            xaxis: {
                categories: ['Facebook', 'Google', 'Website', 'Referral', 'Event', 'Other'],
            }
        };
        
        if (typeof ApexCharts !== 'undefined') {
            var chart = new ApexCharts(document.getElementById('leads-by-source-chart'), options);
            chart.render();
        }
    }
});
</script>
@endpush