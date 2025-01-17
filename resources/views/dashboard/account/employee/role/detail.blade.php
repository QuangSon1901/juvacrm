@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
            {{$details['department']['name']}} - {{$details['level']['name']}}
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
    <div class="flex flex-col gap-5 lg:gap-7.5">
        <div class="card card-grid min-w-full">
            <div class="card-header py-5 flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách thành viên
                </h3>
                <div class="flex gap-6">
                    <a href="/team/add-member/{{$details['department']['id']}}" class="btn btn-light btn-sm">
                        Thêm nhân viên
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <input type="text" value="{{$details['department']['id']}}" data-filter="department_id" hidden>
                        <input type="text" value="{{$details['level']['id']}}" data-filter="level_id" hidden>
                        <table class="table table-auto table-border">
                            <thead>
                                <tr>
                                <th class="w-[60px] text-center">
                                                STT
                                            </th>
                                    <th class="min-w-[250px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Thành viên
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[250px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">
                                                Chức vụ
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]">
                                    </th>
                                </tr>
                            </thead>
                                    @include('dashboard.layouts.tableloader', ['currentlist' => "/role/employee-in-role"])
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
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Phân quyền
                </h3>
            </div>
            <div class="card-table scrollable-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left text-gray-300 font-normal min-w-[300px]">
                                Mô-đun
                            </th>
                            <th class="min-w-24 text-gray-700 font-normal text-center">
                                Xem
                            </th>
                            <th class="min-w-24 text-gray-700 font-normal text-center">
                                Chỉnh sửa
                            </th>
                            <th class="min-w-24 text-gray-700 font-normal text-center">
                                Xoá
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-900 font-medium">
                        <tr>
                            <td class="!py-5.5">
                                Danh sách nhân viên
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                        </tr>
                        <tr>
                            <td class="!py-5.5">
                                Danh sách công việc
                            </td>
                            <td class="!py-5.5 text-center">
                                <input checked="" class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                            <td class="!py-5.5 text-center">
                                <input class="checkbox checkbox-sm" name="" type="checkbox" value="">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer justify-end py-7.5 gap-2.5">
                <a class="btn btn-light btn-outline" href="#">
                    Mặc định
                </a>
                <a class="btn btn-primary" href="#">
                    Lưu thay đổi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@push("scripts")
<script>
    async function saveRemoveMemberTeam(user_id, department_id) {
        let method = "post",
            url = "/team/remove-member",
            params = null,
            data = {
                user_id,
                department_id
            }
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
</script>
@endpush