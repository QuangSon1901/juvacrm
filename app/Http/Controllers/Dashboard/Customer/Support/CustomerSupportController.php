<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\ConsultationLog;
use App\Models\Customer;
use App\Models\CustomerClass;
use App\Models\CustomerLead;
use App\Models\Service;
use App\Models\Upload;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerSupportController extends Controller
{
    public function index()
    {
        $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $statuses = CustomerLead::select('id', 'name', 'color')->where('type', 2)->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();
        $classes = CustomerClass::select('id', 'name', 'color')->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();

        // Thêm thống kê khách hàng để hiển thị trong dashboard
        $customerStatistics = [
            'total_customers' => Customer::where('type', '>', Customer::TYPE_LEAD)->count(),
            'new_today' => Customer::where('type', '>', Customer::TYPE_LEAD)
                ->whereDate('created_at', today())->count(),
            'active_consultations' => Consultation::whereHas('logs', function($query) {
                $query->where('action', '<', 2); // Chưa hoàn thành tư vấn
            })->count(),
            'due_follow_ups' => Customer::where('type', '>', Customer::TYPE_LEAD)
                ->where('last_interaction_date', '<', now()->subDays(14))
                ->count(),
            'by_status' => CustomerLead::where('type', 2)->withCount(['customers' => function($query) {
                $query->where('type', '>', Customer::TYPE_LEAD);
            }])->get(),
        ];

        return view("dashboard.customer.support.index", [
            'services' => $services,
            'statuses' => $statuses,
            'classes' => $classes,
            'statistics' => $customerStatistics
        ]);
    }

    public function consultation($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return abort(404, 'Khách hàng không tồn tại.');
        }

        // Tự động tạo nhật ký tư vấn mặc định nếu chưa có
        if ($customer->consultations->isEmpty()) {
            $consultations = [
                ['customer_id' => $id, 'name' => 'Nhật ký lần 1'],
                ['customer_id' => $id, 'name' => 'Nhật ký lần 2'],
                ['customer_id' => $id, 'name' => 'Nhật ký lần 3'],
            ];
            Consultation::insert($consultations);
            
            // Cập nhật ngày tương tác cho khách hàng
            $customer->updateLastInteraction();
        }

        $customer = Customer::find($id);
        
        // Lấy các hoạt động tư vấn sắp tới
        $upcomingAppointments = Appointment::where('customer_id', $id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();
        
        // Lấy lịch sử tư vấn gần đây nhất
        $recentConsultations = ConsultationLog::whereIn('consultation_id', 
                                    $customer->consultations->pluck('id'))
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
        
        $result = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'classification' => [
                'id' => $customer->classification->id,
                'name' => $customer->classification->name,
                'color' => $customer->classification->color
            ],
            'status' => [
                'id' => $customer->status->id,
                'name' => $customer->status->name,
                'color' => $customer->status->color
            ],
            'type' => $customer->type,
            'type_name' => $customer->getTypeName(),
            'updated_at' => $customer->updated_at,
            'consultations' => $customer->consultations->map(function ($cons, $_) {
                // Lấy trạng thái tư vấn hiện tại
                $latestLog = ConsultationLog::where('consultation_id', $cons->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $status = 'Chưa bắt đầu';
                $statusColor = 'neutral';
                $actionCode = null;
                
                if ($latestLog) {
                    $actionCode = $latestLog->action;
                    switch ($latestLog->action) {
                        case 0:
                            $status = 'Hỏi nhu cầu KH';
                            $statusColor = 'primary';
                            break;
                        case 1:
                            $status = 'Tư vấn gói';
                            $statusColor = 'warning';
                            break;
                        case 2:
                            $status = 'Lập hợp đồng';
                            $statusColor = 'success';
                            break;
                        case 3:
                            $status = 'Gửi bảng giá';
                            $statusColor = 'info';
                            break;
                        case 4:
                            $status = 'Khách từ chối';
                            $statusColor = 'danger';
                            break;
                        case 5:
                            $status = 'Đặt lịch tư vấn lại';
                            $statusColor = 'gray';
                            break;
                    }
                }
                
                return [
                    "index" => ++$_,
                    "id" => $cons->id,
                    "name" => $cons->name,
                    "status" => $status,
                    "action_code" => $actionCode,
                    "status_color" => $statusColor,
                    "updated_at" => $cons->updated_at
                ];
            })
        ];
        
        // Thông tin về lịch hẹn sắp tới
        $upcomingInfo = [];
        foreach ($upcomingAppointments as $appointment) {
            $upcomingInfo[] = [
                'id' => $appointment->id,
                'name' => $appointment->name,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'color' => $appointment->color,
                'formatted_date' => Carbon::parse($appointment->start_time)->format('d/m/Y'),
                'formatted_time' => Carbon::parse($appointment->start_time)->format('H:i') . ' - ' . 
                                   Carbon::parse($appointment->end_time)->format('H:i'),
                'days_away' => Carbon::parse($appointment->start_time)->diffInDays(now()) + 1
            ];
        }
        
        return view("dashboard.customer.support.consultation", [
            'details' => $result,
            'upcoming_appointments' => $upcomingInfo,
            'recent_logs' => $recentConsultations
        ]);
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        $customersQuery = Customer::query()
            ->filterMyCustomer($request['filter']['my_customer'] ?? 0)
            ->filterBlackList($request['filter']['black_list'] ?? 1)
            ->filterByServices($request['filter']['services'] ?? '')
            ->filterByStatus($request['filter']['status_id'] ?? 0)
            ->filterByClass($request['filter']['class_id'] ?? 0)
            ->where('type', '>', Customer::TYPE_LEAD) // Chỉ lấy khách hàng thật (không phải lead)
            ->search($request['filter']['search'] ?? '');
            
        // Thêm bộ lọc mới - theo loại khách hàng (prospect hoặc customer)
        if (isset($request['filter']['customer_type'])) {
            $customersQuery->where('type', $request['filter']['customer_type']);
        }
        
        // Thêm bộ lọc theo thời gian tương tác cuối
        if (isset($request['filter']['interaction'])) {
            switch ($request['filter']['interaction']) {
                case 'recent': // Tương tác trong 7 ngày qua
                    $customersQuery->whereDate('last_interaction_date', '>=', now()->subDays(7));
                    break;
                case 'medium': // Tương tác trong 8-30 ngày qua
                    $customersQuery->whereDate('last_interaction_date', '>=', now()->subDays(30))
                                  ->whereDate('last_interaction_date', '<', now()->subDays(7));
                    break;
                case 'old': // Tương tác cách đây hơn 30 ngày
                    $customersQuery->whereDate('last_interaction_date', '<', now()->subDays(30));
                    break;
                case 'none': // Chưa từng tương tác
                    $customersQuery->whereNull('last_interaction_date');
                    break;
            }
        }

        $paginationResult = PaginationService::paginate($customersQuery, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($customer, $key) use ($offset) {
            // Thêm thông tin về tương tác gần nhất
            $lastConsultation = ConsultationLog::whereIn('consultation_id', 
                                    $customer->consultations->pluck('id'))
                                ->orderBy('created_at', 'desc')
                                ->first();
            
            $lastInteractionText = "Chưa có tương tác";
            if ($customer->last_interaction_date) {
                $daysSince = now()->diffInDays($customer->last_interaction_date);
                
                if ($daysSince == 0) {
                    $lastInteractionText = "Hôm nay";
                } elseif ($daysSince == 1) {
                    $lastInteractionText = "Hôm qua";
                } elseif ($daysSince <= 30) {
                    $lastInteractionText = $daysSince . " ngày trước";
                } else {
                    $lastInteractionText = Carbon::parse($customer->last_interaction_date)->format('d/m/Y');
                }
            }
            
            return [
                'index' => $offset + $key + 1,
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'services' => $customer->getServicesArray(),
                'company' => $customer->company,
                'status' => [
                    'id' => $customer->status->id ?? 0,
                    'name' => $customer->status->name ?? '',
                    'color' => $customer->status->color ?? '',
                ],
                'classification' => [
                    'id' => $customer->classification->id ?? 0,
                    'name' => $customer->classification->name ?? '',
                    'color' => $customer->classification->color ?? '',
                ],
                'source' => $customer->source,
                'staff' => [
                    'id' => $customer->user->id ?? 0,
                    'name' => $customer->user->name ?? '',
                ],
                'type' => $customer->getTypeName(),
                'last_interaction' => [
                    'date' => $customer->last_interaction_date,
                    'text' => $lastInteractionText
                ],
                'consultation_status' => $lastConsultation ? $lastConsultation->action : null,
                'updated_at' => $customer->updated_at,
                'is_active' => $customer->is_active,
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.customer.support.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function consultationRemove(Request $request) {
        $validator = ValidatorService::make($request, [
            'id' => 'required|integer|exists:tbl_consultations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $consultation = Consultation::find($request['id']);
            $consultation->update(['is_deleted' => 1]);

            return response()->json([
                'status' => 200,
                'message' => 'Xoá thành công!',
                'data' => [
                    'customer_id' => $consultation->customer_id,
                    'name' => $consultation->name,
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã xoá nhật ký "' . $response->data->name .'" của khách hàng #' . $response->data->customer_id,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $response->data->customer_id,
            ]);
        });
    }

    public function consultationLog(Request $request) 
    {
        $consultation = Consultation::find($request['id']);
        if (!$consultation) {
            return abort(404, 'Nhật ký không tồn tại.');
        }

        $consultationLogs = ConsultationLog::where('consultation_id', $consultation->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($log, $_) {
                return [
                    'index' => ++$_,
                    'id' => $log->id,
                    'message' => $log->message,
                    'status' => $log->action,
                    'created_at' => $log->created_at,
                    'attachments' => $log->getAttachmentsArray(),
                    'has_follow_up' => !empty($log->follow_up_date),
                    'follow_up_date' => $log->follow_up_date,
                    'user' => [
                        'id' => $log->user->id,
                        'name' => $log->user->name
                    ]
                ];
            });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.customer.support.components.consultation_item', [
                'data' => $consultationLogs,
                'consultation' => $consultation
            ])->render(),
        ]);
    }

    public function consultationUpdate(Request $request) {
        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255',
            'id' => 'required|integer|exists:tbl_consultations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {
            $consultation = Consultation::find($request['id']);
            $consultation->update(['name' => $request['name']]);

            return response()->json([
                'status' => 200,
                'message' => 'Chỉnh sửa thành công!',
                'data' => [
                    'customer_id' => $consultation->customer_id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã cập nhật tiêu đề nhật ký "' . $request['name'] .'" cho khách hàng #' . $response->data->customer_id,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $response->data->customer_id,
            ]);
        });
    }

    public function consultationCreate(Request $request)
    {

        $validator = ValidatorService::make($request, [
            'name' => 'required|string|max:255',
            'customer_id' => 'required|integer|exists:tbl_customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        return tryCatchHelper($request, function () use ($request) {

            $data = $request->only('name', 'customer_id');
            $consultation = Consultation::create($data);

            return response()->json([
                'status' => 200,
                'message' => 'Lưu thành công!',
                'data' => [
                    'id' => $consultation->id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã tạo nhật ký "' . $request['name'] .'" cho khách hàng #' . $request['customer_id'],
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $request['customer_id'],
            ]);
        });
    }

    public function consultationAddLog(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'message' => 'required|string|max:255',
            'action' => 'nullable|integer',
            'phone' => 'nullable|string|max:10',
            'consultation_id' => 'required|integer|exists:tbl_consultations,id',
            'follow_up_date' => 'nullable|date',
            'create_appointment' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        
        return tryCatchHelper($request, function () use ($request) {
            $data = $request->only('message', 'action', 'consultation_id', 'follow_up_date');
            $data['user_id'] = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
            $consultationLog = ConsultationLog::create($data);

            // Xử lý tệp đính kèm
            if (isset($request['attachment']) && count($request['attachment']) > 0) {
                foreach ($request['attachment'] as $attachment) {
                    $upload = Upload::where('driver_id', $attachment)->first();
                    if ($upload) {
                        $upload->update(['fk_key' => 'tbl_logs|id', 'fk_value' => $consultationLog->id]);
                    }
                }
            }

            $consultation = Consultation::find($request['consultation_id']);
            $customer = Customer::find($consultation->customer_id);

            // Cập nhật SĐT nếu có
            if (isset($request['phone']) && $request['phone']!='') {
                $customer->update(['phone' => $request['phone']]);
            }
            
            // Cập nhật ngày tương tác gần nhất
            $customer->updateLastInteraction();
            
            // Tạo lịch hẹn nếu có yêu cầu
            if ($request->has('create_appointment') && $request->create_appointment && $request->follow_up_date) {
                $followUpDate = Carbon::parse($request->follow_up_date);
                
                // Mặc định hẹn 1 tiếng
                $endTime = (clone $followUpDate)->addHour();
                
                Appointment::create([
                    'customer_id' => $customer->id,
                    'user_id' => $data['user_id'],
                    'name' => 'Tư vấn: ' . substr($request->message, 0, 30) . (strlen($request->message) > 30 ? '...' : ''),
                    'start_time' => $followUpDate,
                    'end_time' => $endTime,
                    'note' => $request->message,
                    'color' => 'primary',
                    'is_active' => 1
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lưu thành công!',
                'data' => [
                    'id' => $consultationLog->id,
                    'customer_id' => $consultation->customer_id
                ]
            ]);
        }, function ($request, $response) {
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Đã đăng nhật ký tư vấn #' . $response->data->id,
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $response->data->customer_id,
            ]);
        });
    }
    public function getCustomersNeedingAttention()
    {
        $result = [
            'no_recent_interaction' => Customer::where('type', '>', Customer::TYPE_LEAD)
                ->where(function($query) {
                    $query->whereNull('last_interaction_date')
                          ->orWhere('last_interaction_date', '<', now()->subDays(30));
                })
                ->limit(10)
                ->get(),
                
            'pending_consultations' => Consultation::whereHas('logs', function($query) {
                    $query->where('action', 0); // Đã hẹn tư vấn
                })
                ->with('customer')
                ->limit(10)
                ->get(),
                
            'in_progress_consultations' => Consultation::whereHas('logs', function($query) {
                    $query->where('action', 1); // Đang tư vấn
                })
                ->with('customer')
                ->limit(10)
                ->get(),
        ];
        
        return response()->json([
            'status' => 200,
            'data' => $result
        ]);
    }
}
