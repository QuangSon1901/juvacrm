@extends('dashboard.layouts.layout')
@section('dashboard_content')
<!-- PHẦN 1: HEADER THÔNG TIN CÔNG VIỆC -->
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <!-- Tiêu đề công việc -->
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin công việc
                @if ($details['type'])
                <span class="badge badge-sm badge-outline badge-primary">{{$details['type']}}</span>
                @endif
            </h1>
        </div>
        
        <!-- Nút hành động chính -->
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

<!-- PHẦN 2: NỘI DUNG CHÍNH -->
<div class="container-fixed">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- CỘT TRÁI (2/3): Thông tin chi tiết công việc -->
        <div class="col-span-1 lg:col-span-2">
            <div class="grid gap-5">
                <!-- Thẻ thông tin công việc chính -->
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
                        
                        <!-- Menu dropdown cho hành động phụ -->
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
                    
                    <!-- Nội dung chi tiết công việc -->
                    <div class="card-body lg:py-7.5 grid gap-5">
                        <!-- Cảnh báo quá hạn nếu có -->
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

                        <!-- Tiêu đề công việc với nút chỉnh sửa -->
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
                        
                        <!-- Thông tin trạng thái và tiến độ -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            <!-- Cột trái: Thông tin trạng thái -->
                            <div class="flex flex-col gap-5">
                                <!-- Trạng thái -->
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
                                
                                <!-- Độ ưu tiên -->
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
                                
                                <!-- Người quản lý (nếu là hợp đồng) -->
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
                            
                            <!-- Cột phải: Thông tin thời hạn và tiến độ -->
                            <div class="flex flex-col gap-5">
                                <!-- Hạn chót -->
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
                                
                                <!-- Tiến độ hoàn thành -->
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
                                
                                <!-- Số lượng yêu cầu và hoàn thành -->
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
                        
                        <!-- Mô tả và ghi chú -->
                        <!-- Mô tả công việc -->
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
                        
                        <!-- Ghi chú bổ sung -->
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

                        <!-- PHẦN 3: DANH SÁCH FEEDBACK (nếu là công việc hợp đồng) -->
                        @if ($details['type'] == 'CONTRACT')
                        <div class="menu-separator simple"></div>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Feedback từ người quản lý
                                    </span>
                                    <span id="feedback-count-badge" class="badge badge-xs badge-primary badge-outline">0</span>
                                </div>
                                @if ($details['type'] == 'CONTRACT' && ($details['assign']['id'] == session()->get(ACCOUNT_CURRENT_SESSION)['id'] || true))
                                <button class="btn btn-xs btn-light" onclick="openAddContractFeedbackModal({{$details['id']}})">
                                    <i class="ki-outline ki-plus me-1"></i> Thêm feedback
                                </button>
                                @endif
                            </div>
                            <div class="flex flex-col gap-3" id="contract-feedbacks-list">
                                <div class="text-center text-gray-500 py-3">Đang tải...</div>
                            </div>
                        </div>
                        @endif

                        <!-- PHẦN 4: DANH SÁCH CÔNG VIỆC DỊCH VỤ (nếu là công việc hợp đồng) -->
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
                            
                            <!-- Danh sách các service task -->
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
                                <!-- Service task card -->
                                <div class="bg-white border border-gray-200 rounded-lg p-3" id="service-task-{{ $serviceTask['id'] }}">
                                    <!-- Header với tên và action buttons -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div>
                                            <a href="/task/{{$serviceTask['id']}}" class="text-primary hover:text-primary-active font-medium">
                                                {{$serviceTask['name']}}
                                            </a>
                                            <!-- Badge cho feedback nếu cần -->
                                            <span class="feedback-badge-{{$serviceTask['id']}} hidden badge badge-xs badge-warning ml-2">
                                                Cần giải quyết feedback
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs font-medium">{{$serviceTask['progress'] ?? 0}}%</span>
                                                <div class="w-16 bg-gray-200 rounded h-1.5">
                                                    <div class="bg-blue-600 h-1.5 rounded" style="width: {{$serviceTask['progress'] ?? 0}}%"></div>
                                                </div>
                                            </div>
                                            <!-- Nút giải quyết feedback -->
                                            <button class="btn btn-xs btn-warning feedback-resolve-btn-{{$serviceTask['id']}} hidden" onclick="openResolveFeedbackModal({{$serviceTask['id']}})">
                                                Giải quyết
                                            </button>
                                            <!-- Nút nhận việc hoặc báo cáo -->
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
                                    
                                    <!-- Thông tin tóm tắt service task -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 text-xs">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Mã:</span>
                                                <span>#{{$serviceTask['id']}}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold">Trạng thái:</span>
                                                <span class="badge badge-sm badge-{{$serviceTask['status']['color']}}">
                                                    {{$serviceTask['status']['name']}}
                                                </span>
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
                                                <span class="text-{{$serviceTask['priority']['color']}}">
                                                    {{$serviceTask['priority']['name']}}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Danh sách công việc con của dịch vụ này -->
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
                                            <!-- Danh sách sub-tasks cho service task này -->
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
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between bg-gray-50 p-2 rounded gap-2" id="sub-task-{{ $subTask['id'] }}">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <a href="/task/{{$subTask['id']}}" class="text-xs hover:text-primary font-semibold">
                                                            {{$subTask['name']}}
                                                        </a>
                                                        <!-- Badge feedback nếu cần -->
                                                        <span class="feedback-badge-{{$subTask['id']}} hidden badge badge-xs badge-warning">
                                                            Cần giải quyết
                                                        </span>
                                                    </div>
                                                    <div class="grid grid-cols-1 gap-1 mt-1 text-xs">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-semibold">Trạng thái:</span>
                                                            <span class="badge badge-sm badge-{{$subTask['status']['color']}}">
                                                                {{$subTask['status']['name']}}
                                                            </span>
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
                                                        <!-- Nút giải quyết feedback -->
                                                        <button class="btn btn-xs btn-warning feedback-resolve-btn-{{$subTask['id']}} hidden" onclick="openResolveFeedbackModal({{$subTask['id']}})">
                                                            Giải quyết
                                                        </button>
                                                        <!-- Nút nhận việc hoặc báo cáo -->
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

                        <!-- PHẦN 5: DANH SÁCH CÔNG VIỆC CON (nếu là công việc dịch vụ) -->
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

                                <!-- Danh sách các sub tasks -->
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
                                            <!-- Nút nhận việc hoặc báo cáo -->
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
                                                <span class="badge badge-sm badge-{{$subTask['status']['color']}}">
                                                    {{$subTask['status']['name']}}
                                                </span>
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
                                                <span class="text-{{$subTask['priority']['color']}}">
                                                    {{$subTask['priority']['name']}}
                                                </span>
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
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CỘT PHẢI (1/3): Thông tin bổ sung, đính kèm và bình luận -->
        <div class="col-span-1">
            <div class="grid gap-5">
                <!-- Thẻ tổng quan -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Tổng quan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 gap-5">
                            <div class="flex flex-col gap-5">
                                <!-- Thông tin hợp đồng -->
                                <div class="checkbox-group">
                                    <span class="checkbox-label text-gray-800 !font-bold">
                                        Hợp đồng:
                                    </span>
                                    <span class="checkbox-label text-gray-800">
                                        @if ($details['contract']['id'] != 0)
                                        <a href="/contract/{{$details['contract']['id']}}">
                                            <span class="checkbox-label font-semibold hover:text-primary-active">
                                                {{$details['contract']['name']}}
                                            </span>
                                        </a>
                                        @else
                                        ---
                                        @endif
                                    </span>
                                </div>
                                
                                <!-- Thông tin dịch vụ -->
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
                                    @if (session()->get(ACCOUNT_CURRENT_SESSION)['id'] == $contribution['user']['id'] || true)
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

                <!-- Danh sách file đính kèm -->
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
                                    <img onerror="this.src='/assets/images/default.svg'" class="w-[30px]" alt="{{$attachment['extension']}}.svg" src="https://drive.google.com/thumbnail?id={{ $attachment['driver_id'] }}&sz=w56">
                                    @else
                                    <img onerror="this.src='/assets/images/default.svg'" class="w-[30px]" alt="{{$attachment['extension']}}.svg" src="{{asset('assets/images/file-types/' . $attachment['extension'] . '.svg')}}">
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

                <!-- Bình luận -->
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

                <!-- Lịch sử -->
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

<!-- PHẦN 6: MODALS -->
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
        <div class="modal-body scrollable-y max-h-[95%]">
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
        <div class="modal-body scrollable-y max-h-[95%]">
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
        <div class="modal-body scrollable-y max-h-[95%]">
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
        <div class="modal-body scrollable-y max-h-[95%]">
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
        <div class="modal-body scrollable-y max-h-[95%]">
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
        <div class="modal-body scrollable-y max-h-[95%]">
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
        <div class="modal-body scrollable-y max-h-[95%]">
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
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="add-contract-feedback-modal" style="z-index: 90;">
    <div class="modal-content max-w-[800px] top-5 lg:top-[5%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thêm feedback cho hợp đồng
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body scrollable-y max-h-[95%]">
            <form id="add-contract-feedback-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="task_id" id="contract-task-id" value="{{$details['id']}}">
                
                <div class="flex flex-col gap-2.5">
                    <div class="form-group">
                        <label class="form-label required">Nội dung feedback</label>
                        <textarea name="comment" class="textarea" rows="3" placeholder="Nhập nội dung feedback..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Chọn các task cần chỉnh sửa</label>
                        <div id="tasks-tree" class="max-h-[400px] overflow-y-auto border rounded-md p-4">
                            <!-- Cây task sẽ được render từ JavaScript -->
                            <div class="text-center text-gray-500 py-3">Đang tải...</div>
                        </div>
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

