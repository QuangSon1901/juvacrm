{{-- resources/views/dashboard/contracts/create.blade.php --}}
@extends('dashboard.layouts.layout')

@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thông tin hợp đồng
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base"></i>
            </button>
            @push("actions")
            <button type="button" class="btn btn-primary px-5 py-2 flex items-center gap-2" onclick="saveCreateContract()">
                <i class="ki-filled ki-check text-white"></i>
                <span>Tạo hợp đồng</span>
            </button>
            @endpush
        </div>
    </div>
</div>

<div class="container-fixed">
    <form id="contract-form" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Party A Information (Left Column) -->
        <div class="col-span-1">
            <div class="grid gap-6">
                @include('dashboard.contract.partials.party-a')
                @include('dashboard.contract.partials.party-b')
            </div>
        </div>

        <!-- Contract Description (Right Columns) -->
        <div class="col-span-1 xl:!col-span-2">
            @include('dashboard.contract.partials.contract-description')
        </div>

        <!-- Services Section (Full Width) -->
        <div class="col-span-1 xl:!col-span-3">
            @include('dashboard.contract.partials.services')
        </div>

        <!-- Payments Section (Full Width) -->
        <div class="col-span-1 xl:!col-span-3">
            @include('dashboard.contract.partials.payments')
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/contracts/contract-service.js') }}"></script>
    <script src="{{ asset('js/contracts/contract-payment.js') }}"></script>
    <script src="{{ asset('js/contracts/contract-main.js') }}"></script>
    
    <script>
        // Initialize with the available data
        const details = @json($details);
        
        // Document ready
        $(document).ready(function() {
            // Focus on contract name field when page loads
            $('input[name="name"]').focus();
            
            // Initialize date pickers
            flatpickrMake($('input[name="sign_date"], input[name="effective_date"], input[name="expiry_date"], input[name="estimate_date"]'), 'date');
        });
    </script>
@endpush