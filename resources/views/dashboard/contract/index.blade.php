@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý hợp đồng
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
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách hợp đồng
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex">
                        <label class="switch switch-sm">
                            <span class="switch-label">
                                Hợp đồng của tôi
                            </span>
                            <input name="check" data-filter="my_contract" type="checkbox" value="1">
                        </label>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        <select data-filter="status" class="select select-sm w-40">
                            <option value="" selected>
                                Tất cả trạng thái
                            </option>
                            <option value="0">Đang chờ</option>
                            <option value="1">Đang triển khai</option>
                            <option value="2">Đã kết thúc</option>
                            <option value="3">Đã huỷ</option>
                        </select>
                        <a href="/contract/create-view" class="btn btn-primary btn-sm">
                            Thêm hợp đồng
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="contracts_table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[50px]">
                                        <span class="sort">
                                            <span class="sort-label">STT</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[250px]">
                                        <span class="sort">
                                            <span class="sort-label">Tên hợp đồng</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[160px]">
                                        <span class="sort">
                                            <span class="sort-label">Nhân viên</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[160px]">
                                        <span class="sort">
                                            <span class="sort-label">Khách hàng</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[140px]">
                                        <span class="sort">
                                            <span class="sort-label">Giá trị</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[140px]">
                                        <span class="sort">
                                            <span class="sort-label">Tiến độ</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[180px]">
                                        <span class="sort">
                                            <span class="sort-label">Thời gian</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/contract-data'])
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