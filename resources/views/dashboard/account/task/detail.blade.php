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
            <button class="btn btn-sm btn-primary" onclick="openClaimTaskModal({{$details['id']}})">
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
                                            Hạn chót:
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

                        <div class="menu-separator simple"></div>
                        <div class="flex flex-col gap-2.5">
                            <div class="rounded-lg p-0">
                                <div class="tab-content">
                                    <!-- Tab feedback -->
                                    <div class="tab-pane" id="task-feedbacks-tab" role="tabpanel">
                                        <div class="p-3">
                                            <div class="flex items-center justify-between mb-3">
                                                <h3 class="font-medium">Feedback từ người quản lý</h3>
                                                @if ($details['assign']['id'] == session()->get(ACCOUNT_CURRENT_SESSION)['id'] || session()->get(ACCOUNT_CURRENT_SESSION)['is_admin'])
                                                <button class="btn btn-sm btn-primary" onclick="openAddFeedbackModal({{$details['id']}})">
                                                    <i class="ki-outline ki-message-star me-1"></i> Thêm feedback
                                                </button>
                                                @endif
                                            </div>
                                            
                                            <div id="task-feedbacks-list" class="space-y-3">
                                                <!-- Danh sách feedback sẽ được render từ JavaScript -->
                                                <div class="text-center text-gray-500 py-5">Đang tải...</div>
                                            </div>
                                        </div>
                                    </div>
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
                            <div class="flex flex-col gap-3">
                                @if (count($details['service_tasks']) == 0)
                                <div class="text-center text-gray-500">Chưa có công việc dịch vụ nào</div>
                                @endif

                                @foreach ($details['service_tasks'] as $serviceTask)
                                @php
                                    $isOverdue = false;
                                    $overdueDays = 0;
                                    if ($serviceTask['due_date']) {
                                        $dueDate = \Carbon\Carbon::parse($serviceTask['due_date']);
                                        $now = \Carbon\Carbon::now();
                                        if ($dueDate->lt($now) && $serviceTask['progress'] < 100) {
                                            $isOverdue = true;
                                            $overdueDays = $now->diffInDays($dueDate);
                                        }
                                    }
                                @endphp
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <!-- Header với tên và action buttons -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div>
                                            <a href="/task/{{$serviceTask['id']}}" class="text-primary hover:text-primary-active font-medium">
                                                {{$serviceTask['name']}}
                                            </a>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs font-medium">{{$serviceTask['progress'] ?? 0}}%</span>
                                                <div class="w-16 bg-gray-200 rounded h-1.5">
                                                    <div class="bg-blue-600 h-1.5 rounded" style="width: {{$serviceTask['progress'] ?? 0}}%"></div>
                                                </div>
                                            </div>
                                            @if (in_array($serviceTask['status']['id'], [1, 2]))
                                            <button class="btn btn-xs btn-primary" onclick="openClaimTaskModal({{$serviceTask['id']}})">
                                            Nhận việc
                                            </button>
                                            @elseif (in_array($serviceTask['status']['id'], [3]) && count($serviceTask['sub_tasks']) == 0)
                                            <button class="btn btn-xs btn-success" onclick="openReportMissionsModal({{$serviceTask['id']}}, '{{$serviceTask['name']}}')">
                                                Báo cáo
                                            </button>
                                            @endif
                                            <a href="/task/{{$serviceTask['id']}}" class="btn btn-xs btn-icon btn-light">
                                                <i class="ki-outline ki-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Thông tin tóm tắt -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 text-xs">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Mã:</span>
                                                <span>#{{$serviceTask['id']}}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Trạng thái:</span>
                                                <span class="badge badge-sm badge-{{$serviceTask['status']['color']}}">{{$serviceTask['status']['name']}}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Số lượng:</span>
                                                <span>{{$serviceTask['qty_completed'] ?? 0}}/{{$serviceTask['qty_request'] ?? 0}} đơn vị</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            @if ($serviceTask['due_date'])
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Hạn chót:</span>
                                                <span>{{formatDateTime($serviceTask['due_date'], 'd-m-Y')}}</span>
                                            </div>
                                            @if ($isOverdue)
                                            <div class="text-danger font-medium">
                                                Đã quá hạn {{$overdueDays}} ngày!
                                            </div>
                                            @endif
                                            @endif
                                            @if ($serviceTask['priority']['id'] != 0)
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Độ ưu tiên:</span>
                                                <span class="text-{{$serviceTask['priority']['color']}}">{{$serviceTask['priority']['name']}}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Hiển thị công việc con của dịch vụ này -->
                                    @if (count($serviceTask['sub_tasks']) > 0)
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        <div class="flex items-center gap-1 mb-1">
                                            <button class="text-xs text-gray-600 flex items-center gap-1 w-full" 
                                                    onclick="toggleSubTasks_{{$serviceTask['id']}}()">
                                                <i class="ki-outline ki-right text-xs" id="icon-{{$serviceTask['id']}}"></i>
                                                <span>{{count($serviceTask['sub_tasks'])}} công việc con</span>
                                            </button>
                                        </div>
                                        <div id="sub-tasks-{{$serviceTask['id']}}" class="hidden pl-3 space-y-2 mt-2">
                                            @foreach ($serviceTask['sub_tasks'] as $subTask)
                                            @php
                                                $isSubTaskOverdue = false;
                                                $subTaskOverdueDays = 0;
                                                if ($subTask['due_date']) {
                                                    $subTaskDueDate = \Carbon\Carbon::parse($subTask['due_date']);
                                                    $now = \Carbon\Carbon::now();
                                                    if ($subTaskDueDate->lt($now) && ($subTask['progress'] ?? 0) < 100) {
                                                        $isSubTaskOverdue = true;
                                                        $subTaskOverdueDays = $now->diffInDays($subTaskDueDate);
                                                    }
                                                }
                                                $subTaskProgress = $subTask['qty_request'] > 0 
                                                    ? round(($subTask['qty_completed'] ?? 0) / $subTask['qty_request'] * 100) 
                                                    : 0;
                                            @endphp
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between bg-gray-50 p-2 rounded gap-2">
                                                <div>
                                                    <a href="/task/{{$subTask['id']}}" class="text-xs hover:text-primary font-semibold">
                                                        Tên: {{$subTask['name']}}
                                                    </a>
                                                    <div class="grid grid-cols-1 gap-1 mt-1 text-xs">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-semibold">Trạng thái:</span>
                                                            <span class="badge badge-sm badge-{{$subTask['status']['color']}}">{{$subTask['status']['name']}}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-semibold">Số lượng:</span>
                                                            <span>{{$subTask['qty_completed'] ?? 0}}/{{$subTask['qty_request'] ?? 0}} đơn vị</span>
                                                        </div>
                                                        @if ($subTask['due_date'])
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-semibold">Hạn chót:</span>
                                                            <span>{{formatDateTime($subTask['due_date'], 'd-m-Y')}}</span>
                                                        </div>
                                                        @if ($isSubTaskOverdue)
                                                        <div class="text-danger text-xs font-medium">
                                                            Đã quá hạn {{$subTaskOverdueDays}} ngày!
                                                        </div>
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-xs font-medium">{{$subTaskProgress}}%</span>
                                                        <div class="w-12 bg-gray-200 rounded h-1">
                                                            <div class="bg-blue-400 h-1 rounded" style="width: {{$subTaskProgress}}%"></div>
                                                        </div>
                                                    </div>
                                                    <div class="flex space-x-1">
                                                        @if (in_array($subTask['status']['id'], [1, 2]))
                                                        <button class="btn btn-xs btn-primary" onclick="openClaimTaskModal({{$subTask['id']}})">
                                                        Nhận việc
                                                        </button>
                                                        @elseif (in_array($subTask['status']['id'], [3]))
                                                        <button class="btn btn-xs btn-success" onclick="openReportMissionsModal({{$subTask['id']}}, '{{$subTask['name']}}')">
                                                            Báo cáo
                                                        </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <script>
                                        function toggleSubTasks_{{$serviceTask['id']}}() {
                                            const content = document.getElementById('sub-tasks-{{$serviceTask['id']}}');
                                            const icon = document.getElementById('icon-{{$serviceTask['id']}}');
                                            
                                            if (content.classList.contains('hidden')) {
                                                content.classList.remove('hidden');
                                                icon.classList.remove('ki-right');
                                                icon.classList.add('ki-down');
                                            } else {
                                                content.classList.add('hidden');
                                                icon.classList.remove('ki-down');
                                                icon.classList.add('ki-right');
                                            }
                                        }
                                    </script>
                                    @endif
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
                            <div class="flex flex-col gap-3">
                                @if (count($details['sub_tasks']) == 0)
                                <div class="text-center text-gray-500">Chưa có công việc con nào</div>
                                @endif

                                @foreach ($details['sub_tasks'] as $subTask)
                                @php
                                    $isOverdue = false;
                                    $overdueDays = 0;
                                    if ($subTask['due_date']) {
                                        $dueDate = \Carbon\Carbon::parse($subTask['due_date']);
                                        $now = \Carbon\Carbon::now();
                                        if ($dueDate->lt($now) && ($subTask['progress'] ?? 0) < 100) {
                                            $isOverdue = true;
                                            $overdueDays = $now->diffInDays($dueDate);
                                        }
                                    }
                                    $subTaskProgress = $subTask['qty_request'] > 0 
                                        ? round(($subTask['qty_completed'] ?? 0) / $subTask['qty_request'] * 100) 
                                        : 0;
                                @endphp
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <!-- Header với tên và action buttons -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div>
                                            <a href="/task/{{$subTask['id']}}" class="text-primary hover:text-primary-active font-medium">
                                                {{$subTask['name']}}
                                            </a>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs font-medium">{{$subTaskProgress}}%</span>
                                                <div class="w-16 bg-gray-200 rounded h-1.5">
                                                    <div class="bg-blue-600 h-1.5 rounded" style="width: {{$subTaskProgress}}%"></div>
                                                </div>
                                            </div>
                                            @if (in_array($subTask['status']['id'], [1, 2]))
                                            <button class="btn btn-xs btn-primary" onclick="openClaimTaskModal({{$subTask['id']}})">
                                            Nhận việc
                                            </button>
                                            @elseif (in_array($subTask['status']['id'], [3]))
                                            <button class="btn btn-xs btn-success" onclick="openReportMissionsModal({{$subTask['id']}}, '{{$subTask['name']}}')">
                                                Báo cáo
                                            </button>
                                            @endif
                                            <a href="/task/{{$subTask['id']}}" class="btn btn-xs btn-icon btn-light">
                                                <i class="ki-outline ki-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Thông tin tóm tắt -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 text-xs">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Mã:</span>
                                                <span>#{{$subTask['id']}}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Trạng thái:</span>
                                                <span class="badge badge-sm badge-{{$subTask['status']['color']}}">{{$subTask['status']['name']}}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Số lượng:</span>
                                                <span>{{$subTask['qty_completed'] ?? 0}}/{{$subTask['qty_request'] ?? 0}} đơn vị</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            @if ($subTask['due_date'])
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Hạn chót:</span>
                                                <span>{{formatDateTime($subTask['due_date'], 'd-m-Y')}}</span>
                                            </div>
                                            @if ($isOverdue)
                                            <div class="text-danger font-medium">
                                                Đã quá hạn {{$overdueDays}} ngày!
                                            </div>
                                            @endif
                                            @endif
                                            @if ($subTask['priority']['id'] != 0)
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Độ ưu tiên:</span>
                                                <span class="text-{{$subTask['priority']['color']}}">{{$subTask['priority']['name']}}</span>
                                            </div>
                                            @endif
                                            @if ($subTask['assign']['id'] != 0)
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Người phụ trách:</span>
                                                <span>{{$subTask['assign']['name']}}</span>
                                            </div>
                                            @endif
                                        </div>
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
                                            <span class="checkbox-label font-normal">Hạn chót: <span class="font-medium">{{formatDateTime($subtask['due_date'], 'd-m-Y')}}</span></span>
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
                                </div>
                                {{--<div class="checkbox-group">
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
                                </div>--}}
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
                    <input class="input hidden" name="due_date" type="text" placeholder="Vui lòng nhập hạn chót">
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

