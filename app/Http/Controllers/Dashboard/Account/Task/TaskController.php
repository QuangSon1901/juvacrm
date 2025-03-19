<?php

namespace App\Http\Controllers\Dashboard\Account\Task;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Contract;
use App\Models\ContractService;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskConfig;
use App\Models\TaskContribution;
use App\Models\TaskFeedback;
use App\Models\TaskFeedbackItem;
use App\Models\TaskMission;
use App\Models\TaskMissionAssignment;
use App\Models\TaskMissionReport;
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

        $task_remake = Task::where('status_id', 7)->count();
        $task_overdue = Task::where('status_id', 6)->count();

        return view("dashboard.account.task.index", ['priorities' => $priorities, 'statuses' => $statuses, 'task_remake' => $task_remake, 'task_overdue' => $task_overdue]);
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
            ->taskNoCompleted($request['filter']['task_no_completed'] ?? 0)
            ->levelTask($request['filter']['level_task'] ?? 'CONTRACT')
            ->priorityTask($request['filter']['priority_task'] ?? '')
            ->statusTask($request['filter']['status_task'] ?? '')
            ->search($request['filter']['search'] ?? '');

        $query->where('is_active', 1);

        // Thêm các quan hệ cần thiết để tránh N+1 query
        $query->with(['priority', 'status', 'assign', 'contract', 'parent', 'childs']);

        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        // Tính toán tiến độ tổng thể cho các task có task con
        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            // Tính toán progress dựa trên task con nếu có
            $progress = $item->progress;
            $totalChilds = $item->childs->count();

            if ($totalChilds > 0) {
                $totalChildProgress = $item->childs->sum('progress');
                $progress = round($totalChildProgress / $totalChilds);
            }

            // Tính toán thời gian còn lại và trạng thái deadline
            $deadlineStatus = null;
            $timeRemaining = null;

            if ($item->due_date) {
                $dueDate = \Carbon\Carbon::parse($item->due_date);
                $now = \Carbon\Carbon::now();

                if ($dueDate->lt($now) && $item->progress < 100) {
                    $deadlineStatus = 'overdue';
                    $timeRemaining = $now->diffForHumans($dueDate);
                } else if ($dueDate->diffInDays($now) <= 3 && $item->progress < 100) {
                    $deadlineStatus = 'upcoming';
                    $timeRemaining = $dueDate->diffForHumans($now);
                }
            }

            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->type,
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
                'parent' => $item->parent ? [
                    'id' => $item->parent->id,
                    'name' => $item->parent->name
                ] : null,
                'contract' => $item->contract ? [
                    'id' => $item->contract->id,
                    'name' => $item->contract->name
                ] : null,
                'start_date' => $item->start_date,
                'due_date' => $item->due_date,
                'deadline_status' => $deadlineStatus,
                'time_remaining' => $timeRemaining,
                'progress' => $progress,
                'estimate_time' => $item->estimate_time,
                'spend_time' => $item->spend_time,
                'qty_request' => $item->qty_request,
                'qty_completed' => $item->qty_completed,
                'sample_image_id' => $item->sample_image_id,
                'result_image_id' => $item->result_image_id,
                'has_children' => $totalChilds > 0,
                'children_count' => $totalChilds,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.task.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function getTaskByIDs(Request $request) {
        $ids = explode(',', $request['ids']); // Chuyển chuỗi thành mảng
        $query = Task::whereIn('parent_id', $ids)->get(); // Lấy danh sách tasks

        return response()->json([
            'status' => 200,
            'data' => $query->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => $item->type,
                    'status_id' => $item->status_id,
                    'parent_id' => $item->parent_id,
                    'qty_completed' => $item->qty_completed,
                    'qty_request' => $item->qty_request,
                    'sample_image_id' => $item->sample_image_id,
                    'result_image_id' => $item->result_image_id,
                ];
            })
        ]);
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
            'sample_image_id' => $task->sample_image_id,
            'result_image_id' => $task->result_image_id,
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
                    'sample_image_id' => $child->sample_image_id,
                    'result_image_id' => $child->result_image_id,
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
                    'sample_image_id' => $serviceTask->sample_image_id,
                    'result_image_id' => $serviceTask->result_image_id,
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
                            'sample_image_id' => $subTask->sample_image_id,
                            'result_image_id' => $subTask->result_image_id,
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
                    'sample_image_id' => $subTask->sample_image_id,
                    'result_image_id' => $subTask->result_image_id,
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
            $isAdmin = true;

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
                'mission_ids' => 'required|array',
                'mission_ids.*' => 'exists:tbl_task_missions,id',
            ],
            [
                'required' => ':attribute không được để trống',
                'exists' => ':attribute không tồn tại',
                'array' => ':attribute phải là mảng',
            ],
            [
                'task_id' => 'Mã công việc',
                'mission_ids' => 'Danh sách nhiệm vụ',
                'mission_ids.*' => 'Nhiệm vụ',
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
            $allowedStatuses = [1, 2, 7]; // 1: "Mới", 2: "Đang chờ", 7: "Cần chỉnh sửa"
            if (!in_array($task->status_id, $allowedStatuses)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Chỉ có thể nhận công việc ở trạng thái chưa bắt đầu hoặc cần chỉnh sửa.',
                ]);
            }

            // Cập nhật trạng thái task
            $inProgressStatusId = 3; // 3: "Đang thực hiện"
            $task->update([
                'status_id' => $inProgressStatusId,
            ]);

            $hasChild = $task->childs->count();

            // Nếu task là task cha, thêm các task con vào danh sách
            if ($task->type == 'SERVICE' && $hasChild != 0) {
                $childTasks = Task::where('parent_id', $task->id)
                    ->where('is_active', 1)
                    ->pluck('id')
                    ->toArray();

                $tasksToAssign = $childTasks;
            } else {
                $tasksToAssign = [$task->id];
            }

            // Lấy ID người dùng hiện tại
            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];

            // Tạo assignment cho mỗi task và mỗi nhiệm vụ
            foreach ($tasksToAssign as $taskId) {
                $taskObj = $taskId == $task->id ? $task : Task::find($taskId);

                // Cập nhật trạng thái của task con nếu cần
                if ($taskId != $task->id) {
                    $taskObj->update(['status_id' => $inProgressStatusId]);
                }

                foreach ($request->mission_ids as $missionId) {
                    // Kiểm tra xem đã có assignment nào chưa
                    $existingAssignment = TaskMissionAssignment::where('task_id', $taskId)
                        ->where('mission_id', $missionId)
                        ->where('user_id', $currentUserId)
                        ->first();

                    if (!$existingAssignment) {
                        TaskMissionAssignment::create([
                            'task_id' => $taskId,
                            'mission_id' => $missionId,
                            'user_id' => $currentUserId,
                            'quantity_required' => $taskObj->qty_request,
                            'quantity_completed' => 0,
                            'status' => 'in_progress'
                        ]);
                    }
                }

                // Log
                LogService::saveLog([
                    'action' => TASK_ENUM_LOG,
                    'ip' => $request->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã nhận công việc và được gán các nhiệm vụ',
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $taskId,
                ]);
            }

            // Cập nhật task cha nếu cần thiết
            if ($task->parent_id) {
                $this->updateParentTaskStatus($task->parent_id);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Bạn đã nhận công việc và ' . (count($tasksToAssign) - 1) . ' công việc con thành công.',
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
     * Phương thức cập nhật trạng thái task cha
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
        $completedStatusId = 4; // ID của trạng thái "Hoàn thành"
        $revisionStatusId = 7; // ID của trạng thái "Cần chỉnh sửa"

        $allCompleted = true;
        $anyNeedsRevision = false;

        foreach ($childTasks as $childTask) {
            if ($childTask->status_id != $completedStatusId) {
                $allCompleted = false;
            }

            if ($childTask->status_id == $revisionStatusId) {
                $anyNeedsRevision = true;
            }
        }
        // Xác định trạng thái mới cho task cha
        $newStatus = 3; // Mặc định là "Đang thực hiện"

        if ($allCompleted) {
            $newStatus = 4; // "Hoàn thành"
        }

        // Tính progress dựa trên task con
        $totalTasks = $childTasks->count();
        $completedTasks = 0;
        $totalProgress = 0;

        foreach ($childTasks as $childTask) {
            $totalProgress += $childTask->progress;
            if ($childTask->status_id == $completedStatusId) {
                $completedTasks++;
            }
        }

        $avgProgress = $totalTasks > 0 ? round($totalProgress / $totalTasks) : 0;

        // Cập nhật tiến độ và số lượng hoàn thành của task cha
        $qtyCompleted = $parentTask->qty_request * ($avgProgress / 100);

        // Cập nhật task cha
        $parentTask->update([
            'status_id' => $newStatus,
            'progress' => $avgProgress,
            'qty_completed' => round($qtyCompleted)
        ]);

        // Nếu task cha còn có task cha nữa, tiếp tục cập nhật lên trên
        if ($parentTask->parent_id) {
            $this->updateParentTaskStatus($parentTask->parent_id);
        }
    }

    /**
 * Cập nhật trạng thái một task cụ thể
 * 
 * @param int $taskId ID của task cần cập nhật
 * @param int $statusId ID của trạng thái mới
 * @return void
 */
