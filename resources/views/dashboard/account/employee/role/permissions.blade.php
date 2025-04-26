@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Phân quyền cho chức vụ {{$details['level']->name}} - {{$details['department']->name}}
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <button class="btn btn-sm btn-light" id="select-all-permissions">
                <i class="ki-filled ki-check-circle fs-2"></i> Chọn tất cả
            </button>
            <button class="btn btn-sm btn-light" id="unselect-all-permissions">
                <i class="ki-filled ki-cross-circle fs-2"></i> Bỏ chọn tất cả
            </button>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Card phân quyền -->
        <div class="card card-grid min-w-full">
            <div class="card-header py-5 flex-wrap gap-2">
                <h3 class="card-title">
                    Bảng phân quyền
                </h3>
                <div class="flex flex-wrap gap-2">
                    <!-- Tổng quan phân quyền -->
                    <div class="flex items-center gap-3">
                        <span class="text-gray-700 text-sm">Đã chọn: <span id="selected-count" class="font-medium text-primary">0</span>/<span id="total-count" class="font-medium">0</span></span>
                        <div class="w-36 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div id="permission-progress-bar" class="h-full bg-primary transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="permissions-form">
                <div class="card-body">
                    <div class="datatable-initialized">
                        <div class="scrollable-x-auto">
                            <table class="table table-border">
                                <thead>
                                    <tr>
                                        <th class="min-w-[200px]">
                                            <span class="text-gray-700 font-normal">Module</span>
                                        </th>
                                        <th class="min-w-[500px]">
                                            <span class="text-gray-700 font-normal">Quyền</span>
                                        </th>
                                        <th class="w-[200px] text-center">
                                            <span class="text-gray-700 font-normal">Thao tác</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-900 font-medium">
                                    @foreach($permissions as $module => $modulePermissions)
                                    <tr>
                                        <td class="!py-5.5 align-top">
                                            <div class="font-medium">{{ $module }}</div>
                                        </td>
                                        <td class="!py-5.5">
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                @foreach($modulePermissions as $permission)
                                                <label class="form-label flex items-center gap-2.5">
                                                    <input class="checkbox module-{{ $module }}" 
                                                           type="checkbox" name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           id="perm_{{ $permission->id }}"
                                                           @if(in_array($permission->id, $assignedPermissions)) checked @endif
                                                           @if(strpos($permission->slug, 'view-') !== 0 && $viewPerm = $modulePermissions->first(function($p) { return strpos($p->slug, 'view-') === 0; }))
                                                           data-dependency="{{ $viewPerm->id }}"
                                                           @endif
                                                    >
                                                    @php
                                                        $icon = '';
                                                        $action = explode('-', $permission->slug)[0] ?? '';
                                                        switch($action) {
                                                            case 'view':
                                                                $icon = '<i class="ki-filled ki-eye text-primary me-1"></i>';
                                                                break;
                                                            case 'create':
                                                                $icon = '<i class="ki-filled ki-plus-squared text-success me-1"></i>';
                                                                break;
                                                            case 'edit':
                                                                $icon = '<i class="ki-filled ki-notepad-edit text-warning me-1"></i>';
                                                                break;
                                                            case 'delete':
                                                                $icon = '<i class="ki-filled ki-trash text-danger me-1"></i>';
                                                                break;
                                                            case 'approve':
                                                                $icon = '<i class="ki-filled ki-check-circle text-success me-1"></i>';
                                                                break;
                                                            case 'assign':
                                                                $icon = '<i class="ki-filled ki-user-tick text-primary me-1"></i>';
                                                                break;
                                                            case 'configure':
                                                                $icon = '<i class="ki-filled ki-setting-2 text-info me-1"></i>';
                                                                break;
                                                            default:
                                                                $icon = '<i class="ki-filled ki-shield-tick text-primary me-1"></i>';
                                                        }
                                                    @endphp
                                                    {!! $icon !!}{{ $permission->name }}
                                                </label>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="!py-5.5 text-center align-top">
                                            <div class="flex flex-col gap-2">
                                                <button type="button" class="btn btn-sm btn-light select-module" data-module="{{ $module }}">
                                                    <i class="ki-filled ki-check fs-2"></i> Chọn tất cả
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light unselect-module" data-module="{{ $module }}">
                                                    <i class="ki-filled ki-cross fs-2"></i> Bỏ chọn tất cả
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-end py-7.5 gap-2.5">
                    <button type="button" class="btn btn-light btn-outline" id="reset-permissions">
                        Mặc định
                    </button>
                    <button type="button" class="btn btn-primary" id="save-permissions">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>

        <!-- Card thành viên trong vai trò -->
        <div class="card card-grid min-w-full">
            <div class="card-header py-5 flex-wrap gap-2">
                <h3 class="card-title">
                    Thành viên có vai trò này
                </h3>
                <div class="flex gap-6">
                    <div class="relative">
                        <i class="ki-filled ki-magnifier leading-none text-md text-gray-500 absolute top-1/2 start-0 -translate-y-1/2 ms-3"></i>
                        <input class="input input-sm pl-8" id="search-members" placeholder="Tìm kiếm thành viên" type="text">
                    </div>
                </div>
            </div>
            <div class="card-body" id="role-members">
                <div data-datatable="true" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-border" id="members_table">
                            <thead>
                                <tr>
                                    <th class="w-[60px] text-center">STT</th>
                                    <th class="min-w-[300px]">
                                        <span class="sort asc">
                                            <span class="sort-label text-gray-700 font-normal">Thành viên</span>
                                            <span class="sort-icon"></span>
                                        </span>
                                    </th>
                                    <th class="w-[225px]">
                                        <span class="sort">
                                            <span class="sort-label text-gray-700 font-normal">Chức vụ</span>
                                            <span class="sort-icon"></span>
                                        </span>
                                    </th>
                                    <th class="w-[60px]"></th>
                                </tr>
                            </thead>
                            <tbody id="members-list">
                                <!-- Nội dung sẽ được tải bằng AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card FAQ -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Hướng dẫn phân quyền
                </h3>
            </div>
            <div class="card-body py-3">
                <div data-accordion="true" data-accordion-expand-all="true">
                    <div class="accordion-item [&:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true">
                        <button class="accordion-toggle py-4" data-accordion-toggle="#faq_1_content">
                            <span class="text-base text-gray-900">
                                Phân quyền là gì?
                            </span>
                            <i class="ki-filled ki-plus text-gray-600 text-sm accordion-active:hidden block"></i>
                            <i class="ki-filled ki-minus text-gray-600 text-sm accordion-active:block hidden"></i>
                        </button>
                        <div class="accordion-content hidden" id="faq_1_content">
                            <div class="text-gray-700 text-md pb-4">
                                Phân quyền là việc xác định và gán các quyền cụ thể cho các vai trò trong hệ thống. Điều này giúp kiểm soát người dùng có thể thực hiện những hành động nào trong hệ thống.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item [&:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true">
                        <button class="accordion-toggle py-4" data-accordion-toggle="#faq_2_content">
                            <span class="text-base text-gray-900">
                                Làm thế nào để phân quyền hiệu quả?
                            </span>
                            <i class="ki-filled ki-plus text-gray-600 text-sm accordion-active:hidden block"></i>
                            <i class="ki-filled ki-minus text-gray-600 text-sm accordion-active:block hidden"></i>
                        </button>
                        <div class="accordion-content hidden" id="faq_2_content">
                            <div class="text-gray-700 text-md pb-4">
                                Để phân quyền hiệu quả, bạn nên áp dụng nguyên tắc "quyền tối thiểu cần thiết". Điều này có nghĩa là chỉ cấp cho người dùng những quyền mà họ thực sự cần để thực hiện công việc của mình.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item [&:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true">
                        <button class="accordion-toggle py-4" data-accordion-toggle="#faq_3_content">
                            <span class="text-base text-gray-900">
                                Nên lưu ý điều gì khi phân quyền?
                            </span>
                            <i class="ki-filled ki-plus text-gray-600 text-sm accordion-active:hidden block"></i>
                            <i class="ki-filled ki-minus text-gray-600 text-sm accordion-active:block hidden"></i>
                        </button>
                        <div class="accordion-content hidden" id="faq_3_content">
                            <div class="text-gray-700 text-md pb-4">
                                Khi phân quyền, bạn cần đảm bảo rằng mỗi vai trò có đủ quyền để hoàn thành nhiệm vụ của họ, nhưng không quá nhiều để gây ra rủi ro bảo mật. Định kỳ xem xét và điều chỉnh quyền để phù hợp với nhu cầu thay đổi của tổ chức.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push("scripts")
<script>
    $(function() {
        // Calculate total permissions
        const totalPermissions = $('input[name="permissions[]"]').length;
        $('#total-count').text(totalPermissions);
        
        // Load role members
        loadRoleMembers();
        
        // Update permissions count
        function updatePermissionsCount() {
            const selectedPermissions = $('input[name="permissions[]"]:checked').length;
            $('#selected-count').text(selectedPermissions);
            
            const percentage = Math.round((selectedPermissions / totalPermissions) * 100);
            $('#permission-progress-bar').css('width', percentage + '%');
        }
        
        // Initial update
        updatePermissionsCount();
        
        // Handle dependencies
        $('input[name="permissions[]"]').on('change', function() {
            const isChecked = $(this).prop('checked');
            const permId = $(this).val();
            
            // If unchecked, uncheck all permissions that depend on this one
            if (!isChecked) {
                $(`input[data-dependency="${permId}"]`).prop('checked', false);
            }
            
            // If checked, make sure the dependency is also checked
            if (isChecked) {
                const dependencyId = $(this).data('dependency');
                if (dependencyId) {
                    $(`#perm_${dependencyId}`).prop('checked', true);
                }
            }
            
            updatePermissionsCount();
        });
        
        // Select all permissions
        $('#select-all-permissions').on('click', function(e) {
            e.preventDefault();
            $('input[name="permissions[]"]').prop('checked', true);
            updatePermissionsCount();
        });
        
        // Unselect all permissions
        $('#unselect-all-permissions').on('click', function(e) {
            e.preventDefault();
            $('input[name="permissions[]"]').prop('checked', false);
            updatePermissionsCount();
        });
        
        // Select module permissions
        $('.select-module').on('click', function(e) {
            e.preventDefault();
            const module = $(this).data('module');
            $(`.module-${module}`).prop('checked', true);
            updatePermissionsCount();
        });
        
        // Unselect module permissions
        $('.unselect-module').on('click', function(e) {
            e.preventDefault();
            const module = $(this).data('module');
            $(`.module-${module}`).prop('checked', false);
            updatePermissionsCount();
        });
        
        // Reset to default permissions
        $('#reset-permissions').on('click', function(e) {
            e.preventDefault();
            // Uncheck all
            $('input[name="permissions[]"]').prop('checked', false);
            
            // Check basic viewing permissions
            $('input[name="permissions[]"]').each(function() {
                if ($(this).next('label').text().toLowerCase().includes('view')) {
                    $(this).prop('checked', true);
                }
            });
            
            updatePermissionsCount();
        });
        
        // Save permissions
        $('#save-permissions').on('click', async function(e) {
            e.preventDefault();
            Notiflix.Loading.circle('Đang lưu quyền...');
            let permissions = [];
            $('input[name="permissions[]"]:checked').each(function() {
                permissions.push($(this).val());
            });
            
            try {
                let res = await axiosTemplate(
                    "post",
                    "/role/{{ $details['level']->id }}/{{ $details['department']->id }}/permissions",
                    null,
                    { permissions: permissions }
                );
                
                Notiflix.Loading.remove();
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                } else {
                    showAlert('warning', res.data.message || "Đã có lỗi xảy ra khi lưu quyền!");
                }
            } catch (error) {
                Notiflix.Loading.remove();
                showAlert('error', "Đã có lỗi xảy ra khi lưu quyền!");
                console.error(error);
            }
        });
        
        // Filter members
        $('#search-members').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $("#members-list tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        // Load role members function
        function loadRoleMembers() {
            $.ajax({
                url: "{{ route('dashboard.account.role.memberInRole') }}",
                type: "GET",
                data: {
                    filter: {
                        department_id: {{ $details['department']->id }},
                        level_id: {{ $details['level']->id }}
                    }
                },
                success: function(response) {
                    if (response.status === 200) {
                        $('#members-list').html(response.content);
                    } else {
                        $('#members-list').html('<tr><td colspan="4" class="text-center">Không thể tải danh sách thành viên</td></tr>');
                    }
                },
                error: function() {
                    $('#members-list').html('<tr><td colspan="4" class="text-center">Không thể tải danh sách thành viên</td></tr>');
                }
            });
        }
    });
</script>
@endpush