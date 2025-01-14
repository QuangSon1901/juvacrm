@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thêm nhân viên
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
    <div class="grid gap-5">
        <div class="card min-w-full">
            <div class="card-header">
                <h3 class="card-title">
                    Thông tin nhân viên
                </h3>
            </div>
            <form id="create-member-form" method="POST" action="/member/create" class="card-body grid gap-5">
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Họ và tên
                        </label>
                        <input class="input" name="name" type="text" placeholder="Họ và tên">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Email
                        </label>
                        <input class="input" name="email" type="text" placeholder="Email">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Tài khoản
                        </label>
                        <input class="input" name="username" type="text" placeholder="Tài khoản">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Số điện thoại
                        </label>
                        <input class="input" name="phone" type="text" placeholder="Số điện thoại">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Ngày sinh
                        </label>
                        <input class="input" name="birth_date" type="text" placeholder="DD/MM/YYYY">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Giới tính
                        </label>
                        <select name="gender" class="select">
                            <option value="0">
                                Nam
                            </option>
                            <option value="1">
                                Nữ
                            </option>
                        </select>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            CCCD
                        </label>
                        <input class="input" name="cccd" type="text" placeholder="Căn cước công dân">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Địa chỉ
                        </label>
                        <input class="input" name="address" type="text" placeholder="Địa chỉ">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Lương cơ bản
                        </label>
                        <input class="input" name="salary" type="text" placeholder="Lương cơ bản">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Ghi chú
                        </label>
                        <textarea class="textarea" name="note" id="" placeholder="Ghi chú"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push("actions")
<button class="btn btn-success" onclick="saveCreateMember()">
    Thêm nhân viên
</button>
@endpush
@push('scripts')
<script>
    async function saveCreateMember() {
        let method = "post",
            url = "/member/create",
            params = null,
            data = $('#create-member-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                alert(res.data.message)
                window.location.reload();
                break;
            default:
                alert(res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                break;
        }
    }
</script>
@endpush