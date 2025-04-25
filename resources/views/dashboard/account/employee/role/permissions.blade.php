@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Phân quyền cho chức vụ {{$details['level']->name}} - {{$details['department']->name}}
            </h1>
        </div>
    </div>
</div>
<div class="container-fixed">
    <div class="grid gap-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Phân quyền
                </h3>
            </div>
            <div class="card-table scrollable-x-auto">
                <form id="permissions-form">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-left text-gray-300 font-normal min-w-[300px]">
                                    Mô-đun
                                </th>
                                <th class="min-w-24 text-gray-700 font-normal text-center">
                                    Xem
                                </th>
                                <th class="min-w-24 text-gray-700 font-normal text-center">
                                    Thêm
                                </th>
                                <th class="min-w-24 text-gray-700 font-normal text-center">
                                    Chỉnh sửa
                                </th>
                                <th class="min-w-24 text-gray-700 font-normal text-center">
                                    Xoá
                                </th>
                                <th class="min-w-24 text-gray-700 font-normal text-center">
                                    Khác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-900 font-medium">
                            @foreach ($permissions as $module => $modulePermissions)
                            <tr>
                                <td class="!py-5.5">
                                    {{ $module }}
                                </td>
                                <td class="!py-5.5 text-center">
                                    @if($viewPerm = $modulePermissions->firstWhere('slug', 'like', 'view-' . strtolower($module)))
                                        <input {{ in_array($viewPerm->id, $assignedPermissions) ? 'checked' : '' }} 
                                               class="checkbox checkbox-sm" 
                                               name="permissions[]" 
                                               type="checkbox" 
                                               value="{{ $viewPerm->id }}">
                                    @endif
                                </td>
                                <td class="!py-5.5 text-center">
                                    @if($createPerm = $modulePermissions->firstWhere('slug', 'like', 'create-' . strtolower($module)))
                                        <input {{ in_array($createPerm->id, $assignedPermissions) ? 'checked' : '' }} 
                                               class="checkbox checkbox-sm" 
                                               name="permissions[]" 
                                               type="checkbox" 
                                               value="{{ $createPerm->id }}">
                                    @endif
                                </td>
                                <td class="!py-5.5 text-center">
                                    @if($editPerm = $modulePermissions->firstWhere('slug', 'like', 'edit-' . strtolower($module)))
                                        <input {{ in_array($editPerm->id, $assignedPermissions) ? 'checked' : '' }} 
                                               class="checkbox checkbox-sm" 
                                               name="permissions[]" 
                                               type="checkbox" 
                                               value="{{ $editPerm->id }}">
                                    @endif
                                </td>
                                <td class="!py-5.5 text-center">
                                    @if($deletePerm = $modulePermissions->firstWhere('slug', 'like', 'delete-' . strtolower($module)))
                                        <input {{ in_array($deletePerm->id, $assignedPermissions) ? 'checked' : '' }} 
                                               class="checkbox checkbox-sm" 
                                               name="permissions[]" 
                                               type="checkbox" 
                                               value="{{ $deletePerm->id }}">
                                    @endif
                                </td>
                                <td class="!py-5.5 text-center">
                                    @foreach($modulePermissions->filter(function($item) use ($module) {
                                        return !in_array(explode('-', $item->slug)[0], ['view', 'create', 'edit', 'delete']);
                                    }) as $otherPerm)
                                        <div class="flex items-center gap-2 mb-2">
                                            <input {{ in_array($otherPerm->id, $assignedPermissions) ? 'checked' : '' }} 
                                                   class="checkbox checkbox-sm" 
                                                   name="permissions[]" 
                                                   type="checkbox" 
                                                   value="{{ $otherPerm->id }}">
                                            <span class="text-xs">{{ ucfirst(explode('-', $otherPerm->slug)[0]) }}</span>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer justify-end py-7.5 gap-2.5">
                <button class="btn btn-light btn-outline" id="reset-permissions">
                    Mặc định
                </button>
                <button class="btn btn-primary" id="save-permissions">
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    $(function() {
        // Reset về quyền mặc định
        $('#reset-permissions').on('click', function() {
            // Bỏ chọn tất cả
            $('input[name="permissions[]"]').prop('checked', false);
            
            // Chọn các quyền mặc định (có thể tùy chỉnh)
            $('input[value="1"]').prop('checked', true); // giả sử quyền xem dashboard có id = 1
        });
        
        // Lưu thay đổi
        $('#save-permissions').on('click', async function() {
            let permissions = [];
            $('input[name="permissions[]"]:checked').each(function() {
                permissions.push($(this).val());
            });
            
            let method = "post",
                url = "/role/{{ $details['level']->id }}/{{ $details['department']->id }}/permissions",
                params = null,
                data = {
                    permissions: permissions
                };
                
            let res = await axiosTemplate(method, url, params, data);
            
            switch (res.data.status) {
                case 200:
                    showAlert('success', res.data.message);
                    break;
                default:
                    showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                    break;
            }
        });
    });
</script>
@endpush