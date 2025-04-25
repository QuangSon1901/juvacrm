@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Hồ sơ nhân viên
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
        <div class="col-span-2">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin cá nhân
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                <tr>
                                    <td class="py-2 min-w-28 text-gray-600 font-normal">
                                        Hình ảnh
                                    </td>
                                    <td class="py-2 text-gray700 font-normal min-w-32 text-2sm">
                                        150x150px JPEG, PNG Image
                                    </td>
                                    <td class="py-2 text-center">
                                        <div class="flex justify-center items-center">
                                            <div class="image-input size-16" data-image-input="true">
                                                <input accept=".png, .jpg, .jpeg" name="avatar" type="file">
                                                <input name="avatar_remove" type="hidden">
                                                <div class="btn btn-icon btn-icon-xs btn-light shadow-default absolute z-1 size-5 -top-0.5 -end-0.5 rounded-full" data-image-input-remove="" data-tooltip="#image_input_tooltip" data-tooltip-trigger="hover">
                                                    <i class="ki-filled ki-cross">
                                                    </i>
                                                </div>
                                                <span class="tooltip" id="image_input_tooltip">
                                                    Nhấn để xóa hoặc quay lại
                                                </span>
                                                <div class="image-input-placeholder rounded-full border-2 border-success image-input-empty:border-gray-300" style="background-image:url({{asset('assets/images/icons/blank.png')}})">
                                                    <div class="image-input-preview rounded-full" style="background-image:url({{asset('assets/images/logo/favicon.png')}})">
                                                    </div>
                                                    <div class="flex items-center justify-center cursor-pointer h-5 left-0 right-0 bottom-0 bg-dark-clarity absolute">
                                                        <svg class="fill-light opacity-80" height="12" viewBox="0 0 14 12" width="14" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M11.6665 2.64585H11.2232C11.0873 2.64749 10.9538 2.61053 10.8382 2.53928C10.7225 2.46803 10.6295 2.36541 10.5698 2.24335L10.0448 1.19918C9.91266 0.931853 9.70808 0.707007 9.45438 0.550249C9.20068 0.393491 8.90806 0.311121 8.60984 0.312517H5.38984C5.09162 0.311121 4.799 0.393491 4.5453 0.550249C4.2916 0.707007 4.08701 0.931853 3.95484 1.19918L3.42984 2.24335C3.37021 2.36541 3.27716 2.46803 3.1615 2.53928C3.04584 2.61053 2.91234 2.64749 2.7765 2.64585H2.33317C1.90772 2.64585 1.49969 2.81486 1.19885 3.1157C0.898014 3.41654 0.729004 3.82457 0.729004 4.25002V10.0834C0.729004 10.5088 0.898014 10.9168 1.19885 11.2177C1.49969 11.5185 1.90772 11.6875 2.33317 11.6875H11.6665C12.092 11.6875 12.5 11.5185 12.8008 11.2177C13.1017 10.9168 13.2707 10.5088 13.2707 10.0834V4.25002C13.2707 3.82457 13.1017 3.41654 12.8008 3.1157C12.5 2.81486 12.092 2.64585 11.6665 2.64585ZM6.99984 9.64585C6.39413 9.64585 5.80203 9.46624 5.2984 9.12973C4.79478 8.79321 4.40225 8.31492 4.17046 7.75532C3.93866 7.19572 3.87802 6.57995 3.99618 5.98589C4.11435 5.39182 4.40602 4.84613 4.83432 4.41784C5.26262 3.98954 5.80831 3.69786 6.40237 3.5797C6.99644 3.46153 7.61221 3.52218 8.1718 3.75397C8.7314 3.98576 9.2097 4.37829 9.54621 4.88192C9.88272 5.38554 10.0623 5.97765 10.0623 6.58335C10.0608 7.3951 9.73765 8.17317 9.16365 8.74716C8.58965 9.32116 7.81159 9.64431 6.99984 9.64585Z" fill="">
                                                            </path>
                                                            <path d="M7 8.77087C8.20812 8.77087 9.1875 7.7915 9.1875 6.58337C9.1875 5.37525 8.20812 4.39587 7 4.39587C5.79188 4.39587 4.8125 5.37525 4.8125 6.58337C4.8125 7.7915 5.79188 8.77087 7 8.77087Z" fill="">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Họ tên
                                    </td>
                                    <td class="py-2 text-gray-800 text-sm">
                                        {{$details['name']}}
                                    </td>
                                    <td class="py-2 text-center">
                                    @if(hasPermission('edit-member'))
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="name">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600 font-normal">
                                        Trạng thái
                                    </td>
                                    <td class="py-3 text-gray-800 font-normal">
                                        <span class="badge badge-sm badge-outline badge-{{$details['is_active'] ? 'success' : 'danger'}}">
                                            {{$details['is_active'] ? 'Đang hoạt động' : 'Đã khoá'}}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center"></td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600 font-normal">
                                        Ngày sinh
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{formatDateTime($details['birth_date'], 'd-m-Y', 'Y-m-d')}}
                                    </td>
                                    <td class="py-3 text-center">
                                    @if(hasPermission('edit-member'))
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="birth_date">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                        @endif
                                        </td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600 font-normal">
                                        Giới tính
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['gender'] == 0 ? 'Nam' : 'Nữ'}}
                                    </td>
                                    <td class="py-3 text-center">
                                    @if(hasPermission('edit-member'))
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="gender">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        CCCD/CMND
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['cccd'] ?? '---'}}
                                    </td>
                                    <td class="py-3 text-center">
                                    @if(hasPermission('edit-member'))
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="cccd">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Địa chỉ
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['address'] ?? '---'}}
                                    </td>
                                    <td class="py-3 text-center">
                                    @if(hasPermission('edit-member'))
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="address">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                        @endif
                                        </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Lương cơ bản
                                    </td>
                                    <td class="py-3 text-gray-700 text-2sm font-normal">
                                        {{formatCurrency($details['salary'] ?? 0)}}
                                    </td>
                                    <td class="py-3 text-center">
                                    @if(hasPermission('edit-member'))
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="salary">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                        @endif
                                        </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Ngày vào làm
                                    </td>
                                    <td class="py-3 text-gray-700 text-2sm font-normal">
                                        {{formatDateTime($details['created_at'], 'd-m-Y')}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin đăng nhập
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                <tr>
                                    <td class="min-w-56 text-gray-600 font-normal">
                                        Tài khoản
                                    </td>
                                    <td class="min-w-60 w-full">
                                        <span class="text-gray-800 text-sm font-normal">
                                            {{$details['username']}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex items-center">
                                            <button data-tooltip="Đặt lại mật khẩu" class="reset-password-btn btn btn-xs btn-icon btn-clear btn-primary" data-id="{{$details['id']}}">
                                                <i class="ki-filled ki-key">
                                                </i>
                                            </button>
                                            <button data-tooltip="{{$details['is_active'] ? 'Khoá tài khoản' : 'Mở khoá'}}" class="lock-account-btn btn btn-sm btn-icon btn-clear btn-danger" data-id="{{$details['id']}}">
                                                <i class="ki-filled ki-lock">
                                                </i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin liên hệ
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-2.5">
                            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                                <div class="flex items-center flex-wrap gap-3.5">
                                    <img alt="" class="size-6 shrink-0" src="{{asset('assets/images/icons/phone.webp')}}">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900 hover:text-primary-active mb-px">
                                            Số điện thoại
                                        </div>
                                        <div class="text-2sm text-gray-700 hover:text-primary-active">
                                            {{$details['phone'] ?? '---'}}
                                        </div>
                                    </div>
                                </div>
                                    @if(hasPermission('edit-member'))
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="phone">
                                    <i class="ki-filled ki-notepad-edit">
                                    </i>
                                </button>
                                @endif
                                </div>
                            <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-3.5 py-2.5">
                                <div class="flex items-center flex-wrap gap-3.5">
                                    <img alt="" class="size-6 shrink-0" src="{{asset('assets/images/icons/gmail.png')}}">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900 hover:text-primary-active mb-px">
                                            Gmail
                                        </div>
                                        <div class="text-2sm text-gray-700 hover:text-primary-active">
                                            {{$details['email'] ?? '---'}}
                                        </div>
                                    </div>
                                </div>
                                    @if(hasPermission('edit-member'))
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-member-modal" data-name="email">
                                    <i class="ki-filled ki-notepad-edit">
                                    </i>
                                </button>
                                @endif
                                </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Phòng ban trực thuộc
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col gap-2 lg:gap-5">
                            @foreach ($details['departments'] as $department)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center grow gap-2.5">
                                    @include("dashboard.layouts.icons.gear")
                                    <div class="flex flex-col">
                                        <a class="text-sm font-semibold text-gray-900 hover:text-primary-active mb-px" href="/team/{{$department['id']}}">
                                            {{$department['name']}}
                                        </a>
                                        <span class="text-xs font-semibold text-gray-600">
                                            {{$department['level_name']}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-member-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật thông tin
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5" data-id="{{$details['id']}}">
                <div class="flex flex-col gap-2.5">
                    <input class="input hidden" name="name" type="text" placeholder="Vui lòng nhập họ tên mới">
                    <input class="input hidden" name="birth_date" type="text" placeholder="Vui lòng nhập ngày sinh mới">
                    <select name="gender" class="select hidden">
                        <option value="" disabled selected>
                            Chọn giới tính
                        </option>
                        <option value="0">
                            Nam
                        </option>
                        <option value="1">
                            Nữ
                        </option>
                    </select>
                    <input class="input hidden" name="cccd" type="text" placeholder="Vui lòng nhập CCCD mới">
                    <input class="input hidden" name="address" type="text" placeholder="Vui lòng nhập địa chỉ mới">
                    <input class="input hidden" name="salary" type="text" placeholder="Vui lòng nhập mức lương mới">
                    <input class="input hidden" name="phone" type="text" placeholder="Vui lòng nhập số điện thoại mới">
                    <input class="input hidden" name="email" type="text" placeholder="Vui lòng nhập email mới">
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
                "{{$details['is_active'] ? 'Khoá tài khoản' : 'Mở khoá'}}",
                "{{$details['is_active'] ? 'Bạn muốn khoá tài khoản này?' : 'Bạn muốn mở khoá tài khoản này?'}}",
                'Đúng',
                'Huỷ',
                () => {
                    postLockAccountUser($(this).attr('data-id'), _this);
                },
                () => {}, {},
            );
        })

        $('button[data-modal-toggle][data-name]').on('click', function() {
            let _this = $(this);
            let _modal = $('#update-member-modal');
            _modal.find('input[name], select[name]').val('').addClass('hidden');

            _modal.find(`input[name=${_this.attr('data-name')}], select[name=${_this.attr('data-name')}]`).removeClass('hidden');
        })

        $('#update-member-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateMember($(this).attr('data-id'), $(this));
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
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                break;
        }
    }

    async function postUpdateMember(id, _this) {
        let field = $('#update-member-modal form').find('input:not(.hidden),select:not(.hidden)');
        let method = "post",
            url = "/member/update",
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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }
</script>
@endpush