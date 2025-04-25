<?php

namespace App\Http\Controllers\Dashboard\Accounting\Report;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index()
    {
        $categories = TransactionCategory::where('is_active', 1)->get();
        $customers = Customer::select('id', 'name')->where('is_active', 1)->get();
        $employees = User::select('id', 'name')->where('is_active', 1)->get();
        
        return view("dashboard.accounting.report.index", compact(
            'categories', 'customers', 'employees'
        ));
    }

    public function getFinancialReport(Request $request)
{
    // Xử lý date_range
    $dateRange = $request->input('date_range');
    if ($dateRange) {
        $now = Carbon::now();
        switch ($dateRange) {
            case 'this_month':
                $dateFrom = $now->copy()->startOfMonth();
                $dateTo = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $dateFrom = $now->copy()->subMonth()->startOfMonth();
                $dateTo = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_quarter':
                $dateFrom = $now->copy()->startOfQuarter();
                $dateTo = $now->copy()->endOfQuarter();
                break;
            case 'last_quarter':
                $dateFrom = $now->copy()->subQuarter()->startOfQuarter();
                $dateTo = $now->copy()->subQuarter()->endOfQuarter();
                break;
            case 'this_year':
                $dateFrom = $now->copy()->startOfYear();
                $dateTo = $now->copy()->endOfYear();
                break;
            case 'last_year':
                $dateFrom = $now->copy()->subYear()->startOfYear();
                $dateTo = $now->copy()->subYear()->endOfYear();
                break;
            case 'custom':
                // Sử dụng date_from và date_to được cung cấp
                $dateFrom = $request->input('date_from') ? 
                    Carbon::createFromFormat('d-m-Y', $request->input('date_from'))->startOfDay() : 
                    Carbon::now()->startOfMonth();
                
                $dateTo = $request->input('date_to') ? 
                    Carbon::createFromFormat('d-m-Y', $request->input('date_to'))->endOfDay() : 
                    Carbon::now()->endOfDay();
                break;
            default:
                $dateFrom = Carbon::now()->startOfMonth();
                $dateTo = Carbon::now()->endOfDay();
                break;
        }
    } else {
        // Xử lý như cũ nếu không có date_range
        $dateFrom = $request->input('date_from') ? 
            Carbon::createFromFormat('d-m-Y', $request->input('date_from'))->startOfDay() : 
            Carbon::now()->startOfMonth();
        
        $dateTo = $request->input('date_to') ? 
            Carbon::createFromFormat('d-m-Y', $request->input('date_to'))->endOfDay() : 
            Carbon::now()->endOfDay();
    }

    $groupBy = $request->input('group_by', 'day');
    $categoryId = $request->input('category_id');
    $targetType = $request->input('target_type');
    
    // Xác định target_id dựa trên target_type
    $targetId = null;
    if ($targetType) {
        if ($targetType === 'client') {
            $targetId = $request->input('target_client_id');
        } elseif ($targetType === 'employee') {
            $targetId = $request->input('target_employee_id');
        }
    }
    
    // Xây dựng query cơ bản
    $query = Transaction::with(['category', 'targetClient', 'targetEmployee'])
        ->whereBetween('paid_date', [$dateFrom, $dateTo])
        ->where('status', 1); // Chỉ lấy các giao dịch đã hoàn tất
    
    // Áp dụng các filter nếu có
    if ($categoryId) {
        $query->where('category_id', $categoryId);
    }
    
    if ($targetType && $targetId) {
        if ($targetType === 'client') {
            $query->where('target_client_id', $targetId);
        } elseif ($targetType === 'employee') {
            $query->where('target_employee_id', $targetId);
        } elseif ($targetType === 'other') {
            $query->whereNotNull('target_other');
        }
    }
    
    // Lấy dữ liệu giao dịch
    $transactions = $query->get();
    
    // Tính toán thu chi theo thời gian
    $timeSeriesData = $this->generateTimeSeriesData($transactions, $dateFrom, $dateTo, $groupBy);
    
    // Tính toán thu chi theo danh mục
    $categoryData = $this->generateCategoryData($transactions);
    
    // Tính toán tổng quan
    $summary = [
        'total_income' => $transactions->where('type', 0)->sum('amount'),
        'total_expense' => $transactions->where('type', 1)->sum('amount'),
        'balance' => $transactions->where('type', 0)->sum('amount') - $transactions->where('type', 1)->sum('amount'),
        'transaction_count' => $transactions->count(),
        'income_count' => $transactions->where('type', 0)->count(),
        'expense_count' => $transactions->where('type', 1)->count(),
    ];

    // Trả về dữ liệu báo cáo
    return response()->json([
        'status' => 200,
        'data' => [
            'time_series' => $timeSeriesData,
            'categories' => $categoryData,
            'summary' => $summary,
            'date_range' => [
                'from' => $dateFrom->format('d/m/Y'),
                'to' => $dateTo->format('d/m/Y'),
            ],
        ],
    ]);
}
    
