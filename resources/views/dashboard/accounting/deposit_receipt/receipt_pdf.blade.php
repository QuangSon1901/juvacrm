<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Biên Nhận Thanh Toán #{{ $receipt_number }}</title>
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
    <div class="watermark">{{ $contract->company_name }}</div>
    
    <div class="header">
        <h1>BIÊN NHẬN THANH TOÁN</h1>
        <div class="company-name">{{ strtoupper($contract->company_name) }}</div>
    </div>

    <div class="footer">
        <div>
            Trang <span class="page-number"></span> | {{ $contract->company_name }} | 
            {{ $contract->company_address }} | MST: {{ $contract->tax_code }}
        </div>
        <div style="margin-top: 5px;">
            Biên nhận được tạo ngày {{ $date_now }}
        </div>
    </div>
    
    <div class="content">
        <div class="receipt-box">
            <div class="receipt-number">BIÊN NHẬN #{{ $receipt_number }}</div>
            
            <table class="info-table">
                <tr>
                    <td>Mã hợp đồng:</td>
                    <td>{{ $contract->contract_number }}</td>
                </tr>
                <tr>
                    <td>Đợt thanh toán:</td>
                    <td>{{ $payment->name }}</td>
                </tr>
                <tr>
                    <td>Loại thanh toán:</td>
                    <td>{{ $payment_stage }}</td>
                </tr>
                <tr>
                    <td>Ngày thanh toán:</td>
                    <td>{{ $payment->due_date ? formatDateTime($payment->due_date, 'd/m/Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Phương thức thanh toán:</td>
                    <td>{{ $method->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Loại tiền tệ:</td>
                    <td>{{ $currency->currency_code ?? 'VND' }}</td>
                </tr>
            </table>
            
            <div class="payment-amount">
                {{ number_format($payment->price, 0, ',', '.') }} {{ $currency->currency_code ?? 'VND' }}
            </div>
            
            <div style="text-align: center;">
                <div class="payment-status">ĐÃ THANH TOÁN</div>
            </div>
        </div>
        
        <h2>THÔNG TIN KHÁCH HÀNG</h2>
        <table class="info-table">
            <tr>
                <td>Khách hàng:</td>
                <td>{{ $provider->name ?? 'N/A' }}</td>
            </tr>
            @if($provider->company_name)
            <tr>
                <td>Công ty:</td>
                <td>{{ $provider->company_name }}</td>
            </tr>
            @endif
            <tr>
                <td>Người đại diện:</td>
                <td>{{ $contract->customer_representative ?? 'N/A' }}</td>
            </tr>
            @if($contract->customer_tax_code)
            <tr>
                <td>Mã số thuế:</td>
                <td>{{ $contract->customer_tax_code }}</td>
            </tr>
            @endif
            <tr>
                <td>Địa chỉ:</td>
                <td>{{ $contract->address ?? $provider->address ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Điện thoại:</td>
                <td>{{ $contract->phone ?? $provider->phone ?? 'N/A' }}</td>
            </tr>
        </table>
        
        <h2>THÔNG TIN HỢP ĐỒNG</h2>
        <table class="info-table">
            <tr>
                <td>Tên hợp đồng:</td>
                <td>{{ $contract->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Mã hợp đồng:</td>
                <td>{{ $contract->contract_number }}</td>
            </tr>
            <tr>
                <td>Tổng giá trị hợp đồng:</td>
                <td>{{ number_format($contract->total_value, 0, ',', '.') }} VND</td>
            </tr>
            @if($contract->category)
            <tr>
                <td>Loại dịch vụ:</td>
                <td>{{ $contract->category->name }}</td>
            </tr>
            @endif
        </table>
        
        @if($payment->note)
        <div class="note-section">
            <strong>Ghi chú:</strong> {{ $payment->note }}
        </div>
        @endif

        @if($payment->reason)
        <div class="note-section">
            <strong>Lý do thanh toán:</strong> {{ $payment->reason }}
        </div>
        @endif

        <div class="signature-section">
            <table width="100%">
                <tr>
                    <td width="50%" style="text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 100px;">NGƯỜI THU</div>
                        <div style="font-weight: bold;">{{ $payment->creator->name ?? $contract->creator->name ?? 'N/A' }}</div>
                        <div>{{ $contract->company_name }}</div>
                    </td>
                    <td width="50%" style="text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 100px;">NGƯỜI NỘP</div>
                        <div style="font-weight: bold;">{{ $provider->name ?? 'N/A' }}</div>
                        <div>{{ $provider->company_name ?? '' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>