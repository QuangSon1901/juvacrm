<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $type_text }} #{{ $receipt_number }}</title>
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
        .receipt-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
            background-color: #f9f9f9;
        }
        .receipt-number {
            font-size: 16px;
            font-weight: bold;
            color: #1a3d66;
            margin-bottom: 15px;
            text-align: center;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            width: 40%;
            font-weight: bold;
        }
        .payment-details {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
        .payment-amount {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #1a3d66;
        }
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.05;
            font-size: 100px;
            color: #1a3d66;
            z-index: -1;
        }
        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            color: white;
            background-color: #4caf50;
            margin-top: 10px;
        }
        .canceled {
            background-color: #f44336;
        }
        .pending {
            background-color: #ff9800;
        }
        .note-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px dashed #ddd;
            background-color: #fafafa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $type_text }}</div>
    
    <div class="header">
        <h1>{{ $type_text }}</h1>
        <div class="company-name">{{ $company_name ?? 'JuvaMedia' }}</div>
    </div>

    <div class="footer">
        <div>
            Trang <span class="page-number"></span> | {{ $company_name ?? 'JuvaMedia' }} | 
            {{ $company_address ?? ''}} | MST: {{ $tax_code ?? ''}}
        </div>
        <div style="margin-top: 5px;">
            Phiếu được tạo ngày {{ $date_now }}
        </div>
    </div>
    
    <div class="content">
        <div class="receipt-box">
            <div class="receipt-number">{{ $type_text }} #{{ $receipt_number }}</div>
            
            <table class="info-table">
                <tr>
                    <td>Danh mục:</td>
                    <td>{{ $category->name }}</td>
                </tr>
                <tr>
                    <td>Nội dung:</td>
                    <td>{{ $transaction->reason }}</td>
                </tr>
                <tr>
                    <td>Ngày thanh toán:</td>
                    <td>{{ formatDateTime($transaction->paid_date, 'd/m/Y') }}</td>
                </tr>
            </table>
            
            <div class="payment-amount">
                @if($transaction->type == 0)
                +{{ number_format($transaction->amount, 0, ',', '.') }} VND
                @else
                -{{ number_format($transaction->amount, 0, ',', '.') }} VND
                @endif
            </div>
            
            <div style="text-align: center;">
                @if($transaction->status == 1)
                <div class="payment-status">ĐÃ HOÀN TẤT</div>
                @elseif($transaction->status == 0)
                <div class="payment-status pending">CHỜ XỬ LÝ</div>
                @else
                <div class="payment-status canceled">ĐÃ HỦY</div>
                @endif
            </div>
        </div>
        
        <h2>THÔNG TIN ĐỐI TƯỢNG</h2>
        <table class="info-table">
            <tr>
                <td>Đối tượng:</td>
                <td>{{ $target['display'] }}</td>
            </tr>
            @if($target['type'] == 'client' && isset($target_details))
            <tr>
                <td>Địa chỉ:</td>
                <td>{{ $target_details->address ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Điện thoại:</td>
                <td>{{ $target_details->phone ?? 'N/A' }}</td>
            </tr>
            @elseif($target['type'] == 'employee' && isset($target_details))
            <tr>
                <td>Email:</td>
                <td>{{ $target_details->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Phòng ban:</td>
                <td>{{ $target_details->department->name ?? 'N/A' }}</td>
            </tr>
            @endif
        </table>
        
        @if($transaction->note)
        <div class="note-section">
            <strong>Ghi chú:</strong> {{ $transaction->note }}
        </div>
        @endif

        <div class="signature-section">
            <table width="100%">
                <tr>
                    <td width="50%" style="text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 100px;">NGƯỜI LẬP PHIẾU</div>
                        <div style="font-weight: bold;">{{ $creator->name ?? 'N/A' }}</div>
                        <div>{{ $company_name ?? 'JuvaMedia' }}</div>
                    </td>
                    <td width="50%" style="text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 100px;">
                            @if($transaction->type == 0)
                            NGƯỜI NỘP TIỀN
                            @else
                            NGƯỜI NHẬN TIỀN
                            @endif
                        </div>
                        <div style="font-weight: bold;">{{ $target['name'] ?? 'N/A' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>