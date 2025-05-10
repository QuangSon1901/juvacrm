@foreach ($data as $item)
<tr data-task-id="{{$item['id']}}" data-task-name="{{$item['name']}}" data-task-type="{{$item['type']}}" data-parent-id="{{$item['parent_id']}}" data-has-children="{{$item['has_children'] ? 'true' : 'false'}}">
    <td class="text-gray-800 font-normal text-center">
        <div class="flex items-center gap-2">
            <input type="checkbox" class="task-checkbox checkbox checkbox-sm" 
                data-task-id="{{$item['id']}}"
                data-task-name="{{$item['name']}}" 
                data-task-type="{{$item['type']}}"
                data-parent-id="{{$item['parent_id']}}"
                data-has-children="{{$item['has_children']}}"
                data-status-id="{{$item['status']['id']}}"
                data-qty-completed="{{$item['qty_completed']}}"
                data-qty-request="{{$item['qty_request']}}"
                data-sample-image="{{$item['sample_image_id']}}"
                data-result-image="{{$item['result_image_id']}}"
                data-contract-note="{{$item['contract']['note']}}"
            >
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        <div class="flex flex-col gap-1">
            @if ($item['status']['id'] != 0)
            <span class="badge badge-sm badge-outline badge-{{$item['status']['color']}}">
                {{$item['status']['name']}}
            </span>
            @else
            ---
            @endif

            @if ($item['deadline_status'] == 'overdue')
            <span class="badge badge-sm badge-danger flex items-center gap-1">
                <i class="ki-solid ki-timer text-xs"></i> Trễ hạn
            </span>
            @elseif($item['deadline_status'] == 'upcoming')
            <span class="badge badge-sm badge-warning flex items-center gap-1">
                <i class="ki-solid ki-timer text-xs"></i> Sắp đến hạn
            </span>
            @endif
        </div>
    </td>
    <td class="text-gray-800 font-normal">
        <!-- Tên công việc với badge ID và loại task -->

        <div class="flex items-center gap-2 mb-1">
            <span class="badge badge-sm badge-outline badge-neutral">
                #{{$item['id']}}
            </span>
            @if ($item['type'])
            <span class="badge badge-sm badge-outline badge-primary">
                {{$item['type']}}
            </span>
            @endif
        </div>
        <a class="text-gray-900 font-medium hover:text-primary">
            {{$item['name']}}
        </a>

        <!-- Thông tin phụ được tổ chức gọn gàng hơn -->
        <div class="flex flex-wrap gap-2 text-xs mt-1">
            @if ($item['parent_id'])
            <a class="text-gray-600 hover:text-primary flex items-center gap-1">
                <i class="ki-outline ki-up-square text-xs"></i>
                <span>Hợp đồng: {{$item['contract']['name'] ?? ''}}</span>
            </a>
            @endif
        </div>

        @if ($item['contract'])
        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
            <i class="ki-outline ki-document text-xs"></i>
            <span>{{$item['contract']['name']}}</span>
        </div>
        @endif
    </td>
    <td class="text-gray-800 font-normal">
        @if ($item['assign']['id'] == 0)
        <span class="badge badge-sm badge-outline badge-warning flex items-center gap-1">
            <i class="ki-outline ki-user-tick text-xs"></i>
            <span>Chưa phân công</span>
        </span>
        @else
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/member/{{$item['assign']['id']}}">
                    {{$item['assign']['name']}}
                </a>
                <span class="text-xs text-gray-600 font-normal">
                    ID: {{$item['assign']['id']}}
                </span>
            </div>
        </div>
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['start_date'])
        <p class="leading-none text-gray-900 font-medium">{{formatDateTime($item['start_date'], 'd-m-Y')}}</p>
        <span class="text-gray-600 text-xs">{{formatDateTime($item['start_date'], 'H:i')}}</span>
        @else
        <span class="text-gray-500">Chưa xác định</span>
        @endif
    </td>
    <td class="text-sm text-gray-800 font-normal">
        @if ($item['due_date'])
        <!-- Deadline với hiển thị rõ ràng hơn -->
        <div class="flex flex-col">
            <p class="leading-none font-medium 
                {{$item['deadline_status'] == 'overdue' ? 'text-danger' : ($item['deadline_status'] == 'upcoming' ? 'text-warning' : 'text-gray-900')}}">
                {{formatDateTime($item['due_date'], 'd-m-Y')}}
            </p>
            <span class="text-gray-600 text-xs">{{formatDateTime($item['due_date'], 'H:i')}}</span>

            @if($item['time_remaining'])
            <div class="mt-1 text-xs px-2 py-0.5 rounded 
                {{$item['deadline_status'] == 'overdue' ? 'bg-danger/10 text-danger' : 'bg-warning/10 text-warning'}}">
                <i class="ki-outline {{$item['deadline_status'] == 'overdue' ? 'ki-timer' : 'ki-timer'}} text-xs mr-1"></i>
                {{$item['time_remaining']}}
            </div>
            @endif
        </div>
        @else
        <span class="text-gray-500">Chưa xác định</span>
        @endif
    </td>
    <td class="text-gray-800 font-normal">
        <div class="max-w-32 bg-gray-200 rounded-sm h-4">
            <div class="{{$item['progress'] >= 100 ? 'bg-success' : 'bg-blue-800'}} h-4 rounded-sm flex items-center {{$item['progress'] == 0 ? 'justify-start' : 'justify-center'}}" style="width: {{$item['progress'] ?? 0}}%">
                <span class="text-xs font-medium text-white">
                    {{$item['progress']}}%
                </span>
            </div>
        </div>
    </td>
</tr>
@endforeach