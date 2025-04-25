@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Danh sách hoa hồng
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <a href="{{ route('dashboard.setting.setting') }}" class="btn btn-sm btn-light">
                <i class="ki-outline ki-arrow-left me-1"></i>
                Quay lại
            </a>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách hoa hồng</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th>STT</th>
                            <th>Hợp đồng</th>
                            <th>Nhân viên</th>
                            <th>Phần trăm</th>
                            <th>Giá trị hợp đồng</th>
                            <th>Số tiền hoa hồng</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commissions as $key => $commission)
                        <tr>
                            <td>{{ ($commissions->currentPage() - 1) * $commissions->perPage() + $key + 1 }}</td>
                            <td>
                                <a href="{{ route('dashboard.contract.contract.detail', $commission->contract->id) }}" class="text-primary fw-bold">
                                    {{ $commission->contract->contract_number }}
                                </a>
                            </td>
                            <td>{{ $commission->user->name }}</td>
                            <td>{{ $commission->commission_percentage }}%</td>
                            <td>{{ number_format($commission->contract_value, 0, ',', '.') }} VNĐ</td>
                            <td>{{ number_format($commission->commission_amount, 0, ',', '.') }} VNĐ</td>
                            <td>{{ $commission->processed_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($commission->is_paid)
                                    <span class="badge badge-success">Đã chi</span>
                                @else
                                    <span class="badge badge-warning">Chờ chi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-5">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection