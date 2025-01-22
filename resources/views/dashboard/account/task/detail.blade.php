@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin công việc
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
        <div class="col-span-1 lg:col-span-2">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header flex-wrap gap-2">
                        <h3 class="card-title flex items-center gap-2">
                            <span>Công việc #{{$details['id']}}</span>
                            @if ($details['status']['id'] != 0)
                            <span class="badge badge-sm badge-outline badge-{{$details['status']['color']}}">
                                {{$details['status']['name']}}
                            </span>
                            @endif
                            @if ($details['priority']['id'] != 0)
                            <span class="badge badge-sm badge-outline badge-{{$details['priority']['color']}}">
                                {{$details['priority']['name']}}
                            </span>
                            @endif
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <a class="menu-link" href="#comment-task-form">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-messages">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Bình luận
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body lg:py-7.5 grid gap-5">
                        @php
                        use Carbon\Carbon;
                        $now = Carbon::now();
                        @endphp
                        @if (formatDateTime($details['due_date'], 'Y-m-d H:i:s' != '') && Carbon::parse($details['due_date'])->lt($now))
                        <div class="badge badge-outline badge-danger px-3">
                            <div class="relative w-full text-sm flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                Công việc này đã quá hạn. Vui lòng kiểm tra và xử lý ngay!
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center justify-between grow border border-gray-200 rounded-xl gap-2 p-5">
                            <div class="flex flex-col lg:flex-row items-center gap-4">
                                @include("dashboard.layouts.icons.user")
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-gray-900">
                                            {{$details['name']}}
                                        </span>
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="name">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                    @if ($details['parent']['id'] != 0)
                                    <div class="form-info text-gray-800 font-normal">
                                        <a href="/task/{{$details['parent']['id']}}" class="hover:text-primary-active">Nhóm: #{{$details['parent']['id']}} - {{$details['parent']['name']}}</a>
                                    </div>
                                    @endif
                                    <div class="form-info text-gray-800 font-normal">
                                        Được thêm bởi <b>{{$details['create_by']['id'] == 0 ? 'Hệ thống' : $details['create_by']['name']}}</b> khoảng <b>{{timeAgo(strtotime($details['created_at']))}}</b> trước. @if ($details['updated_at'] != $details['created_at']) Đã cập nhật <b>{{timeAgo(strtotime($details['updated_at']))}}</b> trước. @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-5">
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Trạng thái:
                                        </span>
                                        @if ($details['status']['id'] != 0)
                                        <span class="checkbox-label text-gray-800">
                                            {{$details['status']['name']}}
                                        </span>
                                        @endif
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="status_id">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Độ ưu tiên:
                                        </span>
                                        @if ($details['priority']['id'] != 0)
                                        <span class="checkbox-label text-gray-800">
                                            {{$details['priority']['name']}}
                                        </span>
                                        @endif
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="priority_id">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Người thực hiện:
                                        </span>
                                        @if ($details['assign']['id'] == 0)
                                        ---
                                        @else
                                        <a class="checkbox-label text-gray-800 hover:text-primary" href="/member/{{$details['assign']['id']}}">
                                            {{$details['assign']['name']}}
                                        </a>
                                        @endif
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="assign_id">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-5">
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Ngày bắt đầu:
                                        </span>
                                        @if ($details['start_date'])
                                        <span class="checkbox-label text-gray-800">
                                            {{formatDateTime($details['start_date'], 'd-m-Y H:i')}}
                                        </span>
                                        @else
                                        ---
                                        @endif
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="start_date">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Ngày kết thúc:
                                        </span>
                                        @if ($details['due_date'])
                                        <span class="checkbox-label text-gray-800">
                                            {{formatDateTime($details['due_date'], 'd-m-Y H:i')}}
                                        </span>
                                        @else
                                        ---
                                        @endif
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="due_date">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            % hoàn thành:
                                        </span>
                                        <div class="flex-1 max-w-32 bg-gray-300 rounded-sm h-4">
                                            <div class="bg-blue-800 h-4 rounded-sm flex items-center {{$details['progress'] == 0 ? 'justify-start' : 'justify-center'}}" style="width: {{$details['progress'] ?? 0}}%">
                                                <span class="text-xs checkbox-label !text-white">
                                                    &nbsp;{{$details['progress']}}%&nbsp;
                                                </span>
                                            </div>
                                        </div>
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="progress">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Thời gian dự kiến:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            {{$details['estimate_time'] ?? 0}}h
                                        </span>
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="estimate_time">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Thời gian thực tế:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            {{$details['spend_time'] ?? 0}}h
                                        </span>
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="spend_time">
                                            <i class="ki-filled ki-notepad-edit">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">
                                    Mô tả
                                </span>
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-description-task-modal">
                                    <i class="ki-filled ki-notepad-edit">
                                    </i>
                                </button>
                            </div>
                            <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                <div class="ql-editor" style="white-space: normal;">
                                    {!! $details['description'] ?? '---' !!}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">
                                    Ghi chú
                                </span>
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="note">
                                    <i class="ki-filled ki-notepad-edit">
                                    </i>
                                </button>
                            </div>
                            <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                <div class="ql-editor" style="white-space: normal;">
                                    {!! nl2br(e($details['note'] ?? '---')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="menu-separator simple"></div>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Chỉ mục
                                    </span>
                                    <span class="badge badge-xs badge-primary badge-outline">{{count($details['childs'])}}</span>
                                </div>
                                <button class="btn btn-light btn-xs" data-modal-toggle="#add-sub-task-modal">
                                    <i class="ki-filled ki-plus">
                                    </i>
                                    Thêm chỉ mục
                                </button>
                            </div>
                            <div class="flex items-center flex-wrap justify-between gap-2.5">
                                @if (count($details['childs']) == 0)
                                ---
                                @endif
                                @foreach ($details['childs'] as $subtask)
                                <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-gray-200 pl-4 hover:bg-gray-50 hover:after:bg-blue-800">
                                    <div class="flex flex-col">
                                        <div>
                                            <a href="/task/{{$subtask['id']}}">
                                                <span class="checkbox-label font-normal text-primary">#{{$subtask['id']}}:</span>
                                                <span class="checkbox-label font-semibold hover:text-primary-active">{{$subtask['name']}}</span>
                                            </a>
                                        </div>
                                        <div>
                                            <span class="checkbox-label font-normal text-{{$subtask['status']['color']}}">{{$subtask['status']['name'] ?? '---'}}</span>
                                            @if ($subtask['priority']['id'] != 0)
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$subtask['priority']['color']}}">{{$subtask['priority']['name']}}</span>
                                            @endif
                                            @if ($subtask['assign']['id'] != 0)
                                            <span>-</span>
                                            <span class="checkbox-label font-medium"><a class="hover:text-primary-active" href="/member/{{$subtask['assign']['id']}}">{{$subtask['assign']['name']}}</a></span>
                                            @endif
                                            @if ($subtask['start_date'])
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">Từ <span class="font-medium">{{formatDateTime($subtask['start_date'], 'd-m-Y')}}</span></span>
                                            @endif
                                            @if ($subtask['due_date'])
                                            <span class="checkbox-label font-normal">đến <span class="font-medium">{{formatDateTime($subtask['due_date'], 'd-m-Y')}}</span></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="menu" data-menu="true">
                                        <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical">
                                                </i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                                <div class="menu-item">
                                                    <a class="menu-link" href="/task/{{$subtask['id']}}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-search-list">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Xem
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-separator"></div>
                                                <div class="menu-item">
                                                    <a class="menu-link" href="/task/{{$subtask['id']}}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Chỉnh sửa
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-item">
                                                    <button class="menu-link" onclick="postRemoveSubTask({{$subtask['id']}})">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-trash">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Gỡ
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
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
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Tổng quan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 gap-5">
                            <div class="flex flex-col gap-5">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Hợp đồng:
                                    </span>
                                    <span class="checkbox-label text-gray-800">
                                        @if ($details['contract']['id'] != 0)
                                        {{$details['contract']['name']}}
                                        @else
                                        ---
                                        @endif
                                    </span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="contract_id">
                                        <i class="ki-filled ki-notepad-edit">
                                        </i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Dịch vụ:
                                    </span>
                                    <ul class="space-y-2">
                                        @if ($details['service']['id'] != 0)
                                        <li class="text-xs checkbox-label text-gray-800">{{$details['service']['name']}}</li>
                                        @else
                                        <li class="text-xs checkbox-label text-gray-800">---</li class="text-xs">
                                        @endif
                                        @if ($details['service_other'])
                                        <li class="text-xs checkbox-label text-gray-800">{{$details['service_other']}}</li>
                                        @endif
                                    </ul>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="service_id">
                                        <i class="ki-filled ki-notepad-edit">
                                        </i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Tiền thưởng hoàn thành:
                                    </span>
                                    <span class="checkbox-label text-gray-800">
                                        {{formatCurrency($details['bonus_amount'] ?? 0)}}đ
                                    </span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="bonus_amount">
                                        <i class="ki-filled ki-notepad-edit">
                                        </i>
                                    </button>
                                </div>
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Tiền phạt trễ hạn:
                                    </span>
                                    <span class="checkbox-label text-gray-800">
                                        {{formatCurrency($details['deduction_amount'] ?? 0)}}đ
                                    </span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="deduction_amount">
                                        <i class="ki-filled ki-notepad-edit">
                                        </i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Đính kèm
                        </h3>
                        <button class="btn btn-light btn-xs" data-modal-toggle="#upload-file-task-modal">
                            <i class="ki-outline ki-exit-up"></i>
                            Tải lên
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-2.5 lg:gap-5">
                            @foreach ($attachments as $attachment)
                            <div class="flex items-center gap-3">
                                <a href="https://drive.google.com/file/d/{{ $attachment['driver_id'] }}/view" target="_blank" class="flex items-center grow gap-2.5">
                                    @if (Str::startsWith($attachment['type'], 'image/') && $attachment['extension'] != 'svg')
                                    <img class="w-[30px]" alt="{{$attachment['extension']}}.svg" src="https://drive.google.com/thumbnail?id={{ $attachment['driver_id'] }}&sz=w56">
                                    @else
                                    <img class="w-[30px]" alt="{{$attachment['extension']}}.svg" src="{{asset('assets/images/file-types/' . $attachment['extension'] . '.svg')}}">
                                    @endif
                                    <div class="flex flex-col">
                                        <span style="overflow-wrap: anywhere;" class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                            {{$attachment['name']}}
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            {{formatBytes($attachment['size'])}}
                                        </span>
                                    </div>
                                </a>
                                <div class="menu" data-menu="true">
                                    <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-dots-vertical">
                                            </i>
                                        </button>
                                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                            <div class="menu-item">
                                                <a class="menu-link" href="https://drive.google.com/uc?id={{ $attachment['driver_id'] }}&export=download" download>
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-file-down">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Tải xuống
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <button class="menu-link" onclick="postRemoveAttachmentTask({{$attachment['id']}})">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-delete-files">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Gỡ
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Bình luận
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="comment-task-form" class="flex flex-col gap-2.5">
                            <div class="form-info leading-5 text-gray-800 font-normal">
                                <ul class="ml-4 list-disc space-y-2">
                                    @foreach ($details['comments'] as $comment)
                                    <li class="text-xs">
                                        <p class="mb-1"><b>{{$comment['user']['name'] ?? 'Ẩn danh'}}</b> - <span class="text-xs">{{timeAgo(strtotime($comment['created_at']))}}</span></p>
                                        <div>{!! nl2br(e($comment['message'])) !!}</div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="form-info leading-5 text-gray-800 font-normal">
                                <textarea class="textarea text-2sm text-gray-600 font-normal" name="message" rows="2" placeholder="Nhập bình luận tại đây"></textarea>
                            </div>
                            <div class="form-info leading-5 text-gray-800 font-normal flex justify-end">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Đăng
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Lịch sử
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="flex flex-col scrollable-y-auto max-h-[500px]">
                            @foreach ($activity_logs as $log)
                            <div class="flex items-start relative">
                                @if ($log['index'] != count($activity_logs) - 1)
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                @endif
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base">
                                    </i>
                                </div>
                                <div class="ps-2.5 mb-7 text-md grow">
                                    <div class="flex flex-col">
                                        <div class="text-xs text-gray-800">
                                            Cập nhật bởi <b>{{$log['user']['name'] ?? '---'}}</b>
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            Khoảng {{timeAgo(strtotime($log['created_at']))}} trước
                                        </span>
                                        <ul class="ml-4 list-disc">
                                            <li class="text-xs">{{$log['details']}}</li>
                                        </ul>
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

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-task-modal" style="z-index: 90;">
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
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <select name="status_id" class="select hidden">
                        <option value="" disabled selected>
                            Chọn trạng thái
                        </option>
                        @foreach ($statuses as $status)
                        <option value="{{$status['id']}}">
                            {{$status['name']}}
                        </option>
                        @endforeach
                    </select>
                    <select name="priority_id" class="select hidden">
                        <option value="" disabled selected>
                            Chọn mức độ ưu tiên
                        </option>
                        @foreach ($priorities as $priority)
                        <option value="{{$priority['id']}}">
                            {{$priority['name']}}
                        </option>
                        @endforeach
                    </select>
                    <select name="assign_id" class="select hidden">
                        <option value="" disabled selected>
                            Chọn thành viên
                        </option>
                        @foreach ($users as $user)
                        <option value="{{$user['id']}}">
                            {{$user['name']}}
                        </option>
                        @endforeach
                    </select>
                    <input class="input hidden" name="name" type="text" placeholder="Vui lòng tên công việc mới">
                    <input class="input hidden" name="start_date" type="text" placeholder="Vui lòng nhập ngày bắt đầu">
                    <input class="input hidden" name="due_date" type="text" placeholder="Vui lòng nhập ngày kết thúc">
                    <select name="progress" class="select hidden">
                        <option value="" selected>
                            Chọn tiến trình
                        </option>
                        @foreach ([0, 20, 50, 60, 80, 90, 100] as $percent)
                        <option value="{{$percent}}">
                            {{$percent}}%
                        </option>
                        @endforeach
                    </select>
                    <select name="contract_id" class="select hidden">
                        <option value="" selected>Chọn hợp đồng</option>
                        @foreach ($contracts as $contract)
                        <option value="{{$contract['id']}}">
                            {{$contract['name']}}
                        </option>
                        @endforeach
                    </select>
                    <select name="service_id" class="select p-2.5 hidden">
                        <option value="" selected>Chọn dịch vụ</option>
                        @foreach ($services as $services)
                        <option value="{{$services['id']}}">
                            {{$services['name']}}
                        </option>
                        @endforeach
                    </select>
                    <input class="input hidden" name="estimate_time" type="text" placeholder="Vui lòng nhập thời gian dự kiến">
                    <input class="input hidden" name="spend_time" type="text" placeholder="Vui lòng nhập thời gian thực tế">
                    <input class="input hidden" name="deduction_amount" type="text" placeholder="Vui lòng nhập tiền phạt">
                    <input class="input hidden" name="bonus_amount" type="text" placeholder="Vui lòng nhập thời tiền thưởng">
                    <textarea class="textarea text-2sm text-gray-600 font-normal hidden" name="note" rows="5" placeholder="Nhập ghi chú tại đây"></textarea>
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
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-description-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[1000px] top-5">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật mô tả
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div id="description_editor"></div>
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
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-sub-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chọn chỉ mục mới
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    @php
                    $existingIds = collect($details['childs'])->pluck('id')->toArray();
                    @endphp
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Chọn chỉ mục có sẵn
                        </span>
                    </div>
                    <select name="sub_task" class="select">
                        @foreach ($tasks as $task)
                        @if (!in_array($task['id'], $existingIds))
                        <option value="{{ $task['id'] }}">
                            #{{ $task['id'] }} - {{ $task['name'] }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
                <div class="flex items-center justify-center">
                    <a class="btn btn-link" href="/task/create?parent_id={{$details['id']}}">
                        Hoặc tạo chỉ mục mới
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="upload-file-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Tải lên tệp
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5" enctype="multipart/form-data">
                <div class="flex flex-col gap-2.5">
                    <div class="checkbox-group">
                        <span class="checkbox-label text-gray-800 !font-bold">
                            Chọn tệp đính kèm
                        </span>
                    </div>
                    <input name="file" class="file-input" type="file" />
                    <input hidden class="input hidden" name="id" type="text" value="{{$details['id']}}">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Tải lên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    let _descriptionQuill;

    $(function() {
        let modalUploadFileTask = document.querySelector('#upload-file-task-modal');
        let instanceUploadFileTask = KTModal.getInstance(modalUploadFileTask);

        $('button[data-modal-toggle="#update-task-modal"][data-name]').on('click', function() {
            let _this = $(this);
            let _modal = $('#update-task-modal');
            _modal.find('input[name], select[name], textarea[name]').val('').addClass('hidden');

            _modal.find(`input[name=${_this.attr('data-name')}], select[name=${_this.attr('data-name')}], textarea[name=${_this.attr('data-name')}]`).removeClass('hidden');
        })

        $('button[data-modal-toggle="#update-description-task-modal"]').on('click', function() {
            _descriptionQuill.clipboard.dangerouslyPasteHTML("");
        })

        instanceUploadFileTask.on('hidden', () => {
            $('#upload-file-task-modal input[name=file]').val('');
        });

        $('#update-task-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateTask();
        })

        $('#comment-task-form').on('submit', function(e) {
            e.preventDefault();
            postCommentTask($(this));
        })

        $('#add-sub-task-modal form').on('submit', function(e) {
            e.preventDefault();
            postAddSubTask($(this));
        })

        $('#upload-file-task-modal form').on('submit', function(e) {
            e.preventDefault();
            postUploadFileTask(this);
        })

        $('#update-description-task-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateDescriptionTask($(this));
        })

        flatpickrMake($("input[name=start_date]"), 'datetime');
        flatpickrMake($("input[name=due_date]"), 'datetime');

        _descriptionQuill = quillTemplate("#description_editor", "350px");
    })

    async function postUpdateTask() {
        let field = $('#update-task-modal form').find('input:not(.hidden),select:not(.hidden),textarea:not(.hidden)');
        let method = "post",
            url = "/task/update",
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

    async function postCommentTask(_this) {
        let method = "post",
            url = "/task/add-comment",
            params = null,
            data = _this.serialize() + "&id={{$details['id']}}";
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

    async function postAddSubTask(_this) {
        let method = "post",
            url = "/task/update-sub-task",
            params = null,
            data = _this.serialize() + "&id={{$details['id']}}&type={{ADD_ENUM_TYPE}}";
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

    async function postUploadFileTask(_this) {
        let method = "post",
            url = "/task/upload-file-task",
            params = null,
            data = new FormData(_this);
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

    async function postRemoveAttachmentTask(attach_id) {
        let method = "post",
            url = "/task/remove-attachment-task",
            params = null,
            data = {
                id: attach_id,
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

    async function postRemoveSubTask(sub_task) {
        let method = "post",
            url = "/task/update-sub-task",
            params = null,
            data = {
                id: "{{$details['id']}}",
                sub_task,
                type: "{{REMOVE_ENUM_TYPE}}"
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

    async function postUpdateDescriptionTask() {
        let method = "post",
            url = "/task/update",
            params = null,
            data = {
                id: "{{$details['id']}}",
                description: _descriptionQuill.root.innerHTML,
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