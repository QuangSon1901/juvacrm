@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý thu chi
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
    <!-- Card thống kê -->
    <div class="grid !grid-cols-1 md:!grid-cols-3 gap-5 mb-5">
        <div class="card shadow-sm border border-gray-200">
            <div class="card-body flex items-center p-4">
                <div class="size-12 flex items-center justify-center rounded-md bg-success-dark bg-opacity-10 me-3">
                    <i class="ki-filled ki-arrow-down text-2xl text-success"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600 font-medium mb-1">Tổng thu</div>
                    <div class="text-xl font-semibold text-gray-900" id="total-income">0₫</div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border border-gray-200">
            <div class="card-body flex items-center p-4">
                <div class="size-12 flex items-center justify-center rounded-md bg-danger-dark bg-opacity-10 me-3">
                    <i class="ki-filled ki-arrow-up text-2xl text-danger"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600 font-medium mb-1">Tổng chi</div>
                    <div class="text-xl font-semibold text-gray-900" id="total-expense">0₫</div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border border-gray-200">
            <div class="card-body flex items-center p-4">
                <div class="size-12 flex items-center justify-center rounded-md bg-primary-dark bg-opacity-10 me-3">
                    <i class="ki-filled ki-dollar text-2xl text-primary"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-600 font-medium mb-1">Số dư</div>
                    <div class="text-xl font-semibold text-gray-900" id="balance">0₫</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card danh sách phiếu thu chi -->
    <div class="grid gap-5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách phiếu thu chi
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex flex-wrap gap-2.5">
                        <select data-filter="type" class="select select-sm w-40">
                            <option value="" selected>Tất cả loại</option>
                            <option value="0">Phiếu thu</option>
                            <option value="1">Phiếu chi</option>
                        </select>
                        <select data-filter="category_id" class="select select-sm w-40">
                            <option value="" selected>Tất cả danh mục</option>
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <select data-filter="status" class="select select-sm w-40">
                            <option value="" selected>Tất cả trạng thái</option>
                            <option value="0">Chờ xử lý</option>
                            <option value="1">Hoàn tất</option>
                            <option value="2">Đã hủy</option>
                        </select>
                        <div class="dropdown" data-dropdown="true">
                            <button class="dropdown-toggle btn btn-sm btn-primary">
                                Thêm mới
                                <i class="ki-outline ki-down ms-1 dropdown-open:hidden"></i>
                                <i class="ki-outline ki-up ms-1 hidden dropdown-open:block"></i>
                            </button>
                            <div class="dropdown-content w-44">
                                <div class="menu menu-default flex flex-col w-full">
                                    <div class="menu-item">
                                        <button class="menu-link" data-modal-toggle="#create-income-modal">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-arrow-down text-success"></i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm phiếu thu
                                            </span>
                                        </button>
                                    </div>
                                    <div class="menu-item">
                                        <button class="menu-link" data-modal-toggle="#create-expense-modal">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-arrow-up text-danger"></i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm phiếu chi
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="transactions_table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[50px]">
                                        <span class="sort">
                                            <span class="sort-label">STT</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[100px]">
                                        <span class="sort">
                                            <span class="sort-label">Loại</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">Danh mục</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[180px]">
                                        <span class="sort">
                                            <span class="sort-label">Đối tượng</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">Số tiền</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Ngày thanh toán</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label">Nội dung</span>
                                        </span>
                                    </th>
                                    <th class="min-w-[120px]">
                                        <span class="sort">
                                            <span class="sort-label">Trạng thái</span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/transaction/data'])
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

<!-- Modal thêm phiếu thu -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-income-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm phiếu thu</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-income-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="type" value="0">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Danh mục thu <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="income-category-id" name="category_id" required>
                        <option value="">Chọn danh mục thu</option>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $category)
                                @if($category->type == 0)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đối tượng <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="income-target-type" name="target_type" required>
                        <option value="client">Khách hàng</option>
                        <option value="employee">Nhân viên</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 income-target-client">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khách hàng <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="income-target-client-id" name="target_client_id">
                        <option value="">Chọn khách hàng</option>
                        @if(isset($customers) && count($customers) > 0)
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 income-target-employee hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nhân viên <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="income-target-employee-id" name="target_employee_id">
                        <option value="">Chọn nhân viên</option>
                        @if(isset($employees) && count($employees) > 0)
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 income-target-other hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đối tượng khác <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="income-target-other" name="target_other">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Số tiền <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="number" id="income-amount" name="amount" min="0" step="1000" required>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ngày thanh toán <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="text" id="income-paid-date" name="paid_date" data-flatpickr="true" required>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nội dung <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="income-reason" name="reason" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ghi chú
                            </span>
                        </div>
                        <textarea class="textarea" id="income-note" name="note" rows="2"></textarea>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Trạng thái
                            </span>
                        </div>
                        <select class="select" id="income-status" name="status">
                            <option value="1">Hoàn tất</option>
                            <option value="0">Chờ xử lý</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm phiếu thu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal thêm phiếu chi -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-expense-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Thêm phiếu chi</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-expense-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="type" value="1">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Danh mục chi <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="expense-category-id" name="category_id" required>
                        <option value="">Chọn danh mục chi</option>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $category)
                                @if($category->type == 1)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đối tượng <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="expense-target-type" name="target_type" required>
                        <option value="client">Khách hàng</option>
                        <option value="employee">Nhân viên</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 income-target-client">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khách hàng <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="expense-target-client-id" name="target_client_id">
                        <option value="">Chọn khách hàng</option>
                        @if(isset($customers) && count($customers) > 0)
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 income-target-employee hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nhân viên <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="expense-target-employee-id" name="target_employee_id">
                        <option value="">Chọn nhân viên</option>
                        @if(isset($employees) && count($employees) > 0)
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 income-target-other hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đối tượng khác <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="expense-target-other" name="target_other">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Số tiền <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="number" id="expense-amount" name="amount" min="0" step="1000" required>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ngày thanh toán <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="text" id="expense-paid-date" name="paid_date" data-flatpickr="true" required>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nội dung <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="expense-reason" name="reason" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ghi chú
                            </span>
                        </div>
                        <textarea class="textarea" id="expense-note" name="note" rows="2"></textarea>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Trạng thái
                            </span>
                        </div>
                        <select class="select" id="expense-status" name="status">
                            <option value="1">Hoàn tất</option>
                            <option value="0">Chờ xử lý</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Thêm phiếu chi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa phiếu thu chi -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="edit-transaction-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] modal-center-y">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">Sửa phiếu thu chi</h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="edit-transaction-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="id" id="edit-transaction-id">
                <input type="hidden" name="type" id="edit-transaction-type">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Danh mục <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="edit-category-id" name="category_id" required>
                        <option value="">Chọn danh mục</option>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-type="{{ $category->type }}">{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đối tượng <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="edit-target-type" name="target_type" required>
                        <option value="client">Khách hàng</option>
                        <option value="employee">Nhân viên</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 edit-target-client">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Khách hàng <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="edit-target-client-id" name="target_client_id">
                        <option value="">Chọn khách hàng</option>
                        @if(isset($customers) && count($customers) > 0)
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 edit-target-employee hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nhân viên <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <select class="select" id="edit-target-employee-id" name="target_employee_id">
                        <option value="">Chọn nhân viên</option>
                        @if(isset($employees) && count($employees) > 0)
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-2.5 edit-target-other hidden">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Đối tượng khác <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="edit-target-other" name="target_other">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Số tiền <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="number" id="edit-amount" name="amount" min="0" step="1000" required>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ngày thanh toán <span class="text-red-500">*</span>
                            </span>
                        </div>
                        <input class="input" type="text" id="edit-paid-date" name="paid_date" data-flatpickr="true" required>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Nội dung <span class="text-red-500">*</span>
                        </span>
                    </div>
                    <input class="input" type="text" id="edit-reason" name="reason" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Ghi chú
                            </span>
                        </div>
                        <textarea class="textarea" id="edit-note" name="note" rows="2"></textarea>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">
                                Trạng thái
                            </span>
                        </div>
                        <select class="select" id="edit-status" name="status">
                            <option value="1">Hoàn tất</option>
                            <option value="0">Chờ xử lý</option>
                            <option value="2">Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        // Khởi tạo flatpickr
        flatpickrMake($("[data-flatpickr]"), 'datetime');
        
        // Xử lý hiển thị trường đối tượng
        $('#income-target-type, #expense-target-type, #edit-target-type').on('change', function() {
            let targetType = $(this).val();
            let prefix = $(this).attr('id').split('-')[0];
            
            $(`.${prefix}-target-client, .${prefix}-target-employee, .${prefix}-target-other`).addClass('hidden');
            $(`.${prefix}-target-${targetType}`).removeClass('hidden');
            
            // Reset các trường không hiển thị
            if (targetType !== 'client') $(`.${prefix}-target-client select`).val('');
            if (targetType !== 'employee') $(`.${prefix}-target-employee select`).val('');
            if (targetType !== 'other') $(`.${prefix}-target-other input`).val('');
        });
        
        // Xử lý form thêm phiếu thu
        $('#create-income-form').on('submit', async function(e) {
            e.preventDefault();
            
            // Validate theo target type
            let targetType = $('#income-target-type').val();
            if (targetType === 'client' && !$('#income-target-client-id').val()) {
                showAlert('warning', 'Vui lòng chọn khách hàng');
                return;
            }
            if (targetType === 'employee' && !$('#income-target-employee-id').val()) {
                showAlert('warning', 'Vui lòng chọn nhân viên');
                return;
            }
            if (targetType === 'other' && !$('#income-target-other').val()) {
                showAlert('warning', 'Vui lòng nhập thông tin đối tượng khác');
                return;
            }
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/transaction/create', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-income-modal')).hide();
                    $('#create-income-form')[0].reset();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi thêm phiếu thu.');
                console.error(error);
            }
        });
        
        // Xử lý form thêm phiếu chi
        $('#create-expense-form').on('submit', async function(e) {
            e.preventDefault();
            
            // Validate theo target type
            let targetType = $('#expense-target-type').val();
            if (targetType === 'client' && !$('#expense-target-client-id').val()) {
                showAlert('warning', 'Vui lòng chọn khách hàng');
                return;
            }
            if (targetType === 'employee' && !$('#expense-target-employee-id').val()) {
                showAlert('warning', 'Vui lòng chọn nhân viên');
                return;
            }
            if (targetType === 'other' && !$('#expense-target-other').val()) {
                showAlert('warning', 'Vui lòng nhập thông tin đối tượng khác');
                return;
            }
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/transaction/create', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-expense-modal')).hide();

                    $('#create-expense-form')[0].reset();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi thêm phiếu chi.');
                console.error(error);
            }
        });
        
        // Xử lý form sửa phiếu thu chi
        $('#edit-transaction-form').on('submit', async function(e) {
            e.preventDefault();
            
            // Validate theo target type
            let targetType = $('#edit-target-type').val();
            if (targetType === 'client' && !$('#edit-target-client-id').val()) {
                showAlert('warning', 'Vui lòng chọn khách hàng');
                return;
            }
            if (targetType === 'employee' && !$('#edit-target-employee-id').val()) {
                showAlert('warning', 'Vui lòng chọn nhân viên');
                return;
            }
            if (targetType === 'other' && !$('#edit-target-other').val()) {
                showAlert('warning', 'Vui lòng nhập thông tin đối tượng khác');
                return;
            }
            
            let formData = $(this).serialize();
            
            try {
                let res = await axiosTemplate('post', '/transaction/update', null, formData);
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#edit-transaction-modal')).hide();
                    callAjaxDataTable($('.updater'));
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi cập nhật phiếu.');
                console.error(error);
            }
        });
        
        // Cập nhật tổng quan khi load dữ liệu
        $(document).on('ajaxComplete', function(event, xhr, settings) {
            if (settings.url && settings.url.includes('/transaction/data')) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.summary) {
                        $('#total-income').text(formatNumberLikePhp(response.summary.total_income) + '₫');
                        $('#total-expense').text(formatNumberLikePhp(response.summary.total_expense) + '₫');
                        $('#balance').text(formatNumberLikePhp(response.summary.balance) + '₫');
                        
                        // Đổi màu số dư nếu âm
                        if (response.summary.balance < 0) {
                            $('#balance').addClass('text-danger').removeClass('text-gray-900');
                        } else {
                            $('#balance').removeClass('text-danger').addClass('text-gray-900');
                        }
                    }
                } catch (e) {
                    console.error('Lỗi khi cập nhật tổng quan', e);
                }
            }
        });
    });
    
    // Mở modal sửa phiếu và đổ dữ liệu
    function openEditTransactionModal(id, type, categoryId, targetType, targetId, targetName, amount, paidDate, reason, note, status) {
        $('#edit-transaction-id').val(id);
        $('#edit-transaction-type').val(type);
        $('#edit-category-id').val(categoryId);
        $('#edit-target-type').val(targetType);
        
        // Hiển thị trường đối tượng phù hợp
        $('.edit-target-client, .edit-target-employee, .edit-target-other').addClass('hidden');
        $(`.edit-target-${targetType}`).removeClass('hidden');
        
        // Đặt giá trị cho đối tượng
        if (targetType === 'client') {
            $('#edit-target-client-id').val(targetId);
        } else if (targetType === 'employee') {
            $('#edit-target-employee-id').val(targetId);
        } else {
            $('#edit-target-other').val(targetName);
        }
        
        $('#edit-amount').val(amount);
        $('#edit-paid-date').val(paidDate);
        $('#edit-reason').val(reason);
        $('#edit-note').val(note);
        $('#edit-status').val(status);
        
        // Đặt lại title cho modal
        $('.modal-title', '#edit-transaction-modal').text(type == 0 ? 'Sửa phiếu thu' : 'Sửa phiếu chi');
        
        // Cập nhật lại flatpickr cho trường ngày tháng
        flatpickrMake($('#edit-paid-date'), 'datetime');
        
        // Lọc danh mục theo loại phiếu
        $('#edit-category-id option').each(function() {
            let optionType = $(this).data('type');
            if (optionType !== undefined && optionType !== type) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
        
        KTModal.getInstance(document.querySelector('#edit-transaction-modal')).show();

    }
    
    // Hàm xác nhận phiếu
    async function confirmTransaction(id) {
        try {
            Notiflix.Confirm.show(
                'Xác nhận phiếu',
                'Bạn có chắc chắn muốn xác nhận phiếu này thành trạng thái hoàn tất?',
                'Xác nhận',
                'Hủy',
                async function() {
                    let res = await axiosTemplate('post', '/transaction/update', null, {id: id, status: 1});
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi xác nhận phiếu.');
            console.error(error);
        }
    }
    
    // Hàm hủy phiếu
    async function cancelTransaction(id) {
        try {
            Notiflix.Confirm.show(
                'Hủy phiếu',
                'Bạn có chắc chắn muốn hủy phiếu này? Hành động này không thể hoàn tác!',
                'Hủy phiếu',
                'Không',
                async function() {
                    let res = await axiosTemplate('post', '/transaction/cancel', null, {id: id});
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        callAjaxDataTable($('.updater'));
                    } else {
                        showAlert('warning', res.data.message);
                    }
                }
            );
        } catch (error) {
            showAlert('error', 'Đã xảy ra lỗi khi hủy phiếu.');
            console.error(error);
        }
    }
</script>
@endpush