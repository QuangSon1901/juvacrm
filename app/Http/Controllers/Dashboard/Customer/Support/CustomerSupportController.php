<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationLog;
use App\Models\Customer;
use App\Models\CustomerClass;
use App\Models\CustomerLead;
use App\Models\Service;
use App\Services\PaginationService;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{
    public function index()
    {
        $services = Service::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $statuses = CustomerLead::select('id', 'name', 'color')->where('type', 2)->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();
        $classes = CustomerClass::select('id', 'name', 'color')->where('is_active', 1)->orderBy('sort', 'asc')->get()->toArray();

        return view("dashboard.customer.support.index", [
            'services' => $services,
            'statuses' => $statuses,
            'classes' => $classes,
        ]);
    }

    public function consultation($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return abort(404, 'Khách hàng không tồn tại.');
        }

        $result = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'classification' => $customer->classification->name,
            'updated_at' => $customer->updated_at,
            'consultations' => $customer->consultations->map(function($cons) {
                return [
                    "id" => $cons->id,
                    "name" => $cons->name,
                    "created_at" => $cons->created_at,
                    "logs" => $cons->consultation_logs->map(function ($log) {
                        return [
                            "id" => $log->id,
                            "message" => $log->message,
                            "status" => $log->status,
                            "created_at" => $log->created_at,
                        ];
                    })
                ];
            })
        ];
        return view("dashboard.customer.support.consultation", ['details' => $result]);
    }

    public function data(Request $request) {
        $currentPage = $request->input('page', 1);

        $customersQuery = Customer::query()
            ->filterMyCustomer($request['filter']['my_customer'] ?? 0)
            ->filterBlackList($request['filter']['black_list'] ?? 1)
            ->filterByServices($request['filter']['services'] ?? '')
            ->filterByStatus($request['filter']['status_id'] ?? 0)
            ->filterByClass($request['filter']['class_id'] ?? 0)
            ->filterByType($request['filter']['lead'] ?? 1)
            ->search($request['filter']['search'] ?? '');

        $paginationResult = PaginationService::paginate($customersQuery, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($customer, $key) use ($offset) {
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

    public function consultationCreate(Request $request) {
        $customer = Customer::find($request['id']);
        if (!$customer) {
            return abort(404, 'Khách hàng không tồn tại.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        try {
            $validated['customer_id'] = $customer->id;
            Consultation::create($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Lưu thành công!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => $th,
            ]);
        }
    }

    public function consultationAddLog(Request $request) {
        $consultation = Consultation::find($request['consultation_id']);
        if (!$consultation) {
            return abort(404, 'Không tồn tại.');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'consultation_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        try {
            ConsultationLog::create($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Lưu thành công!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => $th,
            ]);
        }
    }
}
