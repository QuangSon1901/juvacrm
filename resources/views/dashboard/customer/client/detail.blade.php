@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin khách hàng
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
                            Thông tin liên hệ
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Họ tên
                                    </td>
                                    <td class="py-2 text-gray-800 text-sm">
                                        {{$details['name']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Số điện thoại
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['phone']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Email
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['email']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Địa chỉ
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['address']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Ghi chú
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['note']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Nguồn
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$details['source']['name']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Hình thức liên hệ
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        @foreach ($details['contacts'] as $contact)
                                        {{$contact['name']}}
                                        <br>
                                        @endforeach
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Lần tương tác gần nhất
                                    </td>
                                    <td class="py-3 text-gray-700 text-sm font-normal">
                                        {{$contact['updated_at']}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin công ty
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table class="table align-middle text-sm text-gray-500">
                            <tbody>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Tên công ty
                                    </td>
                                    <td class="py-2 text-gray-800 text-sm font-normal">
                                        {{$details['company']}}
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Ngành
                                    </td>
                                    <td class="py-2 text-gray-800 text-sm font-normal">
                                        Media
                                    </td>
                                    <td class="py-2 text-center">
                                        <a class="btn btn-sm btn-icon btn-clear btn-primary" href="#">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </a>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thống kê khách hàng
                        </h3>
                    </div>
                    <div class="card-body">
                        <label class="checkbox-group mb-2">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Tổng số hợp đồng:
                            </span>
                            <span class="checkbox-label text-gray-800">
                                0 hợp đồng
                            </span>
                        </label>
                        <div class="flex items-center flex-wrap gap-2">
                            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-green-400 bg-green-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                                <span class="text-green-900 text-md leading-none font-medium">
                                    0
                                </span>
                                <span class="text-green-700 text-2sm">
                                    Hoàn thành
                                </span>
                            </div>
                            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-blue-400 bg-blue-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                                <span class="text-blue-900 text-md leading-none font-medium">
                                    0
                                </span>
                                <span class="text-blue-700 text-2sm">
                                    Đang thực hiện
                                </span>
                            </div>
                            <div class="grid grid-cols-1 content-between gap-1.5 border border-dashed border-red-400 bg-red-100 shrink-0 rounded-md px-3.5 py-2 min-w-24 max-w-auto">
                                <span class="text-red-900 text-md leading-none font-medium">
                                    0
                                </span>
                                <span class="text-red-700 text-2sm">
                                    Đã huỷ
                                </span>
                            </div>
                        </div>
                        <label class="checkbox-group mb-2 mt-4">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Tổng số dư tài chính:
                            </span>
                            <span class="checkbox-label text-gray-800">
                                0 đ
                            </span>
                        </label>
                        <ul class="list-disc ml-4">
                            <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Số tiền đã thu:
                                </span>
                                <span class="text-green-700 text-2sm font-medium">
                                    0 đ
                                </span>
                            </li>
                            <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Số tiền còn nợ:
                                </span>
                                <span class="text-red-700 text-2sm font-medium">
                                    0 đ
                                </span>
                            </li>
                        </ul>
                        <label class="checkbox-group mb-2 mt-4">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Hình thức thanh toán
                            </span>
                        </label>
                        <ul class="list-disc ml-4">
                            <!-- <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Chuyển khoản ngân hàng
                                </span>
                            </li>
                            <li>
                                <span class="text-gray-800 text-2sm leading-none">
                                    Tiền mặt
                                </span>
                            </li> -->
                        </ul>
                    </div>
                </div>
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Dịch vụ khách quan tâm
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-wrap gap-2.5 mb-2">
                            @foreach ($details['services'] as $service)
                            <span class="badge badge-sm badge-light badge-outline">
                                {{$service['name']}}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection