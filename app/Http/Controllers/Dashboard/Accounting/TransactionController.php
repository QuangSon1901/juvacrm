<?php

namespace App\Http\Controllers\Dashboard\Accounting\Transaction;

use App\Http\Controllers\Controller;
use App\Models\ContractPayment;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    public function index()
    {
        $categories = TransactionCategory::where('is_active', 1)->get();
        $customers = Customer::select('id', 'name')->where('is_active', 1)->get();
        $employees = User::select('id', 'name')->where('is_active', 1)->get();
        
        return view("dashboard.accounting.transaction.index", compact(
            'categories', 'customers', 'employees'
        ));
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        // Xây dựng query cơ bản
        $query = Transaction::with(['category', 'targetClient', 'targetEmployee', 'payment'])
            ->when($request->input('filter.search'), function ($query, $search) {
                $query->where('reason', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%");
            })
            ->when($request->has('filter.type') && $request->input('filter.type') !== null, function ($query) use ($request) {
                $query->where('type', $request->input('filter.type'));
            })
            ->when($request->has('filter.category_id') && $request->input('filter.category_id') !== null, function ($query) use ($request) {
                $query->where('category_id', $request->input('filter.category_id'));
            })
            ->when($request->has('filter.status') && $request->input('filter.status') !== null, function ($query) use ($request) {
                $query->where('status', $request->input('filter.status'));
            })
            ->when($request->has('filter.target_client_id') && $request->input('filter.target_client_id') !== null, function ($query) use ($request) {
                $query->where('target_client_id', $request->input('filter.target_client_id'));
            })
            ->when($request->has('filter.target_employee_id') && $request->input('filter.target_employee_id') !== null, function ($query) use ($request) {
                $query->where('target_employee_id', $request->input('filter.target_employee_id'));
            })
            ->when($request->has('filter.date_from') && $request->input('filter.date_from') !== null, function ($query) use ($request) {
                $query->whereDate('paid_date', '>=', formatDateTime($request->input('filter.date_from'), 'Y-m-d', 'd-m-Y'));
            })
            ->when($request->has('filter.date_to') && $request->input('filter.date_to') !== null, function ($query) use ($request) {
                $query->whereDate('paid_date', '<=', formatDateTime($request->input('filter.date_to'), 'Y-m-d', 'd-m-Y'));
            });

        // Phân trang
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        // Format dữ liệu trả về
        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'type' => $item->type,
                'type_text' => $item->type == 0 ? 'Thu' : 'Chi',
                'category' => [
                    'id' => $item->category->id ?? 0,
                    'name' => $item->category->name ?? 'N/A',
                ],
                'target' => $this->formatTarget($item),
                'amount' => $item->amount,
                'paid_date' => $item->paid_date,
                'paid_date_formatted' => $item->paid_date ? formatDateTime($item->paid_date, 'd/m/Y') : 'N/A',
                'reason' => $item->reason,
                'note' => $item->note,
                'status' => $item->status,
                'status_text' => $this->getStatusText($item->status),
                'created_at' => $item->created_at,
                'created_at_formatted' => formatDateTime($item->created_at, 'd/m/Y H:i'),
                'payment_id' => $item->payment_id,
            ];
        });

        // Tính tổng thu chi
        $totalIncome = $paginationResult['data']->where('type', 0)->where('status', 1)->sum('amount');
        $totalExpense = $paginationResult['data']->where('type', 1)->where('status', 1)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.accounting.transaction.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'balance' => $balance,
            ],
        ]);
    }

    /**
     * Tạo giao dịch mới
     */
    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'type' => 'required|in:0,1',
            'category_id' => 'required|exists:tbl_transaction_categories,id',
            'target_type' => 'required|in:client,employee,other',
            'target_client_id' => 'required_if:target_type,client|nullable|exists:tbl_customers,id',
            'target_employee_id' => 'required_if:target_type,employee|nullable|exists:tbl_users,id',
            'target_other' => 'required_if:target_type,other|nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'paid_date' => 'required|date_format:d-m-Y H:i:s',
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            // Kiểm tra loại đối tượng và gán giá trị tương ứng
            $targetClientId = null;
            $targetEmployeeId = null;
            $targetOther = null;

            switch ($request->target_type) {
                case 'client':
                    $targetClientId = $request->target_client_id;
                    break;
                case 'employee':
                    $targetEmployeeId = $request->target_employee_id;
                    break;
                case 'other':
                    $targetOther = $request->target_other;
                    break;
            }

            // Tạo giao dịch
            $transaction = Transaction::create([
                'type' => $request->type,
                'category_id' => $request->category_id,
                'target_client_id' => $targetClientId,
                'target_employee_id' => $targetEmployeeId,
                'target_other' => $targetOther,
                'amount' => $request->amount,
                'paid_date' => formatDateTime($request->paid_date, 'Y-m-d H:i:s', 'd-m-Y H:i:s'),
                'reason' => $request->reason,
                'note' => $request->note,
                'status' => $request->status,
            ]);

            LogService::saveLog([
                'action' => 'CREATE_TRANSACTION',
                'ip' => $request->getClientIp(),
                'details' => "Đã tạo phiếu " . ($request->type == 0 ? 'thu' : 'chi') . ": " . $request->reason,
                'fk_key' => 'tbl_transactions|id',
                'fk_value' => $transaction->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Tạo phiếu ' . ($request->type == 0 ? 'thu' : 'chi') . ' thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Cập nhật giao dịch
     */
    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_transactions,id',
            'type' => 'nullable|in:0,1',
            'category_id' => 'nullable|exists:tbl_transaction_categories,id',
            'target_type' => 'nullable|in:client,employee,other',
            'target_client_id' => 'nullable|exists:tbl_customers,id',
            'target_employee_id' => 'nullable|exists:tbl_users,id',
            'target_other' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'paid_date' => 'nullable|date_format:d-m-Y H:i:s',
            'reason' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $transaction = Transaction::findOrFail($request->id);
            
            // Kiểm tra nếu giao dịch đã liên kết với biên nhận, không cho phép thay đổi loại và số tiền
            if ($transaction->payment_id && ($request->has('type') || $request->has('amount'))) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không thể thay đổi loại giao dịch hoặc số tiền của phiếu đã liên kết với biên nhận',
                ]);
            }

            // Chuẩn bị dữ liệu cập nhật
            $data = array_filter($request->only([
                'type', 'category_id', 'amount', 'reason', 'note', 'status'
            ]), function ($value) {
                return $value !== null;
            });

            // Xử lý ngày thanh toán
            if ($request->filled('paid_date')) {
                $data['paid_date'] = formatDateTime($request->paid_date, 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            // Xử lý đối tượng giao dịch
            if ($request->filled('target_type')) {
                $data['target_client_id'] = null;
                $data['target_employee_id'] = null;
                $data['target_other'] = null;

                switch ($request->target_type) {
                    case 'client':
                        $data['target_client_id'] = $request->target_client_id;
                        break;
                    case 'employee':
                        $data['target_employee_id'] = $request->target_employee_id;
                        break;
                    case 'other':
                        $data['target_other'] = $request->target_other;
                        break;
                }
            }

            $transaction->update($data);

            LogService::saveLog([
                'action' => 'UPDATE_TRANSACTION',
                'ip' => $request->getClientIp(),
                'details' => "Đã cập nhật phiếu " . ($transaction->type == 0 ? 'thu' : 'chi') . " #" . $transaction->id,
                'fk_key' => 'tbl_transactions|id',
                'fk_value' => $transaction->id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật phiếu ' . ($transaction->type == 0 ? 'thu' : 'chi') . ' thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Hủy giao dịch
     */
    public function cancel(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($request->id);
            
            // Kiểm tra nếu giao dịch đã liên kết với biên nhận, không cho phép hủy
            if ($transaction->payment_id) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không thể hủy phiếu đã liên kết với biên nhận. Vui lòng hủy biên nhận trước.',
                ]);
            }

            $transaction->update(['status' => 2]); // Đánh dấu là đã hủy

            LogService::saveLog([
                'action' => 'CANCEL_TRANSACTION',
                'ip' => $request->getClientIp(),
                'details' => "Đã hủy phiếu " . ($transaction->type == 0 ? 'thu' : 'chi') . " #" . $transaction->id,
                'fk_key' => 'tbl_transactions|id',
                'fk_value' => $transaction->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Hủy phiếu ' . ($transaction->type == 0 ? 'thu' : 'chi') . ' thành công',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Xuất phiếu thu chi dưới dạng PDF
     */
    public function exportTransactionReceipt($id)
    {
        try {
            // Lấy thông tin giao dịch
            $transaction = Transaction::with(['category', 'targetClient', 'targetEmployee'])->findOrFail($id);

            // Chuẩn bị dữ liệu cho PDF
            $data = [
                'transaction' => $transaction,
                'category' => $transaction->category,
                'target' => $this->formatTarget($transaction),
                'date_now' => date('d/m/Y'),
                'receipt_number' => ($transaction->type == 0 ? 'THU-' : 'CHI-') . $transaction->id . '-' . date('Ymd'),
                'type_text' => $transaction->type == 0 ? 'PHIẾU THU' : 'PHIẾU CHI',
                'status_text' => $this->getStatusText($transaction->status),
            ];

            // Tạo PDF
            $pdf = Pdf::loadView('dashboard.accounting.transaction.receipt_pdf', $data);
            $pdf->setPaper('A5', 'portrait');

            // Lưu log
            LogService::saveLog([
                'action' => 'EXPORT_TRANSACTION_RECEIPT',
                'ip' => request()->getClientIp(),
                'details' => "Đã xuất " . ($transaction->type == 0 ? 'phiếu thu' : 'phiếu chi') . " #" . $data['receipt_number'],
                'fk_key' => 'tbl_transactions|id',
                'fk_value' => $transaction->id,
            ]);

            // Tải xuống PDF
            return $pdf->download(($transaction->type == 0 ? "PhieuThu_" : "PhieuChi_") . $data['receipt_number'] . ".pdf");
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xuất phiếu: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Format thông tin đối tượng giao dịch
     */
    private function formatTarget($transaction)
    {
        if ($transaction->target_client_id) {
            return [
                'type' => 'client',
                'id' => $transaction->targetClient->id ?? 0,
                'name' => $transaction->targetClient->name ?? 'N/A',
                'display' => 'Khách hàng: ' . ($transaction->targetClient->name ?? 'N/A'),
            ];
        } elseif ($transaction->target_employee_id) {
            return [
                'type' => 'employee',
                'id' => $transaction->targetEmployee->id ?? 0,
                'name' => $transaction->targetEmployee->name ?? 'N/A',
                'display' => 'Nhân viên: ' . ($transaction->targetEmployee->name ?? 'N/A'),
            ];
        } elseif ($transaction->target_other) {
            return [
                'type' => 'other',
                'name' => $transaction->target_other,
                'display' => 'Đối tượng khác: ' . $transaction->target_other,
            ];
        } else {
            return [
                'type' => 'unknown',
                'display' => 'Không xác định',
            ];
        }
    }

    /**
     * Lấy text hiển thị cho trạng thái giao dịch
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Chờ xử lý';
            case 1:
                return 'Hoàn tất';
            case 2:
                return 'Đã hủy';
            default:
                return 'Không xác định';
        }
    }
}