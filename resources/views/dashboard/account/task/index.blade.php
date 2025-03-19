@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quản lý công việc
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
@if ($task_remake > 0 || $task_overdue > 0)
    <div class="flex flex-wrap gap-2 px-6 py-2">
        @if ($task_overdue > 0)
        <div class="badge badge-outline badge-danger px-3">
            <div class="relative w-full text-sm flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                CẢNH BÁO: Có công việc QUÁ HẠN nghiêm trọng <span class="underline cursor-pointer" onclick="$('select[data-filter=status_task]').val('6').trigger('change');document.getElementById('tasks-table').scrollIntoView({ behavior: 'smooth' });">Xem ngay</span>
            </div>
        </div>
        @endif
        @if ($task_remake > 0)
        <div class="badge badge-outline badge-info px-3">
            <div class="relative w-full text-sm flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-purple-500"></span>
                </span>
                CHÚ Ý: Có công việc bị từ chối, yêu cầu xử lý ngay <span class="underline cursor-pointer" onclick="$('select[data-filter=status_task]').val('7').trigger('change');document.getElementById('tasks-table').scrollIntoView({ behavior: 'smooth' });">Xem ngay</span>
            </div>
        </div>
        @endif
    </div>
@endif
<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <div class="card card-grid min-w-full">
            <div class="card-header flex-wrap gap-2">
                <h3 class="card-title">
                    Danh sách công việc
                </h3>
                <div class="flex flex-wrap gap-2 lg:gap-5">
                    <div class="flex flex-wrap gap-2.5">
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-wrap lg:justify-end gap-2">
                                <select data-filter="level_task" class="select select-sm w-40">
                                    <option value="CONTRACT">
                                        Theo hợp đồng
                                    </option>
                                    <option value="SERVICE" selected>
                                        Theo dịch vụ
                                    </option>
                                </select>
                                <select data-filter="priority_task" class="select select-sm w-40">
                                    <option value="" selected>
                                        Tất cả mức độ
                                    </option>
                                    @foreach ($priorities as $priority)
                                    <option value="{{$priority['id']}}">
                                        {{$priority['name']}}
                                    </option>
                                    @endforeach
                                </select>
                                <select data-filter="status_task" class="select select-sm w-40">
                                    <option value="" selected>
                                        Tất cả trạng thái
                                    </option>
                                    @foreach ($statuses as $status)
                                    <option value="{{$status['id']}}">
                                        {{$status['name']}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-wrap lg:justify-end gap-2">
                                <div class="flex">
                                    <label class="switch switch-sm">
                                        <span class="switch-label">
                                            Công việc chưa hoàn thành
                                        </span>
                                        <input name="check" data-filter="task_no_completed" type="checkbox" value="1">
                                    </label>
                                </div>
                                <div class="relative">
                                    <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3">
                                    </i>
                                    <input class="input input-sm pl-8" id="search-input" data-filter="search" placeholder="Tìm kiếm" type="text">
                                </div>
                                <div class="flex flex-wrap lg:justify-end gap-2">
                                    <button id="bulk-action-btn" class="btn btn-warning btn-sm hidden" data-modal-toggle="#bulk-action-modal">
                                        <i class="ki-filled ki-check me-1"></i> Thao tác nhanh (<span id="selected-count">0</span>)
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- <div>
                            <a href="/task/create" class="btn btn-primary btn-sm">
                                Thêm công việc
                            </a>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div data-datatable="false" id="current_sessions_table" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table id="tasks-table" class="table table-border" data-datatable-table="true">
                            <thead>
                                <tr>
                                    <th class="w-[200px]">
                                        <span class="sort">
                                            <span class="sort-label"></span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Trạng thái
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[300px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Tên công việc
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Người thực hiện
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Ngày bắt đầu
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Ngày kết thúc
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[150px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                % hoàn thành
                                            </span>
                                        </span>
                                    </th>
                                    <th class="min-w-[240px]">
                                        <span class="sort">
                                            <span class="sort-label">
                                                Thời gian dự kiến
                                            </span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            @include('dashboard.layouts.tableloader', ['currentlist' => '/task-data'])
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

<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="bulk-action-modal" style="z-index: 90;">
    <div class="modal-content modal-center-y lg:max-w-[1100px] max-h-[95%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thao tác nhanh
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body scrollable-y">
            <div id="bulk-tasks-container" class="space-y-4">
                <!-- Task cards will be rendered here dynamically -->
            </div>
        </div>
    </div>
</div>

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

<template id="task-card-template">
    <div class="task-card bg-white border border-gray-500 rounded-lg p-4 shadow-sm" data-task-id="">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <!-- Task info -->
            <div class="flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="badge badge-sm badge-outline badge-neutral task-id-badge"></span>
                            <span class="badge badge-sm badge-outline badge-primary task-type-badge"></span>
                            <span class="badge badge-sm badge-outline task-status-badge"></span>
                        </div>
                        <h4 class="text-gray-900 font-medium mb-2 task-name"></h4>
                        <div class="flex items-center gap-1">
                            <i class="ki-outline ki-abstract-26 text-xs text-gray-600"></i>
                            <span class="task-qty text-xs"></span>
                        </div>
                        <div class="task-progress-container">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-sm h-1.5 task-progress-bar-container">
                                    <div class="bg-blue-800 h-1.5 rounded-sm task-progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-700 task-progress-text">0%</span>
                            </div>
                        </div>
                    </div>
                    <!-- Image preview section -->
                    <div class="flex justify-end gap-2 mt-3 image-preview-container">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-800 mb-1">Ảnh mẫu</span>
                            <div class="h-20 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 sample-image-container">
                                <img src="/assets/images/default.svg" alt="Ảnh mẫu" class="w-20 h-20 object-cover sample-image">
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-800 mb-1">Ảnh kết quả</span>
                            <div class="h-20 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 result-image-container">
                                <img src="/assets/images/default.svg" alt="Ảnh kết quả" class="w-20 h-20 object-cover result-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Action buttons -->
            <div class="flex flex-col gap-2 min-w-[120px]">
                <div class="flex justify-end">
                    <button class="btn btn-sm btn-primary claim-btn">
                        <i class="ki-outline ki-check me-1"></i>Nhận việc
                    </button>
                </div>
                <div class="flex justify-end">
                    <button class="btn btn-sm btn-warning report-btn">
                        <i class="ki-outline ki-flag me-1"></i>Báo cáo
                    </button>
                </div>
            </div>
        </div>
        <!-- Subtasks container -->
        <div class="subtasks-container mt-3 pt-3 border-t border-gray-100 hidden">
            <div class="text-sm font-medium mb-2 flex items-center gap-2">
                <span>Công việc con</span>
                <span class="badge badge-xs badge-primary badge-outline subtasks-count">0</span>
            </div>
            <div class="space-y-3 subtasks-list pl-3">
                <!-- Subtasks will be rendered here -->
            </div>
        </div>
    </div>
</template>

<!-- Template for subtask item -->
<template id="subtask-item-template">
    <div class="subtask-item bg-gray-50 rounded-lg p-3 border border-gray-500" data-task-id="">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
            <!-- Subtask info -->
            <div class="flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="badge badge-sm badge-outline badge-neutral subtask-id-badge"></span>
                            <span class="badge badge-sm badge-outline subtask-status-badge"></span>
                        </div>
                        <h5 class="text-gray-900 font-medium text-sm mb-1 subtask-name"></h5>
                        <div class="flex items-center gap-1">
                            <i class="ki-outline ki-abstract-26 text-xs text-gray-600"></i>
                            <span class="subtask-qty"></span>
                        </div>
                        <div class="subtask-progress-container">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-sm h-1.5 subtask-progress-bar-container">
                                    <div class="bg-blue-800 h-1.5 rounded-sm subtask-progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-700 subtask-progress-text">0%</span>
                            </div>
                        </div>
                    </div>
                    <!-- Image preview section for subtask -->
                    <div class="flex justify-end gap-2 mt-2 image-preview-container">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-800 mb-1">Ảnh mẫu</span>
                            <div class="h-16 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 subtask-sample-image-container">
                                <img src="/assets/images/default.svg" alt="Ảnh mẫu" class="w-20 h-20 object-cover subtask-sample-image">
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-800 mb-1">Ảnh kết quả</span>
                            <div class="h-16 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 subtask-result-image-container">
                                <img src="/assets/images/default.svg" alt="Ảnh kết quả" class="w-20 h-20 object-cover subtask-result-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Action buttons for subtask -->
            <div class="flex flex-col gap-2 min-w-[120px]">
                <div class="flex justify-end">
                    <button class="btn btn-xs btn-primary subtask-claim-btn">
                        <i class="ki-outline ki-check me-1"></i>Nhận việc
                    </button>
                </div>
                <div class="flex justify-end">
                    <button class="btn btn-xs btn-warning subtask-report-btn">
                        <i class="ki-outline ki-flag me-1"></i>Báo cáo
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection
@push('scripts')
<script>
    // Global variables
    let selectedTasks = new Map();
    let tasksData = new Map();
    let parentChildMap = new Map();

    const cloudinaryName = "{{env(CLOUDINARY_CLOUD_NAME)}}";

    // Initialize after DOM is loaded
    $(function() {
        KTModal.getInstance(document.querySelector('#claim-task-modal')).on('hide', (detail) => {
            $('#bulk-action-btn').trigger('click');
        });

        KTModal.getInstance(document.querySelector('#report-missions-modal')).on('hide', (detail) => {
            $('#bulk-action-btn').trigger('click');
        });

        $(document).off('click').on('click', '.pagination button', async function() {
            let _updater = $(this).closest('.card').find('.updater');
            let page = $(this).attr('data-page');

            $(this).closest('.card').find('.currentpage').val(page);
            Notiflix.Loading.circle('Đang tải dữ liệu...');
            await callAjaxDataTable(_updater);
            Notiflix.Loading.remove();
            callAPIAfterAjaxLoad()
        })

        $(document).off('change').on('change', 'input[type=checkbox][data-filter], select[data-filter]', async function() {
            let _updater = $(this).closest('.card').find('.updater');
            Notiflix.Loading.circle('Đang tải dữ liệu...');
            await callAjaxDataTable(_updater);
            Notiflix.Loading.remove();
            callAPIAfterAjaxLoad()
        })

        $(document).off('keyup').on('keyup', 'input[type=text][data-filter], input[type=password][data-filter]', async function() {
            if (event.keyCode === 13) {
                let _updater = $(this).closest('.card').find('.updater');
                Notiflix.Loading.circle('Đang tải dữ liệu...');
                await callAjaxDataTable(_updater);
                Notiflix.Loading.remove();
                callAPIAfterAjaxLoad()
            }
        })
    
        // Check/uncheck tasks
        $(document).on('change', '.task-checkbox', function() {
            const taskId = $(this).data('task-id').toString();
            
            if ($(this).prop('checked')) {
                selectedTasks.set(taskId, tasksData.get(taskId));
            } else {
                selectedTasks.delete(taskId);
            }
            
            updateSelectedCount();
        });

        // Toggle bulk action button
        function updateSelectedCount() {
            const count = selectedTasks.size;
            $('#selected-count').text(count);
            
            if (count > 0) {
                $('#bulk-action-btn').removeClass('hidden');
            } else {
                $('#bulk-action-btn').addClass('hidden');
            }
        }

        // Open bulk action modal
        $('#bulk-action-btn').on('click', function() {
            renderTasksInModal();
        });

        // Render selected tasks in modal
        function renderTasksInModal() {
            const $container = $('#bulk-tasks-container');
            $container.empty();
            
            // Group tasks by hierarchy (parent tasks first)
            const taskGroups = organizeTasksByHierarchy();
            
            taskGroups.forEach(taskId => {
                const task = tasksData.get(taskId);
                const $taskCard = createTaskCard(task);
                $container.append($taskCard);
                
                // Add subtasks if available
                if (parentChildMap.has(taskId)) {
                    const subtasks = parentChildMap.get(taskId)
                        .filter(id => tasksData.has(id))
                        .map(id => tasksData.get(id));
                    
                    if (subtasks.length > 0) {
                        const $subtasksContainer = $taskCard.find('.subtasks-container');
                        const $subtasksList = $subtasksContainer.find('.subtasks-list');
                        
                        $subtasksContainer.removeClass('hidden');
                        $subtasksContainer.find('.subtasks-count').text(subtasks.length);
                        
                        subtasks.forEach(subtask => {
                            const $subtaskItem = createSubtaskItem(subtask);
                            $subtasksList.append($subtaskItem);
                        });
                    }
                }
            });
        }

        // Organize tasks by hierarchy (parent tasks first)
        function organizeTasksByHierarchy() {
            const result = [];
            const processed = new Set();
            
            // First add all top-level tasks
            selectedTasks.forEach((task, taskId) => {
                if (!task.parentId || !selectedTasks.has(task.parentId.toString())) {
                    result.push(taskId);
                    processed.add(taskId);
                }
            });
            
            // Then add children tasks that were selected but their parents weren't
            selectedTasks.forEach((task, taskId) => {
                if (!processed.has(taskId)) {
                    result.push(taskId);
                    processed.add(taskId);
                }
            });
            
            return result;
        }

        // Create a task card from template
        function createTaskCard(task) {
            const $template = $('#task-card-template').html();
            const $taskCard = $($.parseHTML($template));
            
            $taskCard.attr('data-task-id', task.id);
            $taskCard.find('.task-id-badge').text('#' + task.id);
            $taskCard.find('.task-type-badge').text(task.type);
            $taskCard.find('.task-name').text(task.name);
            $taskCard.find('.task-qty').text('SL: ' + task.qtyCompleted + '/' + task.qtyRequest);
            
            // Calculate progress
            const progress = task.qtyRequest > 0 ? Math.min(100, Math.round((task.qtyCompleted / task.qtyRequest) * 100)) : 0;
            $taskCard.find('.task-progress-bar').css('width', progress + '%');
            $taskCard.find('.task-progress-text').text(progress + '%');
            
            // Set status badge color
            setStatusBadge($taskCard.find('.task-status-badge'), task.statusId);
            
            // Set images if available
            if (task.sampleImage) {
                const sampleImageUrl = `https://res.cloudinary.com/${cloudinaryName}/image/upload/w_80,h_80,q_auto,f_auto/uploads/${task.sampleImage}`;
                $taskCard.find('.sample-image').attr('src', sampleImageUrl);
            }
            
            if (task.resultImage) {
                const resultImageUrl = `https://res.cloudinary.com/${cloudinaryName}/image/upload/w_80,h_80,q_auto,f_auto/uploads/${task.resultImage}`;
                $taskCard.find('.result-image').attr('src', resultImageUrl);
            }
            
            // Configure action buttons based on task status
            configureBulkActionButtons($taskCard, task);
            
            return $taskCard;
        }

        // Create a subtask item from template
        function createSubtaskItem(task) {
            const $template = $('#subtask-item-template').html();
            const $subtaskItem = $($.parseHTML($template));
            
            $subtaskItem.attr('data-task-id', task.id);
            $subtaskItem.find('.subtask-id-badge').text('#' + task.id);
            $subtaskItem.find('.subtask-name').text(task.name);
            $subtaskItem.find('.subtask-qty').text('SL: ' + task.qtyCompleted + '/' + task.qtyRequest);
            
            // Calculate progress
            const progress = task.qtyRequest > 0 ? Math.min(100, Math.round((task.qtyCompleted / task.qtyRequest) * 100)) : 0;
            $subtaskItem.find('.subtask-progress-bar').css('width', progress + '%');
            $subtaskItem.find('.subtask-progress-text').text(progress + '%');
            
            // Set status badge color
            setStatusBadge($subtaskItem.find('.subtask-status-badge'), task.statusId);
            
            // Set images if available
            if (task.sampleImage) {
                const sampleImageUrl = `https://res.cloudinary.com/${cloudinaryName}/image/upload/w_80,h_80,q_auto,f_auto/uploads/${task.sampleImage}`;
                $subtaskItem.find('.subtask-sample-image').attr('src', sampleImageUrl);
            }
            
            if (task.resultImage) {
                const resultImageUrl = `https://res.cloudinary.com/${cloudinaryName}/image/upload/w_80,h_80,q_auto,f_auto/uploads/${task.resultImage}`;
                $subtaskItem.find('.subtask-result-image').attr('src', resultImageUrl);
            }
            
            // Configure action buttons based on task status
            configureBulkActionButtons($subtaskItem, task, true);
            
            return $subtaskItem;
        }

        // Set status badge color and text based on status ID
        function setStatusBadge($badge, statusId) {
            let statusText = '';
            let statusColor = '';
            
            switch (statusId) {
                case 1:
                    statusText = 'Chưa bắt đầu';
                    statusColor = 'warning';
                    break;
                case 2:
                    statusText = 'Đang chờ';
                    statusColor = 'warning';
                    break;
                case 3:
                    statusText = 'Đang thực hiện';
                    statusColor = 'info';
                    break;
                case 4:
                    statusText = 'Hoàn thành';
                    statusColor = 'success';
                    break;
                case 5:
                    statusText = 'Đã huỷ';
                    statusColor = 'danger';
                    break;
                case 6:
                    statusText = 'Quá hạn';
                    statusColor = 'danger';
                    break;
                case 7:
                    statusText = 'Cần chỉnh sửa';
                    statusColor = 'danger';
                    break;
                case 8:
                    statusText = 'Đã kết thúc';
                    statusColor = 'neutral';
                    break;
                default:
                    statusText = 'Khác';
                    statusColor = 'gray';
            }
            
            $badge.text(statusText);
            $badge.addClass('badge-' + statusColor);
        }

        // Configure action buttons based on task status and type
        function configureBulkActionButtons($element, task, isSubtask = false) {
            const prefix = isSubtask ? 'subtask-' : '';
            const $claimBtn = $element.find('.' + prefix + 'claim-btn');
            const $reportBtn = $element.find('.' + prefix + 'report-btn');
            
            // For CONTRACT tasks or parent tasks with children, hide both buttons
            if (task.type === 'CONTRACT' || (task.hasChildren && !isSubtask)) {
                $claimBtn.addClass('hidden');
                $reportBtn.addClass('hidden');
                return;
            }
            
            // For tasks that can be claimed (status 1, 2, 7)
            if ([1, 2, 7].includes(task.statusId)) {
                $claimBtn.removeClass('hidden');
                $reportBtn.addClass('hidden');
                
                // Set up claim button click handler
                $claimBtn.off('click').on('click', function() {
                    openClaimTaskModalFromBulk(task.id, isSubtask ? $element : null);
                });
            } 
            // For tasks that can be reported (status 3)
            else if (task.statusId === 3) {
                $claimBtn.addClass('hidden');
                $reportBtn.removeClass('hidden');
                
                // Set up report button click handler
                $reportBtn.off('click').on('click', function() {
                    openReportTaskModalFromBulk(task.id, task.name, isSubtask ? $element : null);
                });
            } 
            // For completed tasks (status 4), hide both buttons
            else {
                $claimBtn.addClass('hidden');
                $reportBtn.addClass('hidden');
            }
        }

        // Function to open the claim task modal from bulk modal
        function openClaimTaskModalFromBulk(taskId, $subtaskElement = null) {
            $('#claim-task-id').val(taskId);
            
            // Load missions
            loadMissions().then(() => {
                // When missions are loaded, show the claim modal
                KTModal.getInstance(document.querySelector('#claim-task-modal')).show();
                
                // Override the submit handler
                $('#claim-task-form').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    
                    if ($('input[name="mission_ids[]"]:checked').length === 0) {
                        showAlert('warning', 'Vui lòng chọn ít nhất một nhiệm vụ');
                        return;
                    }
                    
                    const formData = $(this).serialize();
                    
                    Notiflix.Loading.circle('Đang xử lý...');
                    // Submit the form via AJAX
                    axios.post('/task/claim', formData)
                        .then(response => {
                            if (response.data.status === 200) {
                                showAlert('success', response.data.message);
                                KTModal.getInstance(document.querySelector('#claim-task-modal')).hide();
                                
                                // Update the task status in the UI without reloading
                                updateTaskAfterClaim(taskId, $subtaskElement);
                            } else {
                                showAlert('warning', response.data.message);
                            }
                            Notiflix.Loading.remove();
                        })
                        .catch(error => {
                            console.error('Error claiming task:', error);
                            showAlert('error', 'Không thể nhận việc');
                            Notiflix.Loading.remove();
                        });
                });
            });
        }

        // Function to open the report task modal from bulk modal
        function openReportTaskModalFromBulk(taskId, taskName, $subtaskElement = null) {
            $('#report-task-name').text(taskName);
            
            // Load task missions
            loadTaskMissions(taskId).then(() => {
                // When missions are loaded, show the report modal
                KTModal.getInstance(document.querySelector('#report-missions-modal')).show();
                
                // Override the submit handler
                $('#report-missions-form').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = $(this).serialize();
                    
                    // Submit the form via AJAX
                    Notiflix.Loading.circle('Đang xử lý...');
                    axios.post('/task/report-mission', formData)
                        .then(response => {
                            if (response.data.status === 200) {
                                showAlert('success', response.data.message);
                                KTModal.getInstance(document.querySelector('#report-missions-modal')).hide();
                                
                                // Update the task status in the UI without reloading
                                updateTaskAfterReport(taskId, $subtaskElement);
                            } else {
                                showAlert('warning', response.data.message);
                            }
                            Notiflix.Loading.remove();
                        })
                        .catch(error => {
                            console.error('Error reporting missions:', error);
                            showAlert('error', 'Không thể báo cáo nhiệm vụ');
                            Notiflix.Loading.remove();
                        });
                });
            });
        }

        // Update task in UI after claiming
        function updateTaskAfterClaim(taskId, $subtaskElement = null) {
            const task = tasksData.get(taskId.toString());
            if (!task) return;
            
            // Update task status in our data
            task.statusId = 3; // Set to "Đang thực hiện"
            
            // If this is a subtask in the bulk modal
            if ($subtaskElement) {
                $subtaskElement.find('.subtask-status-badge')
                    .removeClass('badge-primary badge-warning badge-info badge-success badge-danger badge-gray')
                    .addClass('badge-info')
                    .text('Đang thực hiện');
                
                // Change buttons visibility
                $subtaskElement.find('.subtask-claim-btn').addClass('hidden');
                $subtaskElement.find('.subtask-report-btn').removeClass('hidden');
                
                // Set up report button click handler
                $subtaskElement.find('.subtask-report-btn').off('click').on('click', function() {
                    openReportTaskModalFromBulk(taskId, task.name, $subtaskElement);
                });
            } 
            // If this is a task in the main bulk modal
            else {
                const $taskCard = $(`.task-card[data-task-id="${taskId}"]`);
                
                $taskCard.find('.task-status-badge')
                    .removeClass('badge-primary badge-warning badge-info badge-success badge-danger badge-gray')
                    .addClass('badge-info')
                    .text('Đang thực hiện');
                
                // Change buttons visibility
                $taskCard.find('.claim-btn').addClass('hidden');
                $taskCard.find('.report-btn').removeClass('hidden');
                
                // Set up report button click handler
                $taskCard.find('.report-btn').off('click').on('click', function() {
                    openReportTaskModalFromBulk(taskId, task.name);
                });
            }
            
            // Update in the main table as well
            const $tableRow = $(`.task-checkbox[data-task-id="${taskId}"]`).closest('tr');
            $tableRow.find('.badge-outline').first().removeClass().addClass('badge badge-sm badge-outline badge-info').text('Đang thực hiện');
            renderTasksInModal();
        }

        // Update task in UI after reporting
        function updateTaskAfterReport(taskId, $subtaskElement = null) {
            // Get the updated task data from the server
            Notiflix.Loading.circle('Đang xử lý...');
            axios.get(`/task/get-status/${taskId}`)
                .then(response => {
                    if (response.data.status === 200) {
                        const updatedTask = response.data.data;
                        const task = tasksData.get(taskId.toString());
                        
                        if (!task) return;
                        
                        // Update task data
                        task.statusId = updatedTask.status_id;
                        task.qtyCompleted = updatedTask.qty_completed;
                        task.progress = updatedTask.progress;
                        
                        // Calculate progress
                        const progress = task.progress;
                        
                        // If this is a subtask in the bulk modal
                        if ($subtaskElement) {
                            // Update status badge
                            setStatusBadge($subtaskElement.find('.subtask-status-badge'), task.statusId);
                            
                            // Update quantity
                            $subtaskElement.find('.subtask-qty').text(`SL: ${task.qtyCompleted}/${task.qtyRequest}`);
                            
                            // Update progress bar
                            $subtaskElement.find('.subtask-progress-bar').css('width', progress + '%');
                            $subtaskElement.find('.subtask-progress-text').text(progress + '%');
                            
                            // Configure buttons based on new status
                            configureBulkActionButtons($subtaskElement, task, true);
                        } 
                        // If this is a task in the main bulk modal
                        else {
                            const $taskCard = $(`.task-card[data-task-id="${taskId}"]`);
                            
                            // Update status badge
                            setStatusBadge($taskCard.find('.task-status-badge'), task.statusId);
                            
                            // Update quantity
                            $taskCard.find('.task-qty').text(`SL: ${task.qtyCompleted}/${task.qtyRequest}`);
                            
                            // Update progress bar
                            $taskCard.find('.task-progress-bar').css('width', progress + '%');
                            $taskCard.find('.task-progress-text').text(progress + '%');
                            
                            // Configure buttons based on new status
                            configureBulkActionButtons($taskCard, task);
                        }
                        
                        // Update in the main table as well
                        const $tableRow = $(`.task-checkbox[data-task-id="${taskId}"]`).closest('tr');
                        
                        // Update status badge
                        if (task.statusId === 4) {
                            $tableRow.find('.badge-outline').first().removeClass().addClass('badge badge-sm badge-outline badge-success').text('Hoàn thành');
                        } else {
                            $tableRow.find('.badge-outline').first().removeClass().addClass('badge badge-sm badge-outline badge-info').text('Đang thực hiện');
                        }
                        
                        // Update progress bar
                        $tableRow.find('.bg-blue-800, .bg-success').css('width', progress + '%');
                        $tableRow.find('.bg-blue-800, .bg-success').find('span').text(progress + '%');
                        
                        // Check if parent task needs to be updated
                        if (task.parentId && updatedTask.parent_updated) {
                            updateParentTaskUI(task.parentId);
                        }

                        renderTasksInModal();
                    }
                    Notiflix.Loading.remove();
                })
                .catch(error => {
                    console.error('Error getting task status:', error);
                    Notiflix.Loading.remove();
                });
        }

        // Update parent task UI after child task status changes
        function updateParentTaskUI(parentId) {
            Notiflix.Loading.circle('Đang xử lý...');
            axios.get(`/task/get-status/${parentId}`)
                .then(response => {
                    if (response.data.status === 200) {
                        const parentTask = response.data.data;
                        
                        if (!parentTask) return;
                        
                        // Update in our data
                        if (tasksData.has(parentId.toString())) {
                            const task = tasksData.get(parentId.toString());
                            task.statusId = parentTask.status_id;
                            task.progress = parentTask.progress;
                        }
                        
                        // Update parent in the main table
                        const $tableRow = $(`.task-checkbox[data-task-id="${parentId}"]`).closest('tr');
                        
                        // Update status badge based on new status
                        let statusClass = 'badge-info';
                        let statusText = 'Đang thực hiện';
                        
                        if (parentTask.status_id === 4) {
                            statusClass = 'badge-success';
                            statusText = 'Hoàn thành';
                        } else if (parentTask.status_id === 7) {
                            statusClass = 'badge-danger';
                            statusText = 'Cần chỉnh sửa';
                        }
                        
                        $tableRow.find('.badge-outline').first().removeClass().addClass(`badge badge-sm badge-outline ${statusClass}`).text(statusText);
                        
                        // Update progress bar
                        $tableRow.find('.bg-blue-800, .bg-success').css('width', parentTask.progress + '%');
                        $tableRow.find('.bg-blue-800, .bg-success').find('span').text(parentTask.progress + '%');
                        
                        // Update parent task in bulk modal if present
                        const $taskCard = $(`.task-card[data-task-id="${parentId}"]`);
                        if ($taskCard.length > 0) {
                            // Update status badge
                            setStatusBadge($taskCard.find('.task-status-badge'), parentTask.status_id);
                            
                            // Update progress bar
                            $taskCard.find('.task-progress-bar').css('width', parentTask.progress + '%');
                            $taskCard.find('.task-progress-text').text(parentTask.progress + '%');
                        }
                        
                        // If this parent also has a parent, update it too
                        if (parentTask.parent_id && parentTask.parent_updated) {
                            updateParentTaskUI(parentTask.parent_id);
                        }
                    }
                    Notiflix.Loading.remove();
                })
                .catch(error => {
                    console.error('Error getting parent task status:', error);
                    Notiflix.Loading.remove();
                });
        }

        // Override loadMissions to return a promise
        window.loadMissions = function() {
            Notiflix.Loading.circle('Đang xử lý...');
            return new Promise((resolve, reject) => {
                axios.get('/task/missions')
                    .then(response => {
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
                            resolve();
                        } else {
                            showAlert('warning', response.data.message);
                            reject(new Error(response.data.message));
                        }
                        Notiflix.Loading.remove();
                    })
                    .catch(error => {
                        console.error('Error loading missions:', error);
                        showAlert('error', 'Không thể tải danh sách nhiệm vụ');
                        reject(error);
                        Notiflix.Loading.remove();
                    });
            });
        };

        // Override loadTaskMissions to return a promise
        window.loadTaskMissions = function(taskId) {
            Notiflix.Loading.circle('Đang xử lý...');
            return new Promise((resolve, reject) => {
                axios.get('/task/task-missions', {
                    params: { task_id: taskId }
                })
                    .then(response => {
                        if (response.data.status === 200) {
                            const data = response.data.data;
                            let html = '';
                            
                            data.assignments.forEach(assignment => {
                                const remaining = assignment.quantity_required - assignment.quantity_completed;
                                if (remaining <= 0) return; // Skip if completed
                                
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
                                const cloudName = "{{env('CLOUDINARY_CLOUD_NAME')}}";
                                const info = `<div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-blue-800">Thông tin nhiệm vụ</h4>
                                        <span class="text-xs font-medium px-2 py-1 bg-blue-100 text-blue-800 rounded-full">ID: #${data.task.id}</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500">Tên nhiệm vụ</span>
                                                <span class="font-medium text-gray-900">${data.task.name}</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500">Tiến độ</span>
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${data.task.progress}%"></div>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700">${data.task.progress}%</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500">Hoàn thành</span>
                                                <span class="font-medium text-gray-900">${data.task.qty_completed}/${data.task.qty_request}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 mb-1">Mẫu</span>
                                                <div class="h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                                    <img onerror="this.src='/assets/images/default.svg'" src="https://res.cloudinary.com/${cloudName}/image/upload/w_119,h_94,q_auto,f_auto/uploads/${data.task.sample_image_id}" alt="Ảnh mẫu" class="w-full h-full object-cover">
                                                </div>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 mb-1">Kết quả</span>
                                                <div class="h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                                    <img onerror="this.src='/assets/images/default.svg'" src="https://res.cloudinary.com/${cloudName}/image/upload/w_119,h_94,q_auto,f_auto/uploads/${data.task.result_image_id}" alt="Ảnh kết quả" class="w-full h-full object-cover">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                                html = info + html;
                            }
                            
                            $('#mission-reports').html(html);
                            resolve();
                        } else {
                            showAlert('warning', response.data.message);
                            reject(new Error(response.data.message));
                        }
                        Notiflix.Loading.remove();
                    })
                    .catch(error => {
                        console.error('Error loading task missions:', error);
                        showAlert('error', 'Không thể tải danh sách nhiệm vụ của task');
                        reject(error);
                        Notiflix.Loading.remove();
                    });
            });
        };
    });

    
    function loadDataTable() {
        const _updater = $(".updater");

        if (_updater.length == 0) return;

        $.each(_updater, async (_, _this) => {
            Notiflix.Loading.circle('Đang tải dữ liệu...');
            await callAjaxDataTable($(_this));
            Notiflix.Loading.remove();
            callAPIAfterAjaxLoad()
        })
    }

    async function callAPIAfterAjaxLoad() {
        selectedTasks.clear();
        $('#bulk-action-btn').addClass('hidden');
        let taskIDs = []
        $('.task-checkbox').each(function() {
            const $checkbox = $(this);
            const taskId = $checkbox.data('task-id');
            taskIDs.push($checkbox.data('task-id'));

            tasksData.set(taskId.toString(), {
                id: taskId,
                name: $checkbox.data('task-name'),
                type: $checkbox.data('task-type'),
                parentId: $checkbox.data('parent-id'),
                hasChildren: $checkbox.data('has-children'),
                statusId: $checkbox.data('status-id'),
                qtyCompleted: $checkbox.data('qty-completed'),
                qtyRequest: $checkbox.data('qty-request'),
                sampleImage: $checkbox.data('sample-image'),
                resultImage: $checkbox.data('result-image')
            });

            if ($checkbox.data('parent-id')) {
                const parentId = $checkbox.data('parent-id').toString();
                if (!parentChildMap.has(parentId)) {
                    parentChildMap.set(parentId, []);
                }
                parentChildMap.get(parentId).push(taskId.toString());
            }
        });

        await getTaskByIDs(taskIDs);
    }

    async function getTaskByIDs(taskIDs) {
        let method = "get",
            url = '/task/get-list-by-ids',
            params = {
                ids: taskIDs.join(',')
            },
            data = null;
        let res = await axiosTemplate(method, url, params, data);

        if (res.data.status === 200) {
            $(res.data.data).each((_, item) => {
                const taskId = item.id;
                tasksData.set(taskId.toString(), {
                    id: taskId,
                    name: item.name,
                    type: item.type,
                    parentId: item.parent_id,
                    hasChildren: 0,
                    statusId: item.status_id,
                    qtyCompleted: item.qty_completed,
                    qtyRequest: item.qty_request,
                    sampleImage: item.sample_image_id,
                    resultImage: item.result_image_id
                });

                if (item.parent_id) {
                    const parentId = item.parent_id.toString();
                    if (!parentChildMap.has(parentId)) {
                        parentChildMap.set(parentId, []);
                    }
                    parentChildMap.get(parentId).push(taskId.toString());
                }
            })
        }
    }
</script>
@endpush