<!-- Modal giải quyết feedback -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="resolve-feedback-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Giải quyết feedback cho task #<span id="resolve-feedback-task-id"></span>
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body scrollable-y max-h-[95%]">
            <form id="resolve-feedback-form" class="grid gap-5 px-0 py-5">
                <input type="hidden" name="feedback_item_id" id="feedback-item-id" value="">
                
                <div class="flex flex-col gap-2.5">
                    <div id="feedback-details" class="bg-gray-50 p-3 rounded mb-3">
                        <!-- Thông tin chi tiết feedback sẽ hiển thị ở đây -->
                        <div class="text-center text-gray-500 py-2">Đang tải...</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Ghi chú giải quyết (tùy chọn)</label>
                        <textarea name="comment" class="textarea" rows="3" placeholder="Nhập ghi chú về cách bạn đã giải quyết feedback..."></textarea>
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Đánh dấu đã giải quyết
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- PHẦN 7: JAVASCRIPT -->
@push('scripts')
<script>
// PHẦN 1: KHỞI TẠO VÀ THIẾT LẬP CHUNG
let _descriptionQuill;

$(function() {
    // Khởi tạo các modal và thiết lập sự kiện
    let modalUploadFileTask = document.querySelector('#upload-file-task-modal');
    let instanceUploadFileTask = KTModal.getInstance(modalUploadFileTask);

    // Xử lý nút cập nhật thông tin
    $('button[data-modal-toggle="#update-task-modal"][data-name]').on('click', function() {
        let _this = $(this);
        let _modal = $('#update-task-modal');
        _modal.find('input[name], select[name], textarea[name]').val('').addClass('hidden');
        _modal.find(`input[name=${_this.attr('data-name')}], select[name=${_this.attr('data-name')}], textarea[name=${_this.attr('data-name')}]`).removeClass('hidden');
    });

    // Xử lý nút cập nhật mô tả
    $('button[data-modal-toggle="#update-description-task-modal"]').on('click', function() {
        _descriptionQuill.clipboard.dangerouslyPasteHTML("{!! $details['description'] !!}");
    });

    // Reset form tải file khi đóng modal
    instanceUploadFileTask.on('hidden', () => {
        $('#upload-file-task-modal input[name=file]').val('');
    });

    // Thiết lập các form submit handlers
    $('#update-task-modal form').on('submit', function(e) {
        e.preventDefault();
        postUpdateTask();
    });

    $('#comment-task-form').on('submit', function(e) {
        e.preventDefault();
        postCommentTask($(this));
    });

    $('#add-sub-task-modal form').on('submit', function(e) {
        e.preventDefault();
        postAddSubTask($(this));
    });

    $('#upload-file-task-modal form').on('submit', function(e) {
        e.preventDefault();
        postUploadFileTask(this);
    });

    $('#update-description-task-modal form').on('submit', function(e) {
        e.preventDefault();
        postUpdateDescriptionTask($(this));
    });

    $('#report-completion-form').on('submit', function(e) {
        e.preventDefault();
        postReportCompletion($(this));
    });

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

    // Xử lý form thêm feedback
    $('#add-contract-feedback-form').on('submit', async function(e) {
        e.preventDefault();
        
        // Kiểm tra chọn ít nhất một task
        if ($('input[name="revision_tasks[]"]:checked').length === 0) {
            showAlert('warning', 'Vui lòng chọn ít nhất một task đã hoàn thành cần chỉnh sửa');
            return;
        }
        
        // Kiểm tra nội dung feedback
        if ($('textarea[name="comment"]').val().trim() === '') {
            showAlert('warning', 'Vui lòng nhập nội dung feedback');
            return;
        }
        
        const formData = $(this).serialize();
        
        try {
            const response = await axios.post('/task/add-feedback', formData);
            
            if (response.data.status === 200) {
                showAlert('success', response.data.message);
                KTModal.getInstance(document.querySelector('#add-contract-feedback-modal')).hide();
                loadContractFeedbacks({{$details['id']}});
            } else {
                showAlert('warning', response.data.message);
            }
        } catch (error) {
            console.error('Error adding feedback:', error);
            showAlert('error', 'Không thể thêm feedback');
        }
    });

    // Xử lý form giải quyết feedback
    $('#resolve-feedback-form').on('submit', async function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        try {
            const response = await axios.post('/task/resolve-feedback-item', formData);
            
            if (response.data.status === 200) {
                showAlert('success', response.data.message);
                KTModal.getInstance(document.querySelector('#resolve-feedback-modal')).hide();
                
                // Nạp lại danh sách feedback
                loadContractFeedbacks({{$details['id']}});
            } else {
                showAlert('warning', response.data.message);
            }
        } catch (error) {
            console.error('Error resolving feedback:', error);
            showAlert('error', 'Không thể giải quyết feedback');
        }
    });

    // Khởi tạo datepicker và editor
    flatpickrMake($("input[name=due_date]"), 'datetime');
    _descriptionQuill = quillTemplate("#description_editor", "350px");
    
    // Tải danh sách feedback khi trang tải xong
    loadContractFeedbacks({{$details['id']}});
});

