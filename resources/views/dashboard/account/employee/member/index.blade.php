@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Nhân sự
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
    <div class="grid gap-5 lg:gap-7.5">
        <div class="card card-grid min-w-full">
            <div class="card-header py-5 flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách nhân viên
                </h3>
                <div class="flex flex-wrap gap-2">
                    <label class="switch switch-sm">
                        <input class="order-2" id="filter-active" data-filter="is_active" name="check" type="checkbox" value="1">
                        <span class="switch-label order-1">
                            Đang hoạt động
                        </span>
                    </label>
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                        </i>
                        <input class="input input-sm pl-8" id="search-input" data-filter="search" placeholder="Tìm kiếm" type="text">
                    </div>
                    <a href="/member/create-view" class="btn btn-primary btn-sm">
                        Thêm nhân viên
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" id="members-table">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">
                                        STT
                                    </th>
                                    <th class="min-w-[300px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Họ tên
                                            </span>
                                        </span>
                                    </th>
                                    <th class="text-gray-700 font-normal min-w-[220px]">
                                        Phòng ban
                                    </th>
                                    <th class="min-w-[165px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Liên hệ
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[165px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Trạng thái
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[165px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Tham gia
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]">
                                    </th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/member/data'])
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
        $(document).on('click', '.reset-password-btn', function() {
            Notiflix.Confirm.show(
                'Đặt lại mật khẩu',
                'Bạn muốn đặt lại mật khẩu cho tài khoản này?',
                'Đúng',
                'Huỷ',
                () => {
                    postResetPasswordUser($(this).attr('data-id'));
                },
                () => {}, {},
            );
        })

        $(document).on('click', '.lock-account-btn', function() {
            let _this = $(this);
            Notiflix.Confirm.show(
                'Khoá tài khoản',
                'Bạn muốn khoá tài khoản này?',
                'Đúng',
                'Huỷ',
                () => {
                    postLockAccountUser($(this).attr('data-id'), _this);
                },
                () => {}, {},
            );
        })
    })

    async function postResetPasswordUser(id) {
        let method = "post",
            url = "/member/reset-password",
            params = null,
            data = {
                id
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                Notiflix.Report.success(res.data.message, 'Mật khẩu mới: ' + res.data.data.password, 'Okay');
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                break;
        }
    }

    async function postLockAccountUser(id, _this) {
        let method = "post",
            url = "/member/lock-account",
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