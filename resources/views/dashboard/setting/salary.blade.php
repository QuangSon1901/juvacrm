@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Salary Settings
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <a href="{{ route('dashboard.setting.setting') }}" class="btn btn-sm btn-light">
                <i class="ki-outline ki-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Global Salary Configuration</h3>
        </div>
        <div class="card-body">
            <form id="salary-settings-form" class="grid gap-5">
                <div class="flex flex-col gap-2.5">
                    <label class="text-gray-900 font-semibold text-2sm">
                        Hourly Rate (VND/hour) <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="number" name="hourly_rate" min="0" value="{{ $salarySettings['hourly_rate'] }}" required>
                    <span class="text-xs text-gray-500">Used for attendance-based calculations</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Tax Rate (%)
                        </label>
                        <input class="input" type="number" name="tax_rate" min="0" max="100" step="0.1" value="{{ $salarySettings['tax_rate'] }}">
                    </div>
                    
                    <div class="flex flex-col gap-2.5">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Insurance Rate (%)
                        </label>
                        <input class="input" type="number" name="insurance_rate" min="0" max="100" step="0.1" value="{{ $salarySettings['insurance_rate'] }}">
                    </div>
                </div>
                
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Handle form submission
        $('#salary-settings-form').on('submit', async function(e) {
            e.preventDefault();
            
            const hourlyRate = $(this).find('input[name="hourly_rate"]').val();
            const taxRate = $(this).find('input[name="tax_rate"]').val();
            const insuranceRate = $(this).find('input[name="insurance_rate"]').val();
            
            try {
                Notiflix.Loading.circle('Saving settings...');
                
                const res = await axiosTemplate('post', '/setting/salary/update', null, {
                    hourly_rate: hourlyRate,
                    tax_rate: taxRate,
                    insurance_rate: insuranceRate
                });
                
                Notiflix.Loading.remove();
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                Notiflix.Loading.remove();
                showAlert('error', 'An error occurred while saving settings');
                console.error(error);
            }
        });
    });
</script>
@endpush