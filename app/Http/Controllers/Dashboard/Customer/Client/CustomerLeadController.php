<?php

namespace App\Http\Controllers\Dashboard\Customer\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerClass;
use App\Models\CustomerLead;
use App\Models\Service;
use App\Services\LogService;
use App\Services\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLeadController extends Controller
{
    public function leads() {
        $services = Service::where('is_active', 1)->get();
        $classes = CustomerClass::where('is_active', 1)->orderBy('sort', 'asc')->get();
        $leads = CustomerLead::where('is_active', 1)->orderBy('sort', 'asc')->get();
        $sources = [];
        $contacts = [];
        $statuses = [];

        foreach ($leads as $lead) {
            switch ($lead->type) {
                case 0:
                    array_push($contacts, $lead);
                    break;
                case 1:
                    array_push($sources, $lead);
                    break;
                case 2:
                    array_push($statuses, $lead);
                    break;
            }
        }

        // Thêm thống kê tổng quan
        $leadStatistics = [
            'total' => Customer::where('type', Customer::TYPE_LEAD)->count(),
            'new_today' => Customer::where('type', Customer::TYPE_LEAD)
                ->whereDate('created_at', today())->count(),
            'high_potential' => Customer::where('type', Customer::TYPE_LEAD)
                ->where('lead_score', '>=', 60)->count(),
            'no_interaction' => Customer::where('type', Customer::TYPE_LEAD)
                ->whereNull('last_interaction_date')
                ->orWhere('last_interaction_date', '<', now()->subDays(30))
                ->count(),
        ];

        $conversionStats = [
            'lead_to_customer' => $this->calculateConversionRate(Customer::TYPE_LEAD, Customer::TYPE_CUSTOMER),
            'response_rate' => 75.4, // Giả lập dữ liệu, trong thực tế cần tính toán
        ];
        
        return view("dashboard.customer.client.leads", [
            'services' => $services,
            'classes' => $classes,
            'sources' => $sources,
            'contacts' => $contacts,
            'statuses' => $statuses,
            'statistics' => $leadStatistics,
            'conversion_stats' => $conversionStats
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
            ->filterByType(0)
            ->search($request['filter']['search'] ?? '');
            
        // Thêm lọc theo nguồn
        if (!empty($request['filter']['source_id'])) {
            $customersQuery->where('source_id', $request['filter']['source_id']);
        }
        
        // Thêm lọc theo điểm tiềm năng
        if (!empty($request['filter']['lead_score'])) {
            $score = (int)$request['filter']['lead_score'];
            switch ($score) {
                case 1: // Thấp
                    $customersQuery->whereBetween('lead_score', [1, 30]);
                    break;
                case 2: // Trung bình
                    $customersQuery->whereBetween('lead_score', [31, 60]);
                    break;
                case 3: // Cao
                    $customersQuery->where('lead_score', '>', 60);
                    break;
            }
        }
        
        // Thêm sắp xếp theo ngày tương tác hoặc điểm tiềm năng
        if (!empty($request['filter']['sort_by'])) {
            switch ($request['filter']['sort_by']) {
                case 'last_interaction':
                    $customersQuery->orderBy('last_interaction_date', 'desc');
                    break;
                case 'lead_score':
                    $customersQuery->orderBy('lead_score', 'desc');
                    break;
                case 'created_at':
                    $customersQuery->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $customersQuery->orderBy('updated_at', 'desc');
        }

        $paginationResult = PaginationService::paginate($customersQuery, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        $result = $paginationResult['data']->map(function ($customer, $key) use ($offset) {
            // Đảm bảo khách hàng có điểm tiềm năng
            if ($customer->lead_score === null) {
                $customer->calculateLeadScore();
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
                'source' => [
                    'id' => $customer->source->id ?? 0,
                    'name' => $customer->source->name ?? '',
                ],
                'staff' => [
                    'id' => $customer->user->id ?? 0,
                    'name' => $customer->user->name ?? '',
                ],
                'lead_score' => $customer->lead_score,
                'last_interaction' => $customer->last_interaction_date,
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
    
    // Thêm phương thức để chuyển đổi khách hàng tiềm năng thành khách hàng
    public function convertToProspect(Request $request)
    {
        $customerId = $request->input('id');
        $customer = Customer::find($customerId);
        
        if (!$customer) {
            return response()->json([
                'status' => 404,
                'message' => 'Khách hàng không tồn tại.'
            ]);
        }
        
        try {
            $customer->convertToProspect();
            
            LogService::saveLog([
                'action' => CUSTOMER_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => 'Chuyển đổi khách hàng tiềm năng #' . $customerId . ' thành khách hàng',
                'fk_key' => 'tbl_customers|id',
                'fk_value' => $customerId,
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Chuyển đổi khách hàng thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Có lỗi xảy ra khi chuyển đổi khách hàng.',
            ]);
        }
    }
    
    // Lấy thống kê về khách hàng tiềm năng
    public function getLeadStatistics()
    {
        $statistics = [
            'total' => Customer::where('type', Customer::TYPE_LEAD)->count(),
            'by_source' => DB::table('tbl_customers')
                ->select('tbl_customer_lead.name', DB::raw('COUNT(*) as count'))
                ->join('tbl_customer_lead', 'tbl_customers.source_id', '=', 'tbl_customer_lead.id')
                ->where('tbl_customers.type', Customer::TYPE_LEAD)
                ->groupBy('tbl_customer_lead.id', 'tbl_customer_lead.name')
                ->get(),
            'by_status' => DB::table('tbl_customers')
                ->select('tbl_customer_lead.name', DB::raw('COUNT(*) as count'))
                ->join('tbl_customer_lead', 'tbl_customers.status_id', '=', 'tbl_customer_lead.id')
                ->where('tbl_customers.type', Customer::TYPE_LEAD)
                ->where('tbl_customer_lead.type', 2) // type 2 = trạng thái
                ->groupBy('tbl_customer_lead.id', 'tbl_customer_lead.name')
                ->get(),
            'conversion_rate' => [
                'lead_to_prospect' => $this->calculateConversionRate(Customer::TYPE_LEAD, Customer::TYPE_PROSPECT),
                'prospect_to_customer' => $this->calculateConversionRate(Customer::TYPE_PROSPECT, Customer::TYPE_CUSTOMER),
            ]
        ];
        
        return response()->json([
            'status' => 200,
            'data' => $statistics
        ]);
    }
    
    private function calculateConversionRate($fromType, $toType)
    {
        $fromCount = Customer::where('type', $fromType)->count();
        if ($fromCount === 0) return 0;
        
        $toCount = Customer::where('type', $toType)->count();
        return round(($toCount / $fromCount) * 100, 2);
    }
}
