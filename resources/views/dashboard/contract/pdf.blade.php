<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hợp Đồng #{{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .header, .footer {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }
        .table-info, .table-services {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-info th, .table-info td, .table-services th, .table-services td {
            border: 1px solid #000;
            padding: 8px;
        }
        .table-info th {
            background-color: #f2f2f2;
            text-align: left;
            width: 30%;
        }
        .table-services th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .table-services td {
            text-align: right;
        }
        .table-services td:first-child {
            text-align: left;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HỢP ĐỒNG DỊCH VỤ #{{ $contract->contract_number }}</h1>
        <p>CÔNG TY {{ strtoupper($contract->company_name) }}</p>
    </div>

    <table class="table-info">
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
            <td>{{ $contract->customer->name }}</td>
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
            <td>{{ $contract->address }}</td>
        </tr>
        <tr>
            <th>Ngày ký</th>
            <td>{{ \Carbon\Carbon::parse($contract->sign_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Ngày hiệu lực</th>
            <td>{{ \Carbon\Carbon::parse($contract->effective_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Ngày hết hạn</th>
            <td>{{ \Carbon\Carbon::parse($contract->expiry_date)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <h2 style="text-align: center;">DANH SÁCH DỊCH VỤ</h2>
    <table class="table-services">
        <thead>
            <tr>
                <th>Tên dịch vụ</th>
                <th>Số lượng</th>
                <th>Đơn giá (VND)</th>
                <th>Tổng cộng (VND)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $service)
            <tr>
                <td>{{ $service['name'] }}</td>
                <td>{{ $service['quantity'] }}</td>
                <td>{{ number_format($service['price'], 0, ',', '.') }}</td>
                <td>{{ number_format($service['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Tổng giá trị: {{ number_format($total_value, 0, ',', '.') }} VND
    </div>

    <div class="footer">
        <p>Trân trọng,<br>{{ $contract->company_name }}</p>
    </div>
</body>
</html>