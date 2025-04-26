<?php

namespace App\Http\Controllers\Dashboard\Accounting\Commissions;

use App\Http\Controllers\Controller;
use App\Models\ContractCommission;
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

class CommissionController extends Controller
{
    public function report()
    {
        $users = User::where('is_active', 1)->get();
        return view('dashboard.accounting.commissions.report', compact('users'));
    }
    
    public function myCommission()
    {
        $userId = Session::get(ACCOUNT_CURRENT_SESSION)['id'];
        $commissions = ContractCommission::with(['contract', 'transaction'])
                                        ->where('user_id', $userId)
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10);
        
        return view('dashboard.profile.my-commission', compact('commissions'));
    }
    
    public function reportData(Request $request)
    {
        $currentPage = $request->input('page', 1);
        
        $query = ContractCommission::with(['contract', 'user', 'transaction'])
            ->when($request->input('filter.user_id'), function($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->has('filter.is_paid') && $request->input('filter.is_paid') !== null, function($query) use ($request) {
                $query->where('is_paid', $request->input('filter.is_paid'));
            })
            ->when($request->input('filter.date_from'), function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', Carbon::createFromFormat('d-m-Y', $dateFrom)->format('Y-m-d'));
            })
            ->when($request->input('filter.date_to'), function($query, $dateTo) {
                $query->whereDate('created_at', '<=', Carbon::createFromFormat('d-m-Y', $dateTo)->format('Y-m-d'));
            })
            ->orderBy('created_at', 'desc');
        
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
                'contract' => [
                    'id' => $item->contract->id,
                    'number' => $item->contract->contract_number,
                    'name' => $item->contract->name,
                ],
                'commission_percentage' => $item->commission_percentage . '%',
                'contract_value' => number_format($item->contract_value, 0, ',', '.'),
                'commission_amount' => number_format($item->commission_amount, 0, ',', '.'),
                'is_paid' => $item->is_paid,
                'status_text' => $item->is_paid ? 'Đã chi' : 'Chưa chi',
                'processed_at' => $item->processed_at ? formatDateTime($item->processed_at, 'd/m/Y H:i') : '-',
                'transaction' => $item->transaction ? [
                    'id' => $item->transaction->id,
                    'paid_date' => formatDateTime($item->transaction->paid_date, 'd/m/Y'),
                ] : null,
            ];
        });
        
        // Tính tổng hoa hồng
        $totalCommission = $paginationResult['data']->sum('commission_amount');
        $pendingCommission = $paginationResult['data']->where('is_paid', 0)->sum('commission_amount');
        
        return response()->json([
            'status' => 200,
            'content' => view('dashboard.accounting.commissions.ajax-report', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
            'summary' => [
                'total_commission' => number_format($totalCommission, 0, ',', '.'),
                'pending_commission' => number_format($pendingCommission, 0, ',', '.'),
            ],
        ]);
    }
    
    public function payCommission(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_contract_commissions,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        DB::beginTransaction();
        try {
            $commission = ContractCommission::findOrFail($request->id);
            
            // Kiểm tra nếu đã thanh toán
            if ($commission->is_paid) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Hoa hồng này đã được thanh toán',
                ]);
            }
            
            // Tạo phiếu chi
            $category = TransactionCategory::firstOrCreate(
                ['type' => 1, 'name' => 'Chi hoa hồng'],
                ['is_active' => 1]
            );
            
            $transaction = Transaction::create([
                'type' => 1, // Chi
                'category_id' => $category->id,
                'target_employee_id' => $commission->user_id,
                'amount' => $commission->commission_amount,
                'paid_date' => Carbon::now(),
                'status' => 1, // Hoàn tất
                'reason' => "Chi hoa hồng hợp đồng #" . $commission->contract->contract_number . " cho " . $commission->user->name,
                'note' => "Tự động tạo từ hoa hồng #" . $commission->id,
            ]);
            
            // Cập nhật hoa hồng
            $commission->update([
                'is_paid' => 1,
                'transaction_id' => $transaction->id,
            ]);
            
            LogService::saveLog([
                'action' => 'PAY_COMMISSION',
                'ip' => $request->getClientIp(),
                'details' => "Thanh toán hoa hồng #" . $commission->id,
                'fk_key' => 'tbl_contract_commissions|id',
                'fk_value' => $commission->id,
            ]);
            
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Thanh toán hoa hồng thành công',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
    
    public function bulkPayCommission(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'user_id' => 'required|exists:tbl_users,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        DB::beginTransaction();
        try {
            $pendingCommissions = ContractCommission::with('contract')
                                                  ->where('user_id', $request->user_id)
                                                  ->where('is_paid', 0)
                                                  ->get();
            
            if ($pendingCommissions->isEmpty()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không có hoa hồng chưa thanh toán cho nhân viên này',
                ]);
            }
            
            $totalAmount = $pendingCommissions->sum('commission_amount');
            $user = User::find($request->user_id);
            
            // Tạo phiếu chi tổng
            $category = TransactionCategory::firstOrCreate(
                ['type' => 1, 'name' => 'Chi hoa hồng'],
                ['is_active' => 1]
            );
            
            $transaction = Transaction::create([
                'type' => 1, // Chi
                'category_id' => $category->id,
                'target_employee_id' => $request->user_id,
                'amount' => $totalAmount,
                'paid_date' => Carbon::now(),
                'status' => 1, // Hoàn tất
                'reason' => "Chi hoa hồng tổng hợp cho " . $user->name,
                'note' => "Thanh toán " . $pendingCommissions->count() . " hoa hồng với tổng giá trị " . number_format($totalAmount, 0, ',', '.') . " VNĐ",
            ]);
            
            // Cập nhật từng hoa hồng
            foreach ($pendingCommissions as $commission) {
                $commission->update([
                    'is_paid' => 1,
                    'transaction_id' => $transaction->id,
                ]);
            }
            
            LogService::saveLog([
                'action' => 'BULK_PAY_COMMISSION',
                'ip' => $request->getClientIp(),
                'details' => "Thanh toán hàng loạt " . $pendingCommissions->count() . " hoa hồng cho nhân viên #" . $request->user_id,
                'fk_key' => 'tbl_transactions|id',
                'fk_value' => $transaction->id,
            ]);
            
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Thanh toán hàng loạt hoa hồng thành công',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }
}