private function updateTaskStatus($taskId, $statusId)
{
    $task = Task::find($taskId);
    if (!$task) {
        return;
    }
    
    // Cập nhật trạng thái task
    $task->update(['status_id' => $statusId]);
    
    // Nếu task có task con, cập nhật trạng thái các task con
    $childTasks = Task::where('parent_id', $taskId)
        ->where('is_active', 1)
        ->get();
        
    foreach ($childTasks as $childTask) {
        // Nếu status là "Cần chỉnh sửa", áp dụng cho tất cả task con
        if ($statusId == 7) {
            $childTask->update(['status_id' => $statusId]);
            
            // Đệ quy cập nhật trạng thái task con
            $this->updateTaskStatus($childTask->id, $statusId);
        }
        // Nếu status là "Hoàn thành", kiểm tra trước khi áp dụng
        else if ($statusId == 4) {
            // Nếu task con đã hoàn thành công việc (progress = 100%), cập nhật trạng thái
            if ($childTask->progress >= 100) {
                $childTask->update(['status_id' => $statusId]);
            } else {
                // Nếu chưa hoàn thành, đặt về trạng thái đang thực hiện
                $childTask->update(['status_id' => 3]); // 3 = Đang thực hiện
            }
            
            // Đệ quy cập nhật trạng thái task con
            $this->updateTaskStatus($childTask->id, $statusId);
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
                    'sample_image_id' => $task->sample_image_id,
                    'result_image_id' => $task->result_image_id,
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

    /**
     * Đồng bộ lại trạng thái và số lượng của các task theo hợp đồng
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function synchronizeContractTasks(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'contract_id' => 'required|exists:tbl_contracts,id',
            ],
            [
                'required' => ':attribute không được để trống',
                'exists' => ':attribute không tồn tại',
            ],
            [
                'contract_id' => 'Mã hợp đồng',
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

            $contractId = $request->contract_id;
            $contract = Contract::with(['services' => function ($query) {
                $query->where('is_active', 1);
            }])->findOrFail($contractId);

            // Lấy tất cả các task thuộc hợp đồng này theo cấu trúc phân cấp
            // 1. Task hợp đồng (CONTRACT)
            $contractTask = Task::where('contract_id', $contractId)
                ->where('type', 'CONTRACT')
                ->where('is_active', 1)
                ->first();

            if (!$contractTask) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy công việc hợp đồng'
                ]);
            }

            // 2. Lấy danh sách services hiện tại của hợp đồng
            $currentServices = $contract->services;
            $activeServiceIds = $currentServices->pluck('id')->toArray();

            // 3. Lấy tất cả task của hợp đồng và nhóm theo contract_service_id
            $allTasks = Task::where('contract_id', $contractId)
                ->where('is_active', 1)
                ->get();

            $tasksByServiceId = [];
            foreach ($allTasks as $task) {
                if ($task->contract_service_id) {
                    $tasksByServiceId[$task->contract_service_id][] = $task;
                }
            }

            // 4. Lấy danh sách services được đề cập trong tasks nhưng không còn active trong hợp đồng
            $tasksServiceIds = array_keys($tasksByServiceId);
            $removedServiceIds = array_diff($tasksServiceIds, $activeServiceIds);

            // 5. Vô hiệu hóa các task thuộc dịch vụ đã bị xóa
            foreach ($removedServiceIds as $serviceId) {
                if (isset($tasksByServiceId[$serviceId])) {
                    foreach ($tasksByServiceId[$serviceId] as $task) {
                        // Vô hiệu hóa task
                        $task->update(['is_active' => 0]);

                        // Vô hiệu hóa các task con
                        Task::where('parent_id', $task->id)
                            ->update(['is_active' => 0]);

                        // Log
                        LogService::saveLog([
                            'action' => 'TASK_SYNC',
                            'ip' => $request->getClientIp(),
                            'details' => "Vô hiệu hóa task #{$task->id} do dịch vụ liên quan đã bị xóa",
                            'fk_key' => 'tbl_tasks|id',
                            'fk_value' => $task->id,
                        ]);
                    }
                }
            }

            // 6. Duyệt qua các dịch vụ hợp đồng đang active và cập nhật/tạo task tương ứng
            foreach ($currentServices as $service) {
                // Bỏ qua các dịch vụ con (có parent_id)
                if ($service->parent_id) {
                    continue;
                }

                // Bỏ qua các mục giảm giá
                if ($service->type === 'discount') {
                    continue;
                }

                // Kiểm tra xem đã có task cho dịch vụ này chưa
                $existingTasks = isset($tasksByServiceId[$service->id]) ? $tasksByServiceId[$service->id] : [];
                $existingServiceTask = null;

                foreach ($existingTasks as $task) {
                    if ($task->type === 'SERVICE' && $task->is_active === 1) {
                        $existingServiceTask = $task;
                        break;
                    }
                }

                if ($existingServiceTask) {
                    // Cập nhật thông tin task dịch vụ hiện có
                    $existingServiceTask->update([
                        'name' => $service->name,
                        'sample_image_id' => $service->sample_image_id,
                        'result_image_id' => $service->result_image_id,
                        'due_date' => $contract->expiry_date,
                        // Không cập nhật số lượng cho task cha
                    ]);

                    // Log
                    LogService::saveLog([
                        'action' => 'TASK_SYNC',
                        'ip' => $request->getClientIp(),
                        'details' => "Cập nhật thông tin task dịch vụ #{$existingServiceTask->id}",
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $existingServiceTask->id,
                    ]);

                    // Lấy các dịch vụ con thuộc dịch vụ này
                    $childServices = $currentServices->where('parent_id', $service->id)->where('is_active', 1);

                    // Lấy các task con hiện có
                    $existingChildTasks = Task::where('parent_id', $existingServiceTask->id)
                        ->where('is_active', 1)
                        ->get()
                        ->keyBy('contract_service_id');

                    // Cập nhật hoặc tạo mới task cho các dịch vụ con
                    foreach ($childServices as $childService) {
                        if (isset($existingChildTasks[$childService->id])) {
                            // Tìm thấy task con hiện có cho dịch vụ con này
                            $childTask = $existingChildTasks[$childService->id];

                            // Kiểm tra xem có cần cập nhật số lượng không
                            if ($childTask->qty_request != $childService->quantity) {
                                $newQtyRequest = $childService->quantity;
                                $oldQtyRequest = $childTask->qty_request;

                                // Kiểm tra xem task đã hoàn thành chưa
                                $isCompleted = $childTask->status_id >= 4;

                                if ($newQtyRequest > $oldQtyRequest && $isCompleted) {
                                    // Tìm tất cả các task liên quan đến cùng một dịch vụ (cả task gốc và các task bổ sung)
                                    $originalTaskId = $childTask->original_task_id ?? $childTask->id;
                                    $relatedTasks = Task::where(function ($query) use ($originalTaskId) {
                                        $query->where('id', $originalTaskId)
                                            ->orWhere('original_task_id', $originalTaskId);
                                    })
                                        ->where('contract_service_id', $childService->id)
                                        ->where('is_active', 1)
                                        ->get();

                                    // Tính tổng số lượng yêu cầu của tất cả các task liên quan
                                    $totalExistingQty = 0;
                                    foreach ($relatedTasks as $relatedTask) {
                                        $totalExistingQty += $relatedTask->qty_request;
                                    }

                                    // Tính số lượng chênh lệch cần thêm
                                    $additionalQuantity = $newQtyRequest - $totalExistingQty;

                                    // Chỉ tạo task mới nếu thực sự cần thêm số lượng
                                    if ($additionalQuantity > 0) {
                                        // Đếm số task bổ sung đã có
                                        $supplementCount = $childTask->supplementaryTasks->count();

                                        // Tạo tên cho task bổ sung
                                        $supplementName = $childTask->name . " (Bổ sung " . ($supplementCount + 1) . ")";
                                        $originalTaskId = $childTask->original_task_id ?? $childTask->id;
                                        // Tạo task mới với số lượng bổ sung
                                        $newTaskData = [
                                            'name' => $supplementName,
                                            'type' => 'SUB',
                                            'status_id' => 1, // Chưa bắt đầu
                                            'priority_id' => $childTask->priority_id,
                                            'assign_id' => $childTask->assign_id,
                                            'start_date' => date('Y-m-d'),
                                            'due_date' => $contract->expiry_date,
                                            'estimate_time' => $childTask->estimate_time,
                                            'description' => "Công việc bổ sung cho {$childService->name} (phần tăng thêm sau cập nhật hợp đồng lần " . ($supplementCount + 1) . ")",
                                            'qty_request' => $additionalQuantity,
                                            'qty_completed' => 0,
                                            'contract_id' => $contractId,
                                            'contract_service_id' => $childService->id,
                                            'parent_id' => $existingServiceTask->id,
                                            'original_task_id' => $originalTaskId, // Liên kết với task gốc
                                            'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                                            'is_active' => 1,
                                            'sample_image_id' => $childTask->sample_image_id,
                                            'result_image_id' => $childTask->result_image_id,
                                        ];

                                        $newTask = Task::create($newTaskData);

                                        // Log
                                        LogService::saveLog([
                                            'action' => 'TASK_SYNC',
                                            'ip' => $request->getClientIp(),
                                            'details' => "Tạo task bổ sung #{$newTask->id} cho task gốc #{$childTask->id} với số lượng {$additionalQuantity}",
                                            'fk_key' => 'tbl_tasks|id',
                                            'fk_value' => $newTask->id,
                                        ]);

                                        // Cập nhật các mission assignments
                                        $this->updateMissionAssignmentsForNewTask($childTask, $newTask);
                                    }
                                } else if ($newQtyRequest > $oldQtyRequest && !$isCompleted) {
                                    // Nếu task chưa hoàn thành và số lượng tăng, cập nhật số lượng
                                    $childTask->update([
                                        'qty_request' => $newQtyRequest,
                                    ]);

                                    // Cập nhật các mission assignments
                                    $this->updateMissionAssignmentsForExistingTask($childTask, $newQtyRequest);

                                    // Log
                                    LogService::saveLog([
                                        'action' => 'TASK_SYNC',
                                        'ip' => $request->getClientIp(),
                                        'details' => "Cập nhật số lượng task #{$childTask->id} từ {$oldQtyRequest} lên {$newQtyRequest}",
                                        'fk_key' => 'tbl_tasks|id',
                                        'fk_value' => $childTask->id,
                                    ]);
                                }
                                // Nếu số lượng giảm, không cần cập nhật
                            }

                            // Cập nhật các thông tin khác của task con
                            $childTask->update([
                                'name' => $childService->name,
                                'sample_image_id' => $childService->sample_image_id,
                                'result_image_id' => $childService->result_image_id,
                                'due_date' => $contract->expiry_date,
                            ]);
                        } else {
                            // Tạo mới task con cho dịch vụ con
                            $childTaskData = [
                                'name' => $childService->name,
                                'type' => 'SUB',
                                'status_id' => 1, // Chưa bắt đầu
                                'priority_id' => 1, // Mặc định
                                'assign_id' => $contract->user_id,
                                'start_date' => $contract->effective_date,
                                'due_date' => $contract->expiry_date,
                                'estimate_time' => 12, // Giá trị mặc định
                                'description' => "Công việc con {$childService->name} cho dịch vụ {$service->name}",
                                'qty_request' => $childService->quantity,
                                'qty_completed' => 0,
                                'contract_id' => $contractId,
                                'contract_service_id' => $childService->id,
                                'parent_id' => $existingServiceTask->id,
                                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                                'is_active' => 1,
                                'sample_image_id' => $childService->sample_image_id,
                                'result_image_id' => $childService->result_image_id,
                            ];

                            $newChildTask = Task::create($childTaskData);

                            // Log
                            LogService::saveLog([
                                'action' => 'TASK_SYNC',
                                'ip' => $request->getClientIp(),
                                'details' => "Tạo mới task con #{$newChildTask->id} cho dịch vụ con mới",
                                'fk_key' => 'tbl_tasks|id',
                                'fk_value' => $newChildTask->id,
                            ]);
                        }
                    }

                    // Vô hiệu hóa các task con không còn tương ứng với dịch vụ con nào
                    foreach ($existingChildTasks as $serviceId => $childTask) {
                        if (!$childServices->contains('id', $serviceId)) {
                            $childTask->update(['is_active' => 0]);

                            // Log
                            LogService::saveLog([
                                'action' => 'TASK_SYNC',
                                'ip' => $request->getClientIp(),
                                'details' => "Vô hiệu hóa task con #{$childTask->id} do dịch vụ con liên quan đã bị xóa",
                                'fk_key' => 'tbl_tasks|id',
                                'fk_value' => $childTask->id,
                            ]);
                        }
                    }
                } else {
                    // Tạo mới task cho dịch vụ
                    $serviceTaskData = [
                        'name' => $service->name,
                        'type' => 'SERVICE',
                        'status_id' => 1, // Chưa bắt đầu
                        'priority_id' => 1, // Mặc định
                        'assign_id' => $contract->user_id,
                        'start_date' => $contract->effective_date,
                        'due_date' => $contract->expiry_date,
                        'estimate_time' => 24, // Giá trị mặc định
                        'description' => "Công việc thực hiện {$service->name} cho hợp đồng #{$contract->contract_number}",
                        'qty_request' => $service->quantity,
                        'qty_completed' => 0,
                        'contract_id' => $contractId,
                        'service_id' => $service->service_id,
                        'contract_service_id' => $service->id,
                        'parent_id' => $contractTask->id,
                        'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                        'is_active' => 1,
                        'sample_image_id' => $service->sample_image_id,
                        'result_image_id' => $service->result_image_id,
                    ];

                    $newServiceTask = Task::create($serviceTaskData);

                    // Log
                    LogService::saveLog([
                        'action' => 'TASK_SYNC',
                        'ip' => $request->getClientIp(),
                        'details' => "Tạo mới task dịch vụ #{$newServiceTask->id} cho dịch vụ mới",
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $newServiceTask->id,
                    ]);

                    // Tạo tasks cho các dịch vụ con
                    $childServices = $currentServices->where('parent_id', $service->id)->where('is_active', 1);

                    foreach ($childServices as $childService) {
                        $childTaskData = [
                            'name' => $childService->name,
                            'type' => 'SUB',
                            'status_id' => 1, // Chưa bắt đầu
                            'priority_id' => 1, // Mặc định
                            'assign_id' => $contract->user_id,
                            'start_date' => $contract->effective_date,
                            'due_date' => $contract->expiry_date,
                            'estimate_time' => 12, // Giá trị mặc định
                            'description' => "Công việc con {$childService->name} cho dịch vụ {$service->name}",
                            'qty_request' => $childService->quantity,
                            'qty_completed' => 0,
                            'contract_id' => $contractId,
                            'contract_service_id' => $childService->id,
                            'parent_id' => $newServiceTask->id,
                            'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                            'is_active' => 1,
                            'sample_image_id' => $childService->sample_image_id,
                            'result_image_id' => $childService->result_image_id,
                        ];

                        $newChildTask = Task::create($childTaskData);

                        // Log
                        LogService::saveLog([
                            'action' => 'TASK_SYNC',
                            'ip' => $request->getClientIp(),
                            'details' => "Tạo mới task con #{$newChildTask->id} cho dịch vụ con mới",
                            'fk_key' => 'tbl_tasks|id',
                            'fk_value' => $newChildTask->id,
                        ]);
                    }
                }
            }

            // 7. Cập nhật trạng thái và tiến độ cho tất cả task
            $this->recalculateAllTasksProgress($contractId);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Đã đồng bộ thành công trạng thái và số lượng công việc của hợp đồng',
                'data' => [
                    'contract_id' => $contractId,
                    'updated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi đồng bộ: ' . $e->getMessage()
            ]);
        }
    }

    private function updateMissionAssignmentsForNewTask($originalTask, $newTask)
    {
        // Xác định task gốc thực sự
        $realOriginalTask = $originalTask->original_task_id
            ? Task::find($originalTask->original_task_id)
            : $originalTask;

        // Lấy tất cả mission assignments của task gốc thực sự
        $originalAssignments = TaskMissionAssignment::where('task_id', $realOriginalTask->id)
            ->get();

        // Nếu task gốc không có assignments
        if ($originalAssignments->isEmpty()) {
            // Tìm assignments từ task liên quan đến task gốc
            $relatedTasks = Task::where('original_task_id', $realOriginalTask->id)
                ->where('is_active', 1)
                ->pluck('id')
                ->toArray();

            if (!empty($relatedTasks)) {
                $originalAssignments = TaskMissionAssignment::whereIn('task_id', $relatedTasks)
                    ->get();
            }
        }

        foreach ($originalAssignments as $originalAssignment) {
            // Tạo assignment mới cho task mới với cùng mission và user
            TaskMissionAssignment::create([
                'task_id' => $newTask->id,
                'mission_id' => $originalAssignment->mission_id,
                'user_id' => $originalAssignment->user_id,
                'quantity_required' => $newTask->qty_request,
                'quantity_completed' => 0,
                'status' => 'in_progress'
            ]);
        }
    }

    /**
     * Cập nhật mission assignments cho task hiện có khi số lượng thay đổi
     *
     * @param Task $task Task cần cập nhật
     * @param int $newQuantity Số lượng mới
     * @return void
     */
    private function updateMissionAssignmentsForExistingTask($task, $newQuantity)
    {
        // Lấy tất cả mission assignments của task
        $assignments = TaskMissionAssignment::where('task_id', $task->id)
            ->where('status', 'in_progress')
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->update([
                'quantity_required' => $newQuantity
            ]);
        }
    }

    /**
     * Tính toán lại tiến độ cho tất cả task của hợp đồng
     *
     * @param int $contractId ID của hợp đồng
     * @return void
     */
    private function recalculateAllTasksProgress($contractId)
    {
        // Lấy tất cả task con không có task con khác (task mức thấp nhất)
        $leafTasks = Task::where('contract_id', $contractId)
            ->where('is_active', 1)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tbl_tasks as children')
                    ->whereRaw('children.parent_id = tbl_tasks.id')
                    ->where('children.is_active', 1);
            })
            ->get();

        // Cập nhật progress cho từng task dựa trên mission assignments
        foreach ($leafTasks as $task) {
            // Tính dựa trên mission assignments
            $assignments = $task->missionAssignments;

            if ($assignments->isNotEmpty()) {
                $totalRequired = $assignments->sum('quantity_required');
                $totalCompleted = $assignments->sum('quantity_completed');

                $progress = $totalRequired > 0 ? min(100, round(($totalCompleted / $totalRequired) * 100)) : 0;
                $qty_completed = $totalRequired > 0 ? min($task->qty_request, round(($totalCompleted / $totalRequired) * $task->qty_request)) : 0;

                // Xác định trạng thái dựa trên progress và feedback
                $newStatus = 1; // Mặc định: Chưa bắt đầu

                if ($progress > 0 && $progress < 100) {
                    $newStatus = 3; // Đang thực hiện
                } else if ($progress >= 100) {
                    $newStatus = 4; // Hoàn thành
                }

                $task->update([
                    'progress' => $progress,
                    'qty_completed' => $qty_completed,
                    'status_id' => $newStatus
                ]);
            }
        }

        // Cập nhật các task cha từ dưới lên
        $this->updateParentTasksProgress($contractId);
    }

    /**
     * Cập nhật tiến độ của các task cha từ dưới lên
     *
     * @param int $contractId ID của hợp đồng
     * @return void
     */
    private function updateParentTasksProgress($contractId)
    {
        // Lấy tất cả task có parent_id
        $childTasks = Task::where('contract_id', $contractId)
            ->where('is_active', 1)
            ->whereNotNull('parent_id')
            ->get()
            ->groupBy('parent_id');

        // Danh sách các parent_id để xử lý
        $parentIds = $childTasks->keys()->toArray();

        // Xử lý từng parent_id theo cấp độ (từ dưới lên trên)
        while (!empty($parentIds)) {
            $currentParentIds = $parentIds;
            $parentIds = [];

            foreach ($currentParentIds as $parentId) {
                $parent = Task::find($parentId);
                if (!$parent) continue;

                // Lấy tất cả task con đang hoạt động
                $children = $childTasks->get($parentId, collect());

                if ($children->isEmpty()) continue;

                // Tính progress dựa trên task con
                $totalProgress = $children->sum('progress');
                $avgProgress = $children->count() > 0 ? round($totalProgress / $children->count()) : 0;

                // Tính số lượng task con đã hoàn thành
                $completedChildren = $children->filter(function ($child) {
                    return $child->status_id >= 4;
                })->count();

                // Tính số lượng task con cần chỉnh sửa
                $revisionChildren = $children->filter(function ($child) {
                    return $child->status_id == 7;
                })->count();

                // Xác định trạng thái mới cho task cha
                $newStatus = 1; // Mặc định: Chưa bắt đầu

                if ($avgProgress > 0) {
                    if ($completedChildren == $children->count()) {
                        $newStatus = 4; // Hoàn thành
                    } else if ($revisionChildren > 0) {
                        $newStatus = 7; // Cần chỉnh sửa
                    } else {
                        $newStatus = 3; // Đang thực hiện
                    }
                }

                // Cập nhật task cha
                $parent->update([
                    'progress' => $avgProgress,
                    'status_id' => $newStatus
                ]);

                // Thêm parent_id của task cha hiện tại vào danh sách cần xử lý (nếu có)
                if ($parent->parent_id) {
                    $parentIds[] = $parent->parent_id;
                }
            }
        }
    }

    /**
     * Tạo task bổ sung khi số lượng tăng lên
     * 
     * @param Task $originalTask Task gốc đã hoàn thành
     * @param int $additionalQuantity Số lượng bổ sung
     * @return Task
     */
    private function createSupplementTask($originalTask, $additionalQuantity)
    {
        // Đếm số lượng task bổ sung hiện có
        $supplementCount = Task::where('parent_id', $originalTask->parent_id)
            ->where('contract_service_id', $originalTask->contract_service_id)
            ->where('name', 'like', '%' . $originalTask->name . ' (Bổ sung%')
            ->count();

        $supplementNumber = $supplementCount + 1;

        // Tạo tên task bổ sung
        $supplementName = $originalTask->name . ' (Bổ sung ' . $supplementNumber . ')';

        // Tạo task mới
        $supplementTask = Task::create([
            'name' => $supplementName,
            'description' => "Công việc bổ sung cho {$originalTask->name} (phần chênh lệch sau cập nhật số lượng dịch vụ)",
            'note' => $originalTask->note,
            'contract_id' => $originalTask->contract_id,
            'progress' => 0,
            'service_id' => $originalTask->service_id,
            'priority_id' => $originalTask->priority_id,
            'status_id' => 2, // Trạng thái "Đang chờ"
            'estimate_time' => $originalTask->estimate_time,
            'due_date' => $originalTask->due_date,
            'is_active' => 1,
            'qty_request' => $additionalQuantity,
            'qty_completed' => 0,
            'type' => $originalTask->type,
            'contract_service_id' => $originalTask->contract_service_id,
            'parent_id' => $originalTask->parent_id,
            'assign_id' => $originalTask->assign_id,
            'created_id' => $originalTask->created_id,
            'start_date' => date('Y-m-d H:i:s'),
            'sample_image_id' => $originalTask->sample_image_id,
            'result_image_id' => $originalTask->result_image_id,
        ]);

        LogService::saveLog([
            'action' => 'TASK_CREATE_LOG',
            'ip' => request()->getClientIp(),
            'details' => 'Hệ thống tự động tạo task bổ sung #' . $supplementTask->id . ' do số lượng dịch vụ tăng lên',
            'fk_key' => 'tbl_tasks|id',
            'fk_value' => $supplementTask->id,
        ]);

        return $supplementTask;
    }

    /**
     * Đồng bộ số lượng hoàn thành của một task dựa trên báo cáo đóng góp
     *
     * @param Task $task
     * @return void
     */
    private function synchronizeTaskCompletion(Task $task)
    {
        // Lấy tổng số lượng từ các đóng góp
        $totalCompleted = TaskContribution::where('task_id', $task->id)
            ->where('is_active', 1)
            ->sum('quantity');

        // Cập nhật số lượng đã hoàn thành
        $task->qty_completed = $totalCompleted;

        // Tính toán tiến độ phần trăm
        $task->progress = $task->qty_request > 0 ? min(100, round(($totalCompleted / $task->qty_request) * 100)) : 0;

        // Kiểm tra và cập nhật trạng thái
        $this->updateTaskStatusBasedOnProgress($task);

        $task->save();
    }

    /**
     * Đồng bộ task dựa trên trạng thái của các task con
     *
     * @param Task $parentTask
     * @return void
     */
    private function synchronizeTaskWithChildren(Task $parentTask)
    {
        // Lấy tất cả task con đang hoạt động
        $childTasks = Task::where('parent_id', $parentTask->id)
            ->where('is_active', 1)
            ->get();

        if ($childTasks->isEmpty()) {
            // Nếu không có task con, đồng bộ dựa trên đóng góp
            $this->synchronizeTaskCompletion($parentTask);
            return;
        }

        // Tính toán tiến độ dựa trên các task con
        $totalChildTasks = $childTasks->count();
        $completedChildTasks = $childTasks->where('status_id', '>=', 4)->count();

        // Tính phần trăm tiến độ dựa trên tỷ lệ task con hoàn thành
        $parentTask->progress = $totalChildTasks > 0
            ? round(($completedChildTasks / $totalChildTasks) * 100)
            : 0;

        // Nếu parent task chưa hoàn thành (status_id < 4), đặt qty_completed về 0
        // Chỉ task không có task con mới tính qty_completed chính xác
        if ($parentTask->progress < 100) {
            $parentTask->qty_completed = 0;
        }

        // Kiểm tra và cập nhật trạng thái
        $this->updateTaskStatusBasedOnProgress($parentTask);

        $parentTask->save();
    }

    /**
     * Cập nhật trạng thái task dựa theo tiến độ
     *
     * @param Task $task
     * @return void
     */
    private function updateTaskStatusBasedOnProgress(Task $task)
    {
        // Danh sách ID trạng thái
        $newStatusId = 1;        // Mới
        $waitingStatusId = 2;    // Đang chờ
        $inProgressStatusId = 3; // Đang thực hiện
        $completedStatusId = 4;  // Hoàn thành

        // Nếu tiến độ 100%, đánh dấu là hoàn thành
        if ($task->progress >= 100) {
            $task->status_id = $completedStatusId;
        }
        // Nếu tiến độ > 0 nhưng chưa hoàn thành, đánh dấu là đang thực hiện
        else if ($task->progress > 0) {
            $task->status_id = $inProgressStatusId;
        }
        // Giữ nguyên trạng thái nếu chưa có tiến độ
    }

    /**
     * Tính toán tiến độ tổng thể của hợp đồng
     *
     * @param Task $contractTask
     * @return int
     */
    private function calculateContractProgress(Task $contractTask)
    {
        // Lấy lại dữ liệu task từ DB sau khi cập nhật
        $contractTask = Task::find($contractTask->id);

        // Lấy tất cả task dịch vụ của hợp đồng
        $serviceTasks = Task::where('parent_id', $contractTask->id)
            ->where('type', 'SERVICE')
            ->where('is_active', 1)
            ->get();

        // Nếu tiến độ ở task hợp đồng đã đồng bộ từ task dịch vụ, trả về giá trị đó
        return $contractTask->progress;
    }

    /**
     * Cập nhật lại duedate
     *
     * @param
     * @return int
     */
    private function updateDuedateTaskByContract($contract_id)
    {
        $contract = Contract::find($contract_id);
        $tasks = Task::where('contract_id', $contract_id);

        return $tasks->update(['due_date' => $contract->expiry_date]);
    }

    /**
     * Báo cáo hoàn thành nhiệm vụ
     */
    public function reportMission(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'quantities' => 'required|array',
                'quantities.*' => 'required|integer|min:1',
                'notes' => 'nullable|array',
                'notes.*' => 'nullable|string|max:500',
            ],
            [
                'required' => ':attribute không được để trống',
                'integer' => ':attribute phải là số nguyên',
                'min' => ':attribute phải lớn hơn hoặc bằng :min',
                'max' => ':attribute không được vượt quá :max ký tự',
                'array' => ':attribute phải là mảng',
            ],
            [
                'quantities' => 'Số lượng',
                'quantities.*' => 'Số lượng',
                'notes' => 'Ghi chú',
                'notes.*' => 'Ghi chú',
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

            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $task = null;

            foreach ($request->quantities as $assignmentId => $quantity) {
                if ($quantity < 1) continue;

                $assignment = TaskMissionAssignment::find($assignmentId);
                if (!$assignment) {
                    continue;
                }

                // Lưu task cho sử dụng sau
                if (!$task) {
                    $task = $assignment->task;
                }

                // Kiểm tra xem người dùng có quyền báo cáo không
                if ($assignment->user_id != $currentUserId) {
                    continue;
                }

                // Kiểm tra xem số lượng có hợp lệ không
                $remainingQuantity = $assignment->quantity_required - $assignment->quantity_completed;
                if ($quantity > $remainingQuantity) {
                    $quantity = $remainingQuantity;
                }

                // Tạo báo cáo
                $note = isset($request->notes[$assignmentId]) ? $request->notes[$assignmentId] : null;

                $report = TaskMissionReport::create([
                    'assignment_id' => $assignment->id,
                    'user_id' => $currentUserId,
                    'quantity' => $quantity,
                    'note' => $note,
                    'date_completed' => now(),
                ]);

                // Cập nhật số lượng đã hoàn thành cho assignment
                $assignment->quantity_completed += $quantity;

                // Kiểm tra xem assignment đã hoàn thành chưa
                if ($assignment->quantity_completed >= $assignment->quantity_required) {
                    $assignment->status = 'completed';
                }

                $assignment->save();

                // Log
                LogService::saveLog([
                    'action' => TASK_ENUM_LOG,
                    'ip' => $request->getClientIp(),
                    'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã báo cáo hoàn thành ' . $quantity . ' ' . $assignment->mission->name,
                    'fk_key' => 'tbl_tasks|id',
                    'fk_value' => $assignment->task_id,
                ]);
            }

            if (!$task) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không có nhiệm vụ nào được báo cáo.',
                ]);
            }

            // Cập nhật tiến độ của task
            $this->updateTaskProgress($task->id);

            // Cập nhật trạng thái task cha
            if ($task->parent_id) {
                $this->updateParentTaskStatus($task->parent_id);
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Báo cáo hoàn thành nhiệm vụ thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi báo cáo nhiệm vụ: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Cập nhật tiến độ của task dựa trên các mission assignments
     */
    private function updateTaskProgress($taskId)
    {
        $task = Task::find($taskId);
        if (!$task) {
            return;
        }

        // Lấy tất cả assignment của task
        $assignments = $task->missionAssignments;

        if ($assignments->isEmpty()) {
            return;
        }

        // Tính tổng số lượng
        $totalRequired = $assignments->sum('quantity_required');
        $totalCompleted = $assignments->sum('quantity_completed');

        // Cập nhật tiến độ của task
        $progress = $totalRequired > 0 ? min(100, round(($totalCompleted / $totalRequired) * 100)) : 0;
        $qty_completed = $totalRequired > 0 ? min($task->qty_request, round(($totalCompleted / $totalRequired) * $task->qty_request)) : 0;

        // Cập nhật trạng thái của task
        $newStatus = 3; // Đang thực hiện

        if ($progress == 100) {
            $newStatus = 4; // Hoàn thành
        }

        $task->update([
            'progress' => $progress,
            'qty_completed' => $qty_completed,
            'status_id' => $newStatus
        ]);
    }

    /**
     * Đánh dấu feedback đã giải quyết
     */
    public function resolveFeedback(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'feedback_id' => 'required|exists:tbl_task_feedbacks,id',
            ],
            [
                'required' => ':attribute không được để trống',
                'exists' => ':attribute không tồn tại',
            ],
            [
                'feedback_id' => 'Mã feedback',
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

            $feedback = TaskFeedback::find($request->feedback_id);
            if (!$feedback) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Feedback không tồn tại.',
                ]);
            }

            $task = $feedback->task;
            if (!$task) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Công việc không tồn tại.',
                ]);
            }

            // Kiểm tra xem người dùng có được gán nhiệm vụ cho task không
            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $isAdmin = true;

            $hasAssignment = TaskMissionAssignment::where('task_id', $task->id)
                ->where('user_id', $currentUserId)
                ->exists();

            if (!$hasAssignment && !$isAdmin) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền giải quyết feedback này.',
                ]);
            }

            // Đánh dấu feedback đã giải quyết
            $feedback->update([
                'is_resolved' => true,
                'resolved_by' => $currentUserId,
                'resolved_at' => now(),
            ]);

            // Cập nhật trạng thái task
            if (true) {
                // Kiểm tra xem tất cả mission assignments đã hoàn thành chưa
                $allCompleted = true;
                foreach ($task->missionAssignments as $assignment) {
                    if ($assignment->quantity_completed < $assignment->quantity_required) {
                        $allCompleted = false;
                        break;
                    }
                }

                // Kiểm tra task con (nếu có)
                $allChildrenCompleted = true;
                if (in_array($task->type, ['CONTRACT', 'SERVICE'])) {
                    $childTasks = Task::where('parent_id', $task->id)
                        ->where('is_active', 1)
                        ->get();

                    foreach ($childTasks as $childTask) {
                        if ($childTask->status_id < 4) { // Nếu chưa hoàn thành
                            $allChildrenCompleted = false;
                            break;
                        }
                    }
                }

                // Cập nhật trạng thái
                if ($allCompleted && $allChildrenCompleted) {
                    $task->update(['status_id' => 4]); // Hoàn thành
                } else {
                    $task->update(['status_id' => 3]); // Đang thực hiện
                }

                // Cập nhật task cha
                if ($task->parent_id) {
                    $this->updateParentTaskStatus($task->parent_id);
                }
            }

            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã đánh dấu đã giải quyết feedback ' . $feedback->id,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $task->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Đánh dấu đã giải quyết feedback thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xử lý feedback: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Xóa báo cáo nhiệm vụ
     */
    public function deleteMissionReport(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'report_id' => 'required|exists:tbl_task_mission_reports,id',
            ],
            [
                'required' => ':attribute không được để trống',
                'exists' => ':attribute không tồn tại',
            ],
            [
                'report_id' => 'Mã báo cáo',
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

            $report = TaskMissionReport::find($request->report_id);
            if (!$report) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Báo cáo không tồn tại.',
                ]);
            }

            $assignment = $report->assignment;
            if (!$assignment) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Phân công nhiệm vụ không tồn tại.',
                ]);
            }

            $task = $assignment->task;
            if (!$task) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Công việc không tồn tại.',
                ]);
            }

            // Kiểm tra quyền xóa báo cáo
            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $isAdmin = true;

            if ($report->user_id != $currentUserId && !$isAdmin) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền xóa báo cáo này.',
                ]);
            }

            // Lưu số lượng đã báo cáo để cập nhật
            $reportedQuantity = $report->quantity;

            // Xóa báo cáo
            $report->delete();

            // Cập nhật số lượng đã hoàn thành của assignment
            $assignment->quantity_completed -= $reportedQuantity;
            if ($assignment->quantity_completed < 0) {
                $assignment->quantity_completed = 0;
            }

            // Cập nhật trạng thái assignment
            if ($assignment->quantity_completed < $assignment->quantity_required) {
                $assignment->status = 'in_progress';
            }

            $assignment->save();

            // Cập nhật tiến độ và trạng thái task
            $this->updateTaskProgress($task->id);

            // Cập nhật task cha nếu cần
            if ($task->parent_id) {
                $this->updateParentTaskStatus($task->parent_id);
            }

            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã xóa báo cáo nhiệm vụ ' . $report->id,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $task->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Xóa báo cáo nhiệm vụ thành công.',
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
     * Lấy danh sách nhiệm vụ có thể nhận
     */
    public function getMissions(Request $request)
    {
        try {
            // Lấy tất cả nhiệm vụ đang hoạt động
            $missions = TaskMission::where('is_active', 1)
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $missions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách nhiệm vụ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy danh sách nhiệm vụ đã nhận của task
     */
    public function getTaskMissions(Request $request)
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
            // Lấy thông tin task
            $task = Task::find($request->task_id);

            // Lấy danh sách phân công nhiệm vụ của task cho người dùng hiện tại
            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];

            $assignments = TaskMissionAssignment::where('task_id', $request->task_id)
                ->where('user_id', $currentUserId)
                ->with(['mission', 'reports' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->get();

            // Định dạng dữ liệu
            $formattedAssignments = $assignments->map(function ($assignment) {
                $reports = $assignment->reports->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'quantity' => $report->quantity,
                        'note' => $report->note,
                        'date_completed' => $report->date_completed,
                        'created_at' => $report->created_at
                    ];
                });

                return [
                    'id' => $assignment->id,
                    'mission' => [
                        'id' => $assignment->mission->id,
                        'name' => $assignment->mission->name,
                        'salary' => $assignment->mission->salary
                    ],
                    'quantity_required' => $assignment->quantity_required,
                    'quantity_completed' => $assignment->quantity_completed,
                    'status' => $assignment->status,
                    'reports' => $reports
                ];
            });

            return response()->json([
                'status' => 200,
                'data' => [
                    'task' => [
                        'id' => $task->id,
                        'name' => $task->name,
                        'qty_request' => $task->qty_request,
                        'qty_completed' => $task->qty_completed,
                        'result_image_id' => $task->result_image_id,
                        'sample_image_id' => $task->sample_image_id,
                        'progress' => $task->progress
                    ],
                    'assignments' => $formattedAssignments
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách nhiệm vụ của task: ' . $e->getMessage()
            ]);
        }
    }

    /**
 * Hiển thị modal thêm feedback
 */
