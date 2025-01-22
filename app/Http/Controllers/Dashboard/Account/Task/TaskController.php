<?php

namespace App\Http\Controllers\Dashboard\Account\Task;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Contract;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskConfig;
use App\Models\Upload;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
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
            ->myTask((int)$request['filter']['my_task'] ?? 0)
            ->levelTask($request['filter']['level_task'] ?? 'max')
            ->priorityTask($request['filter']['priority_task'] ?? '')
            ->statusTask($request['filter']['status_task'] ?? '')
            ->search($request['filter']['search'] ?? '');

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

    public function detail($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return abort(404, 'Công việc không tồn tại.');
        }

        $result = [
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
                    'start_date' => $child->start_date,
                    'due_date' => $child->due_date,
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
            'start_date' => $task->start_date,
            'due_date' => $task->due_date,
            'progress' => $task->progress,
            'estimate_time' => $task->estimate_time,
            'spend_time' => $task->spend_time,
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at,
        ];

        $priorities = TaskConfig::select('id', 'name', 'color')->where('type', 0)->where('is_active', 1)->orderBy('sort')->get()->toArray();
        $statuses = TaskConfig::select('id', 'name', 'color')->where('type', 1)->where('is_active', 1)->orderBy('sort')->get()->toArray();
        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $activity_logs = ActivityLogs::where('action', TASK_ENUM_LOG)->where('fk_key', 'tbl_tasks|id')->where('fk_value', $id)->orderBy('created_at', 'desc')->get()->map(function ($log, $index) {
            return [
                'index' => $index,
                'id' => $log->id,
                'action' => $log->action,
                'ip' => $log->ip,
                'details' => $log->details,
                'user' => [
                    'id' => $log->user->id,
                    'name' => $log->user->name,
                ],
                'created_at' => $log->created_at,
            ];
        });
        $contracts = Contract::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $attachments = Upload::select('id', 'name', 'type', 'size', 'driver_id', 'extension', 'created_at')->where('action', MEDIA_DRIVER_UPLOAD)->where('fk_key', 'tbl_tasks|id')->where('fk_value', $id)->orderBy('created_at', 'desc')->get()->toArray();
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
        $tasks = Task::select('id', 'name')->whereNotIn('id', $excludedIds)->whereNull('parent_id')->where('is_active', 1)->get()->toArray();

        return view("dashboard.account.task.detail", ['details' => $result, 'priorities' => $priorities, 'statuses' => $statuses, 'users' => $users, 'activity_logs' => $activity_logs, 'contracts' => $contracts, 'services' => $services, 'tasks' => $tasks, 'attachments' => $attachments]);
    } 

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

            $data = $request->only(['name', 'description', 'note', 'contract_id', 'progress', 'service_id', 'priority_id', 'status_id', 'issue_id', 'estimate_time', 'spend_time', 'due_date', 'assign_id', 'sub_name', 'start_date', 'deduction_amount', 'bonus_amount', 'service_other']);
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

    public function removeAttachment(Request $request) {
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

    public function uploadFileTask(Request $request) {
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

    public function configPost(Request $request) {
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

    public function configChangeStatus(Request $request) {
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
                'details' => "Vừa cập nhật lại trạng thái của #" .$request['id'],
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
}
