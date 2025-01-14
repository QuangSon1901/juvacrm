@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin hợp đồng
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
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Bên A (bên cung cấp)
                        </h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Nhân viên phụ trách
                                </label>
                                <select class="select">
                                    <option class="disabled" disabled selected>
                                        Vui lòng chọn
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Chức danh
                                </label>
                                <input class="input" type="text" placeholder="Chức vụ nhân viên">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Bên B (khách hàng)
                        </h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Khách hàng
                                </label>
                                <select class="select">
                                    <option class="disabled" disabled selected>
                                        Vui lòng chọn
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Địa chỉ
                                </label>
                                <input class="input" type="text" placeholder="Địa chỉ khách hàng">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-44">
                                    Điện thoại
                                </label>
                                <input class="input" type="text" placeholder="Số điện thoại">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Mô tả hợp đồng
                        </h3>
                    </div>
                    <div class="card-body grid gap-5">
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Tên hợp đồng
                                </label>
                                <input class="input" type="text" placeholder="Tên hợp đồng">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Loại hình dịch vụ
                                </label>
                                <select class="select">
                                    <option selected>
                                        Chụp ảnh sản phẩm
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Thời gian dự kiến
                                </label>
                                <input class="input max-w-20" type="text" placeholder="Số ngày">
                                <input class="input" type="text" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                <label class="form-label flex items-center gap-1 max-w-56">
                                    Ghi chú
                                </label>
                                <textarea class="textarea" name="" id="" placeholder="Ghi chú"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin dịch vụ
                        </h3>
                        <button class="btn btn-light btn-sm">
                            Thêm dịch vụ
                        </button>
                    </div>
                    <div class="card-table scrollable-x-auto">
                        <div class="scrollable-auto">
                            <table class="table align-middle text-2sm text-gray-600">
                                <tbody>
                                    <tr class="bg-gray-100">
                                        <th class="text-center font-normal min-w-16 !px-1">
                                            STT
                                        </th>
                                        <th class="text-start font-normal min-w-40 !px-1">
                                            Dịch vụ
                                        </th>
                                        <th class="text-start font-normal min-w-20 !px-1">
                                            Số lượng
                                        </th>
                                        <th class="text-start font-normal min-w-20 !px-1">
                                            Giá
                                        </th>
                                        <th class="text-start font-normal min-w-20 !px-1">
                                            Ghi chú
                                        </th>
                                        <th class="min-w-16 !px-1">
                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="text-center !px-1">1</td>
                                        <td class="!px-1">
                                            <select class="select">
                                                <option selected>
                                                    Chọn dịch vụ
                                                </option>
                                            </select>
                                        </td>
                                        <td class="!px-1">
                                            <input class="input" type="text" placeholder="Số lượng">
                                        </td>
                                        <td class="!px-1">
                                            <input class="input" type="text" placeholder="Giá tiền">
                                        </td>
                                        <td class="!px-1">
                                            <input class="input" type="text" placeholder="Ghi chú">
                                        </td>
                                        <td class="text-center !px-1">
                                            <button class="btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-trash !text-red-600">
                                                </i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thanh toán
                        </h3>
                        <button class="btn btn-light btn-sm">
                            Thêm biên nhận
                        </button>
                    </div>
                    <div class="card-table scrollable-x-auto">
                        <div class="scrollable-auto">
                            <table class="table align-middle text-2sm text-gray-600">
                                <tbody>
                                    <tr>
                                        <td class="!px-1 min-w-20">
                                            <input class="input" type="text" placeholder="Biên nhận">
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <input class="input" type="text" placeholder="Giá tiền">
                                        </td>
                                        <td class="!px-1">
                                            <select class="select !w-max">
                                                <option selected>
                                                    VNĐ
                                                </option>
                                                <option value="">USD</option>
                                            </select>
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <select class="select">
                                                <option selected>
                                                    Tiền mặt
                                                </option>
                                                <option value="">Chuyển khoản ngân hàng</option>
                                            </select>
                                        </td>
                                        <td class="!px-1 min-w-20">
                                            <input class="input" type="text" placeholder="DD/MM/YYYY">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection