@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thêm bộ phận/phòng ban
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="col-span-1">
            <div class="flex flex-col gap-5">
                <div class="card min-w-full">
                    <div class="card-header">
                        <h3 class="card-title">
                            Thông tin phòng ban
                        </h3>
                    </div>
                    <div class="card-table scrollable-x-auto pb-3">
                        <table id="info-team-table" class="table align-middle text-sm">
                            <tbody>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Tên phòng ban
                                    </td>
                                    <td class="py-2 text-gray-700 font-normal">
                                        <input class="input" name="name-team" type="text" placeholder="Tên phòng ban">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 font-normal">
                                        Ghi chú
                                    </td>
                                    <td class="py-2 text-gray-700 font-normal">
                                        <input class="input" name="note-team" type="text" placeholder="Ghi chú">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1 lg:col-span-2">
            <div class="flex flex-col gap-5">
                <div class="card card-grid min-w-full">
                    <div class="card-header py-5 flex-wrap gap-2">
                        <h3 class="card-title">
                            Danh sách thành viên
                        </h3>
                        <div class="flex items-center gap-2.5">
                            <button class="btn btn-sm btn-light" onclick="addRowLevelTable()">
                                Thêm thành viên
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div data-datatable="true" data-datatable-page-size="5" class="datatable-initialized">
                            <div class="scrollable-x-auto">
                                <table id="members-table" class="table table-auto table-border" data-datatable-table="true">
                                    <thead>
                                        <tr>
                                            <th class="min-w-[250px]">
                                                <span class="sort asc">
                                                    <span class="sort-label text-gray-700 font-normal">
                                                        Thành viên
                                                    </span>
                                                    <span class="sort-icon">
                                                    </span>
                                                </span>
                                            </th>
                                            <th class="min-w-[250px]">
                                                <span class="sort asc">
                                                    <span class="sort-label text-gray-700 font-normal">
                                                        Chức vụ
                                                    </span>
                                                    <span class="sort-icon">
                                                    </span>
                                                </span>
                                            </th>
                                            <th class="w-[60px]">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="select" name="employee">
                                                    <option selected disabled>
                                                        Chọn nhân viên
                                                    </option>
                                                    @foreach ($users as $user)
                                                    <option value="{{$user['id']}}">{{$user['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2">
                                                    <select class="select" name="level">
                                                        <option selected disabled>
                                                            Chọn chức vụ
                                                        </option>
                                                        @foreach ($levels as $level)
                                                        <option value="{{$level['id']}}">{{$level['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="input !w-max" data-modal-toggle="#create-level-modal">
                                                        Thêm
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-icon btn-light btn-clear" onclick="removeRowLevelTable($(this))">
                                                    <i class="ki-filled ki-trash !text-red-600">
                                                    </i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-level-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thêm chức vụ
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="flex flex-center gap-1">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Tên chức vụ
                        </label>
                    </div>
                    <input class="input" type="text" placeholder="Tên chức vụ">
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
@push("actions")
<button class="btn btn-success" onclick="saveCreateTeam()">
    Thêm bộ phận/phòng ban
</button>
@endpush
@push("scripts")
<script>
    let users = @json($users);
    let levels = @json($levels);
</script>
<script type="text/javascript" src="{{ asset('assets\js\dashboard\account\employee\team\create.js')}}"></script>
@endpush