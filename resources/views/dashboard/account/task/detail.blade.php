@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin công việc
                @if ($details['type'])
                <span class="badge badge-sm badge-outline badge-primary">{{$details['type']}}</span>
                @endif
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            @if (in_array($details['type'], ['SERVICE', 'SUB']) && in_array($details['status']['id'], [1, 2]))
            <button class="btn btn-sm btn-primary" onclick="claimTask({{$details['id']}})">
                <i class="ki-outline ki-check-square me-1"></i>
                Nhận việc
            </button>
            @endif

            @if (in_array($details['type'], ['SERVICE', 'SUB']) && in_array($details['status']['id'], [3]) && (!isset($details['sub_tasks']) || count($details['sub_tasks']) == 0))
            <button class="btn btn-sm btn-success" data-modal-toggle="#report-completion-modal">
                <i class="ki-outline ki-flag me-1"></i>
                Báo cáo hoàn thành
            </button>
            @endif
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
                                    <i class="ki-filled ki-dots-vertical"></i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <a class="menu-link" href="#comment-task-form">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-messages"></i>
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
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                    </div>
                                    @if ($details['parent']['id'] != 0)
                                    <div class="form-info text-gray-800 font-normal">
                                        <a href="/task/{{$details['parent']['id']}}" class="hover:text-primary-active">
                                            @if ($details['type'] == 'SERVICE')
                                            Thuộc hợp đồng: #{{$details['parent']['id']}} - {{$details['parent']['name']}}
                                            @elseif($details['type'] == 'SUB')
                                            Thuộc dịch vụ: #{{$details['parent']['id']}} - {{$details['parent']['name']}}
                                            @else
                                            Nhóm: #{{$details['parent']['id']}} - {{$details['parent']['name']}}
                                            @endif
                                        </a>
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
                                            <i class="ki-filled ki-notepad-edit"></i>
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
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                @if ($details['type'] == 'CONTRACT')
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Người quản lý:
                                        </span>
                                        @if ($details['assign']['id'] == 0)
                                        ---
                                        @else
                                        <a class="checkbox-label text-gray-800 hover:text-primary" href="/member/{{$details['assign']['id']}}">
                                            {{$details['assign']['name']}}
                                        </a>
                                        @endif
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="assign_id">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="flex flex-col gap-5">
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Deadline:
                                        </span>
                                        @if ($details['due_date'])
                                        <span class="checkbox-label text-gray-800">
                                            {{formatDateTime($details['due_date'], 'd-m-Y H:i')}}
                                        </span>
                                        @else
                                        ---
                                        @endif
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="due_date">
                                            <i class="ki-filled ki-notepad-edit"></i>
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
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Số lượng yêu cầu:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            {{$details['qty_request'] ?? 0}}
                                        </span>
                                        <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="qty_request">
                                            <i class="ki-filled ki-notepad-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-5">
                                    <div class="checkbox-group">
                                        <span class="checkbox-label text-gray-800 !font-bold">
                                            Số lượng đã hoàn thành:
                                        </span>
                                        <span class="checkbox-label text-gray-800">
                                            {{$details['qty_completed'] ?? 0}}
                                        </span>
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
                                    <i class="ki-filled ki-notepad-edit"></i>
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
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                            </div>
                            <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                <div class="ql-editor" style="white-space: normal;">
                                    {!! nl2br(e($details['note'] ?? '---')) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Hiển thị danh sách công việc dịch vụ (nếu là công việc hợp đồng) -->
                        @if ($details['type'] == 'CONTRACT' && isset($details['service_tasks']))
                        <div class="menu-separator simple"></div>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Danh sách công việc dịch vụ
                                    </span>
                                    <span class="badge badge-xs badge-primary badge-outline">{{count($details['service_tasks'])}}</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                @if (count($details['service_tasks']) == 0)
                                <div class="text-center text-gray-500">Chưa có công việc dịch vụ nào</div>
                                @endif

                                @foreach ($details['service_tasks'] as $serviceTask)
                                <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-gray-200 pl-4 hover:bg-gray-50 hover:after:bg-blue-800">
                                    <div class="flex flex-col">
                                        <div>
                                            <a href="/task/{{$serviceTask['id']}}">
                                                <span class="checkbox-label font-normal text-primary">#{{$serviceTask['id']}}:</span>
                                                <span class="checkbox-label font-semibold hover:text-primary-active">{{$serviceTask['name']}}</span>
                                            </a>
                                        </div>
                                        <div>
                                            <span class="checkbox-label font-normal text-gray-700">{{$serviceTask['qty_completed'] ?? 0}}/{{$serviceTask['qty_request'] ?? 0}}</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$serviceTask['status']['color']}}">{{$serviceTask['status']['name'] ?? '---'}}</span>
                                            @if ($serviceTask['priority']['id'] != 0)
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$serviceTask['priority']['color']}}">{{$serviceTask['priority']['name']}}</span>
                                            @endif
                                            @if ($serviceTask['due_date'])
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">Deadline: <span class="font-medium">{{formatDateTime($serviceTask['due_date'], 'd-m-Y')}}</span></span>
                                            @endif
                                        </div>

                                        <!-- Hiển thị công việc con của dịch vụ này -->
                                        @if (count($serviceTask['sub_tasks']) > 0)
                                        <div class="mt-2 pl-4">
                                            <div class="text-xs font-medium text-gray-700 mb-1">Công việc con:</div>
                                            @foreach ($serviceTask['sub_tasks'] as $subTask)
                                            <div class="relative flex items-center justify-between gap-1 w-full mt-1 after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[3px] after:h-[78%] after:bg-gray-200 pl-3 hover:bg-gray-50 hover:after:bg-blue-800">
                                                <div class="flex flex-col">
                                                    <div>
                                                        <a href="/task/{{$subTask['id']}}">
                                                            <span class="text-xs font-normal text-primary">#{{$subTask['id']}}:</span>
                                                            <span class="text-xs font-semibold hover:text-primary-active">{{$subTask['name']}}</span>
                                                        </a>
                                                    </div>
                                                    <div class="text-xs">
                                                        <span class="font-normal text-gray-700">{{$subTask['qty_completed'] ?? 0}}/{{$subTask['qty_request'] ?? 0}}</span>
                                                        <span>-</span>
                                                        <span class="font-normal text-{{$subTask['status']['color']}}">{{$subTask['status']['name'] ?? '---'}}</span>
                                                        @if ($subTask['due_date'])
                                                        <span>-</span>
                                                        <span class="font-normal">Deadline: <span class="font-medium">{{formatDateTime($subTask['due_date'], 'd-m-Y')}}</span></span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex space-x-1">
                                                    @if (in_array($subTask['status']['id'], [1, 2]))
                                                    <button class="btn btn-xs btn-primary" onclick="claimTask({{$subTask['id']}})">
                                                        Nhận việc
                                                    </button>
                                                    @elseif (in_array($subTask['status']['id'], [3]))
                                                    <button class="btn btn-xs btn-success" onclick="openReportCompletionModal({{$subTask['id']}}, '{{$subTask['name']}}', {{$subTask['qty_request']}}, {{$subTask['qty_completed']}})">
                                                        Báo cáo
                                                    </button>
                                                    @endif
                                                    <a href="/task/{{$subTask['id']}}" class="btn btn-xs btn-light">
                                                        <i class="ki-outline ki-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex space-x-1">
                                        @if (in_array($serviceTask['status']['id'], [1, 2]))
                                        <button class="btn btn-xs btn-primary" onclick="claimTask({{$serviceTask['id']}})">
                                            Nhận việc
                                        </button>
                                        @elseif (in_array($serviceTask['status']['id'], [3]) && count($serviceTask['sub_tasks']) == 0)
                                        <button class="btn btn-xs btn-success" onclick="openReportCompletionModal({{$serviceTask['id']}}, '{{$serviceTask['name']}}', {{$serviceTask['qty_request']}}, {{$serviceTask['qty_completed']}})">
                                            Báo cáo
                                        </button>
                                        @endif
                                        <a href="/task/{{$serviceTask['id']}}" class="btn btn-xs btn-light">
                                            <i class="ki-outline ki-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Hiển thị danh sách công việc con (nếu là công việc dịch vụ) -->
                        @if ($details['type'] == 'SERVICE' && isset($details['sub_tasks']))
                        <div class="menu-separator simple"></div>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Danh sách công việc con
                                    </span>
                                    <span class="badge badge-xs badge-primary badge-outline">{{count($details['sub_tasks'])}}</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                @if (count($details['sub_tasks']) == 0)
                                <div class="text-center text-gray-500">Chưa có công việc con nào</div>
                                @endif

                                @foreach ($details['sub_tasks'] as $subTask)
                                <div class="relative flex items-center justify-between gap-1 w-full after:absolute after:top-1/2 after:-translate-y-1/2 after:left-0 after:w-[4px] after:h-[78%] after:bg-gray-200 pl-4 hover:bg-gray-50 hover:after:bg-blue-800">
                                    <div class="flex flex-col">
                                        <div>
                                            <a href="/task/{{$subTask['id']}}">
                                                <span class="checkbox-label font-normal text-primary">#{{$subTask['id']}}:</span>
                                                <span class="checkbox-label font-semibold hover:text-primary-active">{{$subTask['name']}}</span>
                                            </a>
                                        </div>
                                        <div>
                                            <span class="checkbox-label font-normal text-gray-700">{{$subTask['qty_completed'] ?? 0}}/{{$subTask['qty_request'] ?? 0}}</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$subTask['status']['color']}}">{{$subTask['status']['name'] ?? '---'}}</span>
                                            @if ($subTask['priority']['id'] != 0)
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$subTask['priority']['color']}}">{{$subTask['priority']['name']}}</span>
                                            @endif
                                            @if ($subTask['due_date'])
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">Deadline: <span class="font-medium">{{formatDateTime($subTask['due_date'], 'd-m-Y')}}</span></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-1">
                                        @if (in_array($subTask['status']['id'], [1, 2]))
                                        <button class="btn btn-xs btn-primary" onclick="claimTask({{$subTask['id']}})">
                                            Nhận việc
                                        </button>
                                        @elseif (in_array($subTask['status']['id'], [3]))
                                        <button class="btn btn-xs btn-success" onclick="openReportCompletionModal({{$subTask['id']}}, '{{$subTask['name']}}', {{$subTask['qty_request']}}, {{$subTask['qty_completed']}})">
                                            Báo cáo
                                        </button>
                                        @endif
                                        <a href="/task/{{$subTask['id']}}" class="btn btn-xs btn-light">
                                            <i class="ki-outline ki-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        {{--
                        <!-- Hiển thị danh sách chỉ mục (cho mục cũ) -->
                        @if (count($details['childs']) > 0)
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
                                    <i class="ki-filled ki-plus"></i>
                                    Thêm chỉ mục
                                </button>
                            </div>
                            <div class="flex items-center flex-wrap justify-between gap-2.5">
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
                                            <span class="checkbox-label font-normal text-gray-700">{{$subtask['qty_completed'] ?? 0}}/{{$subtask['qty_request'] ?? 0}}</span>
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$subtask['status']['color']}}">{{$subtask['status']['name'] ?? '---'}}</span>
                                            @if ($subtask['priority']['id'] != 0)
                                            <span>-</span>
                                            <span class="checkbox-label font-normal text-{{$subtask['priority']['color']}}">{{$subtask['priority']['name']}}</span>
                                            @endif
                                            @if ($subtask['due_date'])
                                            <span>-</span>
                                            <span class="checkbox-label font-normal">Deadline: <span class="font-medium">{{formatDateTime($subtask['due_date'], 'd-m-Y')}}</span></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="menu" data-menu="true">
                                        <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical"></i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                                <div class="menu-item">
                                                    <a class="menu-link" href="/task/{{$subtask['id']}}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-search-list"></i>
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
                                                            <i class="ki-filled ki-pencil"></i>
                                                        </span>
                                                        <span class="menu-title">
                                                            Chỉnh sửa
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-item">
                                                    <button class="menu-link" onclick="postRemoveSubTask({{$subtask['id']}})">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-trash"></i>
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
                        @endif
                        --}}
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
                                        <a href="/contract/{{$details['contract']['id']}}">
                                            <span class="checkbox-label font-semibold hover:text-primary-active">{{$details['contract']['name']}}</span>
                                        </a>
                                        @else
                                        ---
                                        @endif
                                    </span>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="contract_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
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
                                        <li class="text-xs checkbox-label text-gray-800">---</li>
                                        @endif
                                        @if ($details['service_other'])
                                        <li class="text-xs checkbox-label text-gray-800">{{$details['service_other']}}</li>
                                        @endif
                                    </ul>
                                    <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-task-modal" data-name="service_id">
                                        <i class="ki-filled ki-notepad-edit"></i>
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
                                        <i class="ki-filled ki-notepad-edit"></i>
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
                                        <i class="ki-filled ki-notepad-edit"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách người đóng góp công việc -->
                @if (in_array($details['type'], ['SUB', 'SERVICE']) && isset($contributions) && count($contributions) > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Người tham gia
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-3">
                            @foreach ($contributions as $contribution)
                            <div class="flex items-center justify-between border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center gap-2">
                                    <div class="bg-gray-200 rounded-full h-8 w-8 flex items-center justify-center">
                                        <i class="ki-outline ki-user text-gray-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{$contribution['user']['name']}}</div>
                                        <div class="text-xs text-gray-600">
                                            {{formatDateTime($contribution['date_completed'], 'd-m-Y H:i')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-semibold text-primary-600">
                                        {{$contribution['quantity']}} đơn vị
                                    </div>
                                    @if (session()->get(ACCOUNT_CURRENT_SESSION)['id'] == $contribution['user']['id'] || session()->get(ACCOUNT_CURRENT_SESSION)['is_admin'])
                                    <button class="btn btn-xs btn-icon btn-light" onclick="deleteContribution({{$contribution['id']}})">
                                        <i class="ki-outline ki-trash text-danger"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

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
                            @if (count($attachments) == 0)
                            <div class="text-center text-gray-500">Chưa có tệp đính kèm</div>
                            @endif
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
                                            <i class="ki-filled ki-dots-vertical"></i>
                                        </button>
                                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                            <div class="menu-item">
                                                <a class="menu-link" href="https://drive.google.com/uc?id={{ $attachment['driver_id'] }}&export=download" download>
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-file-down"></i>
                                                    </span>
                                                    <span class="menu-title">
                                                        Tải xuống
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <button class="menu-link" onclick="postRemoveAttachmentTask({{$attachment['id']}})">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-delete-files"></i>
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
                                @if (count($details['comments']) == 0)
                                <div class="text-center text-gray-500 mb-3">Chưa có bình luận</div>
                                @else
                                <ul class="ml-4 list-disc space-y-2">
                                    @foreach ($details['comments'] as $comment)
                                    <li class="text-xs">
                                        <p class="mb-1"><b>{{$comment['user']['name'] ?? 'Ẩn danh'}}</b> - <span class="text-xs">{{timeAgo(strtotime($comment['created_at']))}}</span></p>
                                        <div>{!! nl2br(e($comment['message'])) !!}</div>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
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
                            @if (count($activity_logs) == 0)
                            <div class="text-center text-gray-500">Chưa có lịch sử hoạt động</div>
                            @endif
                            @foreach ($activity_logs as $log)
                            <div class="flex items-start relative">
                                @if ($log['index'] != count($activity_logs) - 1)
                                <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300">
                                </div>
                                @endif
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base"></i>
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

<!-- Modal cập nhật thông tin -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật thông tin
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
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
                    <input class="input hidden" name="name" type="text" placeholder="Vui lòng nhập tên công việc mới">
                    <input class="input hidden" name="due_date" type="text" placeholder="Vui lòng nhập deadline">
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
                        @foreach ($services as $service)
                        <option value="{{$service['id']}}">
                            {{$service['name']}}
                        </option>
                        @endforeach
                    </select>
                    <input class="input hidden" name="qty_request" type="text" placeholder="Vui lòng nhập số lượng yêu cầu">
                    <input class="input hidden" name="deduction_amount" type="text" placeholder="Vui lòng nhập tiền phạt">
                    <input class="input hidden" name="bonus_amount" type="text" placeholder="Vui lòng nhập tiền thưởng">
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

<!-- Modal cập nhật mô tả -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-description-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[1000px] top-5">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Cập nhật mô tả
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
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

<!-- Modal thêm chỉ mục -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-sub-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chọn chỉ mục mới
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
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

<!-- Modal tải lên tệp -->
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

<!-- Modal báo cáo hoàn thành -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="report-completion-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Báo cáo hoàn thành: <span id="report-task-name">{{$details['name']}}</span>
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="report-completion-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="form-group">
                        <label class="form-label required">Số lượng đã hoàn thành</label>
                        <input name="quantity" type="number" min="1" max="{{$details['qty_request'] - $details['qty_completed']}}" class="input" placeholder="Nhập số lượng" required>
                        <input type="hidden" name="task_id" value="{{$details['id']}}" id="report-task-id">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ghi chú (nếu có)</label>
                        <textarea name="note" class="textarea" rows="3" placeholder="Nhập ghi chú về công việc đã hoàn thành"></textarea>
                    </div>
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Báo cáo
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
            _descriptionQuill.clipboard.dangerouslyPasteHTML("{!! $details['description'] !!}");
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

        $('#report-completion-form').on('submit', function(e) {
            e.preventDefault();
            postReportCompletion($(this));
        })

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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function postRemoveAttachmentTask(attach_id) {
        Notiflix.Confirm.show(
            'Xoá tệp đính kèm',
            'Bạn có chắc chắn muốn xoá tệp đính kèm này?',
            'Đúng',
            'Huỷ',
            async () => {
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
                            showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                            break;
                    }
                },
                () => {}, {},
        );
    }

    async function postRemoveSubTask(sub_task) {
        Notiflix.Confirm.show(
            'Xoá chỉ mục',
            'Bạn có chắc chắn muốn xoá chỉ mục này?',
            'Đúng',
            'Huỷ',
            async () => {
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
                            showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                            break;
                    }
                },
                () => {}, {},
        );
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
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function claimTask(taskId) {
        Notiflix.Confirm.show(
            'Nhận công việc',
            'Bạn có chắc chắn muốn nhận công việc này?',
            'Đúng',
            'Huỷ',
            async () => {
                    let method = "post",
                        url = "/task/claim",
                        params = null,
                        data = {
                            task_id: taskId
                        };
                    let res = await axiosTemplate(method, url, params, data);
                    switch (res.data.status) {
                        case 200:
                            showAlert('success', res.data.message);
                            window.location.reload();
                            break;
                        default:
                            showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                            break;
                    }
                },
                () => {}, {},
        );
    }

    async function postReportCompletion(_this) {
        let method = "post",
            url = "/task/add-contribution",
            params = null,
            data = _this.serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    }

    async function deleteContribution(contributionId) {
        Notiflix.Confirm.show(
            'Xóa báo cáo',
            'Bạn có chắc chắn muốn xóa báo cáo hoàn thành này?',
            'Đúng',
            'Huỷ',
            async () => {
                    let method = "post",
                        url = "/task/delete-contribution",
                        params = null,
                        data = {
                            contribution_id: contributionId
                        };
                    let res = await axiosTemplate(method, url, params, data);
                    switch (res.data.status) {
                        case 200:
                            showAlert('success', res.data.message);
                            window.location.reload();
                            break;
                        default:
                            showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                            break;
                    }
                },
                () => {}, {},
        );
    }

    // Hàm mở modal báo cáo hoàn thành với thông tin của task cụ thể
    function openReportCompletionModal(taskId, taskName, totalQty, completedQty) {
        // Cập nhật thông tin task vào modal
        $('#report-task-name').text(taskName);
        $('#report-task-id').val(taskId);

        // Cập nhật số lượng tối đa có thể báo cáo
        let maxQty = totalQty - completedQty;
        $('input[name="quantity"]').attr('max', maxQty);

        // Mở modal
        KTModal.getInstance(document.querySelector('#report-completion-modal')).show();
    }
</script>
@endpush