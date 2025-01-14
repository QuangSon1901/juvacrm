<?php

namespace App\Http\Controllers\Dashboard\Customer\Support;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationLog;
use App\Models\Customer;
use App\Models\Service;
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

    public function data(Request $request)
    {
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

        $result = $customers->get()->map(function ($customer) {
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

        return view("dashboard.customer.support.ajax-index", ['data' => $result]);
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