public function showFeedbackForm(Request $request)
{
    $taskId = $request->input('task_id');
    
    // Lấy task hợp đồng với tất cả task con và cháu
    $contractTask = Task::with(['childs' => function($query) {
        $query->where('is_active', 1)
              ->with(['status', 'assign', 'childs' => function($q) {
                  $q->where('is_active', 1)
                    ->with(['status', 'assign', 'childs']);
              }]);
    }])->findOrFail($taskId);
    
    // Kiểm tra quyền
    $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
    if ($contractTask->assign_id != $currentUserId && !true) {
        return response()->json([
            'status' => 403,
            'message' => 'Bạn không có quyền thêm feedback cho task này.'
        ]);
    }
    
    // Kiểm tra type task
    if ($contractTask->type !== 'CONTRACT') {
        return response()->json([
            'status' => 422,
            'message' => 'Chỉ được phép thêm feedback cho task loại hợp đồng.'
        ]);
    }
    
    // Đánh dấu thuộc tính completed và is_leaf trên mỗi task
    $this->markCompletedAndLeafTasks($contractTask);
    
    return response()->json([
        'status' => 200,
        'data' => [
            'contract_task' => $contractTask
        ]
    ]);
}

/**
 * Đệ quy đánh dấu các task đã hoàn thành và là task lá (không có task con)
 */
