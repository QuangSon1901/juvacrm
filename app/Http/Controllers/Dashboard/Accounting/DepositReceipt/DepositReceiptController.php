<?php

namespace App\Http\Controllers\Dashboard\Accounting\DepositReceipt;

use App\Http\Controllers\Controller;
use App\Models\ContractPayment;
use App\Services\LogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DepositReceiptController extends Controller
{
    public function index()
    {
        return view("dashboard.accounting.deposit_receipt.index");
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
}