public function exportReport(Request $request)
{
    // Xử lý date_range
    $dateRange = $request->input('date_range');
    if ($dateRange) {
        $now = Carbon::now();
        switch ($dateRange) {
            case 'this_month':
                $dateFrom = $now->copy()->startOfMonth();
                $dateTo = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $dateFrom = $now->copy()->subMonth()->startOfMonth();
                $dateTo = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_quarter':
                $dateFrom = $now->copy()->startOfQuarter();
                $dateTo = $now->copy()->endOfQuarter();
                break;
            case 'last_quarter':
                $dateFrom = $now->copy()->subQuarter()->startOfQuarter();
                $dateTo = $now->copy()->subQuarter()->endOfQuarter();
                break;
            case 'this_year':
                $dateFrom = $now->copy()->startOfYear();
                $dateTo = $now->copy()->endOfYear();
                break;
            case 'last_year':
                $dateFrom = $now->copy()->subYear()->startOfYear();
                $dateTo = $now->copy()->subYear()->endOfYear();
                break;
            case 'custom':
                // Sử dụng date_from và date_to được cung cấp
                $dateFrom = $request->input('date_from') ? 
                    Carbon::createFromFormat('d-m-Y', $request->input('date_from'))->startOfDay() : 
                    Carbon::now()->startOfMonth();
                
                $dateTo = $request->input('date_to') ? 
                    Carbon::createFromFormat('d-m-Y', $request->input('date_to'))->endOfDay() : 
                    Carbon::now()->endOfDay();
                break;
            default:
                $dateFrom = Carbon::now()->startOfMonth();
                $dateTo = Carbon::now()->endOfDay();
                break;
        }
    } else {
        // Xử lý như cũ nếu không có date_range
        $dateFrom = $request->input('date_from') ? 
            Carbon::createFromFormat('d-m-Y', $request->input('date_from'))->startOfDay() : 
            Carbon::now()->startOfMonth();
        
        $dateTo = $request->input('date_to') ? 
            Carbon::createFromFormat('d-m-Y', $request->input('date_to'))->endOfDay() : 
            Carbon::now()->endOfDay();
    }

    $categoryId = $request->input('category_id');
    $targetType = $request->input('target_type');
    
    // Xác định target_id dựa trên target_type
    $targetId = null;
    if ($targetType) {
        if ($targetType === 'client') {
            $targetId = $request->input('target_client_id');
        } elseif ($targetType === 'employee') {
            $targetId = $request->input('target_employee_id');
        }
    }
    
    // Xây dựng query
    $query = Transaction::with(['category', 'targetClient', 'targetEmployee'])
        ->whereBetween('paid_date', [$dateFrom, $dateTo])
        ->where('status', 1);
    
    if ($categoryId) {
        $query->where('category_id', $categoryId);
    }
    
    if ($targetType && $targetId) {
        if ($targetType === 'client') {
            $query->where('target_client_id', $targetId);
        } elseif ($targetType === 'employee') {
            $query->where('target_employee_id', $targetId);
        } elseif ($targetType === 'other') {
            $query->whereNotNull('target_other');
        }
    }
    
    // Lấy dữ liệu và sắp xếp theo ngày
    $transactions = $query->orderBy('paid_date')->get();
    
    // Tính toán tổng thu, chi
    $totalIncome = $transactions->where('type', 0)->sum('amount');
    $totalExpense = $transactions->where('type', 1)->sum('amount');
    $balance = $totalIncome - $totalExpense;
    
    // Tạo file Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Thiết lập tiêu đề báo cáo
    $sheet->setCellValue('A1', 'BÁO CÁO THU CHI');
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    // Thiết lập thông tin thời gian báo cáo
    $sheet->setCellValue('A2', 'Từ ngày: ' . $dateFrom->format('d/m/Y') . ' đến ngày: ' . $dateTo->format('d/m/Y'));
    $sheet->mergeCells('A2:G2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    // Thiết lập header cho bảng
    $sheet->setCellValue('A4', 'STT');
    $sheet->setCellValue('B4', 'Ngày');
    $sheet->setCellValue('C4', 'Loại');
    $sheet->setCellValue('D4', 'Danh mục');
    $sheet->setCellValue('E4', 'Đối tượng');
    $sheet->setCellValue('F4', 'Nội dung');
    $sheet->setCellValue('G4', 'Số tiền');
    
    // Định dạng header
    $sheet->getStyle('A4:G4')->getFont()->setBold(true);
    $sheet->getStyle('A4:G4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    $sheet->getStyle('A4:G4')->getFill()->getStartColor()->setRGB('D9D9D9');
    
    // Điền dữ liệu giao dịch
    $row = 5;
    foreach ($transactions as $index => $transaction) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, Carbon::parse($transaction->paid_date)->format('d/m/Y'));
        $sheet->setCellValue('C' . $row, $transaction->type == 0 ? 'Thu' : 'Chi');
        $sheet->setCellValue('D' . $row, $transaction->category->name ?? 'N/A');
        
        // Xác định đối tượng giao dịch
        $target = 'Không xác định';
        if ($transaction->target_client_id) {
            $target = 'KH: ' . ($transaction->targetClient->name ?? 'N/A');
        } elseif ($transaction->target_employee_id) {
            $target = 'NV: ' . ($transaction->targetEmployee->name ?? 'N/A');
        } elseif ($transaction->target_other) {
            $target = $transaction->target_other;
        }
        
        $sheet->setCellValue('E' . $row, $target);
        $sheet->setCellValue('F' . $row, $transaction->reason);
        $sheet->setCellValue('G' . $row, $transaction->amount);
        
        // Định dạng số tiền
        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Định dạng màu nền cho từng loại giao dịch
        if ($transaction->type == 0) {
            $sheet->getStyle('C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('C' . $row)->getFill()->getStartColor()->setRGB('E8F5E9');
            $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('006100');
        } else {
            $sheet->getStyle('C' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('C' . $row)->getFill()->getStartColor()->setRGB('FFEBEE');
            $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('C00000');
        }
        
        $row++;
    }
    
    // Thêm tổng kết
    $sheet->setCellValue('A' . $row, '');
    $sheet->setCellValue('B' . $row, '');
    $sheet->setCellValue('C' . $row, '');
    $sheet->setCellValue('D' . $row, '');
    $sheet->setCellValue('E' . $row, '');
    $sheet->setCellValue('F' . $row, 'Tổng thu:');
    $sheet->setCellValue('G' . $row, $totalIncome);
    $sheet->getStyle('F' . $row . ':G' . $row)->getFont()->setBold(true);
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('006100');
    
    $row++;
    $sheet->setCellValue('A' . $row, '');
    $sheet->setCellValue('B' . $row, '');
    $sheet->setCellValue('C' . $row, '');
    $sheet->setCellValue('D' . $row, '');
    $sheet->setCellValue('E' . $row, '');
    $sheet->setCellValue('F' . $row, 'Tổng chi:');
    $sheet->setCellValue('G' . $row, $totalExpense);
    $sheet->getStyle('F' . $row . ':G' . $row)->getFont()->setBold(true);
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('C00000');
    
    $row++;
    $sheet->setCellValue('A' . $row, '');
    $sheet->setCellValue('B' . $row, '');
    $sheet->setCellValue('C' . $row, '');
    $sheet->setCellValue('D' . $row, '');
    $sheet->setCellValue('E' . $row, '');
    $sheet->setCellValue('F' . $row, 'Số dư:');
    $sheet->setCellValue('G' . $row, $balance);
    $sheet->getStyle('F' . $row . ':G' . $row)->getFont()->setBold(true);
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    if ($balance >= 0) {
        $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('006100');
    } else {
        $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('C00000');
    }
    
    // Tự động điều chỉnh chiều rộng cột
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Thêm border cho toàn bộ bảng
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];
    $sheet->getStyle('A4:G' . ($row))->applyFromArray($styleArray);
    
    // Tạo file Excel và trả về response
    $writer = new Xlsx($spreadsheet);
    $filename = 'BaoCaoThuChi_' . $dateFrom->format('dmY') . '_' . $dateTo->format('dmY') . '.xlsx';
    
    $temp_file = tempnam(sys_get_temp_dir(), $filename);
    $writer->save($temp_file);
    
    return response()->download($temp_file, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
}
    
    /**
     * Tạo dữ liệu thu chi theo thời gian
     */
    private function generateTimeSeriesData($transactions, $dateFrom, $dateTo, $groupBy)
    {
        $result = [];
        
        // Xác định khoảng thời gian và format hiển thị
        switch ($groupBy) {
            case 'day':
                $interval = 'day';
                $format = 'd/m/Y';
                $period = new \DatePeriod($dateFrom, new \DateInterval('P1D'), $dateTo);
                break;
            case 'week':
                $interval = 'week';
                $format = '\T\u\ầ\n W/Y';
                $period = new \DatePeriod($dateFrom->startOfWeek(), new \DateInterval('P1W'), $dateTo);
                break;
            case 'month':
                $interval = 'month';
                $format = 'm/Y';
                $period = new \DatePeriod($dateFrom->startOfMonth(), new \DateInterval('P1M'), $dateTo);
                break;
            case 'quarter':
                $interval = 'quarter';
                $format = '\Q\u\ý Q/Y';
                // Tìm ngày đầu quý gần nhất
                $startQuarter = $dateFrom->startOfQuarter();
                $period = new \DatePeriod($startQuarter, new \DateInterval('P3M'), $dateTo);
                break;
            case 'year':
                $interval = 'year';
                $format = 'Y';
                $period = new \DatePeriod($dateFrom->startOfYear(), new \DateInterval('P1Y'), $dateTo);
                break;
            default:
                $interval = 'day';
                $format = 'd/m/Y';
                $period = new \DatePeriod($dateFrom, new \DateInterval('P1D'), $dateTo);
                break;
        }
        
        // Khởi tạo dữ liệu thời gian
        foreach ($period as $date) {
            $result[$date->format($format)] = [
                'income' => 0,
                'expense' => 0,
                'balance' => 0,
            ];
        }
        
        // Gộp dữ liệu giao dịch theo thời gian
        foreach ($transactions as $transaction) {
            $date = Carbon::parse($transaction->paid_date);
            
            // Xác định key theo khoảng thời gian
            switch ($groupBy) {
                case 'day':
                    $key = $date->format($format);
                    break;
                case 'week':
                    $key = $date->startOfWeek()->format($format);
                    break;
                case 'month':
                    $key = $date->format($format);
                    break;
                case 'quarter':
                    $key = $date->startOfQuarter()->format($format);
                    break;
                case 'year':
                    $key = $date->format($format);
                    break;
                default:
                    $key = $date->format($format);
                    break;
            }
            
            // Nếu key không tồn tại (do period có thể không bao gồm tất cả các khoảng)
            if (!isset($result[$key])) {
                $result[$key] = [
                    'income' => 0,
                    'expense' => 0,
                    'balance' => 0,
                ];
            }
            
            // Cộng dồn giá trị thu chi
            if ($transaction->type == 0) {
                $result[$key]['income'] += $transaction->amount;
            } else {
                $result[$key]['expense'] += $transaction->amount;
            }
            $result[$key]['balance'] = $result[$key]['income'] - $result[$key]['expense'];
        }
        
        // Chuyển đổi sang mảng cho dễ xử lý ở frontend
        $timeSeries = [];
        foreach ($result as $date => $values) {
            $timeSeries[] = [
                'date' => $date,
                'income' => $values['income'],
                'expense' => $values['expense'],
                'balance' => $values['balance'],
            ];
        }
        
        return $timeSeries;
    }
    
    /**
     * Tạo dữ liệu thu chi theo danh mục
     */
    private function generateCategoryData($transactions)
    {
        $incomeByCategory = [];
        $expenseByCategory = [];
        
        foreach ($transactions as $transaction) {
            $categoryName = $transaction->category->name ?? 'Không xác định';
            
            if ($transaction->type == 0) {
                if (!isset($incomeByCategory[$categoryName])) {
                    $incomeByCategory[$categoryName] = 0;
                }
                $incomeByCategory[$categoryName] += $transaction->amount;
            } else {
                if (!isset($expenseByCategory[$categoryName])) {
                    $expenseByCategory[$categoryName] = 0;
                }
                $expenseByCategory[$categoryName] += $transaction->amount;
            }
        }
        
        // Chuyển đổi sang mảng cho dễ xử lý ở frontend
        $incomeData = [];
        foreach ($incomeByCategory as $category => $amount) {
            $incomeData[] = [
                'category' => $category,
                'amount' => $amount,
            ];
        }
        
        $expenseData = [];
        foreach ($expenseByCategory as $category => $amount) {
            $expenseData[] = [
                'category' => $category,
                'amount' => $amount,
            ];
        }
        
        return [
            'income' => $incomeData,
            'expense' => $expenseData,
        ];
    }
}