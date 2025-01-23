@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Quy trình tư vấn
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="col-span-1">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-body">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-2.5">
                                <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
                                    <i class="ki-filled ki-user text-base"></i>
                                </div>
                                <div class="flex flex-col flex-1 gap-0.5">
                                    <a class="leading-none font-semibold text-md text-gray-900 hover:text-primary" href="/customer/{{$details['id']}}">
                                        {{$details['name']}}
                                    </a>
                                    <span class="text-2sm text-gray-700 font-normal">
                                        0 hợp đồng
                                    </span>
                                    <div>
                                        @if ($details['classification']['id'] != 0)
                                        <span class="badge badge-sm badge-outline badge-{{$details['classification']['color']}}">
                                            {{$details['classification']['name']}}
                                        </span>
                                        @endif
                                        @if ($details['status']['id'] != 0)
                                        <span class="badge badge-sm badge-outline badge-{{$details['status']['color']}}">
                                            {{$details['status']['name']}}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="menu" data-menu="true">
                                    <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-dots-vertical">
                                            </i>
                                        </button>
                                        <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-sms">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        {{$details['email']}}
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link" href="#">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-phone">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title">
                                                        {{$details['phone']}}
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header gap-2">
                        <h3 class="card-title">
                            Nhật ký tư vấn
                        </h3>
                        <div class="menu" data-menu="true">
                            <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                    <i class="ki-filled ki-dots-vertical">
                                    </i>
                                </button>
                                <div class="menu-dropdown menu-default w-full max-w-[200px]" data-menu-dismiss="true">
                                    <div class="menu-item">
                                        <button class="menu-link" data-modal-toggle="#create-consultation-modal">
                                            <span class="menu-icon">
                                                <i class="ki-filled ki-plus">
                                                </i>
                                            </span>
                                            <span class="menu-title">
                                                Thêm nhật ký
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="grid gap-2.5">
                            @foreach ($details['consultations'] as $cons)
                            <div data-id="{{$cons['id']}}" class="consultation-tab flex items-center gap-3 cursor-pointer py-2 px-4 rounded-lg {{$cons['index']==1?'bg-gray-100 border border-blue-500':'hover:bg-gray-100'}}">
                                <div class="flex items-center grow gap-2.5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 mb-px">
                                            {{$cons['name']}}
                                        </span>
                                        <span class="text-xs text-gray-700">
                                            Cập nhật: {{date('d-m-Y H:i:s', strtotime($cons['updated_at']))}}
                                        </span>
                                    </div>
                                </div>
                                <div class="menu" data-menu="true">
                                    <div class="menu-item menu-item-dropdown" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-dots-vertical">
                                            </i>
                                        </button>
                                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                            <div class="menu-item">
                                                <button data-id="{{$cons['id']}}" class="menu-link" data-modal-toggle="#update-name-consultation-modal">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-pencil">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title text-left">
                                                        Chỉnh sửa
                                                    </span>
                                                </button>
                                            </div>
                                            <div class="menu-item">
                                                <button data-id="{{$cons['id']}}" class="menu-link remove-consulation">
                                                    <span class="menu-icon">
                                                        <i class="ki-filled ki-trash !text-red-600">
                                                        </i>
                                                    </span>
                                                    <span class="menu-title !text-red-600">
                                                        Xoá
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1 lg:col-span-2">
            <div class="grid gap-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Quá trình tư vấn
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="consoltation-logs-body" class="flex-col"></div>
                    </div>
                    <div class="card-footer">
                        <div class="flex flex-col w-full">
                            <form id="add-log-form" class="relative grow">
                                <div id="attachment-preview" class="grid gap-2 py-2"></div>
                                <select class="text-xs text-gray-900 outline-none" name="action">
                                    <option value="0" selected>Hỏi nhu cầu khách hàng</option>
                                    <option value="1">Tư vấn gói</option>
                                    <option value="2">Lập hợp đồng</option>
                                    <option value="3">Gửi bảng giá</option>
                                    <option value="4">Khách từ chối</option>
                                    <option value="5">Đặt lịch tư vấn lại</option>
                                </select>
                                <div class="flex input h-auto ps-4 bg-transparent rounded-lg">
                                    <div style="flex: 1;"><textarea class="py-4 outline-none w-full" name="message" rows="1" placeholder="Bổ sung quá trình tư vấn..." type="text" value=""></textarea></div>
                                    <div class="flex items-center gap-2.5">
                                        <label type="button" class="btn btn-sm btn-icon btn-light btn-clear">
                                            <i class="ki-filled ki-exit-up"></i>
                                            <input id="attachment-input" type="file" class="hidden">
                                        </label>
                                        <button type="submit" class="btn btn-dark btn-sm">
                                            Đăng
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-consultation-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Thêm nhật ký mới
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="flex flex-center gap-1">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Tiêu đề
                        </label>
                    </div>
                    <input class="input" name="name" type="text" placeholder="Vui lòng nhập">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="update-name-consultation-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chỉnh sửa nhật ký
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross">
                </i>
            </button>
        </div>
        <div class="modal-body">
            <form class="grid gap-5 px-0 py-5">
                <div class="flex flex-col gap-2.5">
                    <div class="flex flex-center gap-1">
                        <label class="text-gray-900 font-semibold text-2sm">
                            Tiêu đề
                        </label>
                    </div>
                    <input class="input" name="name" type="text" placeholder="Vui lòng nhập tiêu đề">
                </div>
                <div class="flex flex-col">
                    <button type="submit" class="btn btn-primary justify-center">
                        Xong
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    let consulationDelete = 0;
    let consulationActive = 0;
    $(function() {
        $('.consultation-tab').on('click', function() {
            $('.consultation-tab').removeClass('bg-gray-100 border border-blue-500');
            $(this).addClass('bg-gray-100 border border-blue-500');
            consulationActive = $(this).attr('data-id');
            getConsultationLog($(this).attr('data-id'));

        })

        $('[data-modal-toggle="#update-name-consultation-modal"]').on('click', function() {
            consulationDelete = $(this).attr('data-id');
        })

        $('#create-consultation-modal form').on('submit', function(e) {
            e.preventDefault();
            postCreateConsultation(this);
        })

        $('#update-name-consultation-modal form').on('submit', function(e) {
            e.preventDefault();
            postUpdateNameConsultation(this);
        })

        $('.remove-consulation').on('click', function() {
            Notiflix.Confirm.show(
                'Xoá nhật ký',
                'Bạn có chắc chắn muốn xoá nhật ký này?',
                'Đúng',
                'Huỷ',
                () => {
                    postRemoveConsultation($(this).attr('data-id'));
                },
                () => {}, {},
            );
        })

        $('#attachment-input').on('change', function() {
            postUploadAttachment(this);
        })

        $(document).on('click', '.attachment-remove-btn', function() {
            $(this).closest('.attachment-preview-item').remove();
        })

        $('#add-log-form').on('submit', function(e) {
            e.preventDefault();
            postAddLog(this);
        })

        $('.consultation-tab:eq(0)').trigger('click');
    })

    async function postAddLog(_this) {
        let method = "post",
            url = "/consultation/add-log",
            params = null,
            data = $(_this).serialize() + '&consultation_id=' + consulationActive
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                $(`.consultation-tab[data-id=${consulationActive}]`).trigger('click');
                $('#attachment-preview').html('');
                $('#add-log-form textarea').val();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }

    async function postCreateConsultation(_this) {
        let method = "post",
            url = "/consultation/create",
            params = null,
            data = $(_this).serialize() + "&customer_id={{$details['id']}}";
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }

    async function postUpdateNameConsultation(_this) {
        let method = "post",
            url = "/consultation/update",
            params = null,
            data = $(_this).serialize() + "&id=" + consulationDelete;
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }

    async function postRemoveConsultation(id) {
        let method = "post",
            url = "/consultation/remove",
            params = null,
            data = {
                id
            };
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }

    async function getConsultationLog(id) {
        let method = "get",
            url = "/consultation/log",
            params = {
                id
            },
            data = null;
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                $('#consoltation-logs-body').html(res.data.content)
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
                break;
        }
    }

    async function postUploadAttachment(_this) {
        try {
            let file = $(_this).prop("files")[0];
            if (file) {
                let url = URL.createObjectURL(file);
                let res = await uploadFileTemplate(file);
                if (res.data.status == 200) {
                    let img = '';
                    if (res.data.data.type.startsWith('image/') && res.data.data.extension !== 'svg') {
                        img = `<img class="w-[30px]" alt="${res.data.data.extension}" src="https://drive.google.com/thumbnail?id=${res.data.data.driver_id}&sz=w56">`;
                    } else {
                        img = `<img class="w-[30px]" alt="${res.data.data.extension}" src="/assets/images/file-types/${res.data.data.extension}.svg">`;
                    }

                    $('#attachment-preview').append(`<div class="attachment-preview-item flex items-center gap-4">
                            <input name="attachment[]" value="${res.data.data.driver_id}" type="text" class="hidden">
                            <div class="flex items-center gap-2.5">
                                ${img}
                                <div class="flex flex-col">
                                    <a href="https://drive.google.com/file/d/${res.data.data.driver_id}/view" target="_blank" style="overflow-wrap: anywhere;" class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                        ${res.data.data.name}
                                    </a>
                                    <span class="text-xs text-gray-700">
                                        ${res.data.data.size} - <span class="attachment-remove-btn text-danger text-xs font-semibold">Xoá</span>
                                    </span>
                                </div>
                            </div>
                        </div>`);
                } else {
                    showAlert('error', 'Upload failed!');
                }
            }
        } catch (error) {
            showAlert('error', error);
        }
    }
</script>
@endpush