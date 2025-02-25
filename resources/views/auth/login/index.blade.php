@extends('auth.layouts.layout')
@section('auth_content')
<style>
    .page-bg {
        background-image: url('{{asset("/assets/images/background/bg-auth.png")}}');
    }
</style>
<div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
    <div class="card max-w-[370px] w-full">
        <form id="sign-in-form" class="card-body flex flex-col gap-5 p-10">
            <div class="text-center mb-2.5">
                <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
                    Đăng nhập
                </h3>
            </div>
            <div class="flex flex-col gap-1">
                <label class="form-label font-normal text-gray-900">
                    Tài khoản
                </label>
                <input class="input" name="username" placeholder="Tài khoản" type="text" />
            </div>
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between gap-1">
                    <label class="form-label font-normal text-gray-900">
                        Mật khẩu
                    </label>
                </div>
                <div class="input" data-toggle-password="true">
                    <input name="password" placeholder="Mật khẩu" type="password" value="" />
                    <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
                        <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
                        </i>
                        <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
                        </i>
                    </button>
                </div>
            </div>
            <button type="submmit" class="btn btn-primary flex justify-center grow">
                Đăng nhập
            </button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        $(function() {
            $('#sign-in-form').on('submit', function(e) {
                e.preventDefault();
                postLogin($(this));
            })
        })

        async function postLogin(_this) {
            let method = "post",
                url = "/login",
                params = null,
                data = _this.serialize();
            let res = await axiosTemplate(method, url, params, data);
            switch (res.data.status) {
                case 200:
                    showAlert('success', res.data.message);
                    window.location.href='/';
                    break;
                default:
                    showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                    break;
            }
        }
    </script>
@endpush