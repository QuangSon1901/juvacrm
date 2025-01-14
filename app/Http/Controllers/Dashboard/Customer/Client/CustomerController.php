<?php

namespace App\Http\Controllers\Dashboard\Customer\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerClass;
use App\Models\CustomerLead;
use App\Models\Service;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function detail($id) {
        $customer = Customer::find($id);
        if (!$customer) {
            return abort(404, 'Khách hàng không tồn tại.');
        }

        $result = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'note' => $customer->note,
            'services' => $customer->getServicesArray(),
            'company' => $customer->company,
            'status' => $customer->status,
            'classification' => $customer->classification,
            'source' => $customer->source,
            'staff' => $customer->user,
            'updated_at' => $customer->updated_at,
            'contacts' => $customer->getContactsArray(),
        ];

        return view("dashboard.customer.client.detail", ['details' => $result]);
    }

    public function createView() {
        $services = Service::where('is_active', 1)->get();
        $classes = CustomerClass::where('is_active', 1)->get();
        $leads = CustomerLead::where('is_active', 1)->get();
        $sources = [];
        $contacts = [];
        $status = [];

        foreach ($leads as $lead) {
            switch($lead->type) {
                case 0:
                    array_push($contacts, $lead);
                    break;
                case 1:
                    array_push($sources, $lead);
                    break;
                case 2:
                    array_push($status, $lead);
                    break;
            }
        }

        return view("dashboard.customer.client.create", [
            'services' => $services,
            'classes' => $classes,
            'sources' => $sources,
            'contacts' => $contacts,
            'status' => $status,
        ]);
    }

    public function create(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:tbl_customers,email',
            'address' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'type' => 'nullable|integer',
            'source_id' => 'nullable|integer|exists:tbl_customer_lead,id',
            'services' => 'nullable', // Dữ liệu dạng chuỗi, ví dụ: "1|2|3"
            'class_id' => 'nullable|integer|exists:tbl_customer_class,id',
            'status_id' => 'nullable|integer|exists:tbl_customer_lead,id',
            'contacts' => 'nullable',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            $validated['contact_methods'] = implode("|", $validated['contacts'] ?? []);
            $validated['services'] = implode("|", $validated['services'] ?? []);
            Customer::create($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Khách hàng đã được lưu thành công!',
                'tst' => $request['service']
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => $th,
            ]);
        }
    }

    public function leads() {
        
        return view("dashboard.customer.client.leads");
    }
}
