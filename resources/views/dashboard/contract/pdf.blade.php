<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Báo Giá #{{ $contract->contract_number }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 4cm 1.5cm 2.5cm 1.5cm;
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
        .quote-info {
            margin-bottom: 30px;
            width: 100%;
        }
        .quote-info-box {
            width: 100%;
            float: left;
        }
        .quote-meta {
            float: right;
            width: 40%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }
        .client-info {
            float: left;
            width: 55%;
            box-sizing: border-box;
        }
        .quote-number {
            font-size: 14px;
            font-weight: bold;
            color: #1a3d66;
            margin-bottom: 10px;
        }
        .quote-date {
            margin-bottom: 5px;
        }
        .meta-label {
            font-weight: bold;
            width: 40%;
            display: inline-block;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .table-services {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-services th {
            background-color: #1a3d66;
            color: white;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .table-services td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 11px;
        }
        .table-services .service-name {
            text-align: left;
            width: 40%;
        }
        .table-services .quantity, .table-services .price, .table-services .total {
            text-align: right;
            width: 15%;
        }
        .table-services .note {
            text-align: left;
            font-style: italic;
            color: #666;
            font-size: 10px;
        }
        /* Định dạng cho các loại dịch vụ khác nhau */
        .service-row {
            background-color: #fff;
        }
        .discount-row {
            background-color: #ffeeee;
            color: #d32f2f;
        }
        .custom-row {
            background-color: #e8f5e9;
        }
        .sub-service-row {
            background-color: #f5f5f5;
            font-style: italic;
        }
        .sub-service-row td:first-child {
            padding-left: 20px;
        }
        .total-section {
            width: 100%;
            margin-top: 20px;
        }
        .total-value {
            float: right;
            width: 40%;
            text-align: right;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f8f9fa;
            box-sizing: border-box;
        }
        .total-row {
            margin-bottom: 8px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            color: #1a3d66;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 8px;
        }
        .payment-terms {
            width: 50%;
            float: left;
            box-sizing: border-box;
            padding-right: 20px;
        }
        .payment-schedule {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .payment-schedule th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .payment-schedule td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .payment-schedule .amount {
            text-align: right;
        }
        .note-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px dashed #ddd;
            background-color: #fafafa;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .terms-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 5px;
            font-size: 11px;
            line-height: 1.4;
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
        .vat-notice {
            font-style: italic;
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            background-color: #4caf50;
        }
        .validity-notice {
            margin-top: 10px;
            font-size: 11px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $contract->company_name }}</div>
    
    <div class="header">
        <h1>BÁO GIÁ DỊCH VỤ</h1>
        <div class="company-name">{{ strtoupper($contract->company_name) }}</div>
        @if($contract->name)
        <div style="font-size: 13px; margin-top: 5px; font-weight: bold; color: #1a3d66;">{{ $contract->name }}</div>
        @endif
    </div>

    <div class="footer">
        <div>
            Trang <span class="page-number"></span> | {{ $contract->company_name }} | 
            {{ $contract->company_address }} | MST: {{ $contract->tax_code }}
        </div>
        <div style="margin-top: 5px;">
            Báo giá được tạo ngày {{ $date_now }}
        </div>
    </div>
    
    <div class="content">
        <div class="quote-info clearfix">
            <div class="client-info">
                <h2>THÔNG TIN KHÁCH HÀNG</h2>
                <table style="width: 100%">
                    <tr>
                        <td style="width: 30%; font-weight: bold;">Khách hàng:</td>
                        <td>{{ $contract->provider->name ?? 'N/A' }}</td>
                    </tr>
                    @if($contract->provider->company_name)
                    <tr>
                        <td style="font-weight: bold;">Công ty:</td>
                        <td>{{ $contract->provider->company_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: bold;">Người đại diện:</td>
                        <td>{{ $contract->customer_representative ?? 'N/A' }}</td>
                    </tr>
                    @if($contract->customer_tax_code)
                    <tr>
                        <td style="font-weight: bold;">Mã số thuế:</td>
                        <td>{{ $contract->customer_tax_code }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: bold;">Địa chỉ:</td>
                        <td>{{ $contract->address ?? $contract->provider->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Điện thoại:</td>
                        <td>{{ $contract->phone ?? $contract->provider->phone ?? 'N/A' }}</td>
                    </tr>
                    @if($contract->provider->email)
                    <tr>
                        <td style="font-weight: bold;">Email:</td>
                        <td>{{ $contract->provider->email }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <div class="quote-meta">
                <div class="quote-number">BÁO GIÁ #{{ $contract->contract_number }}</div>
                <div class="quote-date">
                    <span class="meta-label">Ngày báo giá:</span> {{ $date_now }}
                </div>
                <div class="quote-date">
                    <span class="meta-label">Hiệu lực đến:</span> {{ $quote_expiry }}
                </div>
                @if($contract->category)
                <div class="quote-date">
                    <span class="meta-label">Loại dịch vụ:</span> {{ $contract->category->name }}
                </div>
                @endif
                <div class="quote-date">
                    <span class="meta-label">Tình trạng:</span> 
                    <span class="status-badge">Báo giá</span>
                </div>
                <div class="quote-date">
                    <span class="meta-label">Người tạo:</span> {{ $contract->creator->name ?? 'N/A' }}
                </div>
            </div>
        </div>

        <h2>CHI TIẾT BÁO GIÁ</h2>
        
        @foreach ($services_by_product as $productId => $services)
            @if($productId !== 'no_product')
                <div style="margin: 20px 0 10px 0; font-weight: bold; color: #1a3d66; font-size: 14px; padding: 5px 0; border-bottom: 1px solid #eee;">
                    Sản phẩm: {{ isset($product_names[$productId]) ? $product_names[$productId] : 'Sản phẩm #'.$productId }}
                </div>
            @elseif(count($services_by_product) > 1)
                <div style="margin: 20px 0 10px 0; font-weight: bold; color: #1a3d66; font-size: 14px; padding: 5px 0; border-bottom: 1px solid #eee;">
                    Dịch vụ bổ sung
                </div>
            @endif
            
            <table class="table-services">
                <thead>
                    <tr>
                        <th class="service-name">TÊN DỊCH VỤ</th>
                        <th class="quantity">SỐ LƯỢNG</th>
                        <th class="price">ĐƠN GIÁ (VND)</th>
                        <th class="total">THÀNH TIỀN (VND)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr class="{{ $service['type'] }}-row">
                            <td class="service-name">
                                {{ $service['name'] }}
                                @if($service['note'])
                                    <div class="note">{{ $service['note'] }}</div>
                                @endif
                            </td>
                            <td class="quantity"></td>
                            <td class="price">{{ $service['service_type'] == 'individual' ? '' : number_format($service['price'], 0, ',', '.') }}</td>
                            <td class="total">{{ $service['service_type'] == 'individual' ? '' : number_format($service['total'], 0, ',', '.') }}</td>
                        </tr>
                        
                        {{-- Hiển thị dịch vụ con nếu có --}}
                        @if($service['has_sub_services'])
                            @foreach($service['sub_services'] as $subService)
                                <tr class="sub-service-row">
                                    <td class="service-name">
                                        &rarr; {{ $subService['name'] }}
                                        @if($subService['note'])
                                            <div class="note">{{ $subService['note'] }}</div>
                                        @endif
                                    </td>
                                    <td class="quantity">{{ number_format($subService['quantity'], 0, ',', '.') }}</td>
                                    <td class="price">{{ $service['service_type'] == 'individual' ? number_format($service['price'], 0, ',', '.') : 0 }}</td>
                                    <td class="total">{{ $service['service_type'] == 'individual' ? number_format($subService['price'], 0, ',', '.') : 0 }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
            
            @if(!$loop->last)
                <div style="height: 20px;"></div>
            @endif
        @endforeach
        
        <div class="total-section clearfix">
            <div class="payment-terms">
                <h2>ĐIỀU KHOẢN THANH TOÁN</h2>
                @if(count($payment_info) > 0)
                    <table class="payment-schedule">
                        <thead>
                            <tr>
                                <th>Đợt thanh toán</th>
                                <th class="amount">Số tiền (VND)</th>
                                <th>Thời hạn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment_info as $payment)
                            <tr>
                                <td>{{ $payment['name'] }}</td>
                                <td class="amount">{{ number_format($payment['price'], 0, ',', '.') }}</td>
                                <td>{{ $payment['due_date'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Thanh toán 100% giá trị khi ký hợp đồng.</p>
                @endif
            </div>
            
            <div class="total-value">
                <div class="total-row">
                    <span style="display: inline-block; width: 60%; text-align: left;">Tổng cộng:</span>
                    <span style="display: inline-block; width: 40%;">{{ number_format($total_value, 0, ',', '.') }} VND</span>
                </div>
                <div class="grand-total">
                    <span style="display: inline-block; width: 60%; text-align: left;">Tổng giá trị báo giá:</span>
                    <span style="display: inline-block; width: 40%;">{{ number_format($total_value, 0, ',', '.') }} VND</span>
                </div>
                <div class="vat-notice">
                    (Giá đã bao gồm thuế VAT)
                </div>
                <div class="validity-notice">
                    Báo giá có hiệu lực đến ngày {{ $quote_expiry }}
                </div>
            </div>
        </div>

        @if($contract->note)
        <div class="note-section">
            <strong>Ghi chú:</strong> {{ $contract->note }}
        </div>
        @endif

        @if($contract->terms_and_conditions)
        <h2>ĐIỀU KHOẢN VÀ ĐIỀU KIỆN</h2>
        <div class="terms-section">
            {!! nl2br(e($contract->terms_and_conditions)) !!}
        </div>
        @else
        <h2>ĐIỀU KHOẢN VÀ ĐIỀU KIỆN</h2>
        <div class="terms-section">
            <ol style="margin: 0; padding-left: 20px;">
                <li>Báo giá này có giá trị đến ngày {{ $quote_expiry }}.</li>
                <li>Giá đã bao gồm thuế VAT.</li>
                <li>Thanh toán được thực hiện bằng chuyển khoản ngân hàng hoặc tiền mặt.</li>
                <li>Thời gian thực hiện dịch vụ sẽ được tính từ ngày thanh toán đợt đầu tiên.</li>
                <li>Báo giá này sẽ là một phần của hợp đồng sau khi được ký kết.</li>
            </ol>
        </div>
        @endif

        <div class="signature-section">
            <table width="100%">
                <tr>
                    <td width="50%" style="text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 100px;">BÊN CUNG CẤP</div>
                        <div style="font-weight: bold;">{{ $contract->company_name }}</div>
                    </td>
                    <td width="50%" style="text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 100px;">BÊN KHÁCH HÀNG</div>
                        <div style="font-weight: bold;">{{ $contract->provider->name ?? 'N/A' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>