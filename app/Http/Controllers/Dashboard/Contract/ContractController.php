<?php

namespace App\Http\Controllers\Dashboard\Contract;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index() {
        return view("dashboard.contract.index");
    }

    public function createView(Request $request) {
        $users = User::select('id', 'name')->where('is_active', 1)->get()->toArray();
        $customers = Customer::select('id', 'name', 'phone', 'email', 'address')->where('is_active', 1)->get()->toArray();
        $categories = ServiceCategory::where('is_active', 1)->get()->toArray();
        $services = Service::where('is_active', 1)->get()->toArray();
        $payments = PaymentMethod::where('is_active', 1)->get()->toArray();
        $currencies = Currency::where('is_active', 1)->get()->toArray();

        $details = [
            'users' => $users,
            'customers' => $customers,
            'categories' => $categories,
            'services' => $services,
            'payments' => $payments,
            'currencies' => $currencies,
        ];

        if (isset($request['customer_id'])) {
            $customer = Customer::select('id', 'name', 'phone', 'email', 'address')->find($request['customer_id']);
            if ($customer) {
                $details['customer'] = $customer;
            }
        }

        return view("dashboard.contract.create", ['details' => $details]);
    }

    public function create(Request $request) {
        dd($request);
    }

    public function detail() {
        return view("dashboard.contract.detail");
    }
}
