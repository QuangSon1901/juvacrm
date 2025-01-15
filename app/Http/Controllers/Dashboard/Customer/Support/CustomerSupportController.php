<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationLog;
use App\Models\Customer;
use App\Models\Service;
use App\Services\PaginationService;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{
    public function index()
    {
        return view("dashboard.customer.support.index");
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
        $search = $request->input('search', '');
        $type = $request->input('lead') ? 'lead' : 'non-lead';

        $customersQuery = Customer::query()
            ->filterByType($type)
            ->search($search);

        $paginationResult = PaginationService::paginate($customersQuery, $currentPage, TABLE_PERPAGE_NUM);

        $result = $paginationResult['data']->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'services' => $customer->getServicesArray(),
                'company' => $customer->company,
                'status' => $customer->status,
                'classification' => $customer->classification,
                'source' => $customer->source,
                'staff' => $customer->user,
                'updated_at' => $customer->updated_at,
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.customer.support.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    public function dataOld(Request $request)
    {
        $currentPage = isset($request['page']) && is_numeric($request['page']) ? (int)$request['page'] - 1 : 0;
        $offset = $currentPage * TABLE_PERPAGE_NUM;

        $search = $request->input('search', '');

        if ($request['lead']) {
            $customers = Customer::where('type', 0);
        } else {
            $customers = Customer::where('type', '!=', 0);
        }

        if (!empty($search)) {
            $customers->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $totalRecord = $customers->count();

        $result = $customers->offset($offset)->limit(TABLE_PERPAGE_NUM)->get()->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'services' => $customer->getServicesArray(),
                'company' => $customer->company,
                'status' => $customer->status,
                'classification' => $customer->classification,
                'source' => $customer->source,
                'staff' => $customer->user,
                'updated_at' => $customer->updated_at
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view("dashboard.customer.support.ajax-index", ['data' => $result])->render(),
            'sorter' => [
                'perpage' => TABLE_PERPAGE_NUM,
                'totalpages' => (int)ceil($totalRecord / TABLE_PERPAGE_NUM),
                'sorterpage' => $currentPage + 1,
                'sorterrecords' => $totalRecord,
            ]
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