private function markCompletedAndLeafTasks($task)
{
    // Đánh dấu task đã hoàn thành
    $task->is_completed = $task->status_id === 4; // 4 = hoàn thành
    
    if (!isset($task->childs) || $task->childs->isEmpty()) {
        // Đây là task lá (không có task con)
        $task->is_leaf = true;
        return;
    }
    
    // Đây không phải task lá
    $task->is_leaf = false;
    
    // Đệ quy xử lý các task con
    foreach ($task->childs as $childTask) {
        $this->markCompletedAndLeafTasks($childTask);
    }
}

    /**
     * Thêm feedback mới
     */
    public function addFeedback(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'task_id' => 'required|exists:tbl_tasks,id',
                'comment' => 'required|string|max:1000',
                'revision_tasks' => 'required|array',
                'revision_tasks.*' => 'exists:tbl_tasks,id',
            ],
            [
                'required' => ':attribute không được để trống',
                'string' => ':attribute phải là chuỗi ký tự',
                'max' => ':attribute không được vượt quá :max ký tự',
                'array' => ':attribute phải là mảng',
                'exists' => ':attribute không tồn tại',
            ],
            [
                'task_id' => 'Mã công việc',
                'comment' => 'Nội dung feedback',
                'revision_tasks' => 'Danh sách công việc cần chỉnh sửa',
                'revision_tasks.*' => 'Công việc cần chỉnh sửa',
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

            // Kiểm tra quyền - chỉ người quản lý task mới có quyền thêm feedback
            $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $isAdmin = true;

            if ($task->assign_id != $currentUserId && !$isAdmin) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền thêm feedback cho task này.',
                ]);
            }

            // Kiểm tra loại task
            if ($task->type !== 'CONTRACT') {
                return response()->json([
                    'status' => 422,
                    'message' => 'Chỉ được phép thêm feedback cho task loại hợp đồng.',
                ]);
            }

            // Tạo feedback mới
            $feedback = TaskFeedback::create([
                'task_id' => $task->id,
                'user_id' => $currentUserId,
                'comment' => $request->comment,
                'is_resolved' => false,
                'status' => 0 // Đang chờ giải quyết
            ]);

            // Lưu các task cần chỉnh sửa
            $revisionTasks = $request->revision_tasks;
            foreach ($revisionTasks as $taskId) {
                $revisionTask = Task::find($taskId);
                if ($revisionTask) {
                    // Tạo feedback item
                    TaskFeedbackItem::create([
                        'feedback_id' => $feedback->id,
                        'task_id' => $taskId,
                        'is_resolved' => false
                    ]);

                    // Cập nhật trạng thái task cần chỉnh sửa sang status 7
                    $revisionTask->update(['status_id' => 7]); // Cần chỉnh sửa

                    // Log
                    LogService::saveLog([
                        'action' => TASK_ENUM_LOG,
                        'ip' => $request->getClientIp(),
                        'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã thêm task #' . $taskId . ' vào danh sách cần chỉnh sửa trong feedback #' . $feedback->id,
                        'fk_key' => 'tbl_tasks|id',
                        'fk_value' => $taskId,
                    ]);
                }
            }

            // Cập nhật trạng thái task hợp đồng cũng về trạng thái cần chỉnh sửa
            $task->update(['status_id' => 7]); // Cần chỉnh sửa

            LogService::saveLog([
                'action' => TASK_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã thêm feedback #' . $feedback->id . ' vào task #' . $task->id,
                'fk_key' => 'tbl_tasks|id',
                'fk_value' => $task->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Thêm feedback thành công.',
                'data' => [
                    'feedback_id' => $feedback->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi thêm feedback: ' . $e->getMessage(),
            ]);
        }
    }

    public function resolveFeedbackItem(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'feedback_item_id' => 'required|exists:tbl_task_feedback_items,id',
            'comment' => 'nullable|string|max:500',
        ],
        [
            'required' => ':attribute không được để trống',
            'exists' => ':attribute không tồn tại',
            'string' => ':attribute phải là chuỗi ký tự',
            'max' => ':attribute không được vượt quá :max ký tự',
        ],
        [
            'feedback_item_id' => 'Mã feedback item',
            'comment' => 'Ghi chú',
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

        $feedbackItem = TaskFeedbackItem::with(['feedback', 'feedback.task', 'task'])->find($request->feedback_item_id);
        if (!$feedbackItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Feedback item không tồn tại.',
            ]);
        }

        // Kiểm tra quyền 
        $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $isAdmin = true;
        
        // Kiểm tra quyền dựa trên nhiệm vụ hoặc quyền admin
        $hasAssignment = TaskMissionAssignment::where('task_id', $feedbackItem->task_id)
            ->where('user_id', $currentUserId)
            ->exists();

        if (!$hasAssignment && !$isAdmin && $feedbackItem->task->assign_id != $currentUserId) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền đánh dấu đã giải quyết feedback item này.',
            ]);
        }

        // Đánh dấu feedback item đã giải quyết
        $feedbackItem->update([
            'is_resolved' => true,
            'resolved_by' => $currentUserId,
            'resolved_at' => now(),
            'resolver_comment' => $request->comment
        ]);
        
        // Kiểm tra xem tất cả các item của feedback này đã giải quyết chưa
        $allResolved = TaskFeedbackItem::where('feedback_id', $feedbackItem->feedback_id)
            ->where('is_resolved', false)
            ->doesntExist();
            
        // Nếu tất cả đã giải quyết, cập nhật trạng thái feedback
        if ($allResolved) {
            // Chưa đánh dấu feedback đã giải quyết (vẫn chờ xác nhận từ sale)
            // Nhưng cập nhật đã sẵn sàng để xác nhận
            $feedbackItem->feedback->update([
                'status' => 1 // Đã sẵn sàng xác nhận
            ]);
        }

        LogService::saveLog([
            'action' => TASK_ENUM_LOG,
            'ip' => $request->getClientIp(),
            'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã đánh dấu đã giải quyết item #' . $feedbackItem->id . ' của feedback #' . $feedbackItem->feedback_id,
            'fk_key' => 'tbl_tasks|id',
            'fk_value' => $feedbackItem->task_id,
        ]);

        DB::commit();

        return response()->json([
            'status' => 200,
            'message' => 'Đánh dấu đã giải quyết thành công.',
            'data' => [
                'all_resolved' => $allResolved
            ]
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'message' => 'Đã xảy ra lỗi khi đánh dấu đã giải quyết: ' . $e->getMessage(),
        ]);
    }
}

