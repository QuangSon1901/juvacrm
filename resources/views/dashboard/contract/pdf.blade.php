<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hợp Đồng #{{ $contract->contract_number }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 2.5cm 1.5cm 2.5cm 1.5cm;
            line-height: 1.5;
            color: #333;
        }
        .header {
            position: fixed;
            top: 0.5cm;
            left: 1.5cm;
            right: 1.5cm;
            height: 2cm;
            text-align: center;
            border-bottom: 1px solid #ddd;
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
            margin-top: 0.5cm;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        h2 {
            text-align: center;
            font-size: 16px;
            margin: 20px 0 15px 0;
            color: #2c3e50;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .company-name {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .contract-info {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
        }
        .table-info th {
            background-color: #f8f9fa;
            text-align: left;
            width: 30%;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        .table-info td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        .table-info tr:last-child th, .table-info tr:last-child td {
            border-bottom: none;
        }
        .table-services {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-services th {
            background-color: #f8f9fa;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            color: #444;
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
        .total-value {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            padding: 10px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.1;
            font-size: 80px;
            color: #000;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $contract->company_name }}</div>
    
    <div class="header">
        <h1>HỢP ĐỒNG DỊCH VỤ #{{ $contract->contract_number }}</h1>
        <div class="company-name">{{ strtoupper($contract->company_name) }}</div>
    </div>

    <div class="footer">
        <div>
            Trang <span class="page-number"></span> | {{ $contract->company_name }} | 
            {{ $contract->company_address }} | MST: {{ $contract->tax_code }}
        </div>
        <div style="margin-top: 5px;">
            Hợp đồng được tạo ngày {{ $date_now }}
        </div>
    </div>
    
    <div class="content">
        <h2>THÔNG TIN CHUNG</h2>
        <div class="contract-info">
            <table class="table-info">
                <tr>
                    <th>Tên hợp đồng</th>
                    <td>{{ $contract->name }}</td>
                </tr>
                <tr>
                    <th>Bên cung cấp</th>
                    <td>{{ $contract->company_name }}</td>
                </tr>
                <tr>
                    <th>Mã số thuế</th>
                    <td>{{ $contract->tax_code }}</td>
                </tr>
                <tr>
                    <th>Địa chỉ</th>
                    <td>{{ $contract->company_address }}</td>
                </tr>
                <tr>
                    <th>Khách hàng</th>
                    <td>
                        <strong>{{ $contract->provider->name ?? 'N/A' }}</strong>
                        @if($contract->provider->company_name)
                        <br>{{ $contract->provider->company_name }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Người đại diện</th>
                    <td>{{ $contract->customer_representative ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Mã số thuế khách hàng</th>
                    <td>{{ $contract->customer_tax_code ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Địa chỉ khách hàng</th>
                    <td>{{ $contract->address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $contract->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Ngày ký</th>
                    <td>{{ $contract->sign_date ? \Carbon\Carbon::parse($contract->sign_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Ngày hiệu lực</th>
                    <td>{{ $contract->effective_date ? \Carbon\Carbon::parse($contract->effective_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Ngày hết hạn</th>
                    <td>{{ $contract->expiry_date ? \Carbon\Carbon::parse($contract->expiry_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
                @if($contract->estimate_date)
                <tr>
                    <th>Ngày dự kiến hoàn thành</th>
                    <td>{{ \Carbon\Carbon::parse($contract->estimate_date)->format('d/m/Y') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <h2>DANH SÁCH DỊCH VỤ</h2>
        <table class="table-services">
            <thead>
                <tr>
                    <th class="service-name">Tên dịch vụ</th>
                    <th class="quantity">Số lượng</th>
                    <th class="price">Đơn giá (VND)</th>
                    <th class="total">Thành tiền (VND)</th>
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
                        <td class="quantity">{{ number_format($service['quantity'], 0, ',', '.') }}</td>
                        <td class="price">{{ number_format($service['price'], 0, ',', '.') }}</td>
                        <td class="total">{{ number_format($service['total'], 0, ',', '.') }}</td>
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
                                <td class="price">{{ number_format($subService['price'], 0, ',', '.') }}</td>
                                <td class="total">{{ number_format($subService['total'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        
        <div class="total-value">
            Tổng giá trị: {{ number_format($total_value, 0, ',', '.') }} VND
        </div>

        @if($contract->note)
        <div style="margin-top: 20px; padding: 10px; border: 1px dashed #ddd; background-color: #fafafa;">
            <strong>Ghi chú:</strong> {{ $contract->note }}
        </div>
        @endif

        @if($contract->terms_and_conditions)
        <h2>ĐIỀU KHOẢN VÀ ĐIỀU KIỆN</h2>
        <div style="padding: 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 11px; line-height: 1.4;">
            {!! nl2br(e($contract->terms_and_conditions)) !!}
        </div>
        @endif

        <div style="margin-top: 40px; page-break-inside: avoid;">
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