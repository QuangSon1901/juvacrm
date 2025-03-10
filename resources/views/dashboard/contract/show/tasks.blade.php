{{-- resources/views/dashboard/contracts/tabs/tasks.blade.php --}}
<div class="hidden transition-opacity duration-700" id="tab-tasks">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="col-span-1 xl:!col-span-3">
            <div class="card shadow-sm border border-gray-100 overflow-hidden mb-4">
                <div class="card-header bg-white border-b border-gray-100">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-check-square text-blue-500"></i>
                        Tổng hợp công việc
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:!grid-cols-5 gap-4">
                        @php
                            $totalTasks = 0;
                            $completedTasks = 0;
                            $pendingTasks = 0;
                            $inProgressTasks = 0;
                            $cancelTasks = 0;
                            
                            // Hàm đệ quy để đếm task
                            function countTasks($tasks, &$total, &$completed, &$pending, &$inProgress, &$cancelTask) {
                                foreach ($tasks as $task) {
                                    $total++;
                                    if ($task['status']['id'] == 4) {
                                        $completed++;
                                    } elseif ($task['status']['id'] == 1 || $task['status']['id'] == 2) {
                                        $pending++;
                                    } elseif ($task['status']['id'] == 5 || $task['status']['id'] == 6) {
                                        $cancelTasks++;
                                    } else {
                                        $inProgress++;
                                    }
                                    
                                    if (isset($task['children']) && count($task['children']) > 0) {
                                        countTasks($task['children'], $total, $completed, $pending, $inProgress, $cancelTask);
                                    }
                                }
                            }
                            
                            if (isset($details['tasks'])) {
                                countTasks($details['tasks'], $totalTasks, $completedTasks, $pendingTasks, $inProgressTasks, $cancelTasks);
                            }
                            
                            $overallProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 0) : 0;
                        @endphp
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Tổng số công việc</div>
                            <div class="text-lg font-medium text-gray-800">{{ $totalTasks }} công việc</div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-sm text-green-600 mb-1">Đã hoàn thành</div>
                            <div class="text-lg font-medium text-green-600">{{ $completedTasks }} công việc</div>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-sm text-blue-600 mb-1">Đang thực hiện</div>
                            <div class="text-lg font-medium text-blue-800">{{ $inProgressTasks }} công việc</div>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <div class="text-sm text-orange-600 mb-1">Chưa thực hiện</div>
                            <div class="text-lg font-medium text-orange-600">{{ $pendingTasks }} công việc</div>
                        </div>
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <div class="text-sm text-orange-600 mb-1">Đã huỷ</div>
                            <div class="text-lg font-medium text-orange-600">{{ $cancelTasks }} công việc</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-700">Tiến độ tổng thể</span>
                            <span class="text-sm font-medium text-gray-700">{{ $overallProgress }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-200 rounded-full">
                            <div class="h-2.5 bg-blue-600 rounded-full" style="width: {{ $overallProgress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                <div class="card-header bg-white border-b border-gray-100">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-abstract-26 text-purple-500"></i>
                        Danh sách công việc
                    </h3>
                    <a href="{{ route('dashboard.account.task.task') }}?filter[contract_id]={{ $details['id'] }}" class="btn btn-light btn-xs">
                        <i class="ki-filled ki-external-link"></i>
                        Xem trong Task Manager
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="task-tree">
                        @if(isset($details['tasks']) && count($details['tasks']) > 0)
                            @foreach($details['tasks'] as $mainTask)
                                <div class="task-item main-task border-b border-gray-100 hover:bg-gray-50">
                                    <div class="p-4 flex items-center justify-between cursor-pointer" onclick="toggleTaskChildren(this)">
                                        <div class="flex items-center gap-3">
                                            <i class="ki-filled ki-arrow-right task-toggle-icon text-gray-500"></i>
                                            <div class="flex flex-col">
                                                <div class="font-medium text-gray-900 text-sm">{{ $mainTask['name'] }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $mainTask['type'] == 'CONTRACT' ? 'Hợp đồng' : 'Dịch vụ' }} • 
                                                    Phụ trách: {{ $mainTask['assigned_user']['name'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="badge badge-sm badge-outline" style="color: {{ $mainTask['status']['color'] }}; border-color: {{ $mainTask['status']['color'] }};">
                                                {{ $mainTask['status']['name'] }}
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-sm text-gray-700">{{ $mainTask['progress'] }}%</span>
                                                <div class="w-20 h-1.5 bg-gray-200 rounded-full">
                                                    <div class="h-1.5 rounded-full" style="width: {{ $mainTask['progress'] }}%; background-color: {{ $mainTask['status']['color'] }};"></div>
                                                </div>
                                            </div>
                                            <a href="{{ route('dashboard.account.task.detail', $mainTask['id']) }}" class="btn btn-icon btn-xs btn-light">
                                                <i class="ki-filled ki-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="task-children pl-8 hidden">
                                        @if(isset($mainTask['children']) && count($mainTask['children']) > 0)
                                            @foreach($mainTask['children'] as $childTask)
                                                <div class="task-item child-task border-t border-gray-100 hover:bg-gray-50">
                                                    <div class="p-3 flex items-center justify-between cursor-pointer" onclick="toggleTaskChildren(this)">
                                                        <div class="flex items-center gap-3">
                                                            <i class="ki-filled ki-arrow-right task-toggle-icon text-gray-500"></i>
                                                            <div class="flex flex-col">
                                                                <div class="font-medium text-gray-800 text-sm">{{ $childTask['name'] }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $childTask['type'] == 'SERVICE' ? 'Dịch vụ' : 'Khác' }} • 
                                                                    Phụ trách: {{ $childTask['assigned_user']['name'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <div class="badge badge-sm badge-outline" style="color: {{ $childTask['status']['color'] }}; border-color: {{ $childTask['status']['color'] }};">
                                                                {{ $childTask['status']['name'] }}
                                                            </div>
                                                            <div class="flex items-center gap-1">
                                                                <span class="text-sm text-gray-700">{{ $childTask['progress'] }}%</span>
                                                                <div class="w-16 h-1.5 bg-gray-200 rounded-full">
                                                                    <div class="h-1.5 rounded-full" style="width: {{ $childTask['progress'] }}%; background-color: {{ $childTask['status']['color'] }};"></div>
                                                                </div>
                                                            </div>
                                                            <a href="{{ route('dashboard.account.task.detail', $childTask['id']) }}" class="btn btn-icon btn-xs btn-light">
                                                                <i class="ki-filled ki-eye"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="task-children pl-6 hidden">
                                                        @if(isset($childTask['children']) && count($childTask['children']) > 0)
                                                            @foreach($childTask['children'] as $subChildTask)
                                                                <div class="task-item sub-child-task border-t border-gray-100 hover:bg-gray-50">
                                                                    <div class="p-2 flex items-center justify-between">
                                                                        <div class="flex items-center gap-2 pl-5">
                                                                            <div class="flex flex-col">
                                                                                <div class="font-medium text-gray-700 text-sm">{{ $subChildTask['name'] }}</div>
                                                                                <div class="text-xs text-gray-500">
                                                                                    {{ $subChildTask['type'] == 'SUB' ? 'Dịch vụ con' : 'Khác' }} • 
                                                                                    Phụ trách: {{ $subChildTask['assigned_user']['name'] }}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex items-center gap-2">
                                                                            <div class="badge badge-sm badge-outline" style="color: {{ $subChildTask['status']['color'] }}; border-color: {{ $subChildTask['status']['color'] }};">
                                                                                {{ $subChildTask['status']['name'] }}
                                                                            </div>
                                                                            <div class="flex items-center gap-1">
                                                                                <span class="text-xs text-gray-700">{{ $subChildTask['progress'] }}%</span>
                                                                                <div class="w-12 h-1.5 bg-gray-200 rounded-full">
                                                                                    <div class="h-1.5 rounded-full" style="width: {{ $subChildTask['progress'] }}%; background-color: {{ $subChildTask['status']['color'] }};"></div>
                                                                                </div>
                                                                            </div>
                                                                            <a href="{{ route('dashboard.account.task.detail', $subChildTask['id']) }}" class="btn btn-icon btn-xs btn-light">
                                                                                <i class="ki-filled ki-eye"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-6 text-center">
                                <div class="text-gray-400 mb-2">
                                    <i class="ki-filled ki-notepad-edit text-5xl"></i>
                                </div>
                                <h4 class="text-gray-800 font-medium mb-1">Chưa có công việc nào</h4>
                                <p class="text-gray-600 text-sm">Hãy tạo công việc để quản lý tiến độ hợp đồng này.</p>
                                @if($details['status'] == 0)
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline btn-success px-5 py-2 flex items-center gap-2 mx-auto" onclick="saveCreateTaskContract({{$details['id']}})">
                                        <i class="ki-filled ki-plus text-white"></i>
                                        <span>Tạo công việc</span>
                                    </button>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle hiển thị/ẩn công việc con
    function toggleTaskChildren(element) {
        const taskItem = element.closest('.task-item');
        const taskChildren = taskItem.querySelector('.task-children');
        const toggleIcon = taskItem.querySelector('.task-toggle-icon');
        
        if (taskChildren) {
            if (taskChildren.classList.contains('hidden')) {
                taskChildren.classList.remove('hidden');
                toggleIcon.classList.add('rotate-90');
            } else {
                taskChildren.classList.add('hidden');
                toggleIcon.classList.remove('rotate-90');
            }
        }
    }
</script>
@endpush