public function confirmFeedbackResolved(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'feedback_id' => 'required|exists:tbl_task_feedbacks,id',
        ],
        [
            'required' => ':attribute không được để trống',
            'exists' => ':attribute không tồn tại',
        ],
        [
            'feedback_id' => 'Mã feedback',
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

        $feedback = TaskFeedback::with('task')->find($request->feedback_id);
        if (!$feedback) {
            return response()->json([
                'status' => 404,
                'message' => 'Feedback không tồn tại.',
            ]);
        }

        // Kiểm tra quyền - chỉ người quản lý task (sale) mới có quyền xác nhận feedback đã giải quyết
        $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $isAdmin = true;

        if ($feedback->task->assign_id != $currentUserId && !$isAdmin) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền xác nhận feedback này đã giải quyết.',
            ]);
        }

        // Kiểm tra xem tất cả các item đã được giải quyết chưa
        $pendingItems = TaskFeedbackItem::where('feedback_id', $feedback->id)
            ->where('is_resolved', false)
            ->count();

        if ($pendingItems > 0) {
            return response()->json([
                'status' => 422,
                'message' => 'Vẫn còn ' . $pendingItems . ' task chưa được giải quyết.',
            ]);
        }

        // Đánh dấu feedback đã giải quyết
        $feedback->update([
            'is_resolved' => true,
            'resolved_by' => $currentUserId,
            'resolved_at' => now(),
            'status' => 1 // Đã giải quyết
        ]);

        // Lấy danh sách các task cần cập nhật trạng thái
        $taskIds = TaskFeedbackItem::where('feedback_id', $feedback->id)
            ->pluck('task_id')
            ->toArray();

        // Cập nhật trạng thái các task liên quan đến feedback
        foreach ($taskIds as $taskId) {
            $this->updateTaskStatus($taskId, 4); // 4 = Hoàn thành
        }
        
        // Xác định và cập nhật trạng thái task hợp đồng
        $contractTaskId = $feedback->task_id;
        $newContractStatus = $this->determineContractTaskStatus($contractTaskId);
        Task::where('id', $contractTaskId)->update(['status_id' => $newContractStatus]);
        
        // Cập nhật status của task cha (nếu có)
        if ($feedback->task->parent_id) {
            $this->updateParentTaskStatus($feedback->task->parent_id);
        }

        LogService::saveLog([
            'action' => TASK_ENUM_LOG,
            'ip' => $request->getClientIp(),
            'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã xác nhận toàn bộ feedback #' . $feedback->id . ' đã được giải quyết',
            'fk_key' => 'tbl_tasks|id',
            'fk_value' => $feedback->task_id,
        ]);

        DB::commit();

        return response()->json([
            'status' => 200,
            'message' => 'Xác nhận đã giải quyết feedback thành công.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'message' => 'Đã xảy ra lỗi khi xác nhận đã giải quyết feedback: ' . $e->getMessage(),
        ]);
    }
}

