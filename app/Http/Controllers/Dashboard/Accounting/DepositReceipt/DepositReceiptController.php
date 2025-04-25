<?php

namespace App\Http\Controllers\Dashboard\Accounting\DepositReceipt;

use App\Http\Controllers\Controller;
use App\Models\ContractPayment;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Services\LogService;
use App\Services\PaginationService;
use App\Services\ValidatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DepositReceiptController extends Controller
{
    public function index()
    {
        $customers = Customer::select('id', 'name')->where('is_active', 1)->get();
        $methods = PaymentMethod::where('is_active', 1)->get();
        $currencies = Currency::where('is_active', 1)->get();
        
        return view("dashboard.accounting.deposit_receipt.index", compact(
            'customers', 'methods', 'currencies'
        ));
    }

    public function data(Request $request)
    {
        $currentPage = $request->input('page', 1);

        // Xây dựng query cơ bản
        $query = ContractPayment::with(['contract', 'contract.provider', 'method', 'currency', 'creator'])
            ->when($request->input('filter.search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('contract', function($q) use ($search) {
                        $q->where('contract_number', 'like', "%{$search}%");
                    });
            })
            ->when($request->has('filter.payment_stage') && $request->input('filter.payment_stage') !== null, function ($query) use ($request) {
                $query->where('payment_stage', $request->input('filter.payment_stage'));
            })
            ->when($request->has('filter.status') && $request->input('filter.status') !== null, function ($query) use ($request) {
                $query->where('status', $request->input('filter.status'));
            })
            ->when($request->has('filter.customer_id') && $request->input('filter.customer_id') !== null, function ($query) use ($request) {
                $query->whereHas('contract', function($q) use ($request) {
                    $q->where('provider_id', $request->input('filter.customer_id'));
                });
            })
            ->where('is_active', 1);

        // Phân trang
        $paginationResult = PaginationService::paginate($query, $currentPage, TABLE_PERPAGE_NUM);
        $offset = $paginationResult['sorter']['offset'];

        // Format dữ liệu trả về
        $result = $paginationResult['data']->map(function ($item, $key) use ($offset) {
            return [
                'index' => $offset + $key + 1,
                'id' => $item->id,
                'name' => $item->name,
                'contract' => [
                    'id' => $item->contract->id ?? 0,
                    'number' => $item->contract->contract_number ?? 'N/A',
                    'name' => $item->contract->name ?? 'N/A',
                ],
                'customer' => [
                    'id' => $item->contract->provider->id ?? 0,
                    'name' => $item->contract->provider->name ?? 'N/A',
                ],
                'price' => $item->price,
                'currency' => [
                    'id' => $item->currency->id ?? 0,
                    'code' => $item->currency->currency_code ?? 'VND',
                    'symbol' => $item->currency->symbol ?? '₫',
                ],
                'method' => [
                    'id' => $item->method->id ?? 0,
                    'name' => $item->method->name ?? 'N/A',
                ],
                'payment_stage' => $item->payment_stage,
                'payment_stage_text' => $this->getPaymentStageText($item->payment_stage),
                'status' => $item->status,
                'status_text' => $item->status == 1 ? 'Đã thanh toán' : 'Chưa thanh toán',
                'due_date' => $item->due_date,
                'due_date_formatted' => $item->due_date ? formatDateTime($item->due_date, 'd/m/Y') : 'N/A',
                'created_at' => $item->created_at,
                'created_at_formatted' => formatDateTime($item->created_at, 'd/m/Y H:i'),
                'creator' => [
                    'id' => $item->creator->id ?? 0,
                    'name' => $item->creator->name ?? 'N/A',
                ],
            ];
        });

        return response()->json([
            'status' => 200,
            'content' => view('dashboard.accounting.deposit_receipt.ajax-index', ['data' => $result])->render(),
            'sorter' => $paginationResult['sorter'],
        ]);
    }

    /**
     * Tạo biên nhận thanh toán mới
     */
    public function create(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'contract_id' => 'required|exists:tbl_contracts,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'currency_id' => 'required|exists:tbl_currencies,id',
            'method_id' => 'required|exists:tbl_payment_methods,id',
            'due_date' => 'required|date_format:d-m-Y H:i:s',
            'payment_stage' => 'required|in:0,1,2,3',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $contract = Contract::findOrFail($request->contract_id);
            
            $payment = ContractPayment::create([
                'contract_id' => $contract->id,
                'name' => $request->name,
                'price' => $request->price,
                'currency_id' => $request->currency_id,
                'method_id' => $request->method_id,
                'due_date' => formatDateTime($request->due_date, 'Y-m-d H:i:s', 'd-m-Y H:i:s'),
                'payment_stage' => $request->payment_stage,
                'status' => $request->status,
                'created_id' => Session::get(ACCOUNT_CURRENT_SESSION)['id'],
            ]);

            // Nếu trạng thái là đã thanh toán, tạo giao dịch tương ứng
            if ($request->status == 1) {
                $this->createTransaction($payment);
            }

            LogService::saveLog([
                'action' => 'CREATE_PAYMENT_RECEIPT',
                'ip' => $request->getClientIp(),
                'details' => "Đã tạo biên nhận thanh toán: " . $request->name . " cho hợp đồng #" . $contract->contract_number,
                'fk_key' => 'tbl_contract_payments|id',
                'fk_value' => $payment->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Tạo biên nhận thanh toán thành công',
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
     * Cập nhật biên nhận thanh toán
     */
    public function update(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_contract_payments,id',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'currency_id' => 'nullable|exists:tbl_currencies,id',
            'method_id' => 'nullable|exists:tbl_payment_methods,id',
            'due_date' => 'nullable|date_format:d-m-Y H:i:s',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $payment = ContractPayment::findOrFail($request->id);
            
            // Không cho phép chuyển từ đã thanh toán sang chưa thanh toán
            if ($payment->status == 1 && $request->status == 0) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không thể chuyển trạng thái từ đã thanh toán sang chưa thanh toán',
                ]);
            }

            $data = array_filter($request->only(['name', 'price', 'currency_id', 'method_id', 'due_date', 'status']), function ($value) {
                return $value !== null;
            });

            if (isset($data['due_date'])) {
                $data['due_date'] = formatDateTime($data['due_date'], 'Y-m-d H:i:s', 'd-m-Y H:i:s');
            }

            // Nếu cập nhật trạng thái từ chưa thanh toán sang đã thanh toán
            if (isset($data['status']) && $data['status'] == 1 && $payment->status == 0) {
                $payment->update($data);
                $this->createTransaction($payment);
            } else {
                $payment->update($data);
                
                // Nếu đã có giao dịch, cập nhật thông tin giao dịch
                if ($payment->status == 1) {
                    $transaction = Transaction::where('payment_id', $payment->id)->first();
                    if ($transaction) {
                        $transaction->update([
                            'amount' => $payment->price,
                            'reason' => $payment->name,
                            'paid_date' => $payment->due_date,
                        ]);
                    }
                }
            }

            LogService::saveLog([
                'action' => 'UPDATE_PAYMENT_RECEIPT',
                'ip' => $request->getClientIp(),
                'details' => "Đã cập nhật biên nhận thanh toán #" . $payment->id,
                'fk_key' => 'tbl_contract_payments|id',
                'fk_value' => $payment->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật biên nhận thanh toán thành công',
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
     * Xuất biên nhận thanh toán dưới dạng PDF
     * 
     * @param int $id ID của thanh toán
     * @return \Illuminate\Http\Response
     */
    public function exportPaymentReceipt($id)
    {
        try {
            // Lấy thông tin thanh toán với các quan hệ cần thiết
            $payment = ContractPayment::with([
                'contract',
                'contract.user',
                'contract.provider',
                'method',
                'currency',
                'creator' => function ($query) {
                    $query->select('id', 'name');
                }
            ])->findOrFail($id);

            // Chuẩn bị dữ liệu cho PDF
            $data = [
                'payment' => $payment,
                'contract' => $payment->contract,
                'provider' => $payment->contract->provider,
                'method' => $payment->method,
                'currency' => $payment->currency,
                'date_now' => date('d/m/Y'),
                'receipt_number' => 'RECEIPT-' . $payment->id . '-' . date('Ymd'),
                'payment_stage' => $this->getPaymentStageText($payment->payment_stage),
            ];

            // Tạo PDF với mẫu biên nhận thanh toán
            $pdf = Pdf::loadView('dashboard.accounting.deposit_receipt.receipt_pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            // Lưu log
            LogService::saveLog([
                'action' => 'EXPORT_PAYMENT_RECEIPT',
                'ip' => request()->getClientIp(),
                'details' => "Đã xuất biên nhận thanh toán #" . $data['receipt_number'],
                'fk_key' => 'tbl_contract_payments|id',
                'fk_value' => $payment->id,
            ]);

            // Tải xuống PDF
            return $pdf->download("BienNhan_{$data['receipt_number']}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xuất biên nhận thanh toán: ' . $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Hủy biên nhận thanh toán
     */
    public function cancel(Request $request)
    {
        $validator = ValidatorService::make($request, [
            'id' => 'required|exists:tbl_contract_payments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }

        DB::beginTransaction();
        try {
            $payment = ContractPayment::findOrFail($request->id);
            
            // Không cho phép hủy biên nhận đã thanh toán
            if ($payment->status == 1) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Không thể hủy biên nhận đã thanh toán',
                ]);
            }

            $payment->update(['is_active' => 0]);

            LogService::saveLog([
                'action' => 'CANCEL_PAYMENT_RECEIPT',
                'ip' => $request->getClientIp(),
                'details' => "Đã hủy biên nhận thanh toán #" . $payment->id,
                'fk_key' => 'tbl_contract_payments|id',
                'fk_value' => $payment->id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Hủy biên nhận thanh toán thành công',
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
     * Lấy text hiển thị cho giai đoạn thanh toán
     * 
     * @param int $stage Mã giai đoạn thanh toán
     * @return string Text hiển thị
     */
    private function getPaymentStageText($stage)
    {
        switch ($stage) {
            case 0:
                return 'Đặt cọc';
            case 1:
                return 'Tiền thưởng';
            case 2:
                return 'Thanh toán cuối cùng';
            case 3:
                return 'Trừ tiền';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Tạo giao dịch từ biên nhận thanh toán
     */
    private function createTransaction($payment)
    {
        $transactionType = $payment->payment_stage == 3 ? 1 : 0; // Trừ tiền là chi (1), còn lại là thu (0)
        $categoryName = $this->getPaymentStageText($payment->payment_stage);

        // Tìm hoặc tạo danh mục thu chi tương ứng
        $category = TransactionCategory::firstOrCreate(
            ['type' => $transactionType, 'name' => $categoryName],
            ['note' => "Hạng mục cho {$categoryName}", 'is_active' => 1]
        );

        // Tạo giao dịch
        Transaction::create([
            'type' => $transactionType,
            'category_id' => $category->id,
            'target_client_id' => $payment->contract->provider_id,
            'payment_id' => $payment->id,
            'amount' => $payment->price,
            'paid_date' => $payment->due_date ?? date('Y-m-d H:i:s'),
            'status' => 1, // Hoàn tất
            'note' => "Tự động tạo từ biên nhận #{$payment->id} của hợp đồng #{$payment->contract->contract_number}",
            'reason' => $payment->name,
        ]);
    }
}