<!-- Modal nhận việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="claim-task-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Nhận công việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="claim-task-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="task_id" id="claim-task-id" value="">
                
                <div class="flex flex-col gap-2.5">
                    <div class="form-group">
                        <label class="form-label required">Chọn nhiệm vụ cần thực hiện cho công việc này</label>
                        <div class="flex flex-col items-start gap-4 py-4" id="mission-list">
                            <!-- Danh sách nhiệm vụ sẽ được render từ JavaScript -->
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Nhận việc
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal báo cáo nhiệm vụ -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="report-missions-modal" style="z-index: 90;">
    <div class="modal-content max-w-[600px] top-5 lg:top-[10%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Báo cáo hoàn thành nhiệm vụ: <span id="report-task-name"></span>
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="report-missions-form" class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="grid gap-3" id="mission-reports">
                        <!-- Danh sách nhiệm vụ sẽ được render từ JavaScript -->
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

<!-- Modal thêm feedback -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-feedback-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thêm feedback cho công việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="add-feedback-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="task_id" id="feedback-task-id" value="">
                
                <div class="flex flex-col gap-2.5">
                    <div class="form-group">
                        <label class="form-label required">Đánh giá</label>
                        <div class="flex items-center gap-2" id="rating-stars">
                            <i class="ki-solid ki-star fs-2 cursor-pointer text-gray-300" data-rating="1"></i>
                            <i class="ki-solid ki-star fs-2 cursor-pointer text-gray-300" data-rating="2"></i>
                            <i class="ki-solid ki-star fs-2 cursor-pointer text-gray-300" data-rating="3"></i>
                            <i class="ki-solid ki-star fs-2 cursor-pointer text-gray-300" data-rating="4"></i>
                            <i class="ki-solid ki-star fs-2 cursor-pointer text-gray-300" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="rating-value" value="0">
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="needs_revision" id="needs-revision">
                            <label class="form-check-label" for="needs-revision">
                                Yêu cầu chỉnh sửa
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Bình luận</label>
                        <textarea name="comment" class="textarea" rows="3" placeholder="Nhập bình luận về công việc"></textarea>
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Gửi feedback
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

        // Xử lý form nhận việc
        $('#claim-task-form').on('submit', async function(e) {
            e.preventDefault();
            
            // Kiểm tra chọn ít nhất một nhiệm vụ
            if ($('input[name="mission_ids[]"]:checked').length === 0) {
                showAlert('warning', 'Vui lòng chọn ít nhất một nhiệm vụ');
                return;
            }
            
            const formData = $(this).serialize();
            
            try {
                const response = await axios.post('/task/claim', formData);
                if (response.data.status === 200) {
                    showAlert('success', response.data.message);
                    KTModal.getInstance(document.querySelector('#claim-task-modal')).hide();
                    window.location.reload();
                } else {
                    showAlert('warning', response.data.message);
                }
            } catch (error) {
                console.error('Error claiming task:', error);
                showAlert('error', 'Không thể nhận việc');
            }
        });

        // Xử lý form báo cáo nhiệm vụ
        $('#report-missions-form').on('submit', async function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            
            try {
                const response = await axios.post('/task/report-mission', formData);
                if (response.data.status === 200) {
                    showAlert('success', response.data.message);
                    KTModal.getInstance(document.querySelector('#report-missions-modal')).hide();
                    window.location.reload();
                } else {
                    showAlert('warning', response.data.message);
                }
            } catch (error) {
                console.error('Error reporting missions:', error);
                showAlert('error', 'Không thể báo cáo nhiệm vụ');
            }
        });

        // Xử lý đánh giá sao
        $('#rating-stars i').on('click', function() {
            const rating = $(this).data('rating');
            $('#rating-value').val(rating);
            
            $('#rating-stars i').each(function() {
                const starRating = $(this).data('rating');
                if (starRating <= rating) {
                    $(this).removeClass('text-gray-300').addClass('text-warning');
                } else {
                    $(this).removeClass('text-warning').addClass('text-gray-300');
                }
            });
        });

        // Xử lý form thêm feedback
        $('#add-feedback-form').on('submit', async function(e) {
            e.preventDefault();
            
            const rating = $('#rating-value').val();
            if (rating < 1) {
                showAlert('warning', 'Vui lòng chọn đánh giá sao');
                return;
            }
            
            const formData = $(this).serialize();
            
            try {
                const response = await axios.post('/task/add-feedback', formData);
                if (response.data.status === 200) {
                    showAlert('success', response.data.message);
                    KTModal.getInstance(document.querySelector('#add-feedback-modal')).hide();
                    window.location.reload();
                } else {
                    showAlert('warning', response.data.message);
                }
            } catch (error) {
                console.error('Error adding feedback:', error);
                showAlert('error', 'Không thể thêm feedback');
            }
        });

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

    // Xử lý modal nhận việc
    function openClaimTaskModal(taskId) {
        $('#claim-task-id').val(taskId);
        loadMissions();
        KTModal.getInstance(document.querySelector('#claim-task-modal')).show();
    }

    // Tải danh sách nhiệm vụ
    async function loadMissions() {
        try {
            const response = await axios.get('/task/missions');
            if (response.data.status === 200) {
                const missions = response.data.data;
                let html = '';
                
                missions.forEach(mission => {
                    html += `
                    <label for="mission-${mission.id}" class="form-label flex items-center gap-2.5">
                        <input checked class="checkbox" name="mission_ids[]" type="checkbox" value="${mission.id}" id="mission-${mission.id}"/>
                        ${mission.name} <span class="text-gray-600">(${mission.salary}đ)</span>
                    </label>
                    `;
                });
                
                $('#mission-list').html(html);
            } else {
                showAlert('warning', response.data.message);
            }
        } catch (error) {
            console.error('Error loading missions:', error);
            showAlert('error', 'Không thể tải danh sách nhiệm vụ');
        }
    }


    // Xử lý modal báo cáo nhiệm vụ
    function openReportMissionsModal(taskId, taskName) {
        $('#report-task-name').text(taskName);
        loadTaskMissions(taskId);
        KTModal.getInstance(document.querySelector('#report-missions-modal')).show();
    }

    // Tải danh sách nhiệm vụ của task
    async function loadTaskMissions(taskId) {
        try {
            const response = await axios.get('/task/task-missions', {
                params: { task_id: taskId }
            });
            
            if (response.data.status === 200) {
                const data = response.data.data;
                let html = '';
                
                data.assignments.forEach(assignment => {
                    const remaining = assignment.quantity_required - assignment.quantity_completed;
                    if (remaining <= 0) return; // Bỏ qua nếu đã hoàn thành
                    
                    html += `
                    <div class="bg-white p-3 border rounded-lg">
                        <div class="font-medium text-gray-900 mb-2">${assignment.mission.name}</div>
                        <div class="text-sm text-gray-600 mb-2">Hoàn thành: ${assignment.quantity_completed}/${assignment.quantity_required}</div>
                        <div class="grid grid-cols-1 gap-2">
                            <div class="form-group">
                                <label class="form-label text-sm">Số lượng báo cáo (tối đa: ${remaining})</label>
                                <input type="number" name="quantities[${assignment.id}]" class="input" min="1" max="${remaining}" value="1">
                            </div>
                            <div class="form-group">
                                <label class="form-label text-sm">Ghi chú (nếu có)</label>
                                <textarea name="notes[${assignment.id}]" class="textarea" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    `;
                });
                
                if (html === '') {
                    html = '<div class="text-center text-gray-500">Không có nhiệm vụ nào cần báo cáo</div>';
                }
                
                $('#mission-reports').html(html);
            } else {
                showAlert('warning', response.data.message);
            }
        } catch (error) {
            console.error('Error loading task missions:', error);
            showAlert('error', 'Không thể tải danh sách nhiệm vụ của task');
        }
    }

    // Xử lý modal thêm feedback
    function openAddFeedbackModal(taskId) {
        $('#feedback-task-id').val(taskId);
        $('#rating-value').val(0);
        $('#rating-stars i').removeClass('text-warning').addClass('text-gray-300');
        $('#needs-revision').prop('checked', false);
        $('textarea[name="comment"]').val('');
        
        KTModal.getInstance(document.querySelector('#add-feedback-modal')).show();
    }

    // Hàm đánh dấu đã giải quyết feedback
    async function resolveFeedback(feedbackId) {
        try {
            const response = await axios.post('/task/resolve-feedback', {
                feedback_id: feedbackId
            });
            
            if (response.data.status === 200) {
                showAlert('success', response.data.message);
                window.location.reload();
            } else {
                showAlert('warning', response.data.message);
            }
        } catch (error) {
            console.error('Error resolving feedback:', error);
            showAlert('error', 'Không thể đánh dấu đã giải quyết feedback');
        }
    }

    // Hàm xóa báo cáo nhiệm vụ
    async function deleteMissionReport(reportId) {
        Notiflix.Confirm.show(
            'Xóa báo cáo',
            'Bạn có chắc chắn muốn xóa báo cáo này?',
            'Đúng',
            'Hủy',
            async () => {
                try {
                    const response = await axios.post('/task/delete-mission-report', {
                        report_id: reportId
                    });
                    
                    if (response.data.status === 200) {
                        showAlert('success', response.data.message);
                        window.location.reload();
                    } else {
                        showAlert('warning', response.data.message);
                    }
                } catch (error) {
                    console.error('Error deleting report:', error);
                    showAlert('error', 'Không thể xóa báo cáo');
                }
            },
            () => {},
            {}
        );
    }

    // Tải dữ liệu tab nhiệm vụ
    async function loadTaskMissionsTab(taskId) {
        try {
            const response = await axios.get('/task/task-missions', {
                params: { task_id: taskId }
            });
            
            if (response.data.status === 200) {
                const data = response.data.data;
                let html = '';
                
                if (data.assignments.length === 0) {
                    html = '<div class="text-center text-gray-500 py-5">Chưa có nhiệm vụ nào được nhận</div>';
                } else {
                    data.assignments.forEach(assignment => {
                        const progress = assignment.quantity_required > 0 
                            ? Math.round((assignment.quantity_completed / assignment.quantity_required) * 100) 
                            : 0;
                        
                        html += `
                        <div class="bg-white border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">${assignment.mission.name}</div>
                                <div class="text-sm text-gray-600">
                                    ${assignment.quantity_completed}/${assignment.quantity_required} 
                                    (${progress}%)
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${progress}%"></div>
                            </div>
                            `;
                        
                        if (assignment.reports.length > 0) {
                            html += `<div class="mt-2">
                                <div class="text-sm font-medium mb-1">Lịch sử báo cáo:</div>
                                <div class="space-y-2">`;
                            
                            assignment.reports.forEach(report => {
                                const reportDate = new Date(report.date_completed);
                                const formattedDate = reportDate.toLocaleDateString('vi-VN') + ' ' + 
                                    reportDate.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
                                
                                html += `
                                <div class="flex items-center justify-between bg-gray-50 p-2 rounded text-sm">
                                    <div>
                                        <div class="font-medium">${report.quantity} đơn vị</div>
                                        <div class="text-gray-600 text-xs">${formattedDate}</div>
                                        ${report.note ? `<div class="text-gray-700 mt-1">${report.note}</div>` : ''}
                                    </div>
                                    <button class="btn btn-xs btn-icon btn-light" onclick="deleteMissionReport(${report.id})">
                                        <i class="ki-outline ki-trash text-danger"></i>
                                    </button>
                                </div>
                                `;
                            });
                            
                            html += `</div></div>`;
                        }
                        
                        html += `</div>`;
                    });
                }
                
                $('#task-missions-list').html(html);
                $('#missions-count').text(data.assignments.length);
            } else {
                $('#task-missions-list').html(`<div class="text-center text-danger py-5">Lỗi: ${response.data.message}</div>`);
            }
        } catch (error) {
            console.error('Error loading task missions tab:', error);
            $('#task-missions-list').html('<div class="text-center text-danger py-5">Không thể tải dữ liệu</div>');
        }
    }

    // Tải dữ liệu tab feedback
    async function loadTaskFeedbacksTab(taskId) {
        try {
            const response = await axios.get('/task/feedbacks', {
                params: { task_id: taskId }
            });
            
            if (response.data.status === 200) {
                const feedbacks = response.data.data;
                let html = '';
                
                if (feedbacks.length === 0) {
                    html = '<div class="text-center text-gray-500 py-5">Chưa có feedback nào</div>';
                } else {
                    feedbacks.forEach(feedback => {
                        const createdDate = new Date(feedback.created_at);
                        const formattedDate = createdDate.toLocaleDateString('vi-VN') + ' ' + 
                            createdDate.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
                        
                        html += `
                        <div class="bg-white border rounded-lg p-4 ${feedback.needs_revision && !feedback.is_resolved ? 'border-danger' : ''}">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="font-medium text-gray-900">${feedback.user.name}</div>
                                    <div class="text-sm text-gray-600">${formattedDate}</div>
                                </div>
                                <div class="flex items-center">
                        `;
                        
                        // Hiển thị rating
                        for (let i = 1; i <= 5; i++) {
                            html += `<i class="ki-solid ki-star fs-6 ${i <= feedback.rating ? 'text-warning' : 'text-gray-300'}"></i>`;
                        }
                        
                        html += `
                                </div>
                            </div>
                        `;
                        
                        // Hiển thị yêu cầu chỉnh sửa
                        if (feedback.needs_revision) {
                            html += `
                            <div class="flex items-center gap-2 mb-2">
                                <span class="badge badge-${feedback.is_resolved ? 'success' : 'danger'}">
                                    ${feedback.is_resolved ? 'Đã giải quyết' : 'Yêu cầu chỉnh sửa'}
                                </span>
                                ${feedback.is_resolved ? 
                                    `<span class="text-xs text-gray-600">bởi ${feedback.resolver.name} - ${new Date(feedback.resolved_at).toLocaleDateString('vi-VN')}</span>` : 
                                    `<button class="btn btn-xs btn-light-danger" onclick="resolveFeedback(${feedback.id})">
                                        Đánh dấu đã giải quyết
                                    </button>`
                                }
                            </div>
                            `;
                        }
                        
                        // Hiển thị comment
                        if (feedback.comment) {
                            html += `
                            <div class="mt-2 bg-gray-50 p-3 rounded">
                                <div class="text-gray-700">${feedback.comment}</div>
                            </div>
                            `;
                        }
                        
                        html += `</div>`;
                    });
                }
                
                $('#task-feedbacks-list').html(html);
                $('#feedbacks-count').text(feedbacks.length);
            } else {
                $('#task-feedbacks-list').html(`<div class="text-center text-danger py-5">Lỗi: ${response.data.message}</div>`);
            }
        } catch (error) {
            console.error('Error loading task feedbacks tab:', error);
            $('#task-feedbacks-list').html('<div class="text-center text-danger py-5">Không thể tải dữ liệu</div>');
        }
    }

    // Tải dữ liệu mặc định cho tab đầu tiên
    $(document).ready(function() {
        loadTaskFeedbacksTab({{$details['id']}});
    });
</script>
@endpush