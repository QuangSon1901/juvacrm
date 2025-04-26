<?php

namespace App\Http\Controllers\Dashboard\Account\Salary;

use App\Http\Controllers\Controller;
use App\Models\SalaryAdvance;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalaryAdvanceController extends Controller
{
    public function index()
    {
        $users = User::where('is_active', 1)->get();
        return view('dashboard.account.salary.advance', compact('users'));
    }
    
    public function advanceData(Request $request)
    {
        $currentPage = $request->input('page', 1);
        
        $query = SalaryAdvance::with(['user', 'approver', 'transaction'])
            ->when($request->input('filter.user_id'), function($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->has('filter.status') && $request->input('filter.status') !== null, function($query) use ($request) {
                $query->where('status', $request->input('filter.status'));
            })
            ->when($request->input('filter.date_from'), function($query, $dateFrom) {
                $query->whereDate('request_date', '>=', Carbon::createFromFormat('d-m-Y', $dateFrom)->format('Y-m-d'));
            })
            ->when($request->input('filter.date_to'), function($query, $dateTo) {
                $query->whereDate('request_date', '<=', Carbon::createFromFormat('d-m-Y', $dateTo)->format('Y-m-d'));
            })
            ->orderBy('request_date', 'desc');
        
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];
        
        $result = $paginationResult['data']->map(function($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'user' => [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                ],
                'amount' => number_format($item->amount, 0, ',', '.'),
                'request_date' => formatDateTime($item->request_date, 'd/m/Y'),
                'reason' => $item->reason,
                'status' => $item->status,
                'status_text' => $this->getStatusText($item->status),
                'approver' => $item->approver ? [
                    'id' => $item->approver->id,
                    'name' => $item->approver->name,
                ] : null,
                'approval_date' => $item->approval_date ? formatDateTime($item->approval_date, 'd/m/Y') : '-',
                'transaction' => $item->transaction ? [
                    'id' => $item->transaction->id,
                    'paid_date' => formatDateTime($item->transaction->paid_date, 'd/m/Y'),
                ] : null,
                'note' => $item->note,
            ];
        });
        
        return response()->json([
            'status' => 200,
            'content' => view('dashboard.account.salary.ajax-advance', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }
    
    public function createAdvance(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'user_id' => 'required|exists:tbl_users,id',
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date_format:d-m-Y',
            'reason' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $requestDate = Carbon::createFromFormat('d-m-Y', $request->request_date)->format('Y-m-d');
        
        // Kiểm tra nếu người dùng đã có yêu cầu tạm ứng trong tháng
        $existingRequest = SalaryAdvance::where('user_id', $request->user_id)
                                       ->whereMonth('request_date', Carbon::parse($requestDate)->month)
                                       ->whereYear('request_date', Carbon::parse($requestDate)->year)
                                       ->whereIn('status', ['pending', 'approved', 'paid'])
                                       ->first();
        
        if ($existingRequest) {
            return response()->json([
                'status' => 422,
                'message' => 'Nhân viên này đã có yêu cầu tạm ứng trong tháng ' . Carbon::parse($requestDate)->format('m/Y'),
            ]);
        }
        
        $advance = SalaryAdvance::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'request_date' => $requestDate,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        
        LogService::saveLog([
            'action' => 'CREATE_SALARY_ADVANCE',
            'ip' => $request->getClientIp(),
            'details' => "Tạo yêu cầu tạm ứng lương cho nhân viên #" . $request->user_id,
            'fk_key' => 'tbl_salary_advances|id',
            'fk_value' => $advance->id,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Tạo yêu cầu tạm ứng lương thành công',
        ]);
    }
    
    public function updateAdvanceStatus(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_salary_advances,id',
            'status' => 'required|in:approved,rejected,paid',
            'note' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        DB::beginTransaction();
        try {
            $advance = SalaryAdvance::findOrFail($request->id);
            
            // Kiểm tra trạng thái hiện tại
            if ($advance->status === 'rejected') {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không thể cập nhật yêu cầu đã bị từ chối',
                ]);
            }
            
            if ($advance->status === 'paid') {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không thể cập nhật yêu cầu đã được chi trả',
                ]);
            }
            
            // Nếu đánh dấu là đã chi
            if ($request->status === 'paid') {
                // Kiểm tra trạng thái hiện tại phải là approved
                if ($advance->status !== 'approved') {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Chỉ có thể chi trả cho yêu cầu đã được duyệt',
                    ]);
                }
                
                // Tạo phiếu chi
                $category = TransactionCategory::firstOrCreate(
                    ['type' => 1, 'name' => 'Tạm ứng lương'],
                    ['is_active' => 1]
                );
                
                $transaction = Transaction::create([
                    'type' => 1, // Chi
                    'category_id' => $category->id,
                    'target_employee_id' => $advance->user_id,
                    'amount' => $advance->amount,
                    'paid_date' => Carbon::now(),
                    'status' => 1, // Hoàn tất
                    'reason' => "Tạm ứng lương cho " . $advance->user->name,
                    'note' => "Tự động tạo từ yêu cầu tạm ứng #" . $advance->id,
                ]);
                
                // Cập nhật yêu cầu tạm ứng
                $advance->update([
                    'status' => 'paid',
                    'note' => $request->note,
                    'transaction_id' => $transaction->id,
                ]);
            } else {
                // Cập nhật trạng thái
                $advance->update([
                    'status' => $request->status,
                    'note' => $request->note,
                    'approver_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
                    'approval_date' => Carbon::now()->format('Y-m-d'),
                ]);
            }
            
            LogService::saveLog([
                'action' => 'UPDATE_SALARY_ADVANCE_STATUS',
                'ip' => $request->getClientIp(),
                'details' => "Cập nhật trạng thái yêu cầu tạm ứng #" . $advance->id . " thành " . $request->status,
                'fk_key' => 'tbl_salary_advances|id',
                'fk_value' => $advance->id,
            ]);
            
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật trạng thái yêu cầu tạm ứng thành công',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
    
    private function getStatusText($status)
    {
        switch ($status) {
            case 'pending':
                return 'Chờ duyệt';
            case 'approved':
                return 'Đã duyệt';
            case 'rejected':
                return 'Từ chối';
            case 'paid':
                return 'Đã chi';
            default:
                return ucfirst($status);
        }
    }
}