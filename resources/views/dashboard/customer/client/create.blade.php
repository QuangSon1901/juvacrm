@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Thêm khách hàng
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-icon btn-icon-lg size-8 rounded-md hover:bg-gray-200 dropdown-open:bg-gray-200 hover:text-primary text-gray-600" data-modal-toggle="#search_modal">
                <i class="ki-filled ki-magnifier !text-base">
                </i>
            </button>
        </div>
    </div>
</div>
<div class="container-fixed">
    <div class="grid gap-5">
        <div class="card min-w-full">
            <div class="card-header">
                <h3 class="card-title">
                    Thông tin khách hàng
                </h3>
            </div>
            <form id="create-customer-form" class="card-body grid gap-5">
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Họ tên
                        </label>
                        <input class="input" name="name" type="text" placeholder="Họ và tên">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Email
                        </label>
                        <input class="input" name="email" type="text" placeholder="Email">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Số điện thoại
                        </label>
                        <input class="input" name="phone" type="text" placeholder="Số điện thoại">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Địa chỉ
                        </label>
                        <input class="input" name="address" type="text" placeholder="Địa chỉ">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            CCCD
                        </label>
                        <input class="input" name="cccd" type="text" placeholder="Căn cước công dân">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Công ty
                        </label>
                        <input class="input" name="company" type="text" placeholder="Công ty">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                        Nguồn
                        </label>
                        <select name="source_id" class="select">
                        @foreach ($sources as $source)
                        <option value="{{$source['id']}}">{{$source['name']}}</option>
                            
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                        Loại khách hàng
                        </label>
                        <select name="class_id" class="select">
                        @foreach ($classes as $class)
                        <option value="{{$class['id']}}">{{$class['name']}}</option>
                            
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                        Trạng thái khách hàng
                        </label>
                        <select name="status_id" class="select">
                        @foreach ($status as $sts)
                        <option value="{{$sts['id']}}">{{$sts['name']}}</option>
                            
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                        Hình thức liên hệ
                        </label>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($contacts as $contact)
                            <label>
                                <input value="{{$contact['id']}}" type="checkbox" name="contacts[]">
                                <span>{{$contact['name']}}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                            Ghi chú
                        </label>
                        <textarea class="textarea" name="note" id="" placeholder="Ghi chú"></textarea>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="form-label flex items-center gap-1 max-w-56">
                        Dịch vụ khách quan tâm
                        </label>
                        <div class="flex flex-wrap gap-4">
                        <select name="services[]" class="select min-h-96" multiple >
                            <option value="">Không chọn</option>
                        @foreach ($services as $service)
                        <option value="{{$service['id']}}">{{$service['name']}}</option>
                            
                            @endforeach
                        </div>
                            </select>
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push("actions")
<button class="btn btn-success" onclick="saveCreateCustomer()">
    Thêm khách hàng
</button>
@endpush
@push('scripts')
<script>
    async function saveCreateCustomer() {
        let method = "post",
            url = "/customer/create",
            params = null,
            data = $('#create-customer-form').serialize();
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                alert(res.data.message)
                window.location.reload();
                break;
            default:
                alert(res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
                break;
        }
    }
</script>
@endpush