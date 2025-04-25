@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Chăm sóc khách hàng
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
<div class="grid !grid-cols-1 lg:!grid-cols-4 gap-4 mb-5">
    <div class="card bg-white shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center">
                <div class="rounded-full bg-primary-100 p-3 mr-4">
                    <i class="ki-filled ki-profile-user text-primary text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Tổng số khách hàng</p>
                    <h3 class="text-2xl font-bold">{{ $statistics['total_customers'] }}</h3>
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
                    <p class="text-gray-500 text-sm">Lịch hẹn sắp tới</p>
                    <h3 class="text-2xl font-bold">{{ $statistics['upcoming_appointments'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card bg-white shadow-sm">
        <div class="card-body p-4">
            <div class="flex items-center">
                <div class="rounded-full bg-warning-100 p-3 mr-4">
                    <i class="ki-filled ki-chart text-warning text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Đang tư vấn</p>
                    <h3 class="text-2xl font-bold">{{ $statistics['active_consultations'] }}</h3>
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
                    <p class="text-gray-500 text-sm">Cần liên hệ lại</p>
                    <h3 class="text-2xl font-bold">{{ $statistics['due_follow_ups'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách khách hàng
                </h3>

                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap lg:justify-end gap-2">
                            <select data-filter="services" class="select select-sm w-40">
                                <option value="" selected>
                                    Theo dịch vụ
                                </option>
                                @foreach ($services as $service)
                                <option value="{{$service['id']}}">
                                    {{$service['name']}}
                                </option>
                                @endforeach
                            </select>
                            <select data-filter="status_id" class="select select-sm w-40">
                                <option value="" selected>
                                    Theo trạng thái
                                </option>
                                @foreach ($statuses as $status)
                                <option value="{{$status['id']}}">
                                    {{$status['name']}}
                                </option>
                                @endforeach
                            </select>
                            <select data-filter="class_id" class="select select-sm w-40">
                                <option value="" selected>
                                    Theo đối tượng
                                </option>
                                @foreach ($classes as $class_item)
                                <option value="{{$class_item['id']}}">
                                    {{$class_item['name']}}
                                </option>
                                @endforeach
                            </select>
                            <select data-filter="interaction" class="select select-sm w-40">
                                <option value="" selected>Tương tác gần đây</option>
                                <option value="recent">7 ngày qua</option>
                                <option value="medium">8-30 ngày qua</option>
                                <option value="old">Trên 30 ngày</option>
                                <option value="none">Chưa tương tác</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap lg:justify-end gap-2">
                            <div class="flex">
                                <label class="switch switch-sm">
                                    <span class="switch-label">
                                        Khách hàng của tôi
                                    </span>
                                    <input data-filter="my_customer" type="checkbox" value="1">
                                </label>
                            </div>
                            <div class="flex">
                                <label class="switch switch-sm">
                                    <span class="switch-label">
                                        Danh sách đen
                                    </span>
                                    <input data-filter="black_list" type="checkbox" value="1">
                                </label>
                            </div>
                            <div class="relative">
                                <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                                </i>
                                <input class="input input-sm pl-8" id="search-input" data-filter="search" placeholder="Tìm kiếm" type="text">
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="/customer/create-view" class="btn btn-primary btn-sm">
                            Thêm khách hàng
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="scrollable-x-auto">
                        <table class="table table-border">
                            <thead>
                                <tr>
                                    <th class="text-gray-700 font-normal w-[100px]">
                                        STT
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Trạng thái
                                            </span>
                                        </span>
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[250px]">
                                        Khách hàng
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[200px]">
                                        Liên hệ
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[220px]">
                                        Khách Quan Tâm
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[200px]">
                                        Nhân viên CSKH
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[220px]">
                                        Thông tin CTY
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[200px]">
                                        Lần tương tác gần nhất
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[200px]">
                                    Trạng thái tư vấn
                                    </th>
                                    <th class="w-[60px]">
                                    </th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/customer-support-data'])
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
@endsection
@push('scripts')
<script>
    $(function() {
        $(document).on('click', '.black-list-customer-btn', function() {
            let _this = $(this);
            Notiflix.Confirm.show(
                _this.attr('data-active') == 1 ? 'Cho vào danh sách đen' : 'Gỡ khỏi danh sách đen',
                _this.attr('data-active') == 1 ? 'Bạn muốn chuyển khách hàng này vào danh sách đen?' : 'Bạn muốn gỡ khách hàng ày khỏi danh sách đen?',
                'Đúng',
                'Huỷ',
                () => {
                    postBlackListCustomer($(this).attr('data-id'), _this);
                },
                () => {}, {},
            );
        })
    })

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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                break;
        }
    }
</script>
@endpush