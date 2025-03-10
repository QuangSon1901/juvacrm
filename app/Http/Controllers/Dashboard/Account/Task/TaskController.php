<?php

namespace App\Http\Controllers\Dashboard\Account\Task;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Contract;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskConfig;
use App\Models\TaskContribution;
use App\Models\Upload;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $priorities = TaskConfig::select('id', 'name', 'color')->where('type', 0)->where('is_active', 1)->orderBy('sort')->get()->toArray();
        $statuses = TaskConfig::select('id', 'name', 'color')->where('type', 1)->where('is_active', 1)->orderBy('sort')->get()->toArray();

        return view("dashboard.account.task.index", ['priorities' => $priorities, 'statuses' => $statuses]);
    }

    public function createView(Request $request)
    {
        $priorities = TaskConfig::select('id', 'name', 'color')->where('type', 0)->where('is_active', 1)->orderBy('sort')->get()->toArray();
        $statuses = TaskConfig::select('id', 'name', 'color')->where('type', 1)->where('is_active', 1)->orderBy('sort')->get()->toArray();
        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $contracts = Contract::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $tasks = Task::select('id', 'name')->where('is_active', 1)->get()->toArray();

        return view('dashboard.account.task.create', ['parent_active' => $request['parent_id'] ?? 0, 'priorities' => $priorities, 'statuses' => $statuses, 'users' => $users, 'contracts' => $contracts, 'services' => $services, 'tasks' => $tasks]);
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        $query = Task::query()
            ->myTask($request['filter']['my_task'] ?? 0)
            ->levelTask($request['filter']['level_task'] ?? 'CONTRACT')
            ->priorityTask($request['filter']['priority_task'] ?? '')
            ->statusTask($request['filter']['status_task'] ?? '')
            ->search($request['filter']['search'] ?? '');

        $query->where('is_active', 1);

        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];


        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'name' => $item->name,
                'priority' => [
                    'id' => $item->priority->id ?? 0,
                    'name' => $item->priority->name ?? '',
                    'color' => $item->priority->color ?? '',
                ],
                'assign' => [
                    'id' => $item->assign->id ?? 0,
                    'name' => $item->assign->name ?? '',
                ],
                'status' => [
                    'id' => $item->status->id ?? 0,
                    'name' => $item->status->name ?? '',
                    'color' => $item->status->color ?? '',
                ],
                'parent_id' => $item->parent_id,
                'start_date' => $item->start_date,
                'due_date' => $item->due_date,
                'progress' => $item->progress,
                'estimate_time' => $item->estimate_time,
                'spend_time' => $item->spend_time,
                'qty_request' => $item->qty_request,
                'qty_completed' => $item->qty_completed,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });
        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.task.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    // public function detailOld($id)
    // {
    //     $task = Task::find($id);
    //     if (!$task) {
    //         return abort(404, 'Công việc không tồn tại.');
    //     }

    //     $result = [
    //         'id' => $task->id,
    //         'name' => $task->name,
    //         'priority' => [
    //             'id' => $task->priority->id ?? 0,
    //             'name' => $task->priority->name ?? '',
    //             'color' => $task->priority->color ?? '',
    //         ],
    //         'assign' => [
    //             'id' => $task->assign->id ?? 0,
    //             'name' => $task->assign->name ?? '',
    //         ],
    //         'status' => [
    //             'id' => $task->status->id ?? 0,
    //             'name' => $task->status->name ?? '',
    //             'color' => $task->status->color ?? '',
    //         ],
    //         'description' => $task->description,
    //         'note' => $task->note,
    //         'parent' => [
    //             'id' => $task->parent->id ?? 0,
    //             'name' => $task->parent->name ?? '',
    //         ],
    //         'contract' => [
    //             'id' => $task->contract->id ?? 0,
    //             'name' => $task->contract->name ?? '',
    //         ],
    //         'service' => [
    //             'id' => $task->service->id ?? 0,
    //             'name' => $task->service->name ?? '',
    //         ],
    //         'create_by' => [
    //             'id' => $task->createdBy->id ?? 0,
    //             'name' => $task->createdBy->name ?? '',
    //         ],
    //         'service_other' => $task->service_other,
    //         'bonus_amount' => $task->bonus_amount,
    //         'deduction_amount' => $task->deduction_amount,
    //         'childs' => $task->childs->map(function ($child) {
    //             return [
    //                 'id' => $child->id,
    //                 'name' => $child->name,
    //                 'status' => [
    //                     'id' => $child->status->id ?? 0,
    //                     'name' => $child->status->name ?? '',
    //                     'color' => $child->status->color ?? '',
    //                 ],
    //                 'assign' => [
    //                     'id' => $child->assign->id ?? 0,
    //                     'name' => $child->assign->name ?? '',
    //                 ],
    //                 'priority' => [
    //                     'id' => $child->priority->id ?? 0,
    //                     'name' => $child->priority->name ?? '',
    //                     'color' => $child->priority->color ?? '',
    //                 ],
    //                 'start_date' => $child->start_date,
    //                 'due_date' => $child->due_date,
    //                 'qty_request' => $child->qty_request,
    //                 'qty_completed' => $child->qty_completed,
    //             ];
    //         }),
    //         'comments' => $task->comments->map(function ($comment) {
    //             return [
    //                 'id' => $comment->id,
    //                 'message' => $comment->message,
    //                 'user' => [
    //                     'id' => $comment->user->id,
    //                     'name' => $comment->user->name,
    //                 ],
    //                 'created_at' => $comment->created_at
    //             ];
    //         }),
    //         'start_date' => $task->start_date,
    //         'due_date' => $task->due_date,
    //         'progress' => $task->progress,
    //         'estimate_time' => $task->estimate_time,
    //         'spend_time' => $task->spend_time,
    //         'qty_request' => $task->qty_request,
    //         'qty_completed' => $task->qty_completed,
    //         'created_at' => $task->created_at,
    //         'updated_at' => $task->updated_at,
    //     ];

    //     $priorities = TaskConfig::select('id', 'name', 'color')->where('type', 0)->where('is_active', 1)->orderBy('sort')->get()->toArray();
    //     $statuses = TaskConfig::select('id', 'name', 'color')->where('type', 1)->where('is_active', 1)->orderBy('sort')->get()->toArray();
    //     $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
    //     $activity_logs = ActivityLogs::where('action', TASK_ENUM_LOG)->where('fk_key', 'tbl_tasks|id')->where('fk_value', $id)->orderBy('created_at', 'desc')->get()->map(function ($log, $index) {
    //         return [
    //             'index' => $index,
    //             'id' => $log->id,
    //             'action' => $log->action,
    //             'ip' => $log->ip,
    //             'details' => $log->details,
    //             'user' => [
    //                 'id' => $log->user->id,
    //                 'name' => $log->user->name,
    //             ],
    //             'created_at' => $log->created_at,
    //         ];
    //     });
    //     $contracts = Contract::select('id', 'name')->where('is_active', 1)->get()->toArray();
    //     $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
    //     $attachments = Upload::select('id', 'name', 'type', 'size', 'driver_id', 'extension', 'created_at')->where('action', MEDIA_DRIVER_UPLOAD)->where('fk_key', 'tbl_tasks|id')->where('fk_value', $id)->orderBy('created_at', 'desc')->get()->toArray();
    //     function getAllParentTaskIds($taskId)
    //     {
    //         $parentTask = Task::select('parent_id')->where('id', $taskId)->first();
    //         if ($parentTask && $parentTask->parent_id) {
    //             return array_merge([$parentTask->parent_id], getAllParentTaskIds($parentTask->parent_id));
    //         }
    //         return [];
    //     }
    //     $parentIds = getAllParentTaskIds($id);
    //     $excludedIds = array_merge([$id], $parentIds);
    //     $tasks = Task::select('id', 'name')->whereNotIn('id', $excludedIds)->whereNull('parent_id')->where('is_active', 1)->get()->toArray();

    //     return view("dashboard.account.task.detail", ['details' => $result, 'priorities' => $priorities, 'statuses' => $statuses, 'users' => $users, 'activity_logs' => $activity_logs, 'contracts' => $contracts, 'services' => $services, 'tasks' => $tasks, 'attachments' => $attachments]);
    // } 

    public function addComment(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'message' => 'required|string|max:255',
            ],
            [
                'required' => ':attribute không được để trống',
                'string' => ':attribute phải là chuỗi ký tự',
                'max' => ':attribute không được vượt quá :max ký tự',
            ],
            [
                'message' => 'Nội dung',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $task = Task::find($request['id']);

            if (!$task) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Công việc không tồn tại.',
                ]);
            }

            $data = [
                'user_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                'task_id' => $request['id'],
                'message' => $request['message'],
            ];
            TaskComment::create($data);

            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đăng bình luận',
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Đăng bình luận thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi đăng bình luận.',
            ]);
        }
    }

    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255',
            'status_id' => 'required|integer|exists:tbl_task_config,id',
            'priority_id' => 'required|integer|exists:tbl_task_config,id',
            'assign_id' => 'nullable|integer|exists:tbl_users,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'estimate_time' => 'nullable|integer',
            'spend_time' => 'nullable|integer',
            'description' => 'nullable|string|max:5000',
            'note' => 'nullable|string|max:500',
            'bonus_amount' => 'nullable|integer',
            'deduction_amount' => 'nullable|integer',
            'qty_request' => 'required|integer',
            'qty_completed' => 'nullable|integer',
            'parent_id' => 'nullable|integer|exists:tbl_tasks,id',
            'contract_id' => 'nullable|integer|exists:tbl_contracts,id',
            'service_id' => 'nullable|integer|exists:tbl_services,id',
            'service_other' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $data = $request->all();
            if (!empty($data['start_date'])) {
                $data['start_date'] = formatDateTime($data['start_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            if (!empty($data['due_date'])) {
                $data['due_date'] = formatDateTime($data['due_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            if (!$data['qty_completed']) $data['qty_completed'] = 0;

            $data['created_id'] = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $task = Task::create($data);


            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' (#' . Session::get(ACCOUNT_CURRENT_SESSION)['id'] . ') ' . 'đã tạo công việc.',
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $task->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Công việc đã tạo thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Đã xảy ra lỗi khi tạo công việc.',
            ]);
        }
    }

    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'note' => 'nullable|string|max:500',
            'contract_id' => 'nullable|integer|exists:tbl_contracts,id',
            'progress' => 'nullable|integer|between:0,100',
            'service_id' => 'nullable|integer|exists:tbl_services,id',
            'priority_id' => 'nullable|integer|exists:tbl_task_config,id',
            'status_id' => 'nullable|integer|exists:tbl_task_config,id',
            'issue_id' => 'nullable|integer|exists:tbl_task_config,id',
            'estimate_time' => 'nullable|integer',
            'spend_time' => 'nullable|integer',
            'due_date' => 'nullable|date',
            'assign_id' => 'nullable|integer|exists:tbl_users,id',
            'sub_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'bonus_amount' => 'nullable|integer',
            'deduction_amount' => 'nullable|integer',
            'qty_request' => 'nullable|integer',
            'qty_completed' => 'nullable|integer',
            'service_other' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $task = Task::find($request['id']);

            if (!$task) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Công việc không tồn tại.',
                ]);
            }

            $data = $request->only(['name', 'description', 'note', 'contract_id', 'progress', 'service_id', 'priority_id', 'status_id', 'issue_id', 'estimate_time', 'spend_time', 'qty_request', 'qty_completed', 'due_date', 'assign_id', 'sub_name', 'start_date', 'deduction_amount', 'bonus_amount', 'service_other']);
            if (!empty($data['start_date'])) {
                $data['start_date'] = formatDateTime($data['start_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            if (!empty($data['due_date'])) {
                $data['due_date'] = formatDateTime($data['due_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }
            $task->update($data);

            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã cập nhật ' . FIELD_VALIDATE[array_diff(array_keys($data), ['id'])[0]],
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật công việc thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật công việc.',
            ]);
        }
    }

    public function updateSubTask(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_tasks,id',
            'sub_task' => 'required|integer|exists:tbl_tasks,id',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $type = $request['type'] == ADD_ENUM_TYPE ? ADD_ENUM_TYPE : REMOVE_ENUM_TYPE;

            $subTask = Task::find($request['sub_task']);

            $data = [
                'parent_id' => $type == ADD_ENUM_TYPE ? $request['id'] : null
            ];
            $subTask->update($data);

            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => $type == ADD_ENUM_TYPE ? 'Thêm chỉ mục #' . $request['sub_task'] : 'Gỡ chỉ mục #' . $request['sub_task'],
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => $type == ADD_ENUM_TYPE ? 'Thêm chỉ mục thành công.' : 'Gỡ chỉ mục thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi thêm/gỡ chỉ mục.',
            ]);
        }
    }

    public function removeAttachment(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_uploads,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $config = Upload::find($request['id']);
            $config->update(['fk_key' => null, 'fk_value' => null]);

            return response()->json([
                'status' => 200,
                'message' => 'Gỡ tệp thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi gỡ tệp.',
            ]);
        }
    }

    public function uploadFileTask(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_tasks,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,svg,pdf,txt,doc,docx,xls,xlsx,csv,ppt,pptx,zip,rar'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $googleDriveService = new GoogleDriveService();
            $googleDriveService->uploadFile($request->file('file'), MEDIA_DRIVER_UPLOAD, 'tbl_tasks|id', $request['id']);

            return response()->json([
                'status' => 200,
                'message' => 'Tải lên tệp thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi tải lên tệp.',
            ]);
        }
    }

    public function config()
    {
        $priorities = TaskConfig::where('type', 0)->orderBy('sort')->get()->toArray();
        $statuses = TaskConfig::where('type', 1)->orderBy('sort')->get()->toArray();
        $issues = TaskConfig::where('type', 2)->orderBy('sort')->get()->toArray();

        return view("dashboard.account.task.config", ['priorities' => $priorities, 'statuses' => $statuses, 'issues' => $issues]);
    }

    public function configPost(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|int',
            'name' => 'required|string|max:255',
            'sort' => 'required|int',
            'type' => 'required|int',
            'color' => 'required|string|in:success,warning,primary,gray,danger,neutral',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $config = TaskConfig::find($request['id']);
            $data = $request->only('name', 'sort', 'color', 'type');
            if ($config) {
                $config->update($data);
            } else {
                $newTask = TaskConfig::create($data);
            }

            LogService::saveLog([
                'action' => CONFIG_TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => $config ? 'Chỉnh sửa cấu hình #' . $request['id'] : 'Thêm cấu hình #' . $newTask->id,
                'fk_key' => 'tbl_task_config|id',
                'fk_value' => $config ? $request['id'] : $newTask->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => $config ? 'Chỉnh sửa cấu hình thành công.' : 'Thêm cấu hình thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra.',
            ]);
        }
    }

    public function configChangeStatus(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|int',
            'is_active' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $config = TaskConfig::find($request['id']);
            if (!$config) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Cấu hình không tồn tại.',
                ]);
            }
            $config->update(['is_active' => $request['is_active']]);

            LogService::saveLog([
                'action' => CONFIG_TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => "Vừa cập nhật lại trạng thái của #" . $request['id'],
                'fk_key' => 'tbl_task_config|id',
                'fk_value' => $request['id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật trạng thái thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra.',
            ]);
        }
    }

    /**
     * Hiển thị chi tiết công việc
     */
    public function detail($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return abort(404, 'Công việc không tồn tại.');
        }

        // Lấy chi tiết công việc
        $taskDetail = $this->getTaskDetails($task);

        // Nếu là công việc loại CONTRACT, cần hiển thị cả cây công việc
        if ($task->type == 'CONTRACT') {
            $taskDetail['service_tasks'] = $this->getServiceTasks($task);
        }
        // Nếu là công việc loại SERVICE, chỉ hiển thị các công việc con
        else if ($task->type == 'SERVICE') {
            $taskDetail['sub_tasks'] = $this->getSubTasks($task);
        }

        // Lấy các thông tin phụ trợ
        $priorities = TaskConfig::select('id', 'name', 'color')
            ->where('type', 0)
            ->where('is_active', 1)
            ->orderBy('sort')
            ->get()
            ->toArray();

        $statuses = TaskConfig::select('id', 'name', 'color')
            ->where('type', 1)
            ->where('is_active', 1)
            ->orderBy('sort')
            ->get()
            ->toArray();

        $users = User::select('id', 'name')
            ->where('is_active', 1)
            ->get()
            ->toArray();

        $activity_logs = ActivityLogs::where('action', TASK_ENUM_LOG)
            ->where('fk_key', 'tbl_tasks|id')
            ->where('fk_value', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log, $index) {
                return [
                    'index' => $index,
                    'id' => $log->id,
                    'action' => $log->action,
                    'ip' => $log->ip,
                    'details' => $log->details,
                    'user' => [
                        'id' => $log->user->id ?? 0,
                        'name' => $log->user->name ?? '',
                    ],
                    'created_at' => $log->created_at,
                ];
            });

        $contracts = Contract::select('id', 'name')
            ->where('is_active', 1)
            ->get()
            ->toArray();

        $services = Service::select('id', 'name')
            ->where('is_active', 1)
            ->get()
            ->toArray();

        $attachments = Upload::select('id', 'name', 'type', 'size', 'driver_id', 'extension', 'created_at')
            ->where('action', MEDIA_DRIVER_UPLOAD)
            ->where('fk_key', 'tbl_tasks|id')
            ->where('fk_value', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        // Lấy phân bổ công việc - các nhân viên đã đóng góp
        $taskContributions = TaskContribution::where('task_id', $id)
            ->where('is_active', 1)
            ->with('user')
            ->orderBy('date_completed', 'desc')
            ->get()
            ->map(function ($contribution) {
                return [
                    'id' => $contribution->id,
                    'user' => [
                        'id' => $contribution->user->id ?? 0,
                        'name' => $contribution->user->name ?? '',
                    ],
                    'quantity' => $contribution->quantity,
                    'date_completed' => $contribution->date_completed,
                    'note' => $contribution->note,
                    'created_at' => $contribution->created_at,
                ];
            });

        // Lấy danh sách các công việc có thể là công việc con
        function getAllParentTaskIds($taskId)
        {
            $parentTask = Task::select('parent_id')->where('id', $taskId)->first();
            if ($parentTask && $parentTask->parent_id) {
                return array_merge([$parentTask->parent_id], getAllParentTaskIds($parentTask->parent_id));
            }
            return [];
        }

        $parentIds = getAllParentTaskIds($id);
        $excludedIds = array_merge([$id], $parentIds);

        // Nếu là task loại CONTRACT, chỉ hiện các task không có parent
        // Nếu là task loại SERVICE, chỉ hiện các task parent là parent của nó
        // Nếu là task loại SUB, không hiện
        $tasks = [];
        if ($task->type == 'CONTRACT') {
            $tasks = Task::select('id', 'name')
                ->whereNotIn('id', $excludedIds)
                ->whereNull('parent_id')
                ->where('is_active', 1)
                ->get()
                ->toArray();
        } else if ($task->type == 'SERVICE') {
            $tasks = Task::select('id', 'name')
                ->whereNotIn('id', $excludedIds)
                ->where('parent_id', $task->parent_id)
                ->where('is_active', 1)
                ->get()
                ->toArray();
        }

        return view("dashboard.account.task.detail", [
            'details' => $taskDetail,
            'contributions' => $taskContributions,
            'priorities' => $priorities,
            'statuses' => $statuses,
            'users' => $users,
            'activity_logs' => $activity_logs,
            'contracts' => $contracts,
            'services' => $services,
            'tasks' => $tasks,
            'attachments' => $attachments
        ]);
    }

    /**
     * Lấy thông tin chi tiết của một công việc
     */
    private function getTaskDetails($task)
    {
        return [
            'id' => $task->id,
            'name' => $task->name,
            'priority' => [
                'id' => $task->priority->id ?? 0,
                'name' => $task->priority->name ?? '',
                'color' => $task->priority->color ?? '',
            ],
            'assign' => [
                'id' => $task->assign->id ?? 0,
                'name' => $task->assign->name ?? '',
            ],
            'status' => [
                'id' => $task->status->id ?? 0,
                'name' => $task->status->name ?? '',
                'color' => $task->status->color ?? '',
            ],
            'description' => $task->description,
            'note' => $task->note,
            'parent' => [
                'id' => $task->parent->id ?? 0,
                'name' => $task->parent->name ?? '',
            ],
            'contract' => [
                'id' => $task->contract->id ?? 0,
                'name' => $task->contract->name ?? '',
            ],
            'service' => [
                'id' => $task->service->id ?? 0,
                'name' => $task->service->name ?? '',
            ],
            'create_by' => [
                'id' => $task->createdBy->id ?? 0,
                'name' => $task->createdBy->name ?? '',
            ],
            'service_other' => $task->service_other,
            'bonus_amount' => $task->bonus_amount,
            'deduction_amount' => $task->deduction_amount,
            'type' => $task->type,
            'childs' => $task->childs->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'status' => [
                        'id' => $child->status->id ?? 0,
                        'name' => $child->status->name ?? '',
                        'color' => $child->status->color ?? '',
                    ],
                    'assign' => [
                        'id' => $child->assign->id ?? 0,
                        'name' => $child->assign->name ?? '',
                    ],
                    'priority' => [
                        'id' => $child->priority->id ?? 0,
                        'name' => $child->priority->name ?? '',
                        'color' => $child->priority->color ?? '',
                    ],
                    'due_date' => $child->due_date,
                    'qty_request' => $child->qty_request,
                    'qty_completed' => $child->qty_completed,
                    'progress' => $child->progress,
                    'type' => $child->type,
                ];
            }),
            'comments' => $task->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'message' => $comment->message,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ],
                    'created_at' => $comment->created_at
                ];
            }),
            'due_date' => $task->due_date,
            'progress' => $task->progress,
            'qty_request' => $task->qty_request,
            'qty_completed' => $task->qty_completed,
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at,
            'contributors' => $this->getTaskContributors($task->id),
        ];
    }

    /**
     * Lấy danh sách công việc dịch vụ (cho công việc loại CONTRACT)
     */
    private function getServiceTasks($contractTask)
    {
        $serviceTasks = Task::where('parent_id', $contractTask->id)
            ->where('type', 'SERVICE')
            ->where('is_active', 1)
            ->with(['status', 'priority', 'assign', 'childs' => function ($query) {
                $query->where('is_active', 1)->with(['status', 'priority', 'assign']);
            }])
            ->get()
            ->map(function ($serviceTask) {
                return [
                    'id' => $serviceTask->id,
                    'name' => $serviceTask->name,
                    'status' => [
                        'id' => $serviceTask->status->id ?? 0,
                        'name' => $serviceTask->status->name ?? '',
                        'color' => $serviceTask->status->color ?? '',
                    ],
                    'priority' => [
                        'id' => $serviceTask->priority->id ?? 0,
                        'name' => $serviceTask->priority->name ?? '',
                        'color' => $serviceTask->priority->color ?? '',
                    ],
                    'assign' => [
                        'id' => $serviceTask->assign->id ?? 0,
                        'name' => $serviceTask->assign->name ?? '',
                    ],
                    'due_date' => $serviceTask->due_date,
                    'qty_request' => $serviceTask->qty_request,
                    'qty_completed' => $serviceTask->qty_completed,
                    'progress' => $serviceTask->progress,
                    'sub_tasks' => $serviceTask->childs->map(function ($subTask) {
                        return [
                            'id' => $subTask->id,
                            'name' => $subTask->name,
                            'status' => [
                                'id' => $subTask->status->id ?? 0,
                                'name' => $subTask->status->name ?? '',
                                'color' => $subTask->status->color ?? '',
                            ],
                            'priority' => [
                                'id' => $subTask->priority->id ?? 0,
                                'name' => $subTask->priority->name ?? '',
                                'color' => $subTask->priority->color ?? '',
                            ],
                            'assign' => [
                                'id' => $subTask->assign->id ?? 0,
                                'name' => $subTask->assign->name ?? '',
                            ],
                            'due_date' => $subTask->due_date,
                            'qty_request' => $subTask->qty_request,
                            'qty_completed' => $subTask->qty_completed,
                            'progress' => $subTask->progress,
                        ];
                    })
                ];
            });

        return $serviceTasks;
    }

    /**
     * Lấy danh sách công việc con (cho công việc loại SERVICE)
     */
    private function getSubTasks($serviceTask)
    {
        $subTasks = Task::where('parent_id', $serviceTask->id)
            ->where('type', 'SUB')
            ->where('is_active', 1)
            ->with(['status', 'priority', 'assign'])
            ->get()
            ->map(function ($subTask) {
                return [
                    'id' => $subTask->id,
                    'name' => $subTask->name,
                    'status' => [
                        'id' => $subTask->status->id ?? 0,
                        'name' => $subTask->status->name ?? '',
                        'color' => $subTask->status->color ?? '',
                    ],
                    'priority' => [
                        'id' => $subTask->priority->id ?? 0,
                        'name' => $subTask->priority->name ?? '',
                        'color' => $subTask->priority->color ?? '',
                    ],
                    'assign' => [
                        'id' => $subTask->assign->id ?? 0,
                        'name' => $subTask->assign->name ?? '',
                    ],
                    'due_date' => $subTask->due_date,
                    'qty_request' => $subTask->qty_request,
                    'qty_completed' => $subTask->qty_completed,
                    'progress' => $subTask->progress,
                ];
            });

        return $subTasks;
    }

    /**
     * Lấy danh sách người đã đóng góp vào công việc
     */
    private function getTaskContributors($taskId)
    {
        $contributions = TaskContribution::where('task_id', $taskId)
            ->where('is_active', 1)
            ->with('user')
            ->orderBy('date_completed', 'desc')
            ->get();

        // Nhóm đóng góp theo người dùng
        $contributors = [];
        foreach ($contributions as $contribution) {
            $userId = $contribution->user_id;

            if (!isset($contributors[$userId])) {
                $contributors[$userId] = [
                    'user_id' => $userId,
                    'user_name' => $contribution->user->name ?? 'Unknown',
                    'total_quantity' => 0,
                    'contributions' => []
                ];
            }

            $contributors[$userId]['total_quantity'] += $contribution->quantity;
            $contributors[$userId]['contributions'][] = [
                'id' => $contribution->id,
                'quantity' => $contribution->quantity,
                'date_completed' => $contribution->date_completed,
                'note' => $contribution->note,
                'created_at' => $contribution->created_at
            ];
        }

        // Chuyển từ mảng kết hợp sang mảng tuần tự
        return array_values($contributors);
    }

    /**
     * Thêm đóng góp công việc (báo cáo hoàn thành)
     */
    public function addContribution(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'task_id' => 'required|exists:tbl_tasks,id',
                'quantity' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
            ],
            [
                'required' => ':attribute không được để trống',
                'integer' => ':attribute phải là số nguyên',
                'min' => ':attribute phải lớn hơn hoặc bằng :min',
                'max' => ':attribute không được vượt quá :max ký tự',
            ],
            [
                'task_id' => 'Mã công việc',
                'quantity' => 'Số lượng',
                'note' => 'Ghi chú',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();

            $task = Task::find($request->task_id);
            if (!$task) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Công việc không tồn tại.',
                ]);
            }

            // Kiểm tra xem task có task con hay không
            $hasChildTasks = Task::where('parent_id', $task->id)
                ->where('is_active', 1)
                ->exists();

            if ($hasChildTasks) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không thể báo cáo công việc cho task có task con. Vui lòng báo cáo ở các task con.'
                ]);
            }

            // Kiểm tra xem đã hoàn thành hết hay chưa
            $totalCompletedBefore = $task->qty_completed;
            $newTotal = $totalCompletedBefore + $request->quantity;

            if ($newTotal > $task->qty_request) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Số lượng đã hoàn thành vượt quá số lượng yêu cầu.',
                ]);
            }

            // Lưu đóng góp
            $contribution = TaskContribution::create([
                'task_id' => $request->task_id,
                'user_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                'quantity' => $request->quantity,
                'date_completed' => now(),
                'note' => $request->note,
            ]);

            // Cập nhật số lượng đã hoàn thành của công việc
            $task->recalculateCompletedQuantity();
            
            // Tự động cập nhật trạng thái nếu hoàn thành 100%
            if ($task->progress == 100) {
                // Cần biết ID của trạng thái "Hoàn thành"
                $completedStatusId = 4; // Giả sử 4 là ID của trạng thái Hoàn thành
                $task->update(['status_id' => $completedStatusId]);
                
                // Cập nhật task cha nếu tất cả task con đã hoàn thành
                $this->updateParentTaskStatus($task->parent_id);
            }

            // Lưu log
            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã báo cáo hoàn thành ' . $request->quantity . ' đơn vị công việc',
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $request->task_id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Báo cáo hoàn thành công việc thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi báo cáo hoàn thành công việc: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Xóa đóng góp công việc
     */
    public function deleteContribution(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'contribution_id' => 'required|exists:tbl_task_contributions,id',
            ],
            [
                'required' => ':attribute không được để trống',
                'exists' => ':attribute không tồn tại',
            ],
            [
                'contribution_id' => 'Mã đóng góp',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();

            $contribution = TaskContribution::find($request->contribution_id);

            // Chỉ admin hoặc người tạo đóng góp mới được xóa
            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $isAdmin = Session::get(ACCOUNT_CURRENT_SESSION)['is_admin'] ?? false;

            if (!$isAdmin && $contribution->user_id != $currentUserId) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền xóa đóng góp này.',
                ]);
            }

            // Lưu lại thông tin task và số lượng đóng góp trước khi xóa
            $taskId = $contribution->task_id;
            $deletedQuantity = $contribution->quantity;

            // Xóa đóng góp (thay vì cập nhật is_active)
            $contribution->delete();

            // Cập nhật lại số lượng đã hoàn thành và trạng thái của công việc
            $task = Task::find($taskId);
            $task->recalculateCompletedQuantity();
            
            // Kiểm tra và cập nhật trạng thái task
            $completedStatusId = 4; // ID trạng thái "Hoàn thành"
            $inProgressStatusId = 3; // ID trạng thái "Đang thực hiện"
            
            if ($task->qty_completed < $task->qty_request && $task->status_id == $completedStatusId) {
                // Nếu đã hoàn thành nhưng giờ không đủ số lượng, chuyển về trạng thái đang thực hiện
                $task->update(['status_id' => $inProgressStatusId]);
                
                // Cập nhật lại trạng thái của task cha nếu cần
                if ($task->parent_id) {
                    $this->updateParentTaskStatus($task->parent_id);
                }
            }

            // Lưu log
            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã xóa báo cáo hoàn thành ' . $deletedQuantity . ' đơn vị công việc',
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $taskId,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa báo cáo hoàn thành công việc.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xóa báo cáo: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Chức năng cho phép nhân viên tự nhận việc
     */
    public function claimTask(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'task_id' => 'required|exists:tbl_tasks,id',
            ],
            [
                'required' => ':attribute không được để trống',
                'exists' => ':attribute không tồn tại',
            ],
            [
                'task_id' => 'Mã công việc',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();

            $task = Task::find($request->task_id);

            // Kiểm tra xem công việc có phải loại SERVICE hoặc SUB không
            if (!in_array($task->type, ['SERVICE', 'SUB'])) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Chỉ có thể nhận công việc loại dịch vụ hoặc công việc con.',
                ]);
            }

            // Kiểm tra trạng thái công việc
            $allowedStatuses = [1, 2]; // Giả sử 1 là "Mới", 2 là "Đang chờ"
            if (!in_array($task->status_id, $allowedStatuses)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Chỉ có thể nhận công việc ở trạng thái chưa bắt đầu.',
                ]);
            }

            // Cập nhật trạng thái và người thực hiện
            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $inProgressStatusId = 3; // Giả sử 3 là "Đang thực hiện"

            // Danh sách các ID task đã được nhận
            $claimedTaskIds = [$task->id];
            
            // Nhận task hiện tại
            $task->update([
                'status_id' => $inProgressStatusId,
                'assign_id' => $currentUserId
            ]);

            // Nếu task là task cha, tự động nhận tất cả task con
            if ($task->type == 'SERVICE') {
                $childTasks = Task::where('parent_id', $task->id)
                    ->where('is_active', 1)
                    ->whereIn('status_id', $allowedStatuses)
                    ->get();
                
                foreach ($childTasks as $childTask) {
                    $childTask->update([
                        'status_id' => $inProgressStatusId,
                        'assign_id' => $currentUserId
                    ]);
                    
                    $claimedTaskIds[] = $childTask->id;
                }
            }
            
            // Lưu log
            foreach ($claimedTaskIds as $claimedTaskId) {
                LogService::saveLog([
                    'action' => TASK_ENUM_LOG,
                    'ip' => $request->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã nhận công việc này',
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $claimedTaskId,
                ]);
            }

            DB::commit();

            $messageDetail = count($claimedTaskIds) > 1 
                ? ' và ' . (count($claimedTaskIds) - 1) . ' công việc con' 
                : '';
            
            return response()->json([
                'status' => 200,
                'message' => 'Bạn đã nhận công việc' . $messageDetail . ' thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi nhận công việc: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Phương thức mới để kiểm tra và cập nhật trạng thái task cha
     */
    private function updateParentTaskStatus($parentId)
    {
        if (!$parentId) {
            return; // Không có task cha
        }
        
        $parentTask = Task::find($parentId);
        if (!$parentTask) {
            return; // Task cha không tồn tại
        }
        
        // Lấy tất cả task con đang hoạt động
        $childTasks = Task::where('parent_id', $parentId)
            ->where('is_active', 1)
            ->get();
        
        if ($childTasks->isEmpty()) {
            return; // Không có task con
        }
        
        // Kiểm tra xem tất cả task con đã hoàn thành chưa
        $completedStatusId = 4; // Giả sử 4 là ID của trạng thái "Hoàn thành"
        $allCompleted = $childTasks->every(function ($childTask) use ($completedStatusId) {
            return $childTask->status_id == $completedStatusId;
        });
        
        if ($allCompleted) {
            // Nếu tất cả task con đã hoàn thành, cập nhật task cha
            $parentTask->update([
                'status_id' => $completedStatusId,
                'qty_completed' => $parentTask->qty_request, // Đánh dấu là đã hoàn thành 100%
                'progress' => 100
            ]);
            
            // Nếu task cha có task cha nữa, tiếp tục cập nhật lên trên
            if ($parentTask->parent_id) {
                $this->updateParentTaskStatus($parentTask->parent_id);
            }
        } else {
            // Nếu có ít nhất một task con chưa hoàn thành
            $inProgressStatusId = 3; // Giả sử 3 là ID của trạng thái "Đang thực hiện"
            
            // Tính phần trăm hoàn thành dựa trên tỷ lệ task con đã hoàn thành
            $completedChildCount = $childTasks->filter(function ($childTask) use ($completedStatusId) {
                return $childTask->status_id == $completedStatusId;
            })->count();
            
            $progressPercentage = round(($completedChildCount / $childTasks->count()) * 100);
            
            // Cập nhật task cha
            $parentTask->update([
                'status_id' => $inProgressStatusId,
                'progress' => $progressPercentage,
                'qty_completed' => round(($progressPercentage / 100) * $parentTask->qty_request)
            ]);
            
            // Nếu task cha có task cha nữa, tiếp tục cập nhật lên trên
            if ($parentTask->parent_id) {
                $this->updateParentTaskStatus($parentTask->parent_id);
            }
        }
    }

    /**
     * Lấy danh sách công việc có thể nhận (cho API)
     */
    public function getAvailableTasks(Request $request)
    {
        try {
            // Lấy các công việc loại SERVICE hoặc SUB đang ở trạng thái có thể nhận
            $availableStatuses = [1, 2]; // Giả sử 1 là "Mới", 2 là "Đang chờ"

            $query = Task::whereIn('type', ['SERVICE', 'SUB'])
                ->whereIn('status_id', $availableStatuses)
                ->where('is_active', 1);

            // Lọc theo contract_id nếu có
            if ($request->has('contract_id')) {
                $query->where('contract_id', $request->contract_id);
            }

            // Lọc theo loại task
            if ($request->has('task_type')) {
                $query->where('type', $request->task_type);
            }

            // Phân trang
            $perPage = $request->input('per_page', 10);
            $tasks = $query->with(['status', 'priority', 'contract', 'parent'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $formattedTasks = $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'type' => $task->type,
                    'status' => [
                        'id' => $task->status->id ?? 0,
                        'name' => $task->status->name ?? '',
                        'color' => $task->status->color ?? '',
                    ],
                    'priority' => [
                        'id' => $task->priority->id ?? 0,
                        'name' => $task->priority->name ?? '',
                        'color' => $task->priority->color ?? '',
                    ],
                    'contract' => [
                        'id' => $task->contract->id ?? 0,
                        'name' => $task->contract->name ?? '',
                    ],
                    'parent' => [
                        'id' => $task->parent->id ?? 0,
                        'name' => $task->parent->name ?? '',
                    ],
                    'due_date' => $task->due_date,
                    'qty_request' => $task->qty_request,
                    'qty_completed' => $task->qty_completed,
                    'created_at' => $task->created_at,
                ];
            });

            return response()->json([
                'status' => 200,
                'data' => [
                    'tasks' => $formattedTasks,
                    'pagination' => [
                        'total' => $tasks->total(),
                        'per_page' => $tasks->perPage(),
                        'current_page' => $tasks->currentPage(),
                        'last_page' => $tasks->lastPage(),
                    ]
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách công việc: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Lấy thống kê đóng góp của nhân viên
     */
    public function getUserContributions(Request $request)
    {
        try {
            $userId = $request->input('user_id', Session::get(ACCOUNT_CURRENT_SESSION)['id']);
            $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));

            // Lấy tất cả đóng góp trong khoảng thời gian
            $contributions = TaskContribution::where('user_id', $userId)
                ->where('is_active', 1)
                ->whereBetween('date_completed', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->with(['task' => function ($query) {
                    $query->with(['status', 'contract']);
                }])
                ->orderBy('date_completed', 'desc')
                ->get();

            // Tính toán tổng số lượng và hoa hồng
            $totalQuantity = $contributions->sum('quantity');
            $totalBonus = 0;

            // Nhóm theo công việc
            $taskContributions = [];
            foreach ($contributions as $contribution) {
                $taskId = $contribution->task_id;

                if (!isset($taskContributions[$taskId])) {
                    $task = $contribution->task;
                    $taskContributions[$taskId] = [
                        'task_id' => $taskId,
                        'task_name' => $task->name ?? 'Unknown',
                        'task_type' => $task->type ?? '',
                        'contract_id' => $task->contract_id,
                        'contract_name' => $task->contract->name ?? '',
                        'status' => [
                            'id' => $task->status->id ?? 0,
                            'name' => $task->status->name ?? '',
                            'color' => $task->status->color ?? '',
                        ],
                        'total_quantity' => 0,
                        'bonus_amount' => $task->bonus_amount ?? 0,
                        'contributions' => []
                    ];
                }

                $taskContributions[$taskId]['total_quantity'] += $contribution->quantity;

                // Tính hoa hồng (nếu task đã hoàn thành)
                $completedStatusId = 4; // Giả sử 4 là ID của trạng thái Hoàn thành
                if (isset($taskContributions[$taskId]['status']['id']) && $taskContributions[$taskId]['status']['id'] == $completedStatusId) {
                    $taskBonus = $taskContributions[$taskId]['bonus_amount'];
                    $taskTotalQuantity = $contribution->task->qty_request ?: 1;

                    // Hoa hồng = (Tổng tiền hoa hồng / Tổng số lượng) * Số lượng đã làm
                    $contributionBonus = ($taskBonus / $taskTotalQuantity) * $contribution->quantity;
                    $totalBonus += $contributionBonus;
                }

                $taskContributions[$taskId]['contributions'][] = [
                    'id' => $contribution->id,
                    'quantity' => $contribution->quantity,
                    'date_completed' => $contribution->date_completed,
                    'note' => $contribution->note,
                    'created_at' => $contribution->created_at
                ];
            }

            // Chuyển từ mảng kết hợp sang mảng tuần tự
            $taskContributions = array_values($taskContributions);

            return response()->json([
                'status' => 200,
                'data' => [
                    'user_id' => $userId,
                    'total_quantity' => $totalQuantity,
                    'total_bonus' => $totalBonus,
                    'task_contributions' => $taskContributions
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy thống kê đóng góp: ' . $e->getMessage(),
            ]);
        }
    }
}
