@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="container mx-auto px-4 py-6">
    <!-- Tiêu đề và các nút thao tác -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $details['contract_number'] }} - {{ $details['name'] }}</h1>
            <p class="text-gray-600">Tạo bởi: {{ $details['creator']['name'] }} | {{ date('d/m/Y H:i', strtotime($details['created_at'])) }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('dashboard.contract.contract') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
            <a href="{{ route('dashboard.contract.contract', $details['id']) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                <i class="fas fa-edit mr-2"></i>Chỉnh sửa
            </a>
            <a href="{{ route('dashboard.contract.contract', $details['id']) }}" target="_blank" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                <i class="fas fa-print mr-2"></i>In hợp đồng
            </a>
        </div>
    </div>

    <!-- Trạng thái hợp đồng -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <span class="text-lg font-semibold mr-2">Trạng thái:</span>
                @if($details['status'] == 1)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">{{ $details['status_text'] }}</span>
                @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">{{ $details['status_text'] }}</span>
                @endif
            </div>
            
            <!-- Tổng quan thanh toán -->
            <div class="flex space-x-4">
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Tổng giá trị</p>
                    <p class="text-xl font-bold">{{ number_format($details['payment_summary']['total_value']) }} đ</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Đã thanh toán</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($details['payment_summary']['total_paid']) }} đ</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Trừ tiền</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($details['payment_summary']['total_deduction']) }} đ</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Còn lại</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($details['payment_summary']['total_remaining']) }} đ</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Dư</p>
                    <p class="text-xl font-bold text-purple-600">{{ number_format($details['payment_summary']['total_excess']) }} đ</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600 text-sm">Tiến độ thanh toán</p>
                    <div class="w-36 h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500" style="width: {{ $details['payment_summary']['payment_percentage'] }}%"></div>
                    </div>
                    <p class="text-sm mt-1">{{ $details['payment_summary']['payment_percentage'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="grid">
        <div class="scrollable-x-auto">
            <div class="tabs gap-6" data-tabs="true">
                <div class="tab cursor-pointer active" data-tab-toggle="#tab-info">
                    <span class="text-nowrap text-sm">
                        Thông tin chung
                    </span>
                </div>
                <div class="tab cursor-pointer" data-tab-toggle="#tab-services">
                    <span class="text-nowrap text-sm">
                        Dịch vụ & Sản phẩm
                    </span>
                </div>
                <div class="tab cursor-pointer" data-tab-toggle="#tab-payments">
                    <span class="text-nowrap text-sm">
                        Thanh toán
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab nội dung -->
    <div id="tab-content">
        <!-- Thông tin cơ bản -->
        <div id="tab-info" class="transition-opacity duration-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Thông tin về công ty -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Thông tin công ty</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-600">Tên công ty:</p>
                            <p class="font-medium">{{ $details['company_name'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Mã số thuế:</p>
                            <p class="font-medium">{{ $details['tax_code'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Địa chỉ công ty:</p>
                            <p class="font-medium">{{ $details['company_address'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Thông tin về khách hàng -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Thông tin khách hàng</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-600">Nhà cung cấp:</p>
                            <p class="font-medium">{{ $details['provider']['name'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Người đại diện:</p>
                            <p class="font-medium">{{ $details['customer_representative'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Mã số thuế khách hàng:</p>
                            <p class="font-medium">{{ $details['customer_tax_code'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Địa chỉ:</p>
                            <p class="font-medium">{{ $details['address'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Số điện thoại:</p>
                            <p class="font-medium">{{ $details['phone'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Thông tin về hợp đồng -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Thông tin hợp đồng</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-600">Loại hợp đồng:</p>
                            <p class="font-medium">{{ $details['category']['name'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Người phụ trách:</p>
                            <p class="font-medium">{{ $details['user']['name'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Ngày ký:</p>
                            <p class="font-medium">{{ $details['sign_date'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Ngày hiệu lực:</p>
                            <p class="font-medium">{{ $details['effective_date'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Ngày hết hạn:</p>
                            <p class="font-medium">{{ $details['expiry_date'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Ngày triển khai dự kiến:</p>
                            <p class="font-medium">{{ $details['estimate_date'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ghi chú và điều khoản -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Ghi chú & Điều khoản</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-gray-600 mb-1">Ghi chú:</p>
                            <div class="p-3 bg-gray-50 rounded">
                                {!! nl2br(e($details['note'])) ?: '<span class="text-gray-400">Không có ghi chú</span>' !!}
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Điều khoản & Điều kiện:</p>
                            <div class="p-3 bg-gray-50 rounded max-h-40 overflow-y-auto">
                                {!! nl2br(e($details['terms_and_conditions'])) ?: '<span class="text-gray-400">Không có điều khoản</span>' !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dịch vụ & Sản phẩm -->
        <div id="tab-services" class="transition-opacity duration-700 hidden">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Danh sách dịch vụ & sản phẩm</h2>
                
                @foreach($details['contract_items'] as $index => $item)
                    <div class="mb-6 border-b pb-6">
                        @if($item['type'] === 'product')
                            <!-- Hiển thị sản phẩm -->
                            <div class="bg-blue-50 p-3 rounded-t-lg">
                                <h3 class="font-medium">Sản phẩm #{{ $item['product_id'] }}</h3>
                            </div>
                            
                            <!-- Các dịch vụ của sản phẩm -->
                            <div class="mt-2">
                                @foreach($item['services'] as $service)
                                    <div class="border-t px-3 py-2">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-medium">
                                                    @if($service['id'] === 'custom')
                                                        {{ $service['custom_name'] }} (Tùy chỉnh)
                                                    @else
                                                        Dịch vụ #{{ $service['id'] }}
                                                    @endif
                                                </p>
                                                @if(!empty($service['note']))
                                                    <p class="text-sm text-gray-600">{{ $service['note'] }}</p>
                                                @endif
                                            </div>
                                            <div class="font-medium">
                                                {{ number_format($service['price']) }} đ
                                            </div>
                                        </div>
                                        
                                        <!-- Dịch vụ con -->
                                        @if(!empty($service['sub_services']))
                                            <div class="pl-6 mt-2">
                                                @foreach($service['sub_services'] as $subService)
                                                    <div class="bg-gray-50 rounded p-2 mt-1">
                                                        <div class="flex justify-between items-center">
                                                            <div>
                                                                <p>{{ $subService['name'] }}</p>
                                                                @if(!empty($subService['content']))
                                                                    <p class="text-sm text-gray-600">{{ $subService['content'] }}</p>
                                                                @endif
                                                                <p class="text-sm text-gray-600">Số lượng: {{ $subService['quantity'] }}</p>
                                                            </div>
                                                            <div class="font-medium">
                                                                {{ number_format($subService['total']) }} đ
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($item['type'] === 'custom')
                            <!-- Hiển thị mục tùy chỉnh hoặc giảm giá -->
                            <div class="border-t px-3 py-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium">{{ $item['name'] }}</p>
                                        @if(!empty($item['note']))
                                            <p class="text-sm text-gray-600">{{ $item['note'] }}</p>
                                        @endif
                                    </div>
                                    <div class="font-medium {{ $item['price'] < 0 ? 'text-red-600' : '' }}">
                                        {{ number_format($item['price']) }} đ
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Tổng giá trị -->
                <div class="border-t pt-4 flex justify-end">
                    <div class="text-right">
                        <p class="text-lg font-semibold">Tổng giá trị: <span class="text-blue-600">{{ number_format($details['total_value']) }} đ</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanh toán -->
        <div id="tab-payments" class="transition-opacity duration-700 hidden">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Lịch sử thanh toán</h2>
                
                @if(count($details['payments']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="py-3 px-4 text-left">Tên</th>
                                    <th class="py-3 px-4 text-left">Giai đoạn</th>
                                    <th class="py-3 px-4 text-right">Phần trăm</th>
                                    <th class="py-3 px-4 text-right">Số tiền</th>
                                    <th class="py-3 px-4 text-left">Phương thức</th>
                                    <th class="py-3 px-4 text-left">Ngày đến hạn</th>
                                    <th class="py-3 px-4 text-center">Trạng thái</th>
                                    <th class="py-3 px-4 text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details['payments'] as $payment)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4">{{ $payment['name'] }}</td>
                                    <td class="py-2 px-4">
                                        @if($payment['payment_stage'] == 3)
                                            <span class="text-red-600">{{ $payment['payment_stage_text'] }}</span>
                                        @elseif($payment['payment_stage'] == 0)
                                            <span class="text-blue-600">{{ $payment['payment_stage_text'] }}</span>
                                        @elseif($payment['payment_stage'] == 1)
                                            <span class="text-purple-600">{{ $payment['payment_stage_text'] }}</span>
                                        @else
                                            <span class="text-green-600">{{ $payment['payment_stage_text'] }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 text-right">{{ $payment['percentage'] }}%</td>
                                    <td class="py-2 px-4 text-right {{ $payment['payment_stage'] == 3 ? 'text-red-600' : '' }}">
                                        {{ number_format($payment['price']) }} {{ $payment['currency']['code'] }}
                                    </td>
                                    <td class="py-2 px-4">{{ $payment['method']['name'] }}</td>
                                    <td class="py-2 px-4">{{ $payment['due_date_formatted'] }}</td>
                                    <td class="py-2 px-4 text-center">
                                        @if($payment['status'] == 1)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">{{ $payment['status_text'] }}</span>
                                        @else
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">{{ $payment['status_text'] }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button type="button" onclick="editPayment({{ json_encode($payment) }})" class="text-blue-500 hover:text-blue-700">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($payment['status'] == 0)
                                                <button type="button" onclick="markAsPaid({{ $payment['id'] }})" class="text-green-500 hover:text-green-700">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                            <button type="button" onclick="deletePayment({{ $payment['id'] }})" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-gray-500">
                        <p>Chưa có thanh toán nào được tạo.</p>
                    </div>
                @endif

                <div class="mt-4">
                    <button type="button" id="add-payment-btn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>Thêm thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm/sửa thanh toán -->
<div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold" id="payment-modal-title">Thêm thanh toán mới</h3>
                <button type="button" id="close-payment-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="payment-form" method="POST">
                @csrf
                <input type="hidden" name="payment_id" id="payment_id">
                <input type="hidden" name="contract_id" value="{{ $details['id'] }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="payment_name" class="block text-sm font-medium text-gray-700 mb-1">Tên thanh toán</label>
                        <input type="text" id="payment_name" name="payment_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="payment_stage" class="block text-sm font-medium text-gray-700 mb-1">Giai đoạn</label>
                        <select id="payment_stage" name="payment_stage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="0">Đặt cọc</option>
                            <option value="1">Tiền thưởng</option>
                            <option value="2">Thanh toán cuối cùng</option>
                            <option value="3">Trừ tiền</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="payment_percentage" class="block text-sm font-medium text-gray-700 mb-1">Phần trăm (%)</label>
                        <input type="number" id="payment_percentage" name="payment_percentage" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="payment_price" class="block text-sm font-medium text-gray-700 mb-1">Số tiền</label>
                        <input type="number" id="payment_price" name="payment_price" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="payment_currencies" class="block text-sm font-medium text-gray-700 mb-1">Loại tiền</label>
                        <select id="payment_currencies" name="payment_currencies" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency['id'] }}">{{ $currency['currency_name'] }} ({{ $currency['currency_code'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="payment_methods" class="block text-sm font-medium text-gray-700 mb-1">Phương thức thanh toán</label>
                        <select id="payment_methods" name="payment_methods" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @foreach($payment_methods as $method)
                                <option value="{{ $method['id'] }}">{{ $method['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="payment_due_dates" class="block text-sm font-medium text-gray-700 mb-1">Ngày đến hạn</label>
                        <input type="text" id="payment_due_dates" name="payment_due_dates" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="dd/mm/yyyy HH:ii">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="payment_status" name="payment_status" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="payment_status" class="ml-2 block text-sm text-gray-700">Đã thanh toán</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-payment-btn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab switching
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('a[href^="#tab-"]');
        const tabContents = document.querySelectorAll('.tab-pane');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('border-blue-500', 'text-blue-600');
                    t.classList.add('border-transparent');
                });
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Activate clicked tab
                this.classList.add('border-blue-500', 'text-blue-600');
                this.classList.remove('border-transparent');
                
                // Show corresponding content
                const target = this.getAttribute('href').substring(1);
                document.getElementById(target).classList.remove('hidden');
            });
        });
        
        // Payment modal handling
        const paymentModal = document.getElementById('payment-modal');
        const addPaymentBtn = document.getElementById('add-payment-btn');
        const closePaymentModal = document.getElementById('close-payment-modal');
        const cancelPaymentBtn = document.getElementById('cancel-payment-btn');
        const paymentForm = document.getElementById('payment-form');
        const paymentModalTitle = document.getElementById('payment-modal-title');
        
        // Initialize datepicker for due date
        if ($.fn.datetimepicker) {
            $('#payment_due_dates').datetimepicker({
                format: 'd/m/Y H:i',
                step: 15
            });
        }
        
        // Show payment modal
        addPaymentBtn.addEventListener('click', function() {
            resetPaymentForm();
            paymentModalTitle.textContent = 'Thêm thanh toán mới';
            paymentForm.action = "{{ route('dashboard.contract.contract') }}";
            paymentModal.classList.remove('hidden');
        });
        
        // Close payment modal
        closePaymentModal.addEventListener('click', function() {
            paymentModal.classList.add('hidden');
        });
        
        cancelPaymentBtn.addEventListener('click', function() {
            paymentModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                paymentModal.classList.add('hidden');
            }
        });
        
        // Calculate price when percentage changes
        const paymentPercentage = document.getElementById('payment_percentage');
        const paymentPrice = document.getElementById('payment_price');
        
        paymentPercentage.addEventListener('input', function() {
            const percentage = parseFloat(this.value) || 0;
            const totalValue = {{ $details['total_value'] }};
            paymentPrice.value = (percentage * totalValue / 100).toFixed(2);
        });
        
        // Calculate percentage when price changes
        paymentPrice.addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            const totalValue = {{ $details['total_value'] }};
            
            if (totalValue > 0) {
                paymentPercentage.value = (price * 100 / totalValue).toFixed(2);
            }
        });
    });
    
    // Reset payment form
    function resetPaymentForm() {
        document.getElementById('payment_id').value = '';
        document.getElementById('payment_name').value = '';
        document.getElementById('payment_stage').value = '0';
        document.getElementById('payment_percentage').value = '';
        document.getElementById('payment_price').value = '';
        document.getElementById('payment_currencies').value = '1'; // Default to first currency
        document.getElementById('payment_methods').value = '1'; // Default to first method
        document.getElementById('payment_due_dates').value = '';
        document.getElementById('payment_status').checked = false;
    }
    
    // Edit payment
    function editPayment(payment) {
        const paymentModal = document.getElementById('payment-modal');
        const paymentForm = document.getElementById('payment-form');
        const paymentModalTitle = document.getElementById('payment-modal-title');
        
        paymentModalTitle.textContent = 'Chỉnh sửa thanh toán';
        paymentForm.action = "{{ route('dashboard.contract.contract') }}";
        
        document.getElementById('payment_id').value = payment.id;
        document.getElementById('payment_name').value = payment.name;
        document.getElementById('payment_stage').value = payment.payment_stage;
        document.getElementById('payment_percentage').value = payment.percentage;
        document.getElementById('payment_price').value = payment.price;
        document.getElementById('payment_currencies').value = payment.currency.id;
        document.getElementById('payment_methods').value = payment.method.id;
        document.getElementById('payment_due_dates').value = payment.due_date_formatted;
        document.getElementById('payment_status').checked = payment.status === 1;
        
        paymentModal.classList.remove('hidden');
    }
    
    // Mark payment as paid
    function markAsPaid(paymentId) {
        if (confirm('Xác nhận đánh dấu thanh toán này là "Đã thanh toán"?')) {
            fetch(`{{ url('contracts/payment') }}/${paymentId}/mark-as-paid`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    alert('Cập nhật trạng thái thanh toán thành công');
                    window.location.reload();
                } else {
                    alert(data.message || 'Đã xảy ra lỗi');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi');
            });
        }
    }
    
    // Delete payment
    function deletePayment(paymentId) {
        if (confirm('Bạn có chắc chắn muốn xóa thanh toán này không?')) {
            fetch(`{{ url('contracts/payment') }}/${paymentId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    alert('Xóa thanh toán thành công');
                    window.location.reload();
                } else {
                    alert(data.message || 'Đã xảy ra lỗi');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi');
            });
        }
    }
</script>
@endsection