public function requestFeedbackRevision(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'feedback_id' => 'required|exists:tbl_task_feedbacks,id',
            'comment' => 'nullable|string|max:500',
        ],
        [
            'required' => ':attribute không được để trống',
            'exists' => ':attribute không tồn tại',
            'string' => ':attribute phải là chuỗi ký tự',
            'max' => ':attribute không được vượt quá :max ký tự',
        ],
        [
            'feedback_id' => 'Mã feedback',
            'comment' => 'Ghi chú',
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

        $feedback = TaskFeedback::with('task')->find($request->feedback_id);
        if (!$feedback) {
            return response()->json([
                'status' => 404,
                'message' => 'Feedback không tồn tại.',
            ]);
        }

        // Kiểm tra quyền - chỉ người quản lý task mới có quyền yêu cầu làm lại
        $currentUserId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $isAdmin = true;

        if ($feedback->task->assign_id != $currentUserId && !$isAdmin) {
            return response()->json([
                'status' => 403,
                'message' => 'Bạn không có quyền yêu cầu làm lại feedback này.',
            ]);
        }

        // Cập nhật thông tin feedback
        $additionalComment = "\n\nYêu cầu làm lại (" . date('d/m/Y H:i') . "): " . ($request->comment ?: 'Không đạt yêu cầu');
        $feedback->update([
            'is_resolved' => false,
            'status' => 2, // Yêu cầu làm lại
            'comment' => $feedback->comment . $additionalComment
        ]);

        // Đặt lại trạng thái đã giải quyết cho tất cả feedback items
        TaskFeedbackItem::where('feedback_id', $feedback->id)
            ->update([
                'is_resolved' => false,
                'resolved_by' => null,
                'resolved_at' => null,
                'resolver_comment' => null
            ]);

        // Lấy danh sách các task cần đặt lại trạng thái "Cần chỉnh sửa"
        $taskIds = TaskFeedbackItem::where('feedback_id', $feedback->id)
            ->pluck('task_id')
            ->toArray();

        // Cập nhật trạng thái các task liên quan
        foreach ($taskIds as $taskId) {
            $this->updateTaskStatus($taskId, 7); // 7 = Cần chỉnh sửa
        }

        // Khi yêu cầu làm lại, task hợp đồng luôn ở trạng thái cần chỉnh sửa
        Task::where('id', $feedback->task_id)->update(['status_id' => 7]); // Cần chỉnh sửa
        
        // Cập nhật status của task cha (nếu có)
        if ($feedback->task->parent_id) {
            $this->updateParentTaskStatus($feedback->task->parent_id);
        }

        LogService::saveLog([
            'action' => TASK_ENUM_LOG,
            'ip' => $request->getClientIp(),
            'details' => Session::get(ACCOUNT_CURRENT_SESSION)['name'] . ' đã yêu cầu làm lại các task trong feedback #' . $feedback->id,
            'fk_key' => 'tbl_tasks|id',
            'fk_value' => $feedback->task_id,
        ]);

        DB::commit();

        return response()->json([
            'status' => 200,
            'message' => 'Yêu cầu làm lại feedback thành công.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'message' => 'Đã xảy ra lỗi khi yêu cầu làm lại feedback: ' . $e->getMessage(),
        ]);
    }
}

/**
 * Xác định và cập nhật trạng thái task hợp đồng dựa trên tất cả các task con
 * 
 * @param int $contractTaskId ID của task hợp đồng
 * @return int Trạng thái mới của task hợp đồng
 */
private function determineContractTaskStatus($contractTaskId)
{
    // Lấy tất cả các task con (tất cả các cấp) của task hợp đồng
    $allChildTasks = $this->getAllChildTasks($contractTaskId);
    
    if ($allChildTasks->isEmpty()) {
        return 3; // Nếu không có task con, giữ trạng thái đang thực hiện
    }
    
    // Loại bỏ các task đã bị huỷ (status = 6)
    $activeTasks = $allChildTasks->filter(function($task) {
        return $task->status_id != 6;
    });
    
    if ($activeTasks->isEmpty()) {
        return 3; // Nếu tất cả task đều đã huỷ, giữ trạng thái đang thực hiện
    }
    
    // Kiểm tra xem có task nào cần chỉnh sửa không (status = 7)
    $needsRevisionCount = $activeTasks->where('status_id', 7)->count();
    if ($needsRevisionCount > 0) {
        // Nếu có bất kỳ task nào cần chỉnh sửa, task hợp đồng cũng cần chỉnh sửa
        return 7;
    }
    
    // Kiểm tra xem tất cả task đã hoàn thành chưa (status = 4)
    $completedCount = $activeTasks->where('status_id', 4)->count();
    if ($completedCount == $activeTasks->count()) {
        // Nếu tất cả task con đều đã hoàn thành, task hợp đồng cũng hoàn thành
        return 4;
    }
    
    // Trường hợp còn lại: không có task nào cần chỉnh sửa, nhưng chưa tất cả đều hoàn thành
    return 3; // Đang thực hiện
}