// PHẦN 2: AJAX POST ACTIONS - CÁC CHỨC NĂNG CẬP NHẬT DỮ LIỆU
// Cập nhật thông tin task
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

// Thêm bình luận task
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

// Thêm subtask
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

// Upload file
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

// Xóa tệp đính kèm
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

// Xóa subtask
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

// Cập nhật mô tả
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

// Báo cáo hoàn thành
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

// Xóa báo cáo đóng góp
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

// PHẦN 3: MODAL HANDLERS - XỬ LÝ HIỂN THỊ VÀ DỮ LIỆU CHO CÁC MODAL
// Mở modal báo cáo hoàn thành
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

// Mở modal nhận việc
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

// Mở modal báo cáo nhiệm vụ
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
            } else {
                let cloudName = "https://res.cloudinary.com/{{env(CLOUDINARY_CLOUD_NAME)}}/image/upload/w_119,h_94,q_auto,f_auto/uploads/";
                info = `<div class="bg-gray-50 rounded-lg p-4 border border-gray-100" bis_skin_checked="1">
                    <div class="flex items-center justify-between mb-3" bis_skin_checked="1">
                        <h4 class="font-semibold text-blue-800">Thông tin nhiệm vụ</h4>
                        <span class="text-xs font-medium px-2 py-1 bg-blue-100 text-blue-800 rounded-full">ID: #${data.task.id}</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4" bis_skin_checked="1">
                        <div class="space-y-2" bis_skin_checked="1">
                            <div class="flex flex-col" bis_skin_checked="1">
                                <span class="text-xs text-gray-500">Tên nhiệm vụ</span>
                                <span class="font-medium text-gray-900">${data.task.name}</span>
                            </div>
                            <div class="flex flex-col" bis_skin_checked="1">
                                <span class="text-xs text-gray-500">Tiến độ</span>
                                <div class="flex items-center space-x-2" bis_skin_checked="1">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5" bis_skin_checked="1">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${data.task.progress}%" bis_skin_checked="1"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">${data.task.progress}%</span>
                                </div>
                            </div>
                            <div class="flex flex-col" bis_skin_checked="1">
                                <span class="text-xs text-gray-500">Hoàn thành</span>
                                <span class="font-medium text-gray-900">${data.task.qty_completed}/${data.task.qty_request}</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2" bis_skin_checked="1">
                            <div class="flex flex-col" bis_skin_checked="1">
                                <span class="text-xs text-gray-500 mb-1">Mẫu</span>
                                <div class="h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200" bis_skin_checked="1">
                                    <img onerror="this.src='/assets/images/default.svg'" src="${cloudName}${data.task.result_image_id}" alt="Ảnh mẫu" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="flex flex-col" bis_skin_checked="1">
                                <span class="text-xs text-gray-500 mb-1">Kết quả</span>
                                <div class="h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200" bis_skin_checked="1">
                                    <img onerror="this.src='/assets/images/default.svg'" src="${cloudName}${data.task.sample_image_id}" alt="Ảnh mẫu" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                html = info + html;
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

// PHẦN 4: FEEDBACK MANAGEMENT - QUẢN LÝ FEEDBACK
// Mở modal thêm feedback
function openAddContractFeedbackModal(taskId) {
    $('#contract-task-id').val(taskId);
    loadTasksForFeedback(taskId);
    KTModal.getInstance(document.querySelector('#add-contract-feedback-modal')).show();
}

// Tải danh sách task cho feedback
async function loadTasksForFeedback(taskId) {
    try {
        const response = await axios.get('/task/show-feedback-form', {
            params: { task_id: taskId }
        });
        
        if (response.data.status === 200) {
            const contractTask = response.data.data.contract_task;
            let html = buildTaskTree(contractTask);
            $('#tasks-tree').html(html);
            
            // Kích hoạt toggle cho các nút mở rộng
            activateTaskTreeToggles();
        } else {
            $('#tasks-tree').html(`<div class="text-center text-danger py-3">${response.data.message}</div>`);
        }
    } catch (error) {
        console.error('Error loading tasks for feedback:', error);
        $('#tasks-tree').html('<div class="text-center text-danger py-3">Không thể tải danh sách task</div>');
    }
}

// Xây dựng cây task
function buildTaskTree(contractTask) {
    let html = `
    <div class="mb-3">
        <div class="flex items-center gap-2">
            <span class="badge badge-${contractTask.status ? contractTask.status.color : 'gray'} badge-sm">
                ${contractTask.status ? contractTask.status.name : 'Không xác định'}
            </span>
            <span class="font-medium text-gray-900">${contractTask.name}</span>
        </div>
    </div>`;
    
    // Nếu có task con, render các task con
    if (contractTask.childs && contractTask.childs.length > 0) {
        html += '<div class="ml-5 border-l border-gray-200 pl-4 space-y-3">';
        
        // Phân loại các task theo type
        const servicesTasks = contractTask.childs.filter(task => task.type === 'SERVICE');
        const subTasks = contractTask.childs.filter(task => task.type === 'SUB');
        
        // Render task SERVICE nếu có
        if (servicesTasks.length > 0) {
            servicesTasks.forEach(task => {
                html += buildTaskNode(task);
            });
        }
        
        // Render task SUB nếu có
        if (subTasks.length > 0) {
            subTasks.forEach(task => {
                html += buildTaskNode(task);
            });
        }
        
        html += '</div>';
    } else {
        html += '<div class="text-center text-gray-500 py-2">Không có task đã hoàn thành</div>';
    }
    
    return html;
}

// Tạo node cho từng task
function buildTaskNode(task) {
    // Xác định xem có hiển thị checkbox hay không
    // Chỉ hiển thị checkbox cho task lá (không có task con) và đã hoàn thành
    const showCheckbox = task.is_leaf && task.is_completed;
    
    let html = `
    <div class="task-node">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                ${showCheckbox ? 
                  `<input type="checkbox" name="revision_tasks[]" value="${task.id}" class="checkbox">` : 
                  `<div class="w-5"></div>`}
                <span class="text-sm font-medium text-gray-900">#${task.id}: ${task.name}</span>
                ${task.is_completed ? 
                  `<span class="badge badge-success badge-sm">Hoàn thành</span>` : 
                  `<span class="badge badge-${task.status ? task.status.color : 'gray'} badge-sm">
                     ${task.status ? task.status.name : 'Không xác định'}
                   </span>`}
            </div>
        </div>`;
    
    // Nếu có task con, render các task con
    if (task.childs && task.childs.length > 0) {
        html += `
        <div class="task-toggle cursor-pointer mt-1 text-xs text-gray-600 flex items-center gap-1">
            <i class="ki-outline ki-right text-xs"></i>
            <span>Hiển thị ${task.childs.length} task con</span>
        </div>
        <div class="task-children hidden ml-5 border-l border-gray-200 pl-4 mt-2 space-y-3">`;
        
        task.childs.forEach(childTask => {
            html += buildTaskNode(childTask);
        });
        
        html += '</div>';
    }
    
    html += '</div>';
    return html;
}

// Kích hoạt toggle cho cây task
function activateTaskTreeToggles() {
    $('.task-toggle').on('click', function() {
        const children = $(this).next('.task-children');
        const icon = $(this).find('i');
        
        if (children.hasClass('hidden')) {
            children.removeClass('hidden');
            icon.removeClass('ki-right').addClass('ki-down');
        } else {
            children.addClass('hidden');
            icon.removeClass('ki-down').addClass('ki-right');
        }
    });
}

// Cập nhật hiển thị nút giải quyết feedback
function updateFeedbackButtons(feedbacks) {
    // Reset tất cả các badge và button trước
    $('.feedback-badge').addClass('hidden');
    $('.feedback-resolve-btn').addClass('hidden');
    
    // Duyệt qua các feedback để tìm những task cần giải quyết
    feedbacks.forEach(feedback => {
        // Chỉ xử lý các feedback chưa giải quyết và không phải là yêu cầu làm lại
        if (!feedback.is_resolved) {
            feedback.items.forEach(item => {
                if (!item.is_resolved) {
                    // Hiển thị badge và button cho task này
                    $(`.feedback-badge-${item.task.id}`).removeClass('hidden');
                    $(`.feedback-resolve-btn-${item.task.id}`).removeClass('hidden');
                    
                    // Lưu feedback_item_id vào data attribute của button
                    $(`.feedback-resolve-btn-${item.task.id}`).attr('data-item-id', item.id);
                }
            });
        }
    });
}

// Mở modal giải quyết feedback
function openResolveFeedbackModal(taskId) {
    // Tìm feedback_item_id từ button đã click
    const itemId = $(`.feedback-resolve-btn-${taskId}`).attr('data-item-id');
    if (!itemId) {
        showAlert('warning', 'Không tìm thấy thông tin feedback cần giải quyết');
        return;
    }
    
    $('#resolve-feedback-task-id').text(taskId);
    $('#feedback-item-id').val(itemId);
    
    // Tải thông tin chi tiết feedback
    loadFeedbackItemDetails(itemId);
    
    // Mở modal
    KTModal.getInstance(document.querySelector('#resolve-feedback-modal')).show();
}

// Tải thông tin chi tiết của feedback item
async function loadFeedbackItemDetails(itemId) {
    $('#feedback-details').html('<div class="text-center text-gray-500 py-2">Đang tải...</div>');
    
    try {
        const response = await axios.get('/task/feedback-item-details', {
            params: { item_id: itemId }
        });
        
        if (response.data.status === 200) {
            const item = response.data.data;
            const feedback = item.feedback;
            
            let html = `
            <div class="mb-2">
                <h5 class="font-medium text-gray-900">Feedback từ ${feedback.user.name}</h5>
                <p class="text-xs text-gray-600">
                    ${new Date(feedback.created_at).toLocaleDateString('vi-VN')} 
                    ${new Date(feedback.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'})}
                </p>
            </div>
            <div class="text-sm whitespace-pre-line">${feedback.comment}</div>
            <div class="mt-2 pt-2 border-t border-gray-200">
                <div class="text-xs">
                    <span class="font-medium">Task cần giải quyết:</span> 
                    #${item.task.id} - ${item.task.name}
                </div>
            </div>`;
            
            $('#feedback-details').html(html);
        } else {
            $('#feedback-details').html(`<div class="text-danger py-2">${response.data.message}</div>`);
        }
    } catch (error) {
        console.error('Error loading feedback item details:', error);
        $('#feedback-details').html('<div class="text-danger py-2">Không thể tải thông tin feedback</div>');
    }
}

// Tải danh sách feedback
async function loadContractFeedbacks(taskId) {
    try {
        const response = await axios.get('/task/feedbacks', {
            params: { task_id: taskId }
        });
        
        if (response.data.status === 200) {
            const feedbacks = response.data.data;
            let html = '';
            
            if (feedbacks.length === 0) {
                html = '<div class="text-center text-gray-500 py-3">Chưa có feedback nào</div>';
            } else {
                feedbacks.forEach(feedback => {
                    html += renderFeedback(feedback);
                });
            }
            
            $('#contract-feedbacks-list').html(html);
            
            // Cập nhật badge đếm số lượng feedback
            $('#feedback-count-badge').text(feedbacks.length);
            
            // Cập nhật nút giải quyết ở các service task và sub task
            updateFeedbackButtons(feedbacks);
        } else {
            $('#contract-feedbacks-list').html(`<div class="text-center text-danger py-3">Lỗi: ${response.data.message}</div>`);
        }
    } catch (error) {
        console.error('Error loading feedbacks:', error);
        $('#contract-feedbacks-list').html('<div class="text-center text-danger py-3">Không thể tải danh sách feedback</div>');
    }
}

// Render feedback item
function renderFeedback(feedback) {
    const createdDate = new Date(feedback.created_at);
    const formattedDate = createdDate.toLocaleDateString('vi-VN') + ' ' + 
        createdDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

    let statusClass = 'warning';
    let statusIcon = 'ki-flag';

    if (feedback.status === 1) {
        statusClass = 'success';
        statusIcon = 'ki-check-circle';
    } else if (feedback.status === 2) {
        statusClass = 'danger';
        statusIcon = 'ki-cross-circle';
    }

    // Lấy dữ liệu từ Blade PHP
    let idTask = @json($details['id']);
    let type = @json($details['type'] === 'CONTRACT');
    let assignSale = @json($details['assign']['id']) === @json(session()->get(ACCOUNT_CURRENT_SESSION)['id']);

    let confirmFeedback = (!feedback.is_resolved && feedback.all_items_resolved &&
        (type && (assignSale || true))) ?
        `<div class="flex items-center gap-1">
            <button class="btn btn-xs btn-danger request-revision" data-id="${feedback.id}">
                Yêu cầu làm lại
            </button>
            <button class="btn btn-xs btn-success confirm-resolved" data-id="${feedback.id}">
                Xác nhận
            </button>
        </div>` : '';

    let feedbackItems = feedback.items.map(item => `
        <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium">#${item.task.id}:</span>
                <a href="/task/${item.task.id}" class="text-xs hover:text-primary truncate max-w-xs">
                    ${item.task.name}
                </a>
                <span class="badge badge-xs badge-${item.is_resolved ? 'success' : 'warning'}">
                    ${item.is_resolved ? 'Đã giải quyết' : 'Đang xử lý'}
                </span>
            </div>
            ${!item.is_resolved && !feedback.is_resolved && item.task.id === idTask ? 
            `<button class="btn btn-xs btn-success resolve-item" data-id="${item.id}">
                <i class="ki-outline ki-check text-xs me-1"></i>Đánh dấu đã xong
            </button>` : ''}
            ${item.is_resolved && item.resolver ? 
            `<div class="text-xs text-gray-600">
                <i class="ki-outline ki-user text-xs me-1"></i>${item.resolver.name}
            </div>` : ''}
        </div>
    `).join('');

    let html = `
    <div class="bg-white border border-gray-200 rounded-lg p-3" id="feedback-${feedback.id}">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-xs text-gray-800">${feedback.user.name}</span>
                        <span class="text-xs text-gray-600">${formattedDate}</span>
                    </div>
                    <span class="badge badge-sm badge-${statusClass}">
                        <i class="ki-outline ${statusIcon} me-1"></i>
                        ${feedback.status_text}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                ${confirmFeedback}
                <button class="btn btn-xs btn-icon btn-light toggle-feedback" data-id="${feedback.id}">
                    <i class="ki-outline ki-right" id="feedback-toggle-icon-${feedback.id}"></i>
                </button>
            </div>
        </div>
        
        <div id="feedback-details-${feedback.id}" class="hidden pt-3 border-t border-gray-100">
            <div class="mt-3">
                <div class="text-sm font-medium mb-2 flex items-center gap-2">
                    <span>Nội dung</span>
                </div>
                
                <div class="bg-gray-50 p-3 rounded-md mb-3">
                    <div class="text-gray-800 whitespace-pre-line leading-3 text-sm pl-2">${feedback.comment}</div>
                </div>
            </div>
            
            <div class="mt-3">
                <div class="text-sm font-medium mb-2 flex items-center gap-2">
                    <span>Tasks cần chỉnh sửa</span>
                    <span class="badge badge-xs badge-${statusClass} badge-outline">${feedback.items.length}</span>
                </div>
                
                <div class="space-y-2 pl-2">
                    ${feedbackItems}
                </div>
            </div>
        </div>
    </div>`;

    return html;
}

// PHẦN 5: EVENT HANDLERS - XỬ LÝ SỰ KIỆN DOM
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý các event nằm ngoài các handler đã khai báo trước đó
    document.body.addEventListener('click', function(event) {
        // Toggle feedback details
        if (event.target.closest('.toggle-feedback')) {
            const id = event.target.closest('.toggle-feedback').dataset.id;
            const content = document.getElementById(`feedback-details-${id}`);
            const icon = document.getElementById(`feedback-toggle-icon-${id}`);

            content.classList.toggle('hidden');
            icon.classList.toggle('ki-right');
            icon.classList.toggle('ki-down');
        }

        // Xác nhận đã xử lý feedback
        if (event.target.closest('.confirm-resolved')) {
            const id = event.target.closest('.confirm-resolved').dataset.id;
            confirmFeedbackResolved(id);
        }

        // Yêu cầu làm lại feedback
        if (event.target.closest('.request-revision')) {
            const id = event.target.closest('.request-revision').dataset.id;
            requestFeedbackRevision(id);
        }

        // Đánh dấu task đã xong
        if (event.target.closest('.resolve-item')) {
            const id = event.target.closest('.resolve-item').dataset.id;
            resolveFeedbackItem(id);
        }
    });
});

// Đánh dấu một item của feedback đã giải quyết
async function resolveFeedbackItem(itemId) {
    try {
        const response = await axios.post('/task/resolve-feedback-item', {
            feedback_item_id: itemId
        });
        
        if (response.data.status === 200) {
            showAlert('success', response.data.message);
            loadContractFeedbacks({{$details['id']}});
        } else {
            showAlert('warning', response.data.message);
        }
    } catch (error) {
        console.error('Error resolving feedback item:', error);
        showAlert('error', 'Không thể đánh dấu đã giải quyết');
    }
}

// Xác nhận toàn bộ feedback đã được giải quyết
async function confirmFeedbackResolved(feedbackId) {
    try {
        const response = await axios.post('/task/confirm-feedback-resolved', {
            feedback_id: feedbackId
        });
        
        if (response.data.status === 200) {
            showAlert('success', response.data.message);
            loadContractFeedbacks({{$details['id']}});
            // Reload trang sau 1 giây để cập nhật trạng thái
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('warning', response.data.message);
        }
    } catch (error) {
        console.error('Error confirming feedback resolved:', error);
        showAlert('error', 'Không thể xác nhận đã giải quyết');
    }
}

// Yêu cầu làm lại các task trong feedback
async function requestFeedbackRevision(feedbackId) {
    // Hiển thị modal xác nhận với textarea ghi chú
    Notiflix.Confirm.show(
        'Yêu cầu làm lại',
        '<div class="mb-3">Bạn có chắc chắn muốn yêu cầu làm lại các task trong feedback này?</div>' +
        '<div class="form-group">' +
        '<label class="form-label">Ghi chú (tùy chọn)</label>' +
        '<textarea id="revision-comment" class="textarea w-full" rows="3" placeholder="Nhập lý do yêu cầu làm lại..."></textarea>' +
        '</div>',
        'Đúng',
        'Huỷ',
        async () => {
            const comment = $('#revision-comment').val();
            
            try {
                const response = await axios.post('/task/request-feedback-revision', {
                    feedback_id: feedbackId,
                    comment: comment
                });
                
                if (response.data.status === 200) {
                    showAlert('success', response.data.message);
                    loadContractFeedbacks({{$details['id']}});
                    // Reload trang sau 1 giây để cập nhật trạng thái
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showAlert('warning', response.data.message);
                }
            } catch (error) {
                console.error('Error requesting feedback revision:', error);
                showAlert('error', 'Không thể yêu cầu làm lại');
            }
        },
        () => {}, 
        { width: '400px' }
    );
}
</script>
@endpush