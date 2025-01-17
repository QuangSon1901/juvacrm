@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thêm công việc
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
    <form id="create-task-form">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 lg:gap-7.5">
            <div class="col-span-2">
                <div class="grid gap-5 lg:gap-7.5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Thông tin công việc
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="comment-task-form" class="flex flex-col gap-2.5">
                                <div class="grid grid-cols-1 gap-5">
                                    <div class="flex flex-col gap-2.5">
                                        <div class="checkbox-group">
                                            <span class="checkbox-label text-gray-800 !font-bold">
                                                Tên công việc
                                            </span>
                                        </div>
                                        <input class="input" name="name" type="text" placeholder="Tên công việc">
                                    </div>
                                    <div class="menu-separator simple"></div>
                                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                                        <div class="col-span-1">
                                            <div class="grid grid-cols-1 gap-5">
                                                <div class="flex flex-col gap-2.5">
                                                    <div class="checkbox-group">
                                                        <span class="checkbox-label text-gray-800 !font-bold">
                                                            Trạng thái
                                                        </span>
                                                    </div>
                                                    <select name="status_id" class="select">
                                                        <option value="" disabled selected>
                                                            Chọn trạng thái
                                                        </option>
                                                        @foreach ($statuses as $status)
                                                        <option value="{{$status['id']}}">
                                                            {{$status['name']}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex flex-col gap-2.5">
                                                    <div class="checkbox-group">
                                                        <span class="checkbox-label text-gray-800 !font-bold">
                                                            Mức độ ưu tiên
                                                        </span>
                                                    </div>
                                                    <select name="priority_id" class="select">
                                                        <option value="" disabled selected>
                                                            Chọn mức độ ưu tiên
                                                        </option>
                                                        @foreach ($priorities as $priority)
                                                        <option value="{{$priority['id']}}">
                                                            {{$priority['name']}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex flex-col gap-2.5">
                                                    <div class="checkbox-group">
                                                        <span class="checkbox-label text-gray-800 !font-bold">
                                                            Người thực hiện
                                                        </span>
                                                    </div>
                                                    <select name="assign_id" class="select">
                                                        <option value="" selected>
                                                            Chọn thành viên
                                                        </option>
                                                        @foreach ($users as $user)
                                                        <option value="{{$user['id']}}">
                                                            {{$user['name']}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-span-1">
                                            <div class="grid grid-cols-1 gap-5">
                                                <div class="flex flex-col gap-2.5">
                                                    <div class="checkbox-group">
                                                        <span class="checkbox-label text-gray-800 !font-bold">
                                                            Ngày bắt đầu
                                                        </span>
                                                    </div>
                                                    <input class="input" name="start_date" type="text" placeholder="Ngày bắt đầu">
                                                </div>
                                                <div class="flex flex-col gap-2.5">
                                                    <div class="checkbox-group">
                                                        <span class="checkbox-label text-gray-800 !font-bold">
                                                            Ngày kết thúc
                                                        </span>
                                                    </div>
                                                    <input class="input" name="due_date" type="text" placeholder="Ngày kết thúc">
                                                </div>
                                                <div class="flex flex-col gap-2.5">
                                                    <div class="checkbox-group">
                                                        <span class="checkbox-label text-gray-800 !font-bold">
                                                            Thời gian dự kiến
                                                        </span>
                                                    </div>
                                                    <input class="input" name="estimate_time" type="text" placeholder="Tổng số giờ ước tính">
                                                </div>
                                                <div class="flex flex-col gap-2.5">
                                                    <div class="checkbox-group">
                                                        <span class="checkbox-label text-gray-800 !font-bold">
                                                            Thời gian thực tế
                                                        </span>
                                                    </div>
                                                    <input class="input" name="spend_time" type="text" placeholder="Tổng số giờ thực hiện">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-separator simple"></div>
                                    <div class="flex flex-col gap-2.5">
                                        <div class="checkbox-group">
                                            <span class="checkbox-label text-gray-800 !font-bold">
                                                Mô tả công việc
                                            </span>
                                        </div>
                                        <div id="description_editor"></div>
                                    </div>
                                    <div class="menu-separator simple"></div>
                                    <div class="flex flex-col gap-2.5">
                                        <div class="checkbox-group">
                                            <span class="checkbox-label text-gray-800 !font-bold">
                                                Ghi chú
                                            </span>
                                        </div>
                                        <textarea class="textarea" rows="2" name="note" placeholder="Ghi chú"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-1">
                <div class="grid gap-5 lg:gap-7.5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Thiết lập phúc lợi
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="flex flex-col gap-2.5">
                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Tiền thưởng hoàn thành
                                            <span data-tooltip="Người thực hiện sẽ được cộng số tiền thưởng khi hoàn thành công việc" class="text-gray-500 hover:text-primary-active">
                                                <i class="ki-filled ki-information-2"> </i>
                                            </span>
                                        </span>
                                    </div>
                                    <input class="input" name="bonus_amount" type="text" value="0" placeholder="Tiền thưởng hoàn thành">
                                </div>
                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Tiền phạt trễ hạn
                                            <span data-tooltip="Số tiển bị phạt khi quá hạn chưa hoàn thành" class="text-gray-500 hover:text-primary-active">
                                                <i class="ki-filled ki-information-2"> </i>
                                            </span>
                                        </span>
                                    </div>
                                    <input class="input" name="deduction_amount" type="text" value="0" placeholder="Tiền phạt hoàn thành">
                                </div>
                                <div class="text-gray-800 text-xs">
                                    <b class="text-danger">Chú ý:</b> Thiết lập này chỉ dành cho công việc đơn, không thuộc nhóm công việc.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Thông tin liên kết
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="flex flex-col gap-2.5">
                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Nhóm công việc
                                        </span>
                                    </div>
                                    <select name="parent_id" class="select">
                                        <option value="" {{$parent_active == 0 ? 'selected' : ''}}>Công việc đơn</option>
                                        @foreach ($tasks as $task)
                                        <option value="{{$task['id']}}" {{$parent_active == $task['id'] ? 'selected' : ''}}>
                                            #{{$task['id']}} - {{$task['name']}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="menu-separator simple"></div>
                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Hợp đồng liên quan
                                        </span>
                                    </div>
                                    <select name="contract_id" class="select">
                                        <option value="" selected>Chọn hợp đồng</option>
                                        @foreach ($contracts as $contract)
                                        <option value="{{$contract['id']}}">
                                            {{$contract['name']}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <ul class="ml-4 list-disc hidden">
                                        <li class="text-xs">
                                            <p>ID: ---</p>
                                            <p>Tên: ---</p>
                                            <a class="link" href="#">Xem chi tiết</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="menu-separator simple"></div>
                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Dịch vụ liên quan
                                        </span>
                                    </div>
                                    <select name="service_id" class="select p-2.5">
                                        <option value="" selected>Chọn dịch vụ</option>
                                        @foreach ($services as $services)
                                        <option value="{{$services['id']}}">
                                            {{$services['name']}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <input class="input" name="service_other" type="text" placeholder="Dịch vụ khác">
                                </div>
                                <div class="menu-separator simple"></div>
                                <div class="flex flex-col gap-2.5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Đính kèm tệp
                                        </span>
                                    </div>
                                    <button class="btn btn-sm btn-light justify-center w-full" type="button">Tải lên tệp</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@push("actions")
<button class="btn btn-success" onclick="postCreateTask()">
    Thêm công việc
</button>
@endpush
@push('scripts')
<script>
    let _descriptionQuill;
    $(function() {
        flatpickrMake($("input[name=start_date]"), 'datetime');
        flatpickrMake($("input[name=due_date]"), 'datetime');
        _descriptionQuill = quillTemplate("#description_editor", "350px");
    })

    async function postCreateTask() {
        let method = "post",
            url = "/task/create",
            params = null,
            data = {
                ...serializeToObject($('#create-task-form').serializeArray()),
                description: _descriptionQuill.root.innerHTML,
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.href='/task';
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }
</script>
@endpush