/**
 * Lấy tất cả các task con ở mọi cấp của một task
 * 
 * @param int $taskId ID của task cha
 * @return \Illuminate\Support\Collection Danh sách tất cả các task con
 */
private function getAllChildTasks($taskId)
{
    // Lấy các task con trực tiếp
    $directChildren = Task::where('parent_id', $taskId)
        ->where('is_active', 1)
        ->get();
    
    // Khởi tạo collection để lưu tất cả task con
    $allChildren = collect($directChildren);
    
    // Đệ quy lấy tất cả task con ở các cấp thấp hơn
    foreach ($directChildren as $child) {
        $grandChildren = $this->getAllChildTasks($child->id);
        $allChildren = $allChildren->merge($grandChildren);
    }
    
    return $allChildren;
}

    /**
     * Lấy danh sách feedback của task
     */
    public function getFeedbacks(Request $request)
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
            // Lấy danh sách feedback của task
            $task = Task::findOrFail($request->task_id);

            // Nếu là task con, tìm task hợp đồng tương ứng
            $contractTaskId = $task->id;
            if ($task->type !== 'CONTRACT') {
                // Tìm task hợp đồng gốc (có thể qua nhiều cấp)
                $parentId = $task->parent_id;
                while ($parentId) {
                    $parentTask = Task::select('id', 'parent_id', 'type')->find($parentId);
                    if (!$parentTask) break;

                    if ($parentTask->type === 'CONTRACT') {
                        $contractTaskId = $parentTask->id;
                        break;
                    }

                    $parentId = $parentTask->parent_id;
                }
            }

            // Lấy tất cả feedback của task hợp đồng
            $feedbacks = TaskFeedback::where('task_id', $contractTaskId)
                ->with([
                    'user',
                    'resolver',
                    'feedbackItems' => function ($query) use ($task) {
                        $query->with('task', 'resolver');
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Kiểm tra và thêm thông tin xem task hiện tại có trong feedbackItems không
            $feedbacks->map(function ($feedback) use ($task) {
                $feedback->current_task_in_items = $feedback->feedbackItems->contains('task_id', $task->id);

                // Format các feedbackItems để dễ sử dụng ở frontend
                $feedback->formatted_items = $feedback->feedbackItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'is_resolved' => $item->is_resolved,
                        'task' => [
                            'id' => $item->task->id,
                            'name' => $item->task->name,
                            'type' => $item->task->type
                        ],
                        'resolver' => $item->resolver ? [
                            'id' => $item->resolver->id,
                            'name' => $item->resolver->name
                        ] : null,
                        'resolved_at' => $item->resolved_at,
                        'resolver_comment' => $item->resolver_comment
                    ];
                });

                return $feedback;
            });

            // Định dạng dữ liệu trả về
            $formattedFeedbacks = $feedbacks->map(function ($feedback) {
                return [
                    'id' => $feedback->id,
                    'user' => [
                        'id' => $feedback->user->id,
                        'name' => $feedback->user->name
                    ],
                    'comment' => $feedback->comment,
                    'is_resolved' => $feedback->is_resolved,
                    'status' => $feedback->status,
                    'status_text' => $this->getFeedbackStatusText($feedback->status),
                    'resolver' => $feedback->resolver ? [
                        'id' => $feedback->resolver->id,
                        'name' => $feedback->resolver->name
                    ] : null,
                    'resolved_at' => $feedback->resolved_at,
                    'created_at' => $feedback->created_at,
                    'current_task_in_items' => $feedback->current_task_in_items,
                    'items' => $feedback->formatted_items,
                    'all_items_resolved' => $feedback->feedbackItems->every(function ($item) {
                        return $item->is_resolved;
                    })
                ];
            });

            return response()->json([
                'status' => 200,
                'data' => $formattedFeedbacks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách feedback: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper function to get feedback status text
     */
    private function getFeedbackStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Đang chờ giải quyết';
            case 1:
                return 'Đã giải quyết';
            case 2:
                return 'Yêu cầu làm lại';
            default:
                return 'Không xác định';
        }
    }

    // Thêm phương thức trong TaskController
/**
 * Lấy thông tin chi tiết của một feedback item
 */
public function getFeedbackItemDetails(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'item_id' => 'required|exists:tbl_task_feedback_items,id',
        ],
        [
            'required' => ':attribute không được để trống',
            'exists' => ':attribute không tồn tại',
        ],
        [
            'item_id' => 'Mã feedback item',
        ]
    );

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => $validator->errors()->first()
        ]);
    }

    try {
        $feedbackItem = TaskFeedbackItem::with([
            'feedback' => function($query) {
                $query->with('user');
            },
            'task'
        ])->find($request->item_id);

        if (!$feedbackItem) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thông tin feedback item.'
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $feedbackItem
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Đã xảy ra lỗi khi lấy thông tin feedback item: ' . $e->getMessage()
        ]);
    }
}

/**
 * Lấy trạng thái hiện tại của task
 */
public function getTaskStatus($id)
{
    try {
        $task = Task::with(['status', 'parent'])->find($id);
        
        if (!$task) {
            return response()->json([
                'status' => 404,
                'message' => 'Task không tồn tại'
            ]);
        }
        
        // Tính toán tiến độ dựa trên QtyCompleted/QtyRequest
        $progress = $task->qty_request > 0 ? min(100, round(($task->qty_completed / $task->qty_request) * 100)) : 0;
        
        // Kiểm tra xem có cần cập nhật task cha không
        $parentUpdated = false;
        if ($task->parent_id) {
            $parent = $task->parent;
            // Tính toán tiến độ của task cha dựa trên các task con
            $childTasks = Task::where('parent_id', $parent->id)
                ->where('is_active', 1)
                ->get();
                
            if ($childTasks->isNotEmpty()) {
                $totalProgress = 0;
                foreach ($childTasks as $childTask) {
                    $childProgress = $childTask->qty_request > 0 ? 
                        min(100, round(($childTask->qty_completed / $childTask->qty_request) * 100)) : 0;
                    $totalProgress += $childProgress;
                }
                
                $parentProgress = round($totalProgress / $childTasks->count());
                
                // Kiểm tra xem có sự thay đổi về tiến độ không
                if ($parentProgress != $parent->progress) {
                    $parent->progress = $parentProgress;
                    $parent->save();
                    $parentUpdated = true;
                }
            }
        }
        
        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $task->id,
                'name' => $task->name,
                'status_id' => $task->status_id,
                'status_name' => $task->status->name ?? '',
                'status_color' => $task->status->color ?? '',
                'parent_id' => $task->parent_id,
                'qty_completed' => $task->qty_completed,
                'qty_request' => $task->qty_request,
                'progress' => $progress,
                'parent_updated' => $parentUpdated
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
        ]);
    }
}
}
