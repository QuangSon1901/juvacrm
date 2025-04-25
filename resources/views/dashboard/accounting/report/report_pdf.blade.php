<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Tài Chính</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 3cm 1.5cm 2.5cm 1.5cm;
            line-height: 1.5;
            color: #333;
        }
        .header {
            position: fixed;
            top: 0.5cm;
            left: 1.5cm;
            right: 1.5cm;
            text-align: center;
            padding-bottom: 0.3cm;
        }
        .footer {
            position: fixed;
            bottom: 0.5cm;
            left: 1.5cm;
            right: 1.5cm;
            height: 2cm;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 0.3cm;
        }
        .page-number:after {
            content: counter(page);
        }
        .content {
            margin-top: 1cm;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 10px;
            color: #1a3d66;
            text-transform: uppercase;
        }
        h2 {
            font-size: 16px;
            margin: 25px 0 15px 0;
            color: #1a3d66;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .company-name {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table.summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.summary th, table.summary td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.summary th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        table.data th, table.data td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        table.data th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary-card {
            width: 30%;
            float: left;
            margin-right: 3%;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .summary-card:last-child {
            margin-right: 0;
        }
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1a3d66;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-detail {
            font-size: 10px;
            color: #666;
        }
        .income {
            color: #17c653;
        }
        .expense {
            color: #ff5252;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .date-range {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BÁO CÁO TÀI CHÍNH</h1>
        <div class="company-name">{{ $company_name }}</div>
    </div>

    <div class="footer">
        <div>
            Trang <span class="page-number"></span> | {{ $company_name }} | 
            {{ $company_address }} | MST: {{ $tax_code }}
        </div>
        <div style="margin-top: 5px;">
            Báo cáo được tạo ngày {{ $date_now }}
        </div>
    </div>
    
    <div class="content">
        <div class="date-range">
            Khoảng thời gian: {{ $date_from }} - {{ $date_to }}
        </div>
        
        <!-- Thông tin tổng quan -->
        <div class="clearfix">
            <div class="summary-card">
                <div class="summary-title">Tổng thu</div>
                <div class="summary-value income">{{ number_format($total_income, 0, ',', '.') }}₫</div>
                <div class="summary-detail">Số giao dịch: {{ $income_count }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Tổng chi</div>
                <div class="summary-value expense">{{ number_format($total_expense, 0, ',', '.') }}₫</div>
                <div class="summary-detail">Số giao dịch: {{ $expense_count }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Số dư</div>
                <div class="summary-value" style="color: {{ $balance >= 0 ? '#17c653' : '#ff5252' }}">
                    {{ number_format($balance, 0, ',', '.') }}₫
                </div>
                <div class="summary-detail">Tỷ lệ thu/chi: {{ $total_expense > 0 ? number_format($total_income / $total_expense * 100, 1) : '--' }}%</div>
            </div>
        </div>
        
        <!-- Bảng thống kê thu theo danh mục -->
        <h2>Thu theo danh mục</h2>
        <table class="summary">
            <thead>
                <tr>
                    <th width="50">STT</th>
                    <th>Danh mục</th>
                    <th width="100">Số giao dịch</th>
                    <th width="150">Tổng tiền</th>
                    <th width="80">Tỷ lệ</th>
                </tr>
            </thead>
            <tbody>
                @if(count($income_categories) > 0)
                    @foreach($income_categories as $index => $category)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category['category_name'] }}</td>
                        <td>{{ $category['count'] }}</td>
                        <td class="income">{{ number_format($category['total'], 0, ',', '.') }}₫</td>
                        <td>{{ number_format($category['total'] / $total_income * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Bảng thống kê chi theo danh mục -->
        <h2>Chi theo danh mục</h2>
        <table class="summary">
            <thead>
                <tr>
                    <th width="50">STT</th>
                    <th>Danh mục</th>
                    <th width="100">Số giao dịch</th>
                    <th width="150">Tổng tiền</th>
                    <th width="80">Tỷ lệ</th>
                </tr>
            </thead>
            <tbody>
                @if(count($expense_categories) > 0)
                    @foreach($expense_categories as $index => $category)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category['category_name'] }}</td>
                        <td>{{ $category['count'] }}</td>
                        <td class="expense">{{ number_format($category['total'], 0, ',', '.') }}₫</td>
                        <td>{{ number_format($category['total'] / $total_expense * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Chi tiết thu chi theo thời gian -->
        <h2>Chi tiết theo thời gian</h2>
        <table class="summary">
            <thead>
                <tr>
                    <th width="100">Thời gian</th>
                    <th width="150">Thu</th>
                    <th width="150">Chi</th>
                    <th width="150">Số dư</th>
                </tr>
            </thead>
            <tbody>
                @if(count($time_series) > 0)
                    @foreach($time_series as $item)
                    <tr>
                        <td>{{ $item['date'] }}</td>
                        <td class="income">{{ number_format($item['income'], 0, ',', '.') }}₫</td>
                        <td class="expense">{{ number_format($item['expense'], 0, ',', '.') }}₫</td>
                        <td style="color: {{ $item['balance'] >= 0 ? '#17c653' : '#ff5252' }}">
                            {{ number_format($item['balance'], 0, ',', '.') }}₫
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Chi tiết giao dịch -->
        <h2>Chi tiết giao dịch</h2>
        <table class="data">
            <thead>
                <tr>
                    <th width="40">STT</th>
                    <th width="60">Loại</th>
                    <th width="80">Ngày GD</th>
                    <th width="100">Danh mục</th>
                    <th>Đối tượng</th>
                    <th>Nội dung</th>
                    <th width="100">Số tiền</th>
                </tr>
            </thead>
            <tbody>
                @if(count($transactions) > 0)
                    @foreach($transactions as $index => $transaction)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $transaction['type'] == 0 ? 'Thu' : 'Chi' }}</td>
                        <td>{{ $transaction['paid_date'] }}</td>
                        <td>{{ $transaction['category_name'] }}</td>
                        <td>{{ $transaction['target_display'] }}</td>
                        <td>{{ $transaction['reason'] }}</td>
                        <td style="color: {{ $transaction['type'] == 0 ? '#17c653' : '#ff5252' }}">
                            {{ $transaction['type'] == 0 ? '+' : '-' }}{{ number_format($transaction['amount'], 0, ',', '.') }}₫
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" style="text-align: center">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>