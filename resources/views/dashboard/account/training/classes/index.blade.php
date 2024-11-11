@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <!-- Container -->
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch sử đào tạo
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
        </div>
    </div>
    <!-- End of Container -->
</div>
<div class="container-fixed">
    <div class="flex items-center justify-center">
        <img src="http://127.0.0.1:8000/assets/images/icons/comingsoon.png" class="h-60" alt="">
    </div>
</div>
@endsection