<?php

namespace App\Http\Controllers\Dashboard\Customer\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\PaginationService;
use Illuminate\Http\Request;

class CustomerLeadController extends Controller
{
    public function leads() {
        return view("dashboard.customer.client.leads");
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
            'content' => view('dashboard.customer.client.ajax-leads